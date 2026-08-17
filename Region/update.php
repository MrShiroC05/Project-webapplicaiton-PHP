<?php
// File role: update existing region information.
include '../Connection/connect.php';

function decodeRegionId($value) {
    $padding = str_repeat('=', (4 - strlen($value) % 4) % 4);
    $decoded = base64_decode(strtr($value, '-_', '+/') . $padding, true);
    return $decoded !== false ? $decoded : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Region</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <script src="../assets/js/navigation.js"></script>
    <script src="../assets/js/toast.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="toast-container"></div>

    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Edit region</div>
            <h1>Update Region</h1>
        </header>

        <?php 
            $encodedId = $_GET['id'] ?? '';
            $id = decodeRegionId($encodedId);

            if ($id === null) {
                die('Invalid region ID.');
            }

            $sql = "SELECT * FROM region WHERE region_id = '".$id."' ";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            if (!$row) {
                die('Region not found.');
            }
        ?>

        <section class="panel">
            <form id="regionUpdateForm" class="form" action="./EXC.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="region_id" value="<?php echo $row['region_id']; ?>">
                <input type="hidden" id="regionImageData" name="regionImageData">

                <div class="form-row">
                    <label for="regionName">Region Name</label>
                    <input type="text" id="regionName" name="regionName" value="<?php echo $row['region_name']; ?>" required>
                </div>

                <div class="form-row">
                    <label for="regionDescription">Region Description</label>
                    <input type="text" id="regionDescription" name="regionDescription" value="<?php echo $row['region_description']; ?>" required>
                </div>

                <div class="form-row">
                    <label for="regionLogo">Region Logo</label>
                    <input type="file" id="regionLogo" name="regionLogo" accept="image/*">
                </div>

                <div id="cropPreviewWrapper" class="crop-preview-wrapper">
                    <div id="cropStage" class="crop-stage">
                        <img id="cropImage" src="" alt="Crop preview">
                    </div>
                    <button type="button" id="applyCropBtn" class="secondary-btn apply-crop-btn">Use this crop</button>
                </div>

                <button type="submit" class="primary-btn">Update Region</button>
            </form>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="../assets/js/region-crop.js"></script>
</body>
</html>