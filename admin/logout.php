<?php
  session_start();
  $_SESSION = array(); // ล้างตัวแปร session ทั้งหมด
  session_destroy();   // ทำลาย session
  header("Location: login.php"); // กลับไปหน้า login
  exit();
?>
