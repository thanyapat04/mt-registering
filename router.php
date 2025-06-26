<?php
// สำหรับ PHP Built-in server ของ Docker
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$file = __DIR__ . $path;

// ถ้าเป็นไฟล์ static (html, css, js, รูป) ให้ส่งไฟล์ออกไปเลย
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// ถ้าเป็นหน้าแรก → แสดง index.html
if ($path === '/' || $path === '/index.html') {
    include __DIR__ . '/index.html';
    exit;
}

// กรณีเข้า "/admin" → redirect ไป login.php
if ($path === '/admin' || $path === '/admin/') {
    include __DIR__ . '/admin/index.html';
    exit;
}

// ถ้าเป็นไฟล์ PHP ที่มีอยู่ → เรียกมัน
if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    include $file;
    exit;
}

// ไม่พบไฟล์ใด ๆ → ส่ง 404
http_response_code(404);
echo "404 Not Found";
