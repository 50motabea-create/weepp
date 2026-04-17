<?php
session_start();

// ============================================
// عدل هذه المعلومات من لوحة تحكم InfinityFree
// ============================================
$db_host = 'sqlXXX.epizy.com';      // مضيف قاعدة البيانات
$db_user = 'if0_XXXXX';             // اسم مستخدم قاعدة البيانات
$db_pass = 'كلمة_المرور_هنا';        // كلمة مرور قاعدة البيانات
$db_name = 'if0_XXXXX_flexfile';    // اسم قاعدة البيانات
// ============================================

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 1) {
    try {
        $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
        $pdo->exec("USE `$db_name`");
        
        // جدول الملفات
        $pdo->exec("CREATE TABLE IF NOT EXISTS files (
            id INT AUTO_INCREMENT PRIMARY KEY,
            file_name VARCHAR(255) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            file_size BIGINT NOT NULL,
            downloads INT DEFAULT 0,
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // جدول الإعدادات
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT
        )");
        
        // إضافة الإعدادات الافتراضية
        $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('admin_pass', '" . md5('flex2024') . "')")->execute();
        $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('site_name', 'FlexFile')")->execute();
        $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('developer', 'FLEX')")->execute();
        
        // إنشاء مجلد uploads
        if (!file_exists('uploads')) mkdir('uploads', 0755);
        file_put_contents('uploads/.htaccess', "Deny from all");
        
        // إنشاء config.php
        $config = "<?php
session_start();
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');
define('SITE_URL', 'https://flexfile.free.nf');
define('SITE_NAME', 'FlexFile');
define('DEVELOPER', 'FLEX');

try {
    \$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException \$e) {
    die('فشل الاتصال بقاعدة البيانات');
}

function getSetting(\$key) { global \$pdo; \$s = \$pdo->prepare('SELECT setting_value FROM settings WHERE setting_key=?'); \$s->execute([\$key]); return \$s->fetchColumn(); }
function isAdmin() { return isset(\$_SESSION['admin']) && \$_SESSION['admin'] === true; }
function formatSize(\$b) { if(\$b>=1073741824) return round(\$b/1073741824,2).' GB'; if(\$b>=1048576) return round(\$b/1048576,2).' MB'; if(\$b>=1024) return round(\$b/1024,2).' KB'; return \$b.' bytes'; }
?>";
        file_put_contents('config.php', $config);
        
        header('Location: setup.php?step=2');
        exit;
    } catch(PDOException $e) {
        $message = '<p style="color:red">❌ خطأ: ' . $e->getMessage() . '</p>';
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تثبيت FlexFile</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma; background: linear-gradient(135deg, #0f0c29, #302b63); display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .box { background: white; padding: 40px; border-radius: 20px; max-width: 500px; text-align: center; }
        h1 { color: #302b63; } .dev { color: #e94560; font-weight: bold; }
        .btn { background: #e94560; color: white; padding: 12px 30px; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
        .success { color: green; } .warning { background: #fff3cd; padding: 15px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>📦 تثبيت FlexFile</h1>
        <p class="dev">👨‍💻 تم التطوير بواسطة: FLEX</p>
        <?php if($step == 1): ?>
            <?php echo $message; ?>
            <form method="POST"><button type="submit" class="btn">بدء التثبيت</button></form>
        <?php else: ?>
            <p class="success">✅ تم التثبيت بنجاح!</p>
            <p>🔐 كلمة المرور: <code>flex2024</code></p>
            <div class="warning">⚠️ احذف ملف setup.php فوراً!</div>
            <a href="index.php" style="display:inline-block; background:#e94560; color:white; padding:10px 20px; border-radius:8px; text-decoration:none; margin-top:20px;">دخول الموقع</a>
        <?php endif; ?>
    </div>
</body>
</html>