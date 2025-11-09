<?php
require 'config.php';


$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $f = $_FILES['file'];
    // pengecekan file yang lemah (hanya cek ekstensi)
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if ($f['size'] > 2 * 1024 * 1024) {
        $msg = "File terlalu besar.";
    } else if (!in_array($ext, ['jpg', 'png', 'gif', 'txt', 'php'])) {
        $msg = "Ext tidak diijinkan.";
    } else {
        // simpan file DIRECT di folder uploads dengan nama asli (vulnerable to RCE if php allowed)
        $target = __DIR__ . '/uploads/' . basename($f['name']);
        if (move_uploaded_file($f['tmp_name'], $target)) {
            $msg = "Upload sukses: " . htmlspecialchars($f['name']);
        } else {
            $msg = "Upload gagal.";
        }
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Upload</title>
</head>

<body>
    <a href="index.php">&laquo; Back</a>
    <h1>Upload (insecure)</h1>
    <p><?= esc($msg) ?></p>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file"><br><br>
        <button type="submit">Upload</button>
    </form>
    <h3>Uploaded files</h3>
    <ul>
        <?php
        $files = glob(__DIR__ . '/uploads/*');
        foreach ($files as $file) {
            $name = basename($file);
            echo "<li><a href='uploads/" . urlencode($name) . "'>" . esc($name) . "</a></li>";
        }
        ?>
    </ul>
</body>

</html>