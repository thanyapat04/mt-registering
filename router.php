<?php
if (file_exists(__DIR__ . $_SERVER['REQUEST_URI'])) {
    return false;
}
// fallback: ไปที่ index.html เสมอ
include __DIR__ . '/index.html';
?>
