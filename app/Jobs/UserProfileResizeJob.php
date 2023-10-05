<?php

namespace App\Jobs;

use App\Helpers\StorageHelper;
use App\Models\TusFileData;
use App\Models\User;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserProfileResizeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const PROFILE_HEIGHT = 256;
    public const PROFILE_WIDTH = 256;

    private TusFileData $fileData;
    private string $action;

    private string $downloadedFileName;

    /**
     * Determine the timeout value for the maximum upload file size
     * max upload size from FE = 1Gb
     * */
    public int $timeout = 3800;

    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TusFileData $fileData, $action = 'create')
    {
        $this->fileData = $fileData;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->fileData->refresh();

        $this->downloadedFileName = storage_path('app/') . Str::uuid()
            . '.' . pathinfo($this->fileData->name, PATHINFO_EXTENSION);

        try {
            $this->downloadFile();
            $this->processDownloadedFile();
        } catch (Exception $e) {
            Log::error('Error Copy File From S3 Job');
            Log::error($e->getMessage());
        }

        unlink($this->downloadedFileName);
    }

    protected function downloadFile(): void
    {
        $s3Client = new S3Client([
            'region' => config('filesystems.disks.' . $this->fileData->disk . '.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.' . $this->fileData->disk . '.key'),
                'secret' => config('filesystems.disks.' . $this->fileData->disk . '.secret'),
            ],
        ]);

        //Copy from pub storage for parallel processing with moving to private bucket
        $s3Client->getObject(array(
            'Bucket' => config('filesystems.disks.' . $this->fileData->disk . '.bucket'),
            'Key' => $this->fileData->key,
            'SaveAs' => $this->downloadedFileName,
        ));

        $this->fileData->size = filesize($this->downloadedFileName);
        $this->fileData->type = mime_content_type($this->downloadedFileName);
        $this->fileData->save();
    }

    protected function processDownloadedFile(): void
    {
        if (!$this->resizeProfileImage($this->downloadedFileName)) {
            return;
        }

        /** @var User $user */
        $user = $this->fileData->media;

        $fileData = $this->fileData->replicate();
        $fileData->key = $user::PROFILE_URI . $user->getKey() . '.' . Str::random(8)
            . '.' . pathinfo($this->fileData->name, PATHINFO_EXTENSION);

        StorageHelper::storeFile(file_get_contents($this->downloadedFileName), $fileData->key);
        StorageHelper::removeTusFile($user->userDataFile);

        $user->userDataFile()->save($fileData);
    }

    public function resizeProfileImage(string $filePath): bool
    {
        $resizedFile = Image::make($filePath);

        if ($resizedFile->height() <= self::PROFILE_HEIGHT && $resizedFile->width() <= self::PROFILE_WIDTH) {
            return false;
        }

        $resizedFile->resize(self::PROFILE_WIDTH, self::PROFILE_HEIGHT);
        $resizedFile->save();

        return true;
    }
}
