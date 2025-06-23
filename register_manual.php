<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ลงทะเบียนการประชุมด้วยตนเอง</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background-color: #f9f9f9; padding: 40px;">

<div class="ui container">
    
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_emp_id = $_POST['new_emp_id']; 
        $new_emp_name = $_POST['new_emp_name'];
        $new_sec_short = $_POST['new_sec_short'] ?? '';
        $new_sec_full = $_POST['new_sec_full'] ?? '';

        // ทำการตรวจสอบข้อมูลพื้นฐาน
        if (strlen($new_emp_id) < 6 || strlen($new_emp_id) > 8) {
            echo '<div class="ui red message">';
            echo '<h3 class="ui header">เกิดข้อผิดพลาด</h3>';
            echo '<p>กรุณากรอกข้อมูลให้ถูกต้อง รหัสพนักงานต้องมีความยาว 6 หรือ 8 ตัวอักษร</p>';
            echo '<button class="ui button" onclick="window.history.back()">ย้อนกลับ</button>';
            echo '</div>';
            exit();
        }

        // ส่งข้อมูลไป Google Apps Script (ให้ตรงกับ Sheets)
        // ตรวจสอบให้แน่ใจว่า URL นี้รับข้อมูลประเภทนี้ได้
        $url = "https://script.google.com/macros/s/AKfycbxeRlF3j8Ov8i9WH5uniV-EQajYAyKTBImmCu7KuxC8WDW2we_X5Pt0crQgcsDS_m3V/exec"; 
        $data = array(
            'รหัสพนักงาน' => $new_emp_id, 
            'ชื่อ' => $new_emp_name,
            'ส่วนงานย่อ' => $new_sec_short,
            'ส่วนงานเต็ม' => $new_sec_full, 
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
    } 
?>
        <div class="ui positive message">
            <h2 class="ui header">ลงทะเบียนสำเร็จ</h2><br>
            <p><strong>รหัสพนักงาน:</strong> <?= htmlspecialchars($new_emp_id) ?></p>
            <p><strong>ชื่อ:</strong> <?= htmlspecialchars($new_emp_name) ?></p>
            <p><strong>ส่วนงานย่อ:</strong> <?= htmlspecialchars($new_sec_short) ?: '<span style="color:gray;">(ไม่ระบุ)</span>' ?></p>
            <p><strong>ส่วนงานเต็ม:</strong> <?= htmlspecialchars($new_sec_full) ?: '<span style="color:gray;">(ไม่ระบุ)</span>' ?></p>
        </div>

</div>
</body>
</html>
