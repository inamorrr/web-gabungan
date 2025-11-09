<?php
require_once __DIR__ . '/config.php';

// Ambil parameter id dari URL (casting ke int untuk mencegah error sintaks SQL)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo "Post tidak ditemukan (id tidak valid).";
    exit;
}

// Ambil post (prepared statement lebih aman, tetapi kita tetap menampilkan post title/body dengan esc())
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Post tidak ditemukan.";
    exit;
}

// handle comment submission (vulnerable: tidak divalidasi/sanitasi — intentional untuk lab)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author = trim($_POST['author'] ?? 'Anon');
    $content = $_POST['content'] ?? '';

    // kita gunakan prepared statement untuk insert agar tidak menyebabkan error SQL,
    // tapi kita TIDAK melakukan htmlspecialchars pada content supaya stored XSS dapat dipraktikkan.
    $ins = $pdo->prepare("INSERT INTO comments (post_id, author, content) VALUES (?, ?, ?)");
    $ins->execute([$id, $author, $content]);

    // Redirect kembali ke halaman post (prevent form re-submit)
    header("Location: post.php?id=" . $id);
    exit;
}

// Ambil komentar untuk post ini
$comments = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
$comments->execute([$id]);
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= esc($post['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <a href="index.php">&laquo; Back</a>
        <h1><?= esc($post['title']) ?></h1>
        <p><?= esc($post['body']) ?></p>

        <h3>Tinggalkan komentar</h3>
        <form method="POST">
            <input name="author" placeholder="nama"><br>
            <textarea name="content" placeholder="komentar..."></textarea><br>
            <button type="submit">Kirim</button>
        </form>

        <h3>Komentar</h3>
        <?php while ($c = $comments->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="comment">
                <strong><?= esc($c['author']) ?></strong> at <?= $c['created_at'] ?><br>
                <!-- VULNERABLE: komentar ditampilkan tanpa sanitasi sehingga Stored XSS (intentionally) -->
                <p><?= $c['content'] ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>

</html>