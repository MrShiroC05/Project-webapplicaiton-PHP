<?php
// File role: read-only home page showing all regions as cards.
include './Connection/connect.php';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOL lore</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/navigation.css">
    <link rel="stylesheet" href="assets/css/region-list.css">
    <script src="assets/js/navigation.js"></script>
    <script src="assets/js/toast.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="toast-container"></div>

    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">Region overview</div>
            <h1>LOL lore</h1>
            <p>Explore the story world through each region.</p>
        </header>

        <main>
            <section class="panel">
                <div class="section-header-row">
                    <h2 class="section-title">All regions</h2>
                    <a href="Region/listRegion.php" class="primary-btn small-btn">Manage Region</a>
                </div>

                <?php 
                    $sql = "SELECT * FROM region ORDER BY region_id ASC";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        echo "<div class='region-card-grid'>";
                        while($row = $result->fetch_assoc()) {
                            $imagePath = !empty($row["region_image"]) ? $row["region_image"] : '';
                            $id = md5($row["region_id"]);
                            echo "<article class='region-card'>";
                            if (!empty($imagePath)) {
                                echo "<img class='region-card-image' src='./{$imagePath}' alt='{$row['region_name']}'>";
                            } else {
                                echo "<div class='region-card-image empty-card-image'>No image</div>";
                            }
                            echo "<div class='region-card-body'>
                                    <h3>{$row['region_name']}</h3>
                                    <p>{$row['region_description']}</p>
                                  </div>
                              </article>";
                        }
                        echo "</div>";
                    } else {
                        echo "<div class='empty-state'>No region data found.</div>";
                    }
                ?>
            </section>
        </main>
    </div>
</body>
</html>