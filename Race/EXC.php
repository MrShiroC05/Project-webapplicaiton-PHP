<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/notification.php';
include '../method/imageUpload.php';
include '../method/security.php';

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
    requireCsrfToken();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $raceName = cleanText($_POST['race_name'] ?? '', 100);
        $raceDescription = cleanText($_POST['race_description'] ?? '', 5000);
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
        $raceId = resolveEntityId($conn, 'race', cleanText($_POST['race_id'] ?? '', 32));
        $raceName = cleanText($_POST['race_name'] ?? '', 100);
        $raceDescription = cleanText($_POST['race_description'] ?? '', 5000);
        if (!validEntityId($raceId)) {
            redirectWithStatus('./listRace.php', 'error', 'Invalid race ID.');
        }
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

if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($_POST['action'] ?? '') === 'delete') {
    requireCsrfToken();
    $raceId = resolveEntityId($conn, 'race', cleanText($_POST['race_id'] ?? '', 32));
    if ($raceId === '') {
        redirectWithStatus('./listRace.php', 'error', 'Invalid race ID.');
    }

    $currentImage = $raceRepo->getById($raceId)['race_image'] ?? null;
    deleteImageFile($currentImage);

    $conn->begin_transaction();

    // Remove the junction rows first because champions are children of this race through champion_race.
    $childStmt = $conn->prepare("DELETE FROM champion_race WHERE race_id = ?");
    $childStmt->bind_param('s', $raceId);

    if ($childStmt->execute() && $raceRepo->delete($raceId)) {
        $conn->commit();
        redirectWithStatus('./listRace.php', 'success', 'Race deleted successfully.');
    }

    $conn->rollback();
    redirectWithStatus('./listRace.php', 'error', 'Unable to delete race.');
}

redirectWithStatus('./listRace.php', 'error', 'Invalid request.');
