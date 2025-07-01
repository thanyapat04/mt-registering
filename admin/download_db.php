<?php
define('DB_PATH', '/tmp/RegisterForm.db'); // ตำแหน่งฐานข้อมูล
define('DB_URL', getenv('DB_URL'));  // ดึงจาก Environment Variable

if (!file_exists(DB_PATH)) {
    // โหลดจาก Google Drive แค่ครั้งแรก
    $content = file_get_contents(DB_URL);
    if (!$content) {
        exit("ไม่สามารถดาวน์โหลดฐานข้อมูลได้");
    }
    file_put_contents(DB_PATH, $content);
}

try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("เชื่อมต่อฐานข้อมูลล้มเหลว: " . htmlspecialchars($e->getMessage()));
}
?>
