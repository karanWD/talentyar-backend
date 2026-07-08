<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileAttributes;
use RuntimeException;
use Throwable;

class MigrateS3Objects extends Command
{
    protected $signature = 'storage:migrate-s3
        {--source=s3_parspack : Source filesystem disk}
        {--target=s3_arvan : Target filesystem disk}
        {--prefix= : Only migrate objects under this prefix}
        {--limit=0 : Maximum number of source files to process; 0 means unlimited}
        {--overwrite : Replace files that already exist on the target disk}
        {--delete-source : Delete each source file after it is copied}
        {--continue-on-error : Continue migrating after an object copy fails}
        {--dry-run : Show what would be copied without writing anything}';

    protected $description = 'Migrate S3-compatible objects between configured filesystem disks';

    public function handle(): int
    {
        $sourceName = (string) $this->option('source');
        $targetName = (string) $this->option('target');
        $prefix = trim((string) $this->option('prefix'), '/');
        $limit = max(0, (int) $this->option('limit'));
        $overwrite = (bool) $this->option('overwrite');
        $deleteSource = (bool) $this->option('delete-source');
        $continueOnError = (bool) $this->option('continue-on-error');
        $dryRun = (bool) $this->option('dry-run');

        if ($sourceName === $targetName) {
            $this->error('Source and target disks must be different.');

            return self::FAILURE;
        }

        if ($dryRun && $deleteSource) {
            $this->warn('Ignoring --delete-source because --dry-run is enabled.');
            $deleteSource = false;
        }

        try {
            $sourceDisk = Storage::disk($sourceName);
            $targetDisk = Storage::disk($targetName);
        } catch (Throwable $e) {
            $this->error("Could not initialize disks: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info("Migrating files from [{$sourceName}] to [{$targetName}].");

        if ($prefix !== '') {
            $this->line("Prefix: {$prefix}");
        }

        if ($dryRun) {
            $this->warn('Dry run enabled. No files will be copied.');
        }

        $processed = 0;
        $copied = 0;
        $skipped = 0;
        $deleted = 0;
        $failed = 0;

        try {
            $listing = $sourceDisk->getDriver()->listContents($prefix, true);

            foreach ($listing as $attributes) {
                if (! $attributes->isFile()) {
                    continue;
                }

                if ($limit > 0 && $processed >= $limit) {
                    break;
                }

                $processed++;
                $path = $attributes->path();

                try {
                    if (! $overwrite && $targetDisk->exists($path)) {
                        $skipped++;
                        $this->components->twoColumnDetail($path, '<fg=yellow;options=bold>SKIPPED</>');

                        continue;
                    }

                    if ($dryRun) {
                        $copied++;
                        $this->components->twoColumnDetail(
                            $this->formatPathWithSize($path, $attributes),
                            '<fg=blue;options=bold>WOULD COPY</>'
                        );

                        continue;
                    }

                    $this->copyObject($sourceDisk, $targetDisk, $path);
                    $copied++;
                    $this->components->twoColumnDetail($path, '<fg=green;options=bold>COPIED</>');

                    if ($deleteSource) {
                        $sourceDisk->delete($path);
                        $deleted++;
                        $this->components->twoColumnDetail($path, '<fg=red;options=bold>DELETED SOURCE</>');
                    }
                } catch (Throwable $e) {
                    $failed++;
                    $this->error("Failed [{$path}]: {$e->getMessage()}");

                    if (! $continueOnError) {
                        break;
                    }
                }
            }
        } catch (Throwable $e) {
            $this->error("Could not list source files: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Migration summary:');
        $this->table(
            ['Processed', $dryRun ? 'Would copy' : 'Copied', 'Skipped', 'Deleted source', 'Failed'],
            [[$processed, $copied, $skipped, $deleted, $failed]]
        );

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function copyObject(FilesystemAdapter $sourceDisk, FilesystemAdapter $targetDisk, string $path): void
    {
        $stream = $sourceDisk->readStream($path);

        if ($stream === false || ! is_resource($stream)) {
            throw new RuntimeException('Could not open source object stream.');
        }

        try {
            $written = $targetDisk->writeStream($path, $stream);

            if ($written === false) {
                throw new RuntimeException('Could not write target object stream.');
            }
        } finally {
            fclose($stream);
        }
    }

    private function formatPathWithSize(string $path, mixed $attributes): string
    {
        if (! $attributes instanceof FileAttributes || $attributes->fileSize() === null) {
            return $path;
        }

        return "{$path} ({$this->formatBytes($attributes->fileSize())})";
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = (float) $bytes;

        foreach ($units as $unit) {
            if ($size < 1024 || $unit === 'TB') {
                return round($size, 2).' '.$unit;
            }

            $size /= 1024;
        }

        return $bytes.' B';
    }
}
