<?php
require_once 'config.php';
if (isAdmin()) { header('Location: admin.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (md5($_POST['password'] ?? '') === getSetting('admin_pass')) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = '❌ كلمة المرور غير صحيحة';
    }
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول المطور - FlexFile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>🔐 دخول المطور</h1>
            <p class="dev-name">👨‍💻 FLEX</p>
            <?php if ($error): ?><div class="error-msg"><?php echo $error; ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>كلمة المرور:</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-login">🚪 دخول</button>
            </form>
            <div class="login-footer"><a href="index.php">← العودة للموقع</a></div>
        </div>
    </div>
</body>
</html>