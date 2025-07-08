<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// สร้าง CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// เชื่อมต่อกับไฟล์ SQLite 
        require_once __DIR__ . '/../download_db.php';

// เมื่อกดปุ่มบันทึก (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $db->prepare("UPDATE schedule SET topic = :topic, date = :date, start_time = :start, end_time = :end, room = :room, floor = :floor, building = :building WHERE id = 1");
    $stmt->execute([
        ':topic' => $_POST['topic'],
        ':date' => $_POST['date'],
        ':start' => $_POST['start_time'],
        ':end' => $_POST['end_time'],
        ':room' => $_POST['room'],
        ':floor' => $_POST['floor'],
        ':building' => $_POST['building'],
    ]);
    $success = true;
}

// ดึงข้อมูลการประชุม
$schedule = $db->query("SELECT * FROM schedule WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายละเอียดการประชุม</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
</head>
<body>

<div class="ui container" style="padding-top: 30px;">
    <h2 class="ui header">แก้ไขรายละเอียดการประชุม</h2>

    <?php if (!empty($success)): ?>
        <div class="ui green message">บันทึกสำเร็จ</div>
    <?php endif; ?>

    <form class="ui form" method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($schedule['id']) ?>">

        <div class="field">
            <label>หัวข้อการประชุม</label>
            <input type="text" name="topic" value="<?= htmlspecialchars($schedule['topic']) ?>" required>
        </div>

        <div class="two fields">
            <div class="field">
                <label>วันที่ <span style="color: gray;"> (เช่น 15 มิถุนายน 2568)</span></label>
                <input type="text" name="date" value="<?= htmlspecialchars($schedule['date']) ?>" required>
            </div>
            <div class="field">
                <label>เวลาเริ่ม</label>
                <input type="time" name="start_time" value="<?= htmlspecialchars($schedule['start_time']) ?>" required>
            </div>
            <div class="field">
                <label>เวลาสิ้นสุด</label>
                <input type="time" name="end_time" value="<?= htmlspecialchars($schedule['end_time']) ?>" required>
            </div>
        </div>

        <div class="three fields">
            <div class="field">
                <label>ห้อง</label>
                <input type="text" name="room" value="<?= htmlspecialchars($schedule['room']) ?>">
            </div>
            <div class="field">
                <label>ชั้น</label>
                <input type="text" name="floor" value="<?= htmlspecialchars($schedule['floor']) ?>">
            </div>
            <div class="field">
                <label>สถานที่ <span style="color: gray;"> (เช่น อาคารสำนักงานใหญ่)</span></label>
                <input type="text" name="building" value="<?= htmlspecialchars($schedule['building']) ?>">
            </div>
        </div>

        <button class="ui primary button" type="submit">บันทึก</button>

        <a href="upload.php" class="ui button">อัปเดตข้อมูลพนักงาน</a>
        <a href="logout.php" class="ui button">ออกจากระบบ</a>
    </form>
</div>
</body>
</html>
