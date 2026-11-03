<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db.php';

function respond(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requestData(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw ?: '{}', true);
        return is_array($decoded) ? $decoded : [];
    }

    return $_POST;
}

function positiveId(mixed $value): int
{
    $id = filter_var($value, FILTER_VALIDATE_INT);
    return ($id !== false && $id > 0) ? $id : 0;
}

function uploadProductImage(?array $file): string|false|null
{
    if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return false;
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return false;
    }

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowedMimeTypes[$mime])) {
        return false;
    }

    $extension = $allowedMimeTypes[$mime];
    $uploadDirectory = __DIR__ . '/../uploads/';

    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true)) {
        return false;
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $extension;
    if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . $filename)) {
        return false;
    }

    return 'uploads/' . $filename;
}

function removeProductImage(?string $relativePath): void
{
    if (!$relativePath || !str_starts_with($relativePath, 'uploads/')) {
        return;
    }

    $fullPath = __DIR__ . '/../' . $relativePath;
    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}
