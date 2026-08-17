<?php include '../Connection/connect.php';
$result = $conn->query("SELECT * FROM race ORDER BY race_id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Race List</title>
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
            <div class="header-badge">Race overview</div>
            <h1>Race List</h1>
            <a href="./new.php" class="primary-btn small-btn">Add Race</a>
        </header>

        <?php if ($result && $result->num_rows > 0): ?>
            <section class="panel">
                <table class="region-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['race_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['race_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['race_description']); ?></td>
                                <td>
                                    <?php if (!empty($row['race_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($row['race_image']); ?>" alt="Race image" style="max-width: 90px; max-height: 90px; object-fit: cover;">
                                    <?php else: ?>
                                        <span>No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a class="edit-btn" href="./update.php?race_id=<?php echo urlencode($row['race_id']); ?>">Edit</a>
                                        <a class="delete-btn" href="./EXC.php?action=delete&race_id=<?php echo urlencode($row['race_id']); ?>" onclick="return confirm('Delete this race?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        <?php else: ?>
            <div class="empty-state">No race data found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
