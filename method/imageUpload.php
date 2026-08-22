<?php 
function saveUploadedImage($file, $folder) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 5 * 1024 * 1024) {
        return null;
    }

    $imageInfo = @getimagesize($file['tmp_name']);
    $allowedMimeTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!$imageInfo || !isset($allowedMimeTypes[$imageInfo['mime']])) {
        return null;
    }

    $uploadDir = __DIR__ . '/../upload/' . $folder;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $extension = $allowedMimeTypes[$imageInfo['mime']];
    $safeName = uniqid('img_', true) . '.' . $extension;
    $fullPath = $uploadDir . '/' . $safeName; // Full path to save the file

    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return 'upload/' . $folder . '/' . $safeName;
    }

    return null;
}

function saveImageDataUrl($dataUrl, $folder) {
    if (empty($dataUrl) || strpos($dataUrl, 'data:image/') !== 0) {
        return null;
    }

    $uploadDir = __DIR__ . '/../upload/' . $folder;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $parts = explode(',', $dataUrl, 2);
    $header = $parts[0] ?? 'data:image/png;base64';
    $base64Data = $parts[1] ?? '';

    preg_match('/data:image\/([a-zA-Z0-9.+-]+)/', $header, $matches);
    $mimeTypes = ['jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
    $sourceExtension = !empty($matches[1]) ? strtolower($matches[1]) : '';
    if (!isset($mimeTypes[$sourceExtension])) {
        return null;
    }

    $decoded = base64_decode($base64Data, true);
    if ($decoded === false || strlen($decoded) > 5 * 1024 * 1024) {
        return null;
    }

    $imageInfo = @getimagesizefromstring($decoded);
    if (!$imageInfo || $imageInfo['mime'] !== $mimeTypes[$sourceExtension]) {
        return null;
    }
    $ext = $sourceExtension === 'jpeg' ? 'jpg' : $sourceExtension;

    $safeName = uniqid('crop_', true) . '.' . $ext;
    $fullPath = $uploadDir . '/' . $safeName;
    if (file_put_contents($fullPath, $decoded) !== false) {
        return 'upload/' . $folder . '/' . $safeName;
    }

    return null;
}
?>