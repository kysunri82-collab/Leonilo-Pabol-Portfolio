<?php

const MAX_IMAGE_UPLOAD_SIZE = 5 * 1024 * 1024;
const MAX_IMAGE_WIDTH = 6000;
const MAX_IMAGE_HEIGHT = 6000;
const MAX_IMAGE_PIXELS = 20000000;

function storeOptionalImageUpload($file): ?string {
    if ($file === null) {
        return null;
    }

    if (!is_array($file)) {
        throw new InvalidArgumentException("Invalid image upload.");
    }

    if (($file["error"] ?? null) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    return storeUploadedImage($file);
}

function storeUploadedImage(array $file): string {
    if (!isset($file["error"]) || !is_int($file["error"])) {
        throw new InvalidArgumentException("Invalid image upload.");
    }

    if ($file["error"] === UPLOAD_ERR_INI_SIZE || $file["error"] === UPLOAD_ERR_FORM_SIZE) {
        throw new InvalidArgumentException("Image must be 5 MB or smaller.");
    }

    if ($file["error"] === UPLOAD_ERR_NO_FILE) {
        throw new InvalidArgumentException("Choose an image file.");
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException("Image upload failed. Please try again.");
    }

    $temporaryPath = $file["tmp_name"] ?? "";

    if (!is_string($temporaryPath) || $temporaryPath === "" || !is_uploaded_file($temporaryPath)) {
        throw new InvalidArgumentException("Invalid image upload.");
    }

    $actualSize = @filesize($temporaryPath);

    if ($actualSize === false || $actualSize <= 0) {
        throw new InvalidArgumentException("The uploaded image is empty or invalid.");
    }

    if ($actualSize > MAX_IMAGE_UPLOAD_SIZE) {
        throw new InvalidArgumentException("Image must be 5 MB or smaller.");
    }

    if (!class_exists("finfo")) {
        throw new RuntimeException("Image validation is unavailable.");
    }

    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $detectedMime = @$fileInfo->file($temporaryPath);
    $imageInfo = @getimagesize($temporaryPath);

    $allowedImages = [
        "image/jpeg" => ["extension" => "jpg", "type" => IMAGETYPE_JPEG],
        "image/png" => ["extension" => "png", "type" => IMAGETYPE_PNG],
        "image/webp" => ["extension" => "webp", "type" => IMAGETYPE_WEBP]
    ];

    if (
        !is_string($detectedMime) ||
        !isset($allowedImages[$detectedMime]) ||
        !is_array($imageInfo) ||
        !isset($imageInfo[0], $imageInfo[1], $imageInfo[2]) ||
        $imageInfo[2] !== $allowedImages[$detectedMime]["type"]
    ) {
        throw new InvalidArgumentException("Choose a valid JPG, JPEG, PNG, or WEBP image.");
    }

    $width = $imageInfo[0];
    $height = $imageInfo[1];

    if (
        !is_int($width) ||
        !is_int($height) ||
        $width < 1 ||
        $height < 1 ||
        $width > MAX_IMAGE_WIDTH ||
        $height > MAX_IMAGE_HEIGHT ||
        $width > intdiv(MAX_IMAGE_PIXELS, $height)
    ) {
        throw new InvalidArgumentException(
            "Image dimensions must be no larger than 6000 x 6000 pixels and contain 20 megapixels or fewer."
        );
    }

    $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . "uploads";

    if (!is_dir($uploadDirectory) || is_link($uploadDirectory) || !is_writable($uploadDirectory)) {
        throw new RuntimeException("Image upload storage is unavailable.");
    }

    try {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $filename = bin2hex(random_bytes(16)) . "." . $allowedImages[$detectedMime]["extension"];
            $destination = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

            if (!file_exists($destination)) {
                if (!@move_uploaded_file($temporaryPath, $destination)) {
                    throw new RuntimeException("Image upload could not be saved.");
                }

                return "uploads/" . $filename;
            }
        }
    } catch (Throwable $error) {
        if ($error instanceof InvalidArgumentException) {
            throw $error;
        }

        throw new RuntimeException("Image upload could not be saved.");
    }

    throw new RuntimeException("Image upload could not be saved.");
}

function removeUploadedImage(string $relativePath): void {
    if (!preg_match('#^uploads/[a-f0-9]{32}\.(jpg|png|webp)$#D', $relativePath)) {
        return;
    }

    $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . "uploads";
    $filename = substr($relativePath, strlen("uploads/"));
    $filePath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

    if (is_file($filePath) && !is_link($filePath)) {
        @unlink($filePath);
    }
}
