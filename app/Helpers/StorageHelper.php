<?php

namespace App\Helpers;

use App\Exceptions\CustomErrorException;
use App\Exceptions\FileUploadException;
use App\Jobs\MoveFileJob;
use App\Models\Interfaces\StoreFileInterface;
use App\Models\TusFileData;
use Aws\Middleware;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToCheckDirectoryExistence;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

/**
 * Class StorageHelper
 * @package App\Helpers
 */
class StorageHelper
{
    public const SOURCE_FILE_PATH = 'source/';

    private static array $cache = [];

    public static function saveFile(
        mixed $content,
        string $filePath,
        string $name,
        string $mimeType,
        string $mediaType,
        int $mediaId,
        string $target = TusFileData::TARGET_DATA,
        string $disk = 's3',
    ): TusFileData {
        Storage::disk($disk)->put($filePath, $content);

        $fileData = new TusFileData();
        $fileData->name = $name;
        $fileData->key = $filePath;
        $fileData->size = strlen($content);
        $fileData->type = $mimeType;
        $fileData->target = $target;
        $fileData->disk = 's3';
        $fileData->media_type = $mediaType;
        $fileData->media_id = $mediaId;

        return $fileData;
    }

    /**
     * @param TusFileData|null $fileData
     * @param bool $returnTempUrl
     * @param string|null $defaultUrl
     * @return string|null
     */
    public static function getFileUrl(
        ?TusFileData $fileData,
        bool $returnTempUrl = false,
        ?string $defaultUrl = null,
    ): ?string {
        if (!$fileData) {
            if (!$defaultUrl) {
                return null;
            }

            return self::getStorageUri($defaultUrl);
        }

        return self::getStorageUrl($fileData->key, $defaultUrl, $returnTempUrl, $fileData->disk);
    }

    /**
     * @param TusFileData $fileData
     * @param bool $returnTempUrl
     * @return string|null
     */
    public static function getFileHeadUrl(TusFileData $fileData, bool $returnTempUrl = false): ?string
    {
        return self::headUrl($fileData->key, $returnTempUrl, $fileData->disk);
    }

    /**
     * @param TusFileData $fileData
     * @return string
     * @throws FileNotFoundException
     */
    public static function getFileContent(TusFileData $fileData): string
    {
        return self::contentFromDriver($fileData->key, $fileData->disk);
    }

    /**
     * @param TusFileData $fileData
     * @return mixed
     * @throws FileNotFoundException
     */
    public static function getMediaData(TusFileData $fileData): mixed
    {
        $content = self::getFileContent($fileData);

        return json_decode($content, true);
    }

    /**
     * @param TusFileData $file
     */
    public static function removeTusFile(TusFileData $file): void
    {
        $usage = TusFileData::query()
            ->where('key', $file->key)
            ->where('disk', $file->disk)
            ->count();
        if ($usage == 1) {
            Storage::disk($file->disk)->delete($file->key);
        }


        $file->delete();
    }

    public static function existFile(TusFileData $fileData): bool
    {
        return self::exist($fileData->key, $fileData->disk);
    }

    /**
     * Store file
     *
     * @param mixed $file
     * @param string $filePath
     * @param string|null $oldFile
     * @return string
     */
    public static function storeFile(mixed $file, string $filePath, string $oldFile = null): string
    {
        $disk = $disk ?? config('filesystems.default');
        if ($oldFile) {
            Storage::disk($disk)->delete($oldFile);
        }

        return Storage::disk($disk)
            ->putFile(
                $filePath,
                $file,
            );
    }

    /**
     * @param mixed $file
     * @param string $filePath
     * @param string $fileName
     * @param string|null $oldFile
     * @return string
     */
    public static function storeFileAs(mixed $file, string $filePath, string $fileName, string $oldFile = null): string
    {
        if ($oldFile) {
            Storage::disk()->delete($oldFile);
        }

        return Storage::disk()
            ->putFileAs(
                $filePath,
                $file,
                $fileName,
            );
    }

