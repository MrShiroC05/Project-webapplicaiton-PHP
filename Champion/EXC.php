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

function syncChampionRaces($conn, $championId, $selectedRaces) {
    $cleanIds = sanitizeRaceIds($selectedRaces);

    $conn->query("DELETE FROM champion_race WHERE champion_id = '" . $conn->real_escape_string($championId) . "'");

    if (empty($cleanIds)) {
        return;
    }

    $placeholders = array_fill(0, count($cleanIds), '?');
    $sql = "SELECT race_id FROM race WHERE race_id IN (" . implode(',', $placeholders) . ")";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return;
    }

    $types = str_repeat('s', count($cleanIds));
    $stmt->bind_param($types, ...$cleanIds);
    $stmt->execute();
    $result = $stmt->get_result();
    $validRaceIds = [];
    while ($row = $result->fetch_assoc()) {
        $validRaceIds[] = $row['race_id'];
    }
    $stmt->close();

    foreach ($validRaceIds as $raceId) {
        $insertStmt = $conn->prepare("INSERT INTO champion_race (champion_id, race_id) VALUES (?, ?)");
        $insertStmt->bind_param('ss', $championId, $raceId);
        $insertStmt->execute();
        $insertStmt->close();
    }
}

function getCurrentChampionImage($conn, $championId) {
    $stmt = $conn->prepare("SELECT champion_image FROM champion WHERE champion_id = ?");
    $stmt->bind_param('s', $championId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['champion_image'] ?? null;
}


function getNextChampionId($conn) {
    $sql = "SELECT COUNT(*) AS total_rows FROM champion";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $totalRows = $row["total_rows"] + 1; // Increment by 1 to get the next ID


    return 'C' . str_pad($totalRows, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $selectedRaces = sanitizeRaceIds($_POST['races'] ?? []);
        if (count($selectedRaces) > 2) {
            redirectWithStatus('./listChampion.php', 'error', 'Each champion can only have up to 2 races.');
        }

        $championId = getNextChampionId($conn);
        $championName = $_POST['champion_name'] ?? '';
        $championTitle = $_POST['champion_title'] ?? '';
        $championGender = $_POST['champion_gender'] ?? '';
        $championRegion = $_POST['champion_region'] ?? '';
        $championStory = $_POST['champion_story'] ?? '';
        $imagePath = saveUploadedImage($_FILES['champion_image'] ?? null, 'champion');
        
        $stmt = $conn->prepare("INSERT INTO champion (champion_id, champion_name, champion_title, champion_gender, champion_regionId, champion_story, champion_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $championId, $championName, $championTitle, $championGender, $championRegion, $championStory, $imagePath);

        if ($stmt->execute()) {
            syncChampionRaces($conn, $championId, $selectedRaces);
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
        $currentImage = getCurrentChampionImage($conn, $championId);
        $newImage = saveUploadedImage($_FILES['champion_image'] ?? null, 'champion');

        if ($newImage !== null) {
            deleteImageFile($currentImage);
            $imageSql = "UPDATE champion SET champion_name = ?, champion_title = ?, champion_gender = ?, champion_regionId = ?, champion_story = ?, champion_image = ? WHERE champion_id = ?";
            $imageStmt = $conn->prepare($imageSql);
            $imageStmt->bind_param('sssssss', $championName, $championTitle, $championGender, $championRegion, $championStory, $newImage, $championId);
            if ($imageStmt->execute()) {
                syncChampionRaces($conn, $championId, $selectedRaces);
                $imageStmt->close();
                redirectWithStatus('./listChampion.php', 'success', 'Champion image and details updated successfully.');
            }
            redirectWithStatus('./listChampion.php', 'error', 'Unable to update champion image.');
        }

        $stmt = $conn->prepare("UPDATE champion SET champion_name = ?, champion_title = ?, champion_gender = ?, champion_region = ?, champion_story = ? WHERE champion_id = ?");
        $stmt->bind_param('ssssss', $championName, $championTitle, $championGender, $championRegion, $championStory, $championId);

        if ($stmt->execute()) {
            syncChampionRaces($conn, $championId, $selectedRaces);
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

    $currentImage = getCurrentChampionImage($conn, $championId);
    deleteImageFile($currentImage);

    $conn->query("DELETE FROM champion_race WHERE champion_id = '" . $conn->real_escape_string($championId) . "'");
    $stmt = $conn->prepare("DELETE FROM champion WHERE champion_id = ?");
    $stmt->bind_param('s', $championId);

    if ($stmt->execute()) {
        redirectWithStatus('./listChampion.php', 'success', 'Champion deleted successfully.');
    }

    redirectWithStatus('./listChampion.php', 'error', 'Unable to delete champion.');
}

redirectWithStatus('./listChampion.php', 'error', 'Invalid request.');
