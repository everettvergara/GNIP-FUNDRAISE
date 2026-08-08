<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ConvertPublicImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp {--quality=82 : WebP quality 0-100}';

    protected $description = 'Convert public PNG/JPEG images to WebP for faster page loads';

    public function handle(): int
    {
        if (! function_exists('imagewebp')) {
            $this->error('GD WebP support is not available in this PHP build.');

            return self::FAILURE;
        }

        $quality = (int) $this->option('quality');
        $imagesPath = public_path('images');
        $converted = 0;
        $skipped = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($imagesPath),
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $extension = strtolower($file->getExtension());

            if (! in_array($extension, ['png', 'jpg', 'jpeg'], true)) {
                continue;
            }

            $sourcePath = $file->getPathname();
            $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $sourcePath);

            if ($webpPath === null) {
                continue;
            }

            if (file_exists($webpPath) && filemtime($webpPath) >= filemtime($sourcePath)) {
                $skipped++;

                continue;
            }

            $image = match ($extension) {
                'png' => @imagecreatefrompng($sourcePath),
                default => @imagecreatefromjpeg($sourcePath),
            };

            if ($image === false) {
                $this->warn("Skipped (unreadable): {$sourcePath}");

                continue;
            }

            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);

            if (imagewebp($image, $webpPath, $quality)) {
                $converted++;
                $this->line('Converted: '.str_replace(public_path().DIRECTORY_SEPARATOR, '', $webpPath));
            } else {
                $this->warn("Failed: {$sourcePath}");
            }

            imagedestroy($image);
        }

        $this->info("Done. Converted {$converted}, skipped {$skipped} up-to-date file(s).");

        return self::SUCCESS;
    }
}
