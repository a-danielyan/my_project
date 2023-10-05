<?php

namespace App\Jobs;

use App\Models\TusFileData;
use Aws\S3\S3Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MoveFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    private TusFileData $fileData;
    public string|null $destKey;

    public function __construct(TusFileData $fileData, ?string $destKey = null)
    {
        $this->fileData = $fileData;
        $this->destKey = $destKey;
    }


    public function handle(): void
    {
        $this->fileData->refresh();
        if ($this->fileData->disk === 's3') {
            return;
        }

        $s3Client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $s3Client->copy(
            config('filesystems.disks.' . $this->fileData->disk . '.bucket'),
            $this->fileData->key,
            config('filesystems.disks.s3.bucket'),
            $this->destKey ?? $this->fileData->key,
        );

        $this->fileData->disk = 's3';
        if (isset($this->destKey)) {
            $this->fileData->key = $this->destKey;
        }
        $this->fileData->save();
    }

    public function failed(): void
    {
    }
}
