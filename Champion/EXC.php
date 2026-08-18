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

function sanitizeRaceIds($selectedRaces) {
    if (empty($selectedRaces)) {
        return [];
    }

    $unique = [];
    foreach ((array) $selectedRaces as $value) {
        $clean = trim((string) $value);
        if ($clean !== '' && !in_array($clean, $unique, true)) {
            $unique[] = $clean;
        }
    }

    return $unique;
}

$championRepo = new ChampionRepository($conn);
$championRaceRepo = new ChampionRaceRepository($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $selectedRaces = sanitizeRaceIds($_POST['races'] ?? []);
        if (count($selectedRaces) > 2) {
            redirectWithStatus('./listChampion.php', 'error', 'Each champion can only have up to 2 races.');
        }

        $championId = $championRepo->getNextId();
        $championName = $_POST['champion_name'] ?? '';
        $championTitle = $_POST['champion_title'] ?? '';
        $championGender = $_POST['champion_gender'] ?? '';
        $championRegion = $_POST['champion_region'] ?? '';
        $championStory = $_POST['champion_story'] ?? '';
        $croppedImagePath = saveImageDataUrl($_POST['championImageData'] ?? null, 'champion');
        $imagePath = $croppedImagePath ?? saveUploadedImage($_FILES['champion_image'] ?? null, 'champion');

        if ($championRepo->create([
            'champion_id' => $championId,
            'champion_name' => $championName,
            'champion_title' => $championTitle,
            'champion_gender' => $championGender,
            'champion_region' => $championRegion,
            'champion_regionId' => $championRegion,
            'champion_story' => $championStory,
            'champion_image' => $imagePath,
        ]) && $championRaceRepo->sync($championId, $selectedRaces)) {
            redirectWithStatus('./listChampion.php', 'success', 'Champion added successfully.');
        }

        redirectWithStatus('./listChampion.php', 'error', 'Unable to add champion.');
    }

    if ($action === 'update') {
        $championId = $_POST['champion_id'] ?? '';
        $selectedRaces = sanitizeRaceIds($_POST['races'] ?? []);
        if (count($selectedRaces) > 2) {
            redirectWithStatus('./listChampion.php', 'error', 'Each champion can only have up to 2 races.');
        }

        $championName = $_POST['champion_name'] ?? '';
        $championTitle = $_POST['champion_title'] ?? '';
        $championGender = $_POST['champion_gender'] ?? '';
        $championRegion = $_POST['champion_region'] ?? '';
        $championStory = $_POST['champion_story'] ?? '';
        $currentImage = $championRepo->getById($championId)['champion_image'] ?? null;
        $croppedImageData = $_POST['championImageData'] ?? null;
        $newImage = !empty($croppedImageData) ? saveImageDataUrl($croppedImageData, 'champion') : saveUploadedImage($_FILES['champion_image'] ?? null, 'champion');

        if ($newImage !== null) {
            deleteImageFile($currentImage);
            if ($championRepo->update($championId, [
                'champion_name' => $championName,
                'champion_title' => $championTitle,
                'champion_gender' => $championGender,
                'champion_region' => $championRegion,
                'champion_regionId' => $championRegion,
                'champion_story' => $championStory,
                'champion_image' => $newImage,
            ]) && $championRaceRepo->sync($championId, $selectedRaces)) {
                redirectWithStatus('./listChampion.php', 'success', 'Champion image and details updated successfully.');
            }
            redirectWithStatus('./listChampion.php', 'error', 'Unable to update champion image.');
        }

        if ($championRepo->update($championId, [
            'champion_name' => $championName,
            'champion_title' => $championTitle,
            'champion_gender' => $championGender,
            'champion_region' => $championRegion,
            'champion_regionId' => $championRegion,
            'champion_story' => $championStory,
        ]) && $championRaceRepo->sync($championId, $selectedRaces)) {
            redirectWithStatus('./listChampion.php', 'success', 'Champion updated successfully.');
        }

        redirectWithStatus('./listChampion.php', 'error', 'Unable to update champion.');
    }
}

if (($_SERVER['REQUEST_METHOD'] === 'GET') && ($_GET['action'] ?? '') === 'delete') {
    $championId = $_GET['champion_id'] ?? '';
    if ($championId === '') {
        redirectWithStatus('./listChampion.php', 'error', 'Invalid champion ID.');
    }

    $currentImage = $championRepo->getById($championId)['champion_image'] ?? null;
    deleteImageFile($currentImage);

    $conn->query("DELETE FROM champion_race WHERE champion_id = '" . $conn->real_escape_string($championId) . "'");
    if ($championRepo->delete($championId)) {
        redirectWithStatus('./listChampion.php', 'success', 'Champion deleted successfully.');
    }

    redirectWithStatus('./listChampion.php', 'error', 'Unable to delete champion.');
}

redirectWithStatus('./listChampion.php', 'error', 'Invalid request.');
