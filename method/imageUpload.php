<?php 
function saveUploadedImage($file, $folder) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    $uploadDir = __DIR__ . '/../upload/' . $folder;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safeName = uniqid('img_', true) . ($extension ? '.' . $extension : '');
    $fullPath = $uploadDir . '/' . $safeName;

    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return 'upload/' . $folder . '/' . $safeName;
    }

    return null;
}
?>