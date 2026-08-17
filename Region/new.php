<?php include '../Connection/connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Region</title>
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
            <div class="header-badge">Create region</div>
            <h1>New Region</h1>
            <p>Add a new region and keep the image in a clean square layout.</p>
        </header>

        <section class="panel">
            <form id="regionForm" class="form" method="post" action="./EXC.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <input type="hidden" id="regionImageData" name="regionImageData">

                <div class="form-row">
                    <label for="regionName">Region Name</label>
                    <input type="text" id="regionName" name="regionName" placeholder="Enter region name" required>
                </div>

                <div class="form-row">
                    <label for="regionDescription">Region Description</label>
                    <input type="text" id="regionDescription" name="regionDescription" placeholder="Enter a short description" required>
                </div>

                <div class="form-row">
                    <label for="regionLogo">Region Image</label>
                    <input type="file" id="regionLogo" name="regionLogo" accept="image/*" required>
                </div>

                <div id="cropPreviewWrapper" class="crop-preview-wrapper">
                    <div id="cropStage" class="crop-stage">
                        <img id="cropImage" src="" alt="Crop preview">
                    </div>
                    <button type="button" id="applyCropBtn" class="secondary-btn apply-crop-btn">Use this crop</button>
                </div>

                <div class="action-group">
                    <button type="submit" class="primary-btn">Add Region</button>
                    <a href="../index.php" class="secondary-btn">Back to Home</a>
                </div>
            </form>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script src="../assets/js/region-crop.js"></script>
</body>
</html>
