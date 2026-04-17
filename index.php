<?php
require_once 'config.php';

$stmt = $pdo->query('SELECT * FROM files ORDER BY upload_date DESC');
$files = $stmt->fetchAll();
$totalFiles = count($files);
$totalDownloads = $pdo->query('SELECT SUM(downloads) FROM files')->fetchColumn();
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlexFile - مستودع فليكس للملفات</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>📦 FlexFile</h1>
                <p class="dev-credit">👨‍💻 تم التطوير بواسطة: <strong>FLEX</strong></p>
                <?php if (isAdmin()): ?>
                    <a href="admin.php" class="admin-btn">⚙️ لوحة التحكم</a>
                    <a href="logout.php" class="logout-btn">🚪 خروج</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">🔐 دخول المطور</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="stats">
            <div class="stat-card">
                <span class="stat-number"><?php echo $totalFiles; ?></span>
                <span class="stat-label">📄 ملف</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo number_format($totalDownloads); ?></span>
                <span class="stat-label">⬇️ تحميل</span>
            </div>
        </div>

        <div class="files-section">
            <h2>📁 جميع الملفات</h2>
            <?php if (empty($files)): ?>
                <div class="empty-state"><p>📭 لا توجد ملفات حالياً</p></div>
            <?php else: ?>
                <div class="files-grid">
                    <?php foreach ($files as $file): ?>
                        <div class="file-card">
                            <div class="file-icon">
                                <?php 
                                $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])) echo '🖼️';
                                elseif (in_array($ext, ['mp4','avi','mov','mkv','flv'])) echo '🎬';
                                elseif (in_array($ext, ['mp3','wav','flac','aac','ogg'])) echo '🎵';
                                elseif (in_array($ext, ['zip','rar','7z','tar','gz'])) echo '🗜️';
                                elseif ($ext == 'apk') echo '📱';
                                elseif ($ext == 'exe' || $ext == 'msi') echo '💻';
                                elseif ($ext == 'pdf') echo '📕';
                                elseif (in_array($ext, ['doc','docx'])) echo '📝';
                                elseif (in_array($ext, ['xls','xlsx'])) echo '📊';
                                elseif (in_array($ext, ['ppt','pptx'])) echo '📽️';
                                elseif ($ext == 'txt') echo '📃';
                                else echo '📄';
                                ?>
                            </div>
                            <div class="file-info">
                                <h3><?php echo htmlspecialchars($file['original_name']); ?></h3>
                                <div class="file-meta">
                                    <span>📏 <?php echo formatSize($file['file_size']); ?></span>
                                    <span>⬇️ <?php echo $file['downloads']; ?></span>
                                    <span>📅 <?php echo date('Y/m/d', strtotime($file['upload_date'])); ?></span>
                                </div>
                            </div>
                            <a href="download.php?id=<?php echo $file['id']; ?>" class="download-btn">⬇️ تحميل</a>
                        </div>
                    <?php endforeach; ?>
                </div>
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