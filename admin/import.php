<?php
require_once __DIR__ . '/../download_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['datafile']) && $_FILES['datafile']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['datafile']['tmp_name'];
        $ext = pathinfo($_FILES['datafile']['name'], PATHINFO_EXTENSION);

        // อ่านข้อมูลจากไฟล์
        if ($ext === 'csv') {
            $handle = fopen($tmpName, 'r');
            $firstRow = fgetcsv($handle);

            if (!$firstRow) {
                exit("ไม่พบข้อมูลในไฟล์");
            }

            if (count($firstRow) < 5) {
                exit("ไฟล์ควรมีอย่างน้อย 5 คอลัมน์");
            }
            fclose($handle);
            
        } else {
            exit("รองรับเฉพาะไฟล์ .csv เท่านั้น");
        }
    } else {
        echo "กรุณาเลือกไฟล์ที่ต้องการอัปโหลด";
    }

// ลบข้อมูลเดิมถ้าเลือก replace
if ($mode === 'replace') {
    $db->exec("DELETE FROM employee");
}

// เตรียม insert
$stmt = $db->prepare("INSERT INTO employee (emp_id, emp_name, position, sec_short, cc_name) VALUES (?, ?, ?, ?, ?)");

echo "นำเข้าข้อมูลพนักงานเรียบร้อยแล้ว<br>";
echo '<a href="upload.php">ย้อนกลับ</a>';
