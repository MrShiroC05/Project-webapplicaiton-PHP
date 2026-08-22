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
    <link rel="stylesheet" href="assets/css/list.css">
    <script src="assets/js/navigation.js"></script>
    <script src="assets/js/toast.js"></script>
</head>
<body>
    <div id="app-nav"></div>
    <div class="toast-container"></div>

    <div class="page-shell">
        <header class="page-header">
            <div class="header-badge">overview</div>
            <h1>LOL lore</h1>
            <p>Explore the story world through each region.</p>
        </header>

        <main>
            <section class="panel">
                <div class="section-header-row">
                    <h2 class="section-title">About this project</h2>
                </div>
                <p>This project is a simple web application that allows users to manage and explore the lore of League of Legends (LOL) through its regions, champions, and races. Users can view detailed information about each
    region, including its description and associated champions and races. The application provides a user-friendly interface for adding, editing, and deleting regions, champions,
    and races. It also includes a responsive design to ensure accessibility across various devices.</p>
            </section>
            <section class="panel">
                <div class="section-header-row">
                    <h2 class="section-title">Champion list</h2>
                </div>
                    <?php 
                    $sql = "SELECT * FROM champion ORDER BY champion_id ASC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        echo "<div class='card-grid'>";
                        for ($i =0; $i < 8 && $row = $result->fetch_assoc(); $i++) {
                            $imagePath = !empty($row["champion_image"]) ? $row["champion_image"] : '';
                            $id = md5($row["champion_id"]);
                            echo "<article class='card'>";
                            if (!empty($imagePath)) {
                                echo "<img class='card-image' src='./{$imagePath}' alt='{$row['champion_name']}'>";
                            } else {
                                echo "<div class='card-image empty-card-image'>No image</div>";
                            }
                            echo "<div class='card-body'>
                                    <h3>{$row['champion_name']}</h3>
                                    <p>{$row['champion_title']}</p>
                                  </div>
                              </article>";
                        }
                        echo "</div>";
                    } else {
                        echo "<div class='empty-state'>No champion data found.</div>";
                    }
                ?>
                </section>
            <section class="panel">
                <div class="section-header-row">
                    <h2 class="section-title">All regions</h2>
                </div>
                

                <?php 
                    $sql = "SELECT * FROM region ORDER BY region_id ASC";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        echo "<div class='card-grid'>";
                        for ($i = 0; $i < 8 && $row = $result->fetch_assoc(); $i++) {
                            $imagePath = !empty($row["region_image"]) ? $row["region_image"] : '';
                            $id = md5($row["region_id"]);
                            echo "<article class='card'>";
                            if (!empty($imagePath)) {
                                echo "<img class='card-image' src='./{$imagePath}' alt='{$row['region_name']}'>";
                            } else {
                                echo "<div class='card-image empty-card-image'>No image</div>";
                            }
                            echo "<div class='card-body'>
                                    <h3>{$row['region_name']}</h3>
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