    /**
     * Generate full path uri for given key
     *
     * @param array $param
     * @param array $keys
     * @return array
     */
    public static function generateUriByKey(array $param, array $keys): array
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $param) && $param[$key]) {
                $param[$key] = self::getStorageUrl($param[$key]);
            }
        }

        return $param;
    }

    /**
     * @param string $url
     * @param string|null $defaultUrl
     * @param bool $returnTempUrl
     * @param string|null $disk
     * @return string|null
     */
    public static function getStorageUrl(
        string $url,
        ?string $defaultUrl = null,
        bool $returnTempUrl = false,
        ?string $disk = null,
    ): ?string {
        //sub folder in public
        if (str_starts_with($url, '/vendor')) {
            return $url;
        }

        if (!empty($defaultUrl) && empty($url)) {
            return self::getStorageUri($defaultUrl);
        }

        if (empty($url)) {
            return null;
        }

        if ((config('app.env') == 'local') && Storage::disk('public')->exists($url)) {
            return Storage::disk('public')->url($url);
        }

        if ($returnTempUrl) {
            try {
                $key = 'FILE#GET#' . ($disk ?? 's3') . '#' . $url . '#' . implode('#', self::getTrackingParams());

                return Cache::remember($key, 3600, function () use ($disk, $url, $defaultUrl) {
                    if (!empty($defaultUrl) && !self::exist($url, $disk)) {
                        return self::getStorageUri($defaultUrl);
                    }

                    return self::getTemporaryUrl($url, $disk);
                });
            } catch (RuntimeException) {
                //This driver does not support creating temporary urls
            }
        }

        $key = 'FILE#GET#' . ($disk ?? 's3') . '#' . $url;

        return Cache::remember($key, 3600, function () use ($disk, $url, $defaultUrl) {
            // Check if file exists too slow. We should avoid using default

            if (!empty($defaultUrl) && !self::exist($url, $disk)) {
                return self::getStorageUri($defaultUrl);
            }

            return self::getStorageUri($url, $disk);
        });
    }

    protected static function getTemporaryUrl(string $url, ?string $disk = null, $name = 'GetObject'): string
    {
        $adapter = Storage::disk($disk);
        if (!($adapter instanceof AwsS3V3Adapter)) {
            return Storage::disk($disk)->temporaryUrl(
                $url,
                now()->addMinutes(config('filesystems.disks.s3.S3_TEMPORARY_URL_TTL')),
            );
        }

        $s3Client = $adapter->getClient();
        $command = $s3Client->getCommand($name, [
            'Bucket' => config('filesystems.disks')[$disk ?? 's3']['bucket'],
            'Key' => $adapter->path($url),
        ]);

        //Add tracking info for Athena
        $trackingParams = self::getTrackingParams();

        if (!empty($trackingParams)) {
            $command->getHandlerList()->appendBuild(
                Middleware::mapRequest(function (RequestInterface $request) use ($trackingParams) {
                    return $request->withUri(
                        $request->getUri()->withQuery($request->getUri()->getQuery()),
                    );
                }),
            );
        }

        return (string)$s3Client->createPresignedRequest(
            $command,
            now()->addMinutes(config('filesystems.disks.s3.S3_TEMPORARY_URL_TTL')),
        )->getUri();
    }

    /**
     * @throws CustomErrorException
     */
    public static function getSignedUrlForWrite(string $path): string
    {
        $adapter = Storage::disk();
        if (!($adapter instanceof AwsS3V3Adapter)) {
            throw new CustomErrorException('Unsupported storage type. Please use aws s3', 422);
        }

        $s3Client = $adapter->getClient();
        $command = $s3Client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks')[$disk ?? 's3']['bucket'],
            'Key' => $adapter->path($path),
        ]);

        return $s3Client->createPresignedRequest($command, '+1 hours')->getUri();
    }

    private static function getTrackingParams(): array
    {
        if (!auth()->check()) {
            return [];
        }

        $data = [];
        $data['user_id'] = auth()->user()->id;

        return $data;
    }

    public static function headUrl(string $url, bool $returnTempUrl = false, ?string $disk = null): ?string
    {
        try {
            $key = 'FILE#HEAD#' . ($disk ?? 's3') . '#' . $url;

            return Cache::remember($key, 3600, fn() => self::getTemporaryUrl($url, $disk, 'HeadObject'));
        } catch (Exception) {
        }

        return null;
    }

    /**
     * @param UploadedFile $file
     * @param string $fileKey
     * @param string $target
     * @param string $disk
     * @return TusFileData
     * @throws FileUploadException
     */
    public static function storeUploadedFileData(
        UploadedFile $file,
        string $fileKey,
        string $target = TusFileData::TARGET_SOURCE,
        string $disk = 's3',
    ): TusFileData {
        $stream = fopen($file->getRealPath(), 'r');
        if (
            !Storage::disk($disk)->put(
                $fileKey,
                $stream,
            )
        ) {
            throw new FileUploadException();
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        $fileData = new TusFileData();
        $fileData->name = $file->getClientOriginalName();
        $fileData->size = $file->getSize();
        $fileData->type = $file->getClientMimeType();
        $fileData->key = $fileKey;
        $fileData->target = $target;
        $fileData->disk = $disk;
        $fileData->save();

        return $fileData;
    }

    public static function storeFileData(
        array $file,
        StoreFileInterface $model,
        ?string $newFilePath = null,
        string $target = TusFileData::TARGET_SOURCE,
        string $disk = 's3-uppy',
    ): ?TusFileData {
        $fileData = new TusFileData();
        $fileData->name = $file['name'] . (
            str_ends_with($file['name'], '.' . $file['extension'])
                ? ''
                : ('.' . $file['extension']));
        $fileData->size = $file['size'];
        $fileData->type = $file['type'];
        $fileData->key = $file['file_name'] ?? $file['uploadURL'];
        $fileData->target = $target;
        $fileData->disk = $disk;

        $model->storedFiles()->save($fileData);
        if ($disk != 's3') {
            MoveFileJob::dispatch($fileData, $newFilePath)->onQueue('moveFile');
        }

        return $fileData;
    }


    /**
     * @param UploadedFile $file
     * @param string $fileKey
     * @param string $disk
     * @return TusFileData
     * @throws FileUploadException
     */
    public static function replaceDataSourceUploadedFileData(
        UploadedFile $file,
        string $fileKey,
        string $disk = 's3',
    ): TusFileData {
        return self::storeUploadedFileData($file, $fileKey, TusFileData::TARGET_SOURCE, $disk);
    }

    /**
     * @param $filePath
     * @param string $driver
     * @return string
     * @throws FileNotFoundException
     */
    public static function contentFromDriver($filePath, string $driver = 's3'): string
    {
        if (!self::exist($filePath, $driver)) {
            throw new FileNotFoundException('File ' . $filePath . ' does not exists', 422);
        }

        return Storage::disk($driver)->get($filePath);
    }

    public static function exist(string $url, $driver = 's3'): bool
    {
        try {
            return Storage::disk($driver)->exists($url);
        } catch (UnableToCheckDirectoryExistence) {
            return false;
        }
    }

    /**
     * @param string $path
     * @return UploadedFile
     */
    public static function getFileFromUrl(string $path): UploadedFile
    {
        $info = pathinfo($path);
        $file = '/tmp/' . $info['basename'];

        if (self::exist($path, 's3-uppy')) {
            Storage::disk('local')->delete($file);
            Storage::disk('local')->writeStream($file, Storage::disk('s3-uppy')->readStream($path));
            $file = Storage::disk('local')->path($file);
        } else {
            copy($path, $file);
        }

        return new UploadedFile($file, $info['basename']);
    }

    /**
     * @param string|null $resource
     * @param string|null $disk
     * @return string|null
     */
    public static function getStorageUri(?string $resource = null, ?string $disk = null): ?string
    {
        return Storage::disk($disk)->url($resource);
    }

    public static function remove(string $fileLink): bool
    {
        return Storage::disk()->delete($fileLink);
    }

    private static function getSourceFilePath(StoreFileInterface $media, string $name): string
    {
        $extension = last(explode('.', $name));
        $subDir = $media->client_id ?? 'system';

        return $subDir . '/'
            . self::SOURCE_FILE_PATH
            . $media->getKey() . '.' . Str::random() . '.' . $extension;
    }


    public static function getSignedStorageUrl(
        ?string $path,
        ?string $disk = null,
    ): ?string {
        if (empty($path)) {
            return null;
        }

        $disk = $disk ?? config('filesystems.default');
        if (config('filesystems.disks')[$disk]['driver'] == 'local') {
            return Storage::disk($disk)->url($path);
        }


        $key = 'FILE#GET#' . ($disk ?? 's3') . '#' . $path . '#' . implode('#', self::getTrackingParams());
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        self::$cache[$key] = Cache::remember($key, 3600, function () use ($disk, $path) {
            return self::getTemporaryUrl($path, $disk);
        });

        return self::$cache[$key];
    }
}
