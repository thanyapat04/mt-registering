<?php
session_start(); 
ob_start();
try {
        // เชื่อมต่อกับไฟล์ SQLite 
        require_once __DIR__ . '/download_db.php';
        
        $error = "";

        $stmt = $db->query("SELECT username, password FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
        // ตรวจสอบว่าแบบฟอร์มถูกส่งด้วย POST หรือไม่
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"];
            $password = $_POST["password"] ?? '';

            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION["user"] = $user["username"];
                header("Location: detail.php"); // เปลี่ยนไปหน้าหลังล็อกอิน
                exit();
            } else {
                $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            }
            
        }
    } catch (PDOException $e) {
        $error = "เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage());
    }
?>

<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบเจ้าหน้าที่</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background: #f0f2f5;
            padding: 50px;
        }
        .ui.form input {
            font-size: 1em;
        }
        .ui.container {
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="ui container">
    <h2 class="ui dividing header">เข้าสู่ระบบเจ้าหน้าที่</h2>
        
    <form class="ui form" method="POST" action="">
        <div class="field">
            <label>ชื่อผู้ใช้</label>
            <input type="text" name="username" required>
        </div>
        <div class="field">
            <label>รหัสผ่าน</label>
            <input type="password" name="password" required>
        </div>
        <button class="ui primary button" type="submit">เข้าสู่ระบบ</button>
    </form>

</div>

</body>
</html>
