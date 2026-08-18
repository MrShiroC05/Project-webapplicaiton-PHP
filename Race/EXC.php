<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/notification.php';
include '../method/imageUpload.php';

function deleteImageFile($imagePath) {
    if (empty($imagePath)) {
        return;
    }

    $fullPath = __DIR__ . '/../' . $imagePath;
    if (file_exists($fullPath) && is_file($fullPath)) {
        unlink($fullPath);
    }
}

$raceRepo = new RaceRepository($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $raceName = $_POST['race_name'] ?? '';
        $raceDescription = $_POST['race_description'] ?? '';
        $croppedImagePath = saveImageDataUrl($_POST['raceImageData'] ?? null, 'race');
        $imagePath = $croppedImagePath ?? saveUploadedImage($_FILES['race_image'] ?? null, 'race');

        if ($raceRepo->create([
            'race_name' => $raceName,
            'race_description' => $raceDescription,
            'race_image' => $imagePath,
        ])) {
            redirectWithStatus('./listRace.php', 'success', 'Race added successfully.');
        }

        redirectWithStatus('./listRace.php', 'error', 'Unable to add race.');
    }

    if ($action === 'update') {
        $raceId = $_POST['race_id'] ?? '';
        $raceName = $_POST['race_name'] ?? '';
        $raceDescription = $_POST['race_description'] ?? '';
        $currentImage = $raceRepo->getById($raceId)['race_image'] ?? null;
        $croppedImageData = $_POST['raceImageData'] ?? null;
        $newImage = !empty($croppedImageData) ? saveImageDataUrl($croppedImageData, 'race') : saveUploadedImage($_FILES['race_image'] ?? null, 'race');

        if ($newImage !== null) {
            deleteImageFile($currentImage);
            $raceRepo->update($raceId, [
                'race_name' => $raceName,
                'race_description' => $raceDescription,
                'race_image' => $newImage,
            ]);
            redirectWithStatus('./listRace.php', 'success', 'Race image and details updated successfully.');
        }

        if ($raceRepo->update($raceId, [
            'race_name' => $raceName,
            'race_description' => $raceDescription,
        ])) {
            redirectWithStatus('./listRace.php', 'success', 'Race updated successfully.');
        }

        redirectWithStatus('./listRace.php', 'error', 'Unable to update race.');
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'GET') && ($_GET['action'] ?? '') === 'delete') {
    $raceId = $_GET['race_id'] ?? '';
    if ($raceId === '') {
        redirectWithStatus('./listRace.php', 'error', 'Invalid race ID.');
    }

    $currentImage = $raceRepo->getById($raceId)['race_image'] ?? null;
    deleteImageFile($currentImage);

    $conn->query("DELETE FROM champion_race WHERE race_id = '" . $conn->real_escape_string($raceId) . "'");
    if ($raceRepo->delete($raceId)) {
        redirectWithStatus('./listRace.php', 'success', 'Race deleted successfully.');
    }

    redirectWithStatus('./listRace.php', 'error', 'Unable to delete race.');
}

redirectWithStatus('./listRace.php', 'error', 'Invalid request.');
