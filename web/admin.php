<?php
require 'config.php';


// BROKEN ACCESS CONTROL: cek role mudah dimanipulasi; bisa bypass jika cookie/session diset manual
if (!isset($_SESSION['username']) || !is_admin()) {
    echo "<h2>Access Denied</h2>";
    echo "<p>Anda bukan admin. Namun coba manipulasi session untuk akses (lab).</p>";
    echo "<p><a href='index.php'>Back</a></p>";
    exit;
}


// admin view: lihat semua users & comments
$users = $pdo->query("SELECT id, username, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
$comments = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin Panel</title>
</head>

<body>
    <h1>Admin Panel</h1>
    <p><a href="index.php">Back</a></p>
    <h2>Users</h2>
    <ul>
        <?php foreach ($users as $u): ?>
            <li><?= esc($u['username']) ?> (<?= esc($u['role']) ?>)</li>
        <?php endforeach; ?>
    </ul>


    <h2>Comments</h2>
    <?php foreach ($comments as $c): ?>
        <div>
            <strong><?= esc($c['author']) ?></strong> on post <?= esc($c['post_id']) ?>:<br>
            <div><?= $c['content'] ?></div>
        </div>
    <?php endforeach; ?>
</body>

</html>