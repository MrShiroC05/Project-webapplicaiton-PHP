<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/notification.php';
include '../method/imageUpload.php';
include '../method/security.php';

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
    requireCsrfToken();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $regionName = cleanText($_POST['regionName'] ?? '', 100);
        $regionDescription = cleanText($_POST['regionDescription'] ?? '', 5000);
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
        $regionId = resolveEntityId($conn, 'region', cleanText($_POST['region_id'] ?? '', 32));
        $regionName = cleanText($_POST['regionName'] ?? '', 100);
        $regionDescription = cleanText($_POST['regionDescription'] ?? '', 5000);
        if (!validEntityId($regionId)) {
            redirectWithStatus('./listRegion.php', 'error', 'Invalid region ID.');
        }
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

if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($_POST['action'] ?? '') === 'delete') {
    requireCsrfToken();
    $regionId = resolveEntityId($conn, 'region', cleanText($_POST['region_id'] ?? '', 32));
    if ($regionId === null || $regionId === '') {
        redirectWithStatus('./listRegion.php', 'error', 'Invalid region ID.');
    }

    $currentImage = $regionRepo->getById($regionId)['region_image'] ?? null;
    deleteImageFile($currentImage);

    $championRepo = new ChampionRepository($conn);
    $childChampions = $championRepo->getAllWithRegionId($regionId);
    $conn->begin_transaction();
    $childrenDeleted = true;

    // A region owns its champions, so remove each champion's children before deleting the region.
    foreach ($childChampions as $champion) {
        deleteImageFile($champion['champion_image'] ?? null);

        $relationshipStmt = $conn->prepare("DELETE FROM relationship WHERE champion_id = ? OR relateChampion_id = ?");
        $relationshipStmt->bind_param('ss', $champion['champion_id'], $champion['champion_id']);
        $raceStmt = $conn->prepare("DELETE FROM champion_race WHERE champion_id = ?");
        $raceStmt->bind_param('s', $champion['champion_id']);
        $childrenDeleted = $childrenDeleted && $relationshipStmt->execute() && $raceStmt->execute() && $championRepo->delete($champion['champion_id']);
    }

    if ($childrenDeleted && $regionRepo->delete($regionId)) {
        $conn->commit();
        redirectWithStatus('./listRegion.php', 'success', 'Region deleted successfully.');
    }

    $conn->rollback();
    redirectWithStatus('./listRegion.php', 'error', 'Unable to delete region.');
}

redirectWithStatus('./listRegion.php', 'error', 'Invalid request.');