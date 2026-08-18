<?php
include '../Connection/connect.php';
include '../method/database.php';

$raceId = $_GET['race_id'] ?? '';
if ($raceId === '') {
    die('Race ID is required.');
}

$raceRepo = new RaceRepository($conn);
$race = $raceRepo->getById($raceId);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
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
            <form id="raceUpdateForm" data-crop-form data-crop-type="race" action="./EXC.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="race_id" value="<?php echo htmlspecialchars($race['race_id']); ?>">
                <input type="hidden" name="raceImageData" data-crop-data>

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

                <div id="cropPreviewWrapper" data-crop-preview class="crop-preview-wrapper" style="display:none;">
                    <div id="cropStage" class="crop-stage">
                        <img id="cropImage" data-crop-image src="<?php echo !empty($race['race_image']) ? '../' . htmlspecialchars($race['race_image']) : ''; ?>" alt="Crop preview">
                    </div>
                    <button type="button" id="applyCropBtn" data-crop-apply class="secondary-btn apply-crop-btn">Use this crop</button>
                </div>

                <div class="action-group">
                    <button type="submit" class="primary-btn">Update Race</button>
                    <a href="./listRace.php" class="secondary-btn">Back</a>
                </div>
            </form>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="../assets/js/crop.js"></script>
</body>
</html>
