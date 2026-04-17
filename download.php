<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM files WHERE id = ?');
$stmt->execute([$id]);
$file = $stmt->fetch();

if (!$file) { die('الملف غير موجود'); }

$pdo->prepare('UPDATE files SET downloads = downloads + 1 WHERE id = ?')->execute([$id]);
$filePath = 'uploads/' . $file['file_name'];

if (file_exists($filePath)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    die('الملف غير موجود على السيرفر');
}
?>