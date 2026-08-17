<?php
// File role: display all regions and allow management actions.
include '../Connection/connect.php';

function encodeRegionId($value) {
    return rtrim(strtr(base64_encode((string)$value), '+/', '-_'), '=');
}

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
    <title>Region List</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <link rel="stylesheet" href="../assets/css/region-list.css">
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
        // ดึงข้อมูล region ทั้งหมดจากฐานข้อมูล เรียงตาม region_id เพื่อให้แสดงตามลำดับที่เหมาะสม
        $sql = "SELECT * FROM region ORDER BY region_id ASC";
        $result = $conn->query($sql);

        // ถ้ามีข้อมูล region มากกว่า 0 ให้แสดงตาราง และรูปภาพ พร้อมรายละเอียด
        if ($result && $result->num_rows > 0) {
            echo "<section class='panel'>
                    <table class='region-table'>
                        <tr>
                            <th>Region Name</th>
                            <th>Region Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>";

            while ($row = $result->fetch_assoc()) {
                // ตั้งค่า path รูปภาพ ถ้าไม่มีให้แสดงข้อความว่าไม่มีรูป
                $imagePath = !empty($row['region_image']) ? $row['region_image'] : '';
                $encodedId = encodeRegionId($row['region_id']);

                echo "<tr>
                        <td>{$row['region_name']}</td>
                        <td>{$row['region_description']}</td>
                        <td>";

                if (!empty($imagePath)) {
                    echo "<img class='region-image' src='../{$imagePath}' alt='{$row['region_name']}'>";
                } else {
                    echo "<span class='region-no-image'>No image</span>";
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
            // ถ้าไม่มี region ใด ๆ ให้แจ้งชัดเจนว่าไม่มีข้อมูล
            echo "<div class='empty-state'>No region data found.</div>";
        }

        // ปิดการเชื่อมต่อฐานข้อมูล
        $conn->close();
        ?>
    </div>
    <script src="../assets/js/region-actions.js"></script>
</body>
</html>
