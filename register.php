<html>
<body>
<?php 
	$emp_id = $_POST['emp_id'];

    try {
        // เชื่อมต่อกับไฟล์ SQLite ชื่อ employee.db
        $db = new PDO('sqlite:employee.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // เตรียมคำสั่ง SQL แบบปลอดภัย (Prepared Statement)
        $stmt = $db->prepare("SELECT emp_name, department, position FROM employee WHERE emp_id = :emp_id");
        $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && strlen($emp_id) === 6) {
            echo "<h2>ลงทะเบียนสำเร็จ</h2>";
            echo "ชื่อ: " . htmlspecialchars($row['emp_name']) . "<br>";
            echo "แผนก: " . htmlspecialchars($row['department']) . "<br>";
            echo "ตำแหน่ง: " . htmlspecialchars($row['position']) . "<br>";
        } else {
            echo "รหัสไม่ถูกต้อง กรุณากรอกใหม่อีกครั้ง";
        }

    } catch (PDOException $e) {
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    } 
?>
</body>
</html>
