<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class UploadService
{

    public static function storeProductImage(?array $file, ?string $existing = null): ?string
    {
        if ($file === null || ! isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $existing;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('The image upload failed. Please try again.');
        }

        $config = config('app.uploads');

        if ((int) $file['size'] > (int) $config['max_bytes']) {
            throw new RuntimeException('Images may be at most ' . round($config['max_bytes'] / 1048576, 1) . ' MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($file['tmp_name']);
        $allowed = (array) $config['mime_types'];

        if (! isset($allowed[$mime])) {
            throw new RuntimeException('Please upload a JPG, PNG or WebP image.');
        }

        $directory = (string) $config['directory'];

        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('The upload folder is not writable.');
        }

        $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];

        if (! move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
            throw new RuntimeException('The image could not be saved.');
        }

        if ($existing !== null && str_starts_with($existing, 'uploads/products/')) {
            self::remove($existing);
        }

        return 'uploads/products/' . $filename;
    }

    public static function remove(?string $path): void
    {
        if ($path === null || ! str_starts_with($path, 'uploads/products/')) {
            return;
        }

        $file = PUBLIC_PATH . '/' . $path;

        if (is_file($file)) {
            @unlink($file);
        }
    }
}
