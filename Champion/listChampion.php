<?php
include '../Connection/connect.php';
include '../method/database.php';

$championRepo = new ChampionRepository($conn);
$result = $championRepo->getAllWithRaceNames();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Champion List</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <script src="../assets/js/navigation.js"></script>
    <script src="../assets/js/toast.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="toast-container"></div>

    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Champion overview</div>
            <h1>Champion List</h1>
            <a href="./new.php" class="primary-btn small-btn">Add Champion</a>
        </header>

        <?php if (!empty($result)): ?>
            <section class="panel">
                <table class="region-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Title</th>
                            <th>Gender</th>
                            <th>Races</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['champion_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['champion_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['champion_gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['race_names'] ?? 'No race'); ?></td>
                                <td>
                                    <?php if (!empty($row['champion_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($row['champion_image']); ?>" alt="Champion image" style="max-width: 90px; max-height: 90px; object-fit: cover;">
                                    <?php else: ?>
                                        <span>No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a class="edit-btn" href="./update.php?champion_id=<?php echo urlencode($row['champion_id']); ?>">Edit</a>
                                        <a class="delete-btn" href="./EXC.php?action=delete&champion_id=<?php echo urlencode($row['champion_id']); ?>" onclick="return confirm('Delete this champion?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php else: ?>
            <div class="empty-state">No champion data found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
