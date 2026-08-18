<?php
include '../Connection/connect.php';
include '../method/database.php';

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
            echo "<section class='panel'>
                    <table class='table'>
                        <tr>
                            <th>Region Name</th>
                            <th>Region Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>";

            foreach ($regions as $row) {
                $imagePath = !empty($row['region_image']) ? $row['region_image'] : '';
                $encodedId = encodeRegionId($row['region_id']);

                echo "<tr>
                        <td>{$row['region_name']}</td>
                        <td>{$row['region_description']}</td>
                        <td>";

                if (!empty($imagePath)) {
                    echo "<img class='image' src='../{$imagePath}' alt='{$row['region_name']}'>";
                } else {
                    echo "<span class='no-image'>No image</span>";
                }

                echo "</td>
                        <td>
                            <div class='action-group'>
                                <a class='edit-btn' href='update.php?id={$encodedId}'>Edit</a>
                                <a class='delete-btn' href='EXC.php?action=delete&region_id={$encodedId}' data-delete-region>Delete</a>
                            </div>
                        </td>
                    </tr>";
            }

            echo "</table>
                </section>";
        } else {
            echo "<div class='empty-state'>No region data found.</div>";
        }
        ?>
    </div>
    <script src="../assets/js/region-actions.js"></script>
</body>
</html>
