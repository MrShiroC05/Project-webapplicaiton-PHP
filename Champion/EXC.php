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
    requireCsrfToken();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $selectedRaces = sanitizeRaceIds($_POST['races'] ?? []);
        if (count($selectedRaces) > 2) {
            redirectWithStatus('./listChampion.php', 'error', 'Each champion can only have up to 2 races.');
        }

        $championId = $championRepo->getNextId();
        $championName = cleanText($_POST['champion_name'] ?? '', 100);
        $championTitle = cleanText($_POST['champion_title'] ?? '', 150);
        $championGender = cleanText($_POST['champion_gender'] ?? '', 1);
        $championRegion = cleanText($_POST['champion_region'] ?? '', 10);
        $championStory = cleanText($_POST['champion_story'] ?? '', 5000);
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
        $championId = resolveEntityId($conn, 'champion', cleanText($_POST['champion_id'] ?? '', 32));
        if (!validEntityId($championId)) {
            redirectWithStatus('./listChampion.php', 'error', 'Invalid champion ID.');
        }
        $selectedRaces = sanitizeRaceIds($_POST['races'] ?? []);
        if (count($selectedRaces) > 2) {
            redirectWithStatus('./listChampion.php', 'error', 'Each champion can only have up to 2 races.');
        }

        $championName = cleanText($_POST['champion_name'] ?? '', 100);
        $championTitle = cleanText($_POST['champion_title'] ?? '', 150);
        $championGender = cleanText($_POST['champion_gender'] ?? '', 1);
        $championRegion = cleanText($_POST['champion_region'] ?? '', 10);
        $championStory = cleanText($_POST['champion_story'] ?? '', 5000);
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

if (($_SERVER['REQUEST_METHOD'] === 'POST') && ($_POST['action'] ?? '') === 'delete') {
    requireCsrfToken();
    $championId = resolveEntityId($conn, 'champion', cleanText($_POST['champion_id'] ?? '', 32));
    if (!validEntityId($championId)) {
        redirectWithStatus('./listChampion.php', 'error', 'Invalid champion ID.');
    }

    $currentImage = $championRepo->getById($championId)['champion_image'] ?? null;
    deleteImageFile($currentImage);

    $conn->begin_transaction();

    // Remove child rows first because relationship and champion_race reference this champion.
    $relationshipStmt = $conn->prepare("DELETE FROM relationship WHERE champion_id = ? OR relateChampion_id = ?");
    $relationshipStmt->bind_param('ss', $championId, $championId);
    $raceStmt = $conn->prepare("DELETE FROM champion_race WHERE champion_id = ?");
    $raceStmt->bind_param('s', $championId);
    $childrenDeleted = $relationshipStmt->execute() && $raceStmt->execute();

    if ($childrenDeleted && $championRepo->delete($championId)) {
        $conn->commit();
        redirectWithStatus('./listChampion.php', 'success', 'Champion deleted successfully.');
    }

    $conn->rollback();
    redirectWithStatus('./listChampion.php', 'error', 'Unable to delete champion.');
}

redirectWithStatus('./listChampion.php', 'error', 'Invalid request.');
