<?php
$db = new PDO('sqlite:../RegisterForm.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_FILES['datafile']) || $_FILES['datafile']['error'] !== UPLOAD_ERR_OK) {
    die("อัปโหลดไฟล์ล้มเหลว");
}

$mode = $_POST['mode'] ?? 'replace';
$tmpName = $_FILES['datafile']['tmp_name'];
$filename = $_FILES['datafile']['name'];

$data = [];

// อ่าน CSV
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if ($ext === 'csv') {
    if (($handle = fopen($tmpName, 'r')) !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = $row;
        }
        fclose($handle);
    }
} else {
    die("รองรับเฉพาะ .csv เท่านั้น");
}

if (count($data) < 2) {
    die("ไม่มีข้อมูลในไฟล์");
}

// ลบข้อมูลเดิมถ้าเลือก replace
if ($mode === 'replace') {
    $db->exec("DELETE FROM employee");
}

// เตรียม insert
$stmt = $db->prepare("INSERT INTO employee (emp_id, emp_name, position, sec_short, cc_name) VALUES (?, ?, ?, ?, ?)");

echo "นำเข้าข้อมูลพนักงานเรียบร้อยแล้ว<br>";
echo '<a href="upload.php">ย้อนกลับ</a>';
