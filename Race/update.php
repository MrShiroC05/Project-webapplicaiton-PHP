<?php
include '../Connection/connect.php';

$raceId = $_GET['race_id'] ?? '';
if ($raceId === '') {
    die('Race ID is required.');
}

$stmt = $conn->prepare("SELECT * FROM race WHERE race_id = ?");
$stmt->bind_param('s', $raceId);
$stmt->execute();
$race = $stmt->get_result()->fetch_assoc();
if (!$race) {
    die('Race not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Race</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <script src="../assets/js/navigation.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Edit race</div>
            <h1>Update Race</h1>
        </header>

        <section class="panel">
            <form action="./EXC.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="race_id" value="<?php echo htmlspecialchars($race['race_id']); ?>">

                <div class="form-row">
                    <label>Race Name</label>
                    <input type="text" name="race_name" value="<?php echo htmlspecialchars($race['race_name']); ?>" required>
                </div>

                <div class="form-row">
                    <label>Race Description</label>
                    <textarea name="race_description" rows="4"><?php echo htmlspecialchars($race['race_description']); ?></textarea>
                </div>

                <div class="form-row">
                    <label>Race Image</label>
                    <input type="file" name="race_image" accept="image/*">
                    <?php if (!empty($race['race_image'])): ?>
                        <div class="image-preview">
                            <img src="../<?php echo htmlspecialchars($race['race_image']); ?>" alt="Race image" style="max-width: 200px; margin-top: 10px;">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="action-group">
                    <button type="submit" class="primary-btn">Update Race</button>
                    <a href="./listRace.php" class="secondary-btn">Back</a>
                </div>
            </form>
        </section>
    </div>
</body>
</html>
