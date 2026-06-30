<?php
/**
 * File Uploader
 * Handles file uploads securely with validation and UUID renaming
 */

class Uploader {
    private string $uploadPath;
    private array $allowedTypes;
    private int $maxSize;
    private array $errors = [];

    public function __construct(string $uploadPath = null, array $allowedTypes = null, int $maxSize = null) {
        $this->uploadPath = $uploadPath ?: UPLOAD_PATH;
        $this->allowedTypes = $allowedTypes ?: array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_AUDIO_TYPES, ALLOWED_DOC_TYPES);
        $this->maxSize = $maxSize ?: MAX_IMAGE_SIZE;
    }

    /**
     * Upload a single file
     */
    public function upload(string $field, string $subPath = ''): ?string {
        if (!isset($_FILES[$field])) {
            $this->errors[] = 'No file uploaded';
            return null;
        }

        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->translateError($file['error']);
            return null;
        }

        if (!$this->validate($file)) {
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = $this->generateUUID() . '.' . strtolower($extension);
        $targetPath = $this->uploadPath . $subPath;

        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $targetPath . $newName)) {
            $this->errors[] = 'Failed to move uploaded file';
            return null;
        }

        return $newName;
    }

    /**
     * Validate file (size, type)
     */
    private function validate(array $file): bool {
        // Check size
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = 'File size exceeds limit';
            return false;
        }

        // Check extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes, true)) {
            $this->errors[] = 'Invalid file type';
            return false;
        }

        // Check MIME type (server-side)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'audio/mpeg' => 'mp3',
            'audio/mp4' => 'm4a',
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
        ];

        if (!isset($allowedMimeTypes[$mimeType])) {
            $this->errors[] = 'Invalid file MIME type';
            return false;
        }

        // Verify extension matches MIME type
        $expectedExt = $allowedMimeTypes[$mimeType];
        if ($extension !== $expectedExt) {
            $this->errors[] = 'File extension does not match file type';
            return false;
        }

        return true;
    }

    /**
     * Generate UUID v4
     */
    private function generateUUID(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 65535), mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(0, 16383),
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
        );
    }

    /**
     * Translate PHP upload error code to message
     */
    private function translateError(int $code): string {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL    => 'File only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
            UPLOAD_ERR_EXTENSION  => 'Upload stopped by extension'
        ];
        return $messages[$code] ?? 'Unknown upload error';
    }

    /**
     * Get errors
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * Clear errors
     */
    public function clearErrors(): void {
        $this->errors = [];
    }
}
