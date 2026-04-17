<?php
session_start();
define('DB_HOST', 'sqlXXX.epizy.com');
define('DB_USER', 'if0_XXXXX');
define('DB_PASS', 'كلمة_المرور_هنا');
define('DB_NAME', 'if0_XXXXX_flexfile');
define('SITE_URL', 'https://flexfile.free.nf');
define('SITE_NAME', 'FlexFile');
define('DEVELOPER', 'FLEX');

try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die('فشل الاتصال بقاعدة البيانات');
}

function getSetting($key) { global $pdo; $s = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key=?'); $s->execute([$key]); return $s->fetchColumn(); }
function isAdmin() { return isset($_SESSION['admin']) && $_SESSION['admin'] === true; }
function formatSize($b) { if($b>=1073741824) return round($b/1073741824,2).' GB'; if($b>=1048576) return round($b/1048576,2).' MB'; if($b>=1024) return round($b/1024,2).' KB'; return $b.' bytes'; }
?>