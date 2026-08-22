<?php
// เปิด session เพื่อเก็บ token ของผู้ใช้แต่ละคน
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// สร้าง token แบบสุ่มสำหรับใส่ไว้ในฟอร์ม
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// ตรวจว่า token ที่ส่งมากับฟอร์มตรงกับ token ใน session หรือไม่
// ถ้าไม่ตรง แสดงว่าคำขออาจมาจากเว็บอื่นและจะถูกปฏิเสธ
function requireCsrfToken() {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($submittedToken) || !hash_equals(csrfToken(), $submittedToken)) {
        http_response_code(403);
        exit('Invalid security token.');
    }
}

// ตัดช่องว่างและจำกัดความยาวข้อความก่อนนำไปใช้งาน
function cleanText($value, $maxLength = 255) {
    $value = trim((string) $value);
    return function_exists('mb_substr') ? mb_substr($value, 0, $maxLength) : substr($value, 0, $maxLength);
}

// ตรวจว่า ID มีรูปแบบถูกต้อง เช่น C001, R001
function validEntityId($value) {
    return is_string($value) && preg_match('/^[A-Z][0-9]{3}$/', $value) === 1;
}

// แปลง ID จริงเป็น MD5 เพื่อใช้ส่งผ่าน URL หรือฟอร์ม
function hashEntityId($id) {
    return md5((string) $id);
}

// ค้นหา ID จริงจาก MD5 โดยอนุญาตเฉพาะตารางที่กำหนดไว้เท่านั้น
function resolveEntityId($conn, $entity, $hash) {
    $entities = [
        'champion' => ['table' => 'champion', 'column' => 'champion_id'],
        'race' => ['table' => 'race', 'column' => 'race_id'],
        'region' => ['table' => 'region', 'column' => 'region_id'],
    ];

    if (!isset($entities[$entity]) || !is_string($hash) || !preg_match('/^[a-f0-9]{32}$/', $hash)) {
        return null;
    }

    // ใช้ชื่อ table และ column จากรายการที่กำหนดไว้ ป้องกัน SQL injection
    $table = $entities[$entity]['table'];
    $column = $entities[$entity]['column'];
    $stmt = $conn->prepare("SELECT {$column} FROM {$table} WHERE MD5({$column}) = ? LIMIT 1");
    $stmt->bind_param('s', $hash);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row[$column] ?? null;
}
?>
