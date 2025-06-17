<html>
<head>
    <title>ลงทะเบียนการประชุมด้วยตนเอง</title>
</head>
<body>
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_emp_id = $_POST['new_emp_id']; 
        $new_emp_name = $_POST['new_emp_name'];
        $new_department = $_POST['new_department'];
        $new_position = $_POST['new_position'] ?? '';

        // ทำการตรวจสอบข้อมูลพื้นฐาน
        if (empty($new_emp_id) || empty($new_emp_name) || empty($new_department) || empty($new_position) || strlen($new_emp_id) !== 6) {
            echo "<h2>ข้อผิดพลาด:</h2>";
            echo "<p>ข้อมูลไม่ถูกต้องหรือไม่ครบถ้วน กรุณาลองใหม่อีกครั้ง</p>";
            echo '<button onclick="window.history.back()">ย้อนกลับ</button>';
            exit();
        }

        // ส่งข้อมูลไป Google Apps Script (ปรับปรุงคอลัมน์ให้ตรงกับ Sheets)
        // ตรวจสอบให้แน่ใจว่า URL นี้รับข้อมูลประเภทนี้ได้
        $url = "https://script.google.com/macros/s/AKfycbzcE6KiBVwQwU59sOqzOI--TXftO0prcfQcZngd6MzciH52VGIfrYF4z6Zmqi4pMvaX/exec"; 
        $data = array(
            'รหัสพนักงาน' => $new_emp_id, 
            'ชื่อ' => $new_emp_name,
            'แผนก' => $new_department,
            'ตำแหน่ง' => $new_position, 
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json",
                'method'  => 'POST',
                'content' => json_encode($data),
                'ignore_errors' => true // สำคัญ: เพื่อให้ file_get_contents ไม่ fail ทันทีถ้า Google Script มีปัญหา
            ),
        );

        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context); // ใช้ @ เพื่อไม่ให้แสดง warning ถ้า Google Script มีปัญหา

        echo "<h2>ลงทะเบียนสำเร็จ</h2>";
        echo "รหัสพนักงาน: " . htmlspecialchars($new_emp_id) . "<br>";
        echo "ชื่อ: " . htmlspecialchars($new_emp_name) . "<br>";
        echo "แผนก: " . htmlspecialchars($new_department) . "<br>";
        echo "ตำแหน่ง: " . htmlspecialchars($new_position) . "<br>";
      
    } 
?>
</body>
</html>
