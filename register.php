<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ผลการลงทะเบียน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            padding: 40px;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

<div class="ui container">
<?php 
	$emp_id = $_POST['emp_id'];

    try {
        // เชื่อมต่อกับไฟล์ SQLite ชื่อ employee.db
        $db = new PDO('sqlite:meeting.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // เตรียมคำสั่ง SQL แบบปลอดภัย (Prepared Statement)
        $stmt = $db->prepare("SELECT emp_name, department, position FROM employee WHERE emp_id = :emp_id"); //table employee
        $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && (strlen($emp_id) === 6 || strlen($emp_id) === 8)) {
            echo '<div class="ui positive message">';
            echo '<div class="header">ลงทะเบียนสำเร็จ</div>';
            echo '<p>รหัสพนักงาน: <strong>' . htmlspecialchars($emp_id) . '</strong></p>';
            echo '<p>ชื่อ: ' . htmlspecialchars($row['emp_name']) . '</p>';
            echo '<p>ฝ่าย: ' . htmlspecialchars($row['department']) . '</p>';
            echo '<p>ส่วนงาน: ' . htmlspecialchars($row['position']) . '</p>';
            echo '</div>';
		
	    $url = "https://script.google.com/macros/s/AKfycbz1dhdhpzMXyphDDy1RtngJeR2JmitOna2lsUjWPgdTDhRtQhEr_bgmqOjmknZK78Fd/exec";
	    $data = array(
                'รหัสพนักงาน' => $emp_id,
                'ชื่อ' => $row['emp_name'],
                'ฝ่าย' => $row['department'],
                'ส่วนงาน' => $row['position']
            );

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json",
                    'method'  => 'POST',
                    'content' => json_encode($data),
                ),
            );

            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
		
        } else {
            echo '<div class="ui negative message">';
        	echo '<div class="header">รหัสไม่ถูกต้องหรือไม่พบข้อมูล</div>';
        	echo '<p>กรุณาตรวจสอบรหัสพนักงาน หรือกรอกข้อมูลด้วยตนเอง</p>';
        	echo '</div>';

        	echo '<button class="ui button" onclick="window.history.back()">ย้อนกลับ</button>';

        	echo '<div class="ui segment">';
       		echo '<h3 class="ui header">กรอกข้อมูลด้วยตนเอง</h3>';
                ?>
                <form class="ui form" name="addform" method="POST" action="register_manual.php">
        	    <div class="field">
	                <label for="new_emp_id">รหัสพนักงาน <span style="color: gray;"> (หากรหัสพนักงานมี 6 หลัก ไม่จำเป็นต้องใส่ 0 ด้านหน้า)</span></label>
	                <input type="text" id="new_emp_id" name="new_emp_id" required>
	            </div>
	            <div class="field">
	                <label for="new_emp_name">ชื่อ-นามสกุล</label>
	                <input type="text" id="new_emp_name" name="new_emp_name" required>
	            </div>
	            <div class="field">
	                <label for="new_department">ฝ่าย <span style="color: gray;">(optional)</span></label>
	                <input type="text" id="new_department" name="new_department">
	            </div>
	            <div class="field">
	                <label for="new_position">ส่วนงาน <span style="color: gray;">(optional)</span></label>
	                <input type="text" id="new_position" name="new_position">
	            </div>
	            <button class="ui primary button" type="submit">ลงทะเบียน</button>
     		</form>
                <?php
                echo '</div>'; // close additional-form
            }

    } catch (PDOException $e) {
        echo '<div class="ui error message">';
	echo '<div class="header">เกิดข้อผิดพลาด</div>';
	echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
	echo '</div>';
    } 
?>

</div>
	
</body>
</html>
