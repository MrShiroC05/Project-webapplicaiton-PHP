<?php 
function saveUploadedImage($file, $folder) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    $uploadDir = __DIR__ . '/../upload/' . $folder;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); //  Get the file extension
    $safeName = uniqid('img_', true) . ($extension ? '.' . $extension : ''); // Generate a unique name for the file
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
        mkdir($uploadDir, 0777, true);
    }

    $parts = explode(',', $dataUrl, 2);
    $header = $parts[0] ?? 'data:image/png;base64';
    $base64Data = $parts[1] ?? '';

    preg_match('/data:image\/([a-zA-Z0-9.+-]+)/', $header, $matches);
    $ext = !empty($matches[1]) ? strtolower($matches[1]) : 'png';
    if ($ext === 'jpeg') {
        $ext = 'jpg';
    }

    $safeName = uniqid('crop_', true) . '.' . $ext;
    $fullPath = $uploadDir . '/' . $safeName;
    $decoded = base64_decode($base64Data, true);

    if ($decoded === false) {
        return null;
    }

    if (file_put_contents($fullPath, $decoded) !== false) {
        return 'upload/' . $folder . '/' . $safeName;
    }

    return null;
}
?>