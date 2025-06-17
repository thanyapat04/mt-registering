<?php
if (file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    return false; // serve the requested resource as-is
}
include 'index.html'; // fallback to index.html
?>
