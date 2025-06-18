<html>
<head>
    <title>เข้าสู่ระบบผู้ดูแล</title>
</head>
<body>
    <form name="loginpage" method="POST" action="register.php">
        Username: <input type="text" name="username" value="" size="10" />
        <br>
        Password: <input type="text" name="password" value="" size="10" />
        <br>
        <input type="submit" value="Login"/>	
     </form>
  <?php
  session_start();
  $db = new PDO('sqlite:meeting.db');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $username = $_POST["username"];
  $password = $_POST["password"];

  $login = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
  $login->bindValue(':username', $username);
  $login->execute();

  $user = $login->fetch(PDO::FETCH_ASSOC);
  if ($user && password_verify($password, $user['password'])) {
        // เข้าสู่ระบบสำเร็จ
        $_SESSION["user"] = $user["username"];
        header("Location: detail.php"); // เปลี่ยนไปหน้าหลังล็อกอิน
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
?>
  
</body>
</html>
