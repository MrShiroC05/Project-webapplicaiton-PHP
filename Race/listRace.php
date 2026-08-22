<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/security.php';

$raceRepo = new RaceRepository($conn);
$result = $raceRepo->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Race List</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <link rel="stylesheet" href="../assets/css/cards.css">
    <script src="../assets/js/navigation.js"></script>
    <script src="../assets/js/toast.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="toast-container"></div>

    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Race overview</div>
            <h1>Race List</h1>
            <a href="./new.php" class="primary-btn small-btn">Add Race</a>
        </header>

        <?php if (!empty($result)): ?>
            <section class="card-grid">
                <?php foreach ($result as $row): ?>
                    <article class="card">
                        <?php if (!empty($row['race_image'])): ?>
                            <img class="card-image" src="../<?php echo htmlspecialchars($row['race_image']); ?>" alt="<?php echo htmlspecialchars($row['race_name']); ?>">
                        <?php else: ?>
                            <div class="card-image empty-card-image">No image</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($row['race_name']); ?></h3>
                            <div class="action-group">
                                <a class="primary-btn" href="./detail.php?race_id=<?php echo urlencode(hashEntityId($row['race_id'])); ?>">More detail</a>
                                
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="empty-state">No race data found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
