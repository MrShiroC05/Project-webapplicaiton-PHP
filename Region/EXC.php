<?php
include '../Connection/connect.php';
include '../method/notification.php';

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
    <script src="../assets/js/navigation.js"></script>
    <link rel="stylesheet" href="../assets/css/navigation.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <title>Document</title>
</head>
<body>
    <div id="app-nav"></div>
    <?php
    // ตรวจสอบว่าตาราง region มีคอลัมน์ region_image หรือยัง
    // ถ้ายังไม่มี ค่าจะใช้เพื่อป้องกันการ query บันทึกรูปโดยไม่ก่อ error
    function regionImageColumnExists($conn) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'region' AND COLUMN_NAME = 'region_image'");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return (int)$row['total'] > 0;
    }

    // บันทึกรูปภาพที่ถูก crop แล้วเป็น Base64 ให้เก็บเป็นไฟล์จริงในโฟลเดอร์ upload/region
    // เราจะไม่ใช้ region_id จริงในชื่อไฟล์ เพราะถ้าชื่อไฟล์มี R001, R002 อยู่จะทำให้คน inspect ได้รู้ข้อมูลสำคัญของ row
    // วิธีที่ปลอดภัยกว่าคือใช้ชื่อแบบสุ่ม/uuid เพื่อป้องกันการเปิดเผย ID และทำให้ยากต่อการเดา
    function saveRegionImage($dataUrl, $regionId) {
        // ถ้าไม่มีข้อมูลรูป หรือรูปไม่ได้มาจาก data:image/ จะไม่บันทึกอะไร
        if (empty($dataUrl) || strpos($dataUrl, 'data:image/') !== 0) {
            return null;
        }

        // สร้างโฟลเดอร์ upload และ upload/region ถ้ายังไม่มี
        $baseDir = __DIR__ . '/../upload';
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        $regionDir = $baseDir . '/region';
        if (!is_dir($regionDir)) {
            mkdir($regionDir, 0777, true);
        }

        // แยกข้อมูล Base64 ออกจาก header เช่น data:image/png;base64,...
        // header เอาไว้รู้ชนิดไฟล์ เช่น png, jpg
        // base64Data คือข้อมูลภาพจริงที่ต้อง decode กลับเป็น binary
        $parts = explode(',', $dataUrl, 2);
        $header = $parts[0] ?? 'data:image/png;base64';
        $base64Data = $parts[1] ?? '';

        // ดึงนามสกุลไฟล์จาก header เช่น png, jpg
        preg_match('/data:image\/([a-zA-Z0-9.+-]+)/', $header, $matches);
        $ext = !empty($matches[1]) ? strtolower($matches[1]) : 'png';
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }

        // ตั้งชื่อไฟล์แบบสุ่มแทนที่จะใช้ region_id จริง เพื่อป้องกันการเปิดเผย ID ของ row
        // เช่น randomName = 7f2f1fd8a9c44a5a.jpg
        // โดยใช้ uniqid() หรือ random_bytes() ก็ได้ แต่ uniqid() ใช้งานง่ายกว่าและมีความแปลกสำหรับคน inspect
        $safeName = uniqid('', true) . '.' . $ext;
        $filePath = $regionDir . '/' . $safeName;

        // แปลง base64 เป็น binary ก่อนเขียนไฟล์
        $decoded = base64_decode($base64Data, true);
        if ($decoded === false) {
            return null;
        }

        // เขียนไฟล์ลงดิสก์ ถ้าสำเร็จให้คืนค่า path ที่ใช้เก็บใน DB
        // โดย path ที่เก็บใน DB จะเป็นชื่อสุ่ม ไม่ใช่ ID จริง
        if (file_put_contents($filePath, $decoded) !== false) {
            return 'upload/region/' . $safeName;
        }

        return null;
    }

    function getCurrentRegionImage($conn, $regionId) {
        $stmt = $conn->prepare("SELECT region_image FROM region WHERE region_id = ?");
        $stmt->bind_param("s", $regionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['region_image'] ?? null;
    }

    function deleteRegionImageFile($imagePath) {
        if (empty($imagePath)) {
            return;
        }

        $fullPath = __DIR__ . '/../' . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    // ตรวจสอบ method ที่ส่งมา หากเป็น POST หมายถึง มีการ add หรือ update region
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = $_POST['action'];

        // กรณีเพิ่ม region ใหม่
        if ($action === 'add') {
            // นับจำนวน row ปัจจุบัน เพื่อสร้าง region_id ใหม่แบบต่อเนื่อง เช่น R001, R002
            $sql = "SELECT COUNT(*) AS total_rows FROM region";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $totalRows = $row["total_rows"];

            // ดึงค่า input จากฟอร์ม
            $regionId = "R" . str_pad($totalRows + 1, 3, "0", STR_PAD_LEFT);
            $regionName = $_POST['regionName'];
            $regionDescription = $_POST['regionDescription'];
            $regionImage = $_POST['regionImageData'] ?? null;

            // บันทึกรูปภาพแล้วได้ path เช่น upload/region/R001_123456.png
            $imagePath = saveRegionImage($regionImage, $regionId);

            // ตรวจสอบว่ามีคอลัมน์ region_image ในตารางหรือไม่ เพื่อให้ insert ให้ถูกต้อง
            $imageColumnExists = regionImageColumnExists($conn);

            if ($imageColumnExists) {
                // ถ้ามีคอลัมน์ region_image ให้บันทึกทั้งหมดพร้อมรูปภาพ
                $stmt = $conn->prepare("INSERT INTO region (region_id, region_name, region_description, region_image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $regionId, $regionName, $regionDescription, $imagePath);
            } else {
                // ถ้าไม่มีคอลัมน์ region_image ให้ insert เฉพาะข้อมูลที่มีอยู่แล้ว
                $stmt = $conn->prepare("INSERT INTO region (region_id, region_name, region_description) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $regionId, $regionName, $regionDescription);
            }

            // ทดสอบ execute คำสั่ง insert และ redirect กลับไปหน้า listRegion.php พร้อม status
            if ($stmt->execute()) {
                redirectWithStatus('./listRegion.php', 'success', 'New region added successfully.');
            } else {
                redirectWithStatus('./listRegion.php', 'error', 'Error: ' . $stmt->error);
            }

            $stmt->close();
        }
        // กรณีอัปเดต region ที่มีอยู่
        else if ($action === 'update') {
            // รับข้อมูลจากฟอร์ม update
            $regionId = $_POST['region_id'];
            $regionName = $_POST['regionName'];
            $regionDescription = $_POST['regionDescription'];
            $regionImage = $_POST['regionImageData'] ?? null;
            $currentImage = getCurrentRegionImage($conn, $regionId);
            $imageColumnExists = regionImageColumnExists($conn);

            if ($imageColumnExists) {
                if (!empty($regionImage)) {
                    // ถ้ามีรูปภาพใหม่ ให้ลบรูปเก่าก่อนแล้วบันทึกรูปใหม่
                    deleteRegionImageFile($currentImage);
                    $imagePath = saveRegionImage($regionImage, $regionId);
                    $stmt = $conn->prepare("UPDATE region SET region_name = ?, region_description = ?, region_image = ? WHERE region_id = ?");
                    $stmt->bind_param("ssss", $regionName, $regionDescription, $imagePath, $regionId);
                } else {
                    // ถ้าไม่มีรูปใหม่ ให้ลบรูปเก่าทิ้งและกำหนดให้ region_image = NULL
                    deleteRegionImageFile($currentImage);
                    $stmt = $conn->prepare("UPDATE region SET region_name = ?, region_description = ?, region_image = NULL WHERE region_id = ?");
                    $stmt->bind_param("sss", $regionName, $regionDescription, $regionId);
                }
            } else {
                // ถ้าไม่มีคอลัมน์ region_image ให้ update เฉพาะชื่อและรายละเอียด
                $stmt = $conn->prepare("UPDATE region SET region_name = ?, region_description = ? WHERE region_id = ?");
                $stmt->bind_param("sss", $regionName, $regionDescription, $regionId);
            }

            // ทดสอบ execute คำสั่ง update และ redirect กลับหน้า listRegion.php พร้อม status
            if ($stmt->execute()) {
                redirectWithStatus('./listRegion.php', 'success', 'Region updated successfully.');
            } else {
                redirectWithStatus('./listRegion.php', 'error', 'Error: ' . $stmt->error);
            }

            $stmt->close();
        }
    }
    // ถ้าไม่ได้เป็น POST แปลว่าเป็นการลบข้อมูล ในกรณีนี้จะอ่าน action จาก URL
    else {
        $action = $_GET['action'];
        $regionId = decodeRegionId($_GET['region_id'] ?? '');

        if ($regionId === null) {
            die('Invalid region ID.');
        }

        // กรณีลบ region
        if ($action === 'delete') {
            // ตรวจสอบก่อนว่ามี row ที่ต้องการลบจริงหรือไม่
            // ถ้าไม่พบ ให้หยุดทำงานทันทีและไม่ลบไฟล์หรือข้อมูลใด ๆ
            $checkStmt = $conn->prepare("SELECT region_id FROM region WHERE region_id = ? LIMIT 1");
            $checkStmt->bind_param("s", $regionId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $existingRow = $checkResult->fetch_assoc();
            $checkStmt->close();

            if (!$existingRow) {
                redirectWithStatus('./listRegion.php', 'error', 'Error: Region not found.');
            }

            $currentImage = getCurrentRegionImage($conn, $regionId);
            deleteRegionImageFile($currentImage);

            // ใช้ prepared statement เพื่อความปลอดภัยและป้องกัน SQL Injection
            $stmt = $conn->prepare("DELETE FROM region WHERE region_id = ?");
            $stmt->bind_param("s", $regionId);

            if ($stmt->execute()) {
                redirectWithStatus('./listRegion.php', 'success', 'Region deleted successfully.');
            } else {
                redirectWithStatus('./listRegion.php', 'error', 'Error: ' . $stmt->error);
            }

            $stmt->close();
        }
    }

    // ปิดการเชื่อมต่อฐานข้อมูลให้เรียบร้อยหลังจากทำงานเสร็จ
    $conn->close();
    ?>
</body>
</html>