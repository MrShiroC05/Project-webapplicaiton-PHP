<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/security.php';
$raceId = resolveEntityId($conn, 'race', $_GET['race_id'] ?? '');
$raceRepo = new RaceRepository($conn);
$race = $raceRepo->getById($raceId);
if (!$race) { die('Race not found.'); }
$champions = (new ChampionRepository($conn))->getAllWithRace($raceId);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($race['race_name']); ?> - Race</title>
        <link rel="stylesheet" href="../assets/css/base.css">
        <link rel="stylesheet" href="../assets/css/navigation.css">
        <link rel="stylesheet" href="../assets/css/detail.css">
        <script src="../assets/js/navigation.js"></script>
    </head>
<body>
    <div id="app-nav"></div>
    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Race detail</div>
            <h1><?php echo htmlspecialchars($race['race_name']); ?></h1>
            <a class="edit-btn" href="./update.php?race_id=<?php echo urlencode(hashEntityId($raceId)); ?>">Edit</a>
                <form action="./EXC.php" method="post" onsubmit="return confirm('Delete this race?');">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken()); ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="race_id" value="<?php echo htmlspecialchars(hashEntityId($raceId)); ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
        </header>

        <section class="panel detail-hero">
            <div class="detail-hero-image <?php echo empty($race['race_image']) ? 'empty-card-image' : ''; ?>">
                <?php if (!empty($race['race_image'])): ?>
                    <img class="detail-hero-image" src="../<?php echo htmlspecialchars($race['race_image']); ?>" alt="<?php echo htmlspecialchars($race['race_name']); ?>">
                <?php else: ?>
                    No image
                <?php endif; ?>
            </div>
            <div class="detail-copy">
                <h2>About this race</h2>
                <p><?php echo nl2br(htmlspecialchars($race['race_description'] ?: 'No description available.')); ?></p>
            </div>
        </section>

        <section class="panel detail-section">
            <h2>Champions in this race</h2>
            <div class="linked-list">
                <?php foreach ($champions as $champion): ?>
                        <a class="linked-item" href="../Champion/detail.php?champion_id=<?php echo urlencode(hashEntityId($champion['champion_id'])); ?>">
                        <?php if (!empty($champion['champion_image'])): ?>
                            <img src="../<?php echo htmlspecialchars($champion['champion_image']); ?>" alt="<?php echo htmlspecialchars($champion['champion_name']); ?>">
                        <?php else: ?>
                            <span class="empty-linked-image"></span>
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($champion['champion_name']); ?></span>
                    </a>
                <?php endforeach; ?>
                <?php if (!$champions): ?>
                    <p>No champions assigned.</p>
                <?php endif; ?>
            </div>
        </section>

        <a class="secondary-btn relationship-back" href="./listRace.php">Back to Races</a>
    </div>
</body>
</html>