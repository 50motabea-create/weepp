<?php
require_once 'config.php';
if (!isAdmin()) { header('Location: login.php'); exit; }

$message = '';

// رفع ملف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    if ($file['error'] === UPLOAD_ERR_OK && $file['size'] <= MAX_FILE_SIZE) {
        $originalName = $file['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFileName = uniqid() . '_' . time() . '.' . $extension;
        if (move_uploaded_file($file['tmp_name'], 'uploads/' . $newFileName)) {
            $stmt = $pdo->prepare('INSERT INTO files (file_name, original_name, file_size) VALUES (?, ?, ?)');
            $stmt->execute([$newFileName, $originalName, $file['size']]);
            $message = '<div class="success-msg">✅ تم رفع الملف بنجاح!</div>';
        } else {
            $message = '<div class="error-msg">❌ فشل رفع الملف</div>';
        }
    } else {
        $message = '<div class="error-msg">❌ حجم الملف كبير جداً أو حدث خطأ</div>';
    }
}

// حذف ملف
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('SELECT file_name FROM files WHERE id = ?');
    $stmt->execute([$id]);
    $file = $stmt->fetch();
    if ($file) {
        @unlink('uploads/' . $file['file_name']);
        $pdo->prepare('DELETE FROM files WHERE id = ?')->execute([$id]);
        $message = '<div class="success-msg">✅ تم حذف الملف بنجاح!</div>';
    }
}

$files = $pdo->query('SELECT * FROM files ORDER BY upload_date DESC')->fetchAll();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - FlexFile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>⚙️ لوحة تحكم FlexFile</h1>
                <p class="dev-credit">👨‍💻 FLEX</p>
                <a href="index.php" class="home-btn">🏠 الرئيسية</a>
                <a href="logout.php" class="logout-btn">🚪 خروج</a>
            </div>
        </div>
    </header>

    <main class="container">
        <?php echo $message; ?>
        
        <div class="upload-section">
            <h2>📤 رفع ملف جديد</h2>
            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <input type="file" name="file" required>
                <button type="submit" class="upload-btn">⬆️ رفع الملف</button>
            </form>
            <p class="upload-note">الحد الأقصى: 100 ميجابايت</p>
        </div>

        <div class="files-section">
            <h2>📁 الملفات المرفوعة (<?php echo count($files); ?>)</h2>
            <?php if (empty($files)): ?>
                <div class="empty-state"><p>📭 لا توجد ملفات</p></div>
            <?php else: ?>
                <table class="files-table">
                    <thead>
                        <tr>
                            <th>الملف</th>
                            <th>الحجم</th>
                            <th>التحميلات</th>
                            <th>التاريخ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($file['original_name']); ?></td>
                                <td><?php echo formatSize($file['file_size']); ?></td>
                                <td><?php echo $file['downloads']; ?></td>
                                <td><?php echo date('Y/m/d', strtotime($file['upload_date'])); ?></td>
                                <td>
                                    <a href="download.php?id=<?php echo $file['id']; ?>" class="btn-small">⬇️</a>
                                    <a href="?delete=<?php echo $file['id']; ?>" class="btn-delete" onclick="return confirm('حذف الملف؟')">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>© 2024 FlexFile - جميع الحقوق محفوظة</p>
            <p>👨‍💻 تم التطوير بواسطة: <strong>FLEX</strong></p>
        </div>
    </footer>
</body>
</html>