<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/security.php';
$regionId = resolveEntityId($conn, 'region', $_GET['region_id'] ?? '');
$regionRepo = new RegionRepository($conn);
$region = $regionRepo->getById($regionId);
if (!$region) { die('Region not found.'); }
$champions = (new ChampionRepository($conn))->getAllWithRegionId($regionId);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($region['region_name']); ?> - Region</title>
        <link rel="stylesheet" href="../assets/css/base.css">
        <link rel="stylesheet" href="../assets/css/navigation.css">
        <link rel="stylesheet" href="../assets/css/detail.css">
        <script src="../assets/js/navigation.js"></script>
    </head>
<body>
    <div id="app-nav"></div>
    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Region detail</div>
            <h1><?php echo htmlspecialchars($region['region_name']); ?></h1>
            <a class='edit-btn' href='update.php?id=<?php echo urlencode(hashEntityId($regionId)); ?>'>Edit</a>
                <form action="./EXC.php" method="post" onsubmit="return confirm('Delete this region and its champions?');"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken()); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="region_id" value="<?php echo htmlspecialchars(hashEntityId($regionId)); ?>"><button type="submit" class="delete-btn">Delete</button></form>
        </header>

        <section class="panel detail-hero">
            <div class="detail-hero-image <?php echo empty($region['region_image']) ? 'empty-card-image' : ''; ?>">
                <?php if (!empty($region['region_image'])): ?>
                    <img class="detail-hero-image" src="../<?php echo htmlspecialchars($region['region_image']); ?>" alt="<?php echo htmlspecialchars($region['region_name']); ?>">
                <?php else: ?>
                    No image
                <?php endif; ?>
            </div>
            <div class="detail-copy">
                <h2><?php echo htmlspecialchars($region['region_name']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($region['region_description'] ?: 'No description available.')); ?></p>
            </div>
        </section>

        <section class="panel detail-section">
            <h2>Champions in this region</h2>
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

        <a class="secondary-btn relationship-back" href="./listRegion.php">Back to Regions</a>
    </div>
</body>
</html>