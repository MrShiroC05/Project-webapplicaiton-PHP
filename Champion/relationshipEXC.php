<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/notification.php';
include '../method/security.php';

requireCsrfToken();

$championId = resolveEntityId($conn, 'champion', cleanText($_POST['champion_id'] ?? '', 32));
$relatedChampionId = resolveEntityId($conn, 'champion', cleanText($_POST['related_champion_id'] ?? '', 32));
$action = $_POST['action'] ?? '';
$relationshipType = cleanText($_POST['relationship_type'] ?? '', 3);
$validTypes = ['SIB', 'ALS', 'RIV', 'FRI', 'ENM', 'TAL'];
$redirect = './relationship.php?champion_id=' . urlencode(hashEntityId($championId ?: ''));

if (!$championId || !$relatedChampionId || $championId === $relatedChampionId) {
    redirectWithStatus($redirect, 'error', 'Invalid champion relationship.');
}

$championRepo = new ChampionRepository($conn);
$relationshipRepo = new ChampionRelationShip($conn);
if (!$championRepo->getById($championId) || !$championRepo->getById($relatedChampionId)) {
    redirectWithStatus($redirect, 'error', 'Champion not found.');
}

$success = false;
// Every request is scoped to exactly one champion pair from the submitted hidden IDs.
if ($action === 'add' && in_array($relationshipType, $validTypes, true) && !$relationshipRepo->getByChampionPair($championId, $relatedChampionId)) {
    $success = $relationshipRepo->create([
        'champion_id' => $championId,
        'related_champion_id' => $relatedChampionId,
        'relationship_type' => $relationshipType,
    ]);
} elseif ($action === 'update' && in_array($relationshipType, $validTypes, true)) {
    $success = $relationshipRepo->update($championId, $relatedChampionId, $relationshipType);
} elseif ($action === 'delete') {
    $success = $relationshipRepo->delete($championId, $relatedChampionId);
}

redirectWithStatus($redirect, $success ? 'success' : 'error', $success ? 'Relationship saved successfully.' : 'Unable to save relationship.');