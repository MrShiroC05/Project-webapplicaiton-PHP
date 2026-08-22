<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/security.php';

$championId = resolveEntityId($conn, 'champion', $_GET['champion_id'] ?? '');
if ($championId === '') {
    die('Champion ID is required.');
}

$championRepo = new ChampionRepository($conn);
$raceRepo = new RaceRepository($conn);
$championRaceRepo = new ChampionRaceRepository($conn);

$champion = $championRepo->getById($championId);
if (!$champion) {
    die('Champion not found.');
}

$races = $raceRepo->getAll();
$selectedRaceIds = $championRaceRepo->getByChampionId($championId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Champion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <script src="../assets/js/navigation.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Edit champion</div>
            <h1>Update Champion</h1>
        </header>

        <section class="panel">
            <form id="championUpdateForm" data-crop-form data-crop-type="champion" action="./EXC.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken()); ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="champion_id" value="<?php echo htmlspecialchars(hashEntityId($champion['champion_id'])); ?>">
                <input type="hidden" name="championImageData" data-crop-data>

                <div class="form-row">
                    <label>Champion Name</label>
                    <input type="text" name="champion_name" value="<?php echo htmlspecialchars($champion['champion_name']); ?>" required>
                </div>

                <div class="form-row">
                    <label>Champion Title</label>
                    <input type="text" name="champion_title" value="<?php echo htmlspecialchars($champion['champion_title']); ?>" required>
                </div>

                <div class="form-row">
                    <label>Champion Gender</label>
                    <select name="champion_gender" required>
                        <option value="M" <?php echo $champion['champion_gender'] === 'M' ? 'selected' : ''; ?>>Male</option>
                        <option value="F" <?php echo $champion['champion_gender'] === 'F' ? 'selected' : ''; ?>>Female</option>
                        <option value="O" <?php echo $champion['champion_gender'] === 'O' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>Champion Region</label>
                    <select name="champion_region" required>
                        <option value="">Select a region</option>
                        <?php
                        $regionRepo = new RegionRepository($conn);
                        $regions = $regionRepo->getAll();
                        foreach ($regions as $region) {
                            echo '<option value="' . htmlspecialchars($region['region_id']) . '"' . ($champion['champion_regionId'] == $region['region_id'] ? ' selected' : '') . '>' . htmlspecialchars($region['region_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-row">
                    <label>Champion Story</label>
                    <textarea name="champion_story" rows="4" required><?php echo htmlspecialchars($champion['champion_story']); ?></textarea>
                </div>

                <div class="form-row">
                    <label>Champion Image</label>
                    <input type="file" name="champion_image" accept="image/*" >
                    <?php if (!empty($champion['champion_image'])): ?>
                        <div class="image-preview">
                            <img src="../<?php echo htmlspecialchars($champion['champion_image']); ?>" alt="Champion image" style="max-width: 200px; margin-top: 10px;">
                        </div>
                    <?php endif; ?>
                </div>

                <div id="cropPreviewWrapper" data-crop-preview class="crop-preview-wrapper" style="display:none;">
                    <div id="cropStage" class="crop-stage">
                        <img id="cropImage" data-crop-image src="<?php echo !empty($champion['champion_image']) ? '../' . htmlspecialchars($champion['champion_image']) : ''; ?>" alt="Crop preview">
                    </div>
                    <button type="button" id="applyCropBtn" data-crop-apply class="secondary-btn apply-crop-btn">Use this crop</button>
                </div>

                <div class="form-row">
                    <label>Champion Races (max 2)</label>
                    <div class="checkbox-group">
                        <?php foreach ($races as $race): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="races[]" value="<?php echo htmlspecialchars($race['race_id']); ?>" <?php echo in_array($race['race_id'], $selectedRaceIds, true) ? 'checked' : ''; ?>>
                                <?php echo htmlspecialchars($race['race_name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="action-group">
                    <button type="submit" class="primary-btn">Update Champion</button>
                    <a href="./listChampion.php" class="secondary-btn">Back</a>
                </div>
            </form>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="../assets/js/crop.js"></script>
    <script>
        const limit = 2;
        const checkboxes = document.querySelectorAll('input[name="races[]"]');
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const checked = Array.from(checkboxes).filter((item) => item.checked);
                if (checked.length > limit) {
                    checkbox.checked = false;
                    alert('You can only select up to 2 races for one champion.');
                }
            });
        });
    </script>
</body>
</html>
