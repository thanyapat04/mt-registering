<?php
  session_start();

  unset($_SESSION['user']); // ล้างตัวแปร session ทั้งหมด
  session_destroy();   // ทำลาย session

  header("Location: login.php"); // กลับไปหน้า login
  exit();
?>
