<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/security.php';

$championId = resolveEntityId($conn, 'champion', $_GET['champion_id'] ?? '');
$championRepo = new ChampionRepository($conn);
$relationshipRepo = new ChampionRelationShip($conn);
$champion = $championRepo->getById($championId);

if (!$champion) {
    die('Champion not found.');
}

$champions = $championRepo->getAll();

$relationshipTypes = [
    'SIB' => 'SIB - sibling',
    'ALS' => 'ALS - Allies',
    'RIV' => 'RIV - rival',
    'FRI' => 'FRI - Friend',
    'ENM' => 'ENM - Enemy',
    'TAL' => 'TAL - temporary Alliance',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Champion Relationships</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <link rel="stylesheet" href="../assets/css/list.css">
    <link rel="stylesheet" href="../assets/css/relationship.css">
    <script src="../assets/js/navigation.js"></script>
    <script src="../assets/js/toast.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="toast-container"></div>
    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Relationship manager</div>
            <h1><?php echo htmlspecialchars($champion['champion_name']); ?></h1>
            <p>Select a relationship for each champion. Existing relationships can be updated or deleted.</p>
        </header>

        <section class="relationship-grid">
            <?php foreach ($champions as $relatedChampion): ?>
                <?php if ($relatedChampion['champion_id'] === $championId) { continue; } ?>
                <?php
                // Check only this pair so another champion cannot change this card's state.
                $existingRelationship = $relationshipRepo->getByChampionPair($championId, $relatedChampion['champion_id']);
                $existingType = $existingRelationship['relationship_type'] ?? '';
                ?>
                <article class="relationship-card">
                    <?php if (!empty($relatedChampion['champion_image'])): ?>
                        <img class="relationship-image" src="../<?php echo htmlspecialchars($relatedChampion['champion_image']); ?>" alt="<?php echo htmlspecialchars($relatedChampion['champion_name']); ?>">
                    <?php else: ?>
                        <div class="relationship-image empty-card-image">No image</div>
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($relatedChampion['champion_name']); ?></h2>
                    <form action="./relationshipEXC.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken()); ?>">
                        <input type="hidden" name="champion_id" value="<?php echo htmlspecialchars(hashEntityId($championId)); ?>">
                        <input type="hidden" name="related_champion_id" value="<?php echo htmlspecialchars(hashEntityId($relatedChampion['champion_id'])); ?>">
                        <input type="hidden" name="action" value="<?php echo $existingType === '' ? 'add' : 'update'; ?>">
                        <select name="relationship_type" required>
                            <option value="">Select relationship</option>
                            <?php foreach ($relationshipTypes as $type => $label): ?>
                                <option value="<?php echo $type; ?>" <?php echo $existingType === $type ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="<?php echo $existingType === '' ? 'primary-btn' : 'edit-btn'; ?>">
                            <?php echo $existingType === '' ? 'Add' : 'Update'; ?>
                        </button>
                    </form>
                    <?php if ($existingType !== ''): ?>
                        <form action="./relationshipEXC.php" method="post" onsubmit="return confirm('Delete this relationship?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken()); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="champion_id" value="<?php echo htmlspecialchars(hashEntityId($championId)); ?>">
                            <input type="hidden" name="related_champion_id" value="<?php echo htmlspecialchars(hashEntityId($relatedChampion['champion_id'])); ?>">
                            <button type="submit" class="delete-btn relationship-delete">Delete</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
        <a class="secondary-btn relationship-back" href="./listChampion.php">Back to Champions</a>
    </div>
</body>
</html>