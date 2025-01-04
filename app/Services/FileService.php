<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\Contracts\FileStorageInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileService implements FileStorageInterface
{
    /**
     * Store a video file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     * @throws Exception
     */
    public function storeVideo($file): array
    {
        // Validate the file
        if (!$file || !$file->isValid()) {
            throw new FileException('Invalid video file.');
        }

        // Validate MIME type
        $allowedMimeTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv'];
        $mimeType = $file->getClientMimeType();
        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new FileException('Unsupported video format.');
        }

        // Generate a unique filename
        $fileName = Str::random(32) . '.' . $file->getClientOriginalExtension();
        $filePath = "videos/{$fileName}";

        // Store the file
        $path = $file->storeAs('videos', $fileName, 'public');

        if (!$path) {
            throw new FileException('Failed to store video file.');
        }

        return [
            'video_path' => Storage::url($path),
        ];
    }

    /**
     * Store a file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     * @throws Exception
     */
    public function storeFile($file): array
    {
        // Validate the file
        if (!$file || !$file->isValid()) {
            throw new FileException('Invalid file.');
        }

        // Validate MIME type
        $allowedMimeTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        $mimeType = $file->getClientMimeType();
        if (!in_array($mimeType, $allowedMimeTypes)) {
            throw new FileException('Unsupported file format.');
        }

        // Generate a unique filename
        $fileName = Str::random(32) . '.' . $file->getClientOriginalExtension();
        $filePath = "files/{$fileName}";

        // Store the file
        $path = $file->storeAs('files', $fileName, 'public');

        if (!$path) {
            throw new FileException('Failed to store file.');
        }

        return [
            'file_path' => Storage::url($path),
        ];
    }
}
?>
