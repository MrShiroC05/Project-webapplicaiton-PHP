<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/security.php';

$championId = resolveEntityId($conn, 'champion', $_GET['champion_id'] ?? '');
$championRepo = new ChampionRepository($conn);
$champion = $championRepo->getById($championId);
if (!$champion) { die('Champion not found.'); }

$region = (new RegionRepository($conn))->getById($champion['champion_regionId']);
$races = $championRepo->getRacesByChampionId($championId);
$relationships = (new ChampionRelationShip($conn))->getListRelationById($championId);
$relationshipTypes = ['SIB' => 'Sibling', 'ALS' => 'Allies', 'RIV' => 'Rival', 'FRI' => 'Friend', 'ENM' => 'Enemy', 'TAL' => 'Temporary Alliance'];
function detailImage($path, $alt, $class = 'detail-hero-image') {
    if (!empty($path)) return '<img class="' . $class . '" src="../' . htmlspecialchars($path) . '" alt="' . htmlspecialchars($alt) . '">';
    return '<div class="' . $class . ' empty-card-image">No image</div>';
}
?>
<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($champion['champion_name']); ?> - Champion</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <link rel="stylesheet" href="../assets/css/detail.css">
    <script src="../assets/js/navigation.js"></script>
</head><body>
    <div id="app-nav"></div>
    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Champion detail</div>
            <h1><?php echo htmlspecialchars($champion['champion_name']); ?></h1>
            <a class="edit-btn" href="./update.php?champion_id=<?php echo urlencode(hashEntityId($championId)); ?>">Edit</a>
            <form action="./EXC.php" method="post" onsubmit="return confirm('Delete this champion?');">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken()); ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="champion_id" value="<?php echo htmlspecialchars(hashEntityId($championId)); ?>">
                <button type="submit" class="delete-btn">Delete</button>
            </form>
        </header>
        <section class="panel detail-hero">
                <?php echo detailImage($champion['champion_image'], $champion['champion_name']); ?>
                <div class="detail-copy"><h2><?php echo htmlspecialchars($champion['champion_title'] ?: 'No title'); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($champion['champion_story'] ?: 'No story available.')); ?></p>
                </div>
            </section>

            <section class="panel detail-section">
                <p>
                    <h2>Region:</h2> 
                    <?php if ($region): ?>
                        <a class="linked-item" href="../Region/detail.php?region_id=<?php echo urlencode(hashEntityId($region['region_id'])); ?>">
                            <span>
                                <?php echo htmlspecialchars($region['region_name']); ?>
                            </span>
                        </a>
                    <?php else: ?>No region<?php endif; ?>
                    </p>
                <h2>Races</h2>
                <div class="linked-list">
                    <?php foreach ($races as $race): ?>
                        <a class="linked-item" href="../Race/detail.php?race_id=<?php echo urlencode(hashEntityId($race['race_id'])); ?>">
                            <span>
                                <?php echo $race['race_name']; ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    <?php if (!$races): ?>
                        <p>No races assigned.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="panel detail-section">
                <h2>Relationships</h2>
                <a class="edit-btn" href="./relationship.php?champion_id=<?php echo urlencode(hashEntityId($championId)); ?>">Edit Relationship</a>
                <div class="relationship-list">

                <?php foreach ($relationships as $relation): ?><div><a href="./detail.php?champion_id=<?php echo urlencode(hashEntityId($relation['related_champion_id'])); ?>"><?php echo htmlspecialchars($relation['related_champion_name']); ?></a> <span>(<?php echo htmlspecialchars($relationshipTypes[$relation['relationship_type']] ?? $relation['relationship_type']); ?>)</span></div><?php endforeach; ?>
                <?php if (!$relationships): ?><p>No relationships recorded.</p><?php endif; ?>
            </div></section>
            <a class="secondary-btn relationship-back" href="./listChampion.php">Back to Champions</a>
    </div>
</body></html>