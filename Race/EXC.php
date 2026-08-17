<?php
include '../Connection/connect.php';
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

function getCurrentRaceImage($conn, $raceId) {
    $stmt = $conn->prepare("SELECT race_image FROM race WHERE race_id = ?");
    $stmt->bind_param('s', $raceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['race_image'] ?? null;
}

function getNextRaceId($conn) {
    $result = $conn->query("SELECT race_id FROM race ORDER BY CAST(SUBSTRING(race_id, 2) AS UNSIGNED) DESC LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = (int) preg_replace('/\D/', '', $row['race_id']);
        return 'R' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
    }

    return 'R001';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $raceId = getNextRaceId($conn);
        $raceName = $_POST['race_name'] ?? '';
        $raceDescription = $_POST['race_description'] ?? '';
        $imagePath = saveUploadedImage($_FILES['race_image'] ?? null, 'race');

        $stmt = $conn->prepare("INSERT INTO race (race_id, race_name, race_description, race_image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $raceId, $raceName, $raceDescription, $imagePath);

        if ($stmt->execute()) {
            redirectWithStatus('./listRace.php', 'success', 'Race added successfully.');
        }

        redirectWithStatus('./listRace.php', 'error', 'Unable to add race.');
    }

    if ($action === 'update') {
        $raceId = $_POST['race_id'] ?? '';
        $raceName = $_POST['race_name'] ?? '';
        $raceDescription = $_POST['race_description'] ?? '';
        $currentImage = getCurrentRaceImage($conn, $raceId);
        $newImage = saveUploadedImage($_FILES['race_image'] ?? null, 'race');

        if ($newImage !== null) {
            deleteImageFile($currentImage);
            $stmt = $conn->prepare("UPDATE race SET race_name = ?, race_description = ?, race_image = ? WHERE race_id = ?");
            $stmt->bind_param('ssss', $raceName, $raceDescription, $newImage, $raceId);
            if ($stmt->execute()) {
                redirectWithStatus('./listRace.php', 'success', 'Race image and details updated successfully.');
            }
            redirectWithStatus('./listRace.php', 'error', 'Unable to update race image.');
        }

        $stmt = $conn->prepare("UPDATE race SET race_name = ?, race_description = ? WHERE race_id = ?");
        $stmt->bind_param('sss', $raceName, $raceDescription, $raceId);

        if ($stmt->execute()) {
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

    $currentImage = getCurrentRaceImage($conn, $raceId);
    deleteImageFile($currentImage);

    $conn->query("DELETE FROM champion_race WHERE race_id = '" . $conn->real_escape_string($raceId) . "'");
    $stmt = $conn->prepare("DELETE FROM race WHERE race_id = ?");
    $stmt->bind_param('s', $raceId);

    if ($stmt->execute()) {
        redirectWithStatus('./listRace.php', 'success', 'Race deleted successfully.');
    }

    redirectWithStatus('./listRace.php', 'error', 'Unable to delete race.');
}

redirectWithStatus('./listRace.php', 'error', 'Invalid request.');
