<?php
include '../Connection/connect.php';
include '../method/database.php';
include '../method/security.php';

function encodeRegionId($value) {
    return rtrim(strtr(base64_encode((string)$value), '+/', '-_'), '=');
}

function decodeRegionId($value) {
    $padding = str_repeat('=', (4 - strlen($value) % 4) % 4);
    $decoded = base64_decode(strtr($value, '-_', '+/') . $padding, true);
    return $decoded !== false ? $decoded : null;
}

$regionRepo = new RegionRepository($conn);
$regions = $regionRepo->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Region List</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <link rel="stylesheet" href="../assets/css/list.css">
    <script src="../assets/js/navigation.js"></script>
    <script src="../assets/js/toast.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="toast-container"></div>

    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Region overview</div>
            <h1>Region List</h1>
            <a class='edit-btn' href='new.php'>Add New Region</a>
        </header>

        <?php
        if (!empty($regions)) {
            echo "<section class='card-grid'>";

            foreach ($regions as $row) {
                $imagePath = !empty($row['region_image']) ? $row['region_image'] : '';
                $encodedId = encodeRegionId($row['region_id']);

                echo "<article class='card'>";

                if (!empty($imagePath)) {
                    echo "<img class='card-image region-card-image' src='../{$imagePath}' alt='{$row['region_name']}'>";
                } else {
                    echo "<div class='card-image region-card-image empty-card-image'>No image</div>";
                }

                echo "<div class='card-body'>
                            <h3>{$row['region_name']}</h3>
                            <div class='action-group'>
                                <a class='primary-btn' href='detail.php?region_id=" . urlencode(hashEntityId($row['region_id'])) . "'>More detail</a>
                            </div>
                        </div>
                    </article>";
            }

            echo "</section>";
        } else {
            echo "<div class='empty-state'>No region data found.</div>";
        }
        ?>
    </div>
    <script src="../assets/js/region-actions.js"></script>
</body>
</html>
