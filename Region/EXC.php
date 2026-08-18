<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/notification.php';
include '../method/imageUpload.php';

function decodeRegionId($value) {
    $padding = str_repeat('=', (4 - strlen($value) % 4) % 4);
    $decoded = base64_decode(strtr($value, '-_', '+/') . $padding, true);
    return $decoded !== false ? $decoded : null;
}

function deleteImageFile($imagePath) {
    if (empty($imagePath)) {
        return;
    }

    $fullPath = __DIR__ . '/../' . $imagePath;
    if (file_exists($fullPath) && is_file($fullPath)) {
        unlink($fullPath);
    }
}

$regionRepo = new RegionRepository($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $regionName = $_POST['regionName'] ?? '';
        $regionDescription = $_POST['regionDescription'] ?? '';
        $croppedImagePath = saveImageDataUrl($_POST['regionImageData'] ?? null, 'region');
        $imagePath = $croppedImagePath ?? saveUploadedImage($_FILES['regionLogo'] ?? null, 'region');

        if ($regionRepo->create([
            'region_name' => $regionName,
            'region_description' => $regionDescription,
            'region_image' => $imagePath,
        ])) {
            redirectWithStatus('./listRegion.php', 'success', 'New region added successfully.');
        }

        redirectWithStatus('./listRegion.php', 'error', 'Unable to add region.');
    }

    if ($action === 'update') {
        $regionId = $_POST['region_id'] ?? '';
        $regionName = $_POST['regionName'] ?? '';
        $regionDescription = $_POST['regionDescription'] ?? '';
        $currentImage = $regionRepo->getById($regionId)['region_image'] ?? null;
        $croppedImageData = $_POST['regionImageData'] ?? null;
        $newImage = !empty($croppedImageData) ? saveImageDataUrl($croppedImageData, 'region') : saveUploadedImage($_FILES['regionLogo'] ?? null, 'region');

        if ($newImage !== null) {
            deleteImageFile($currentImage);
            if ($regionRepo->update($regionId, [
                'region_name' => $regionName,
                'region_description' => $regionDescription,
                'region_image' => $newImage,
            ])) {
                redirectWithStatus('./listRegion.php', 'success', 'Region updated successfully.');
            }
            redirectWithStatus('./listRegion.php', 'error', 'Unable to update region image.');
        }

        if ($regionRepo->update($regionId, [
            'region_name' => $regionName,
            'region_description' => $regionDescription,
        ])) {
            redirectWithStatus('./listRegion.php', 'success', 'Region updated successfully.');
        }

        redirectWithStatus('./listRegion.php', 'error', 'Unable to update region.');
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'GET') && ($_GET['action'] ?? '') === 'delete') {
    $regionId = decodeRegionId($_GET['region_id'] ?? '');
    if ($regionId === null || $regionId === '') {
        redirectWithStatus('./listRegion.php', 'error', 'Invalid region ID.');
    }

    $currentImage = $regionRepo->getById($regionId)['region_image'] ?? null;
    deleteImageFile($currentImage);

    if ($regionRepo->delete($regionId)) {
        redirectWithStatus('./listRegion.php', 'success', 'Region deleted successfully.');
    }

    redirectWithStatus('./listRegion.php', 'error', 'Unable to delete region.');
}

redirectWithStatus('./listRegion.php', 'error', 'Invalid request.');