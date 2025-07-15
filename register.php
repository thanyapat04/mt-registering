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
        // เชื่อมต่อกับไฟล์ SQLite 
        require_once __DIR__ . '/download_db.php';
	    
        // เตรียมคำสั่ง SQL แบบปลอดภัย (Prepared Statement)
        $stmt = $db->prepare("SELECT * FROM employee WHERE emp_id = :emp_id"); //table employee
        $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && (strlen($emp_id) >= 6 && strlen($emp_id) <= 8)) {
            echo '<div class="ui positive message">';
            echo '<div class="header">ลงทะเบียนสำเร็จ</div>';
	    echo '<br>';
            echo '<p>รหัสพนักงาน: <strong>' . htmlspecialchars($emp_id) . '</strong></p>';
            echo '<p>ชื่อ: ' . htmlspecialchars($row['emp_name']) . '</p>';
	    echo '<p>ตำแหน่ง: ' . htmlspecialchars($row['position'] ?? 'ไม่ระบุ') . '</p>';
            echo '<p>ส่วนงานย่อ: ' . htmlspecialchars($row['sec_short'] ?? 'ไม่ระบุ') . '</p>';
            echo '<p>ชื่อศูนย์ต้นทุน: ' . htmlspecialchars($row['cc_name'] ?? 'ไม่ระบุ') . '</p>';
            echo '</div>';

	    define('$url', getenv('Sheet'));	
	    //$url = "https://script.google.com/macros/s/AKfycbyQcNpLCgjbeVAfGZwmK9suB5OuWPyGl2W5UJ98tIqumUk2-Yu9w9a-UzhjTjhtvcM/exec";
	    $data = array(
                'รหัสพนักงาน' => $emp_id,
                'ชื่อ' => $row['emp_name'],
		'ตำแหน่ง' => $row['position'],
                'ส่วนงานย่อ' => $row['sec_short'],
                'ชื่อศูนย์ต้นทุน' => $row['cc_name']
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
	                <label for="new_emp_id">รหัสพนักงาน <span style="color: gray;"> (ไม่ต้องใส่ 0 ด้านหน้า)</span></label>
	                <input type="text" id="new_emp_id" name="new_emp_id" required>
	            </div>
	            <div class="field">
	                <label for="new_emp_name">ชื่อ-นามสกุล</label>
	                <input type="text" id="new_emp_name" name="new_emp_name" required>
	            </div>
		    <div class="field">
	                <label for="new_position">ตำแหน่ง <span style="color: gray;">(optional)</span></label>
	                <input type="text" id="new_position" name="new_position">
	            </div>
	            <div class="field">
	                <label for="new_sec_short">ส่วนงานย่อ <span style="color: gray;">(optional)</span></label>
	                <input type="text" id="new_sec_short" name="new_sec_short">
	            </div>
	            <div class="field">
	                <label for="new_cc_name">ชื่อศูนย์ต้นทุน <span style="color: gray;">(optional)</span></label>
	                <input type="text" id="new_cc_name" name="new_cc_name">
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
