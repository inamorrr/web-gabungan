<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Vuln PHP Lab</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php
    // header.php berisi tampilan topbar (user + tombol)
    // Pastikan file header.php ada di folder yang sama.
    if (file_exists(__DIR__ . '/header.php')) {
        require_once __DIR__ . '/header.php';
    } else {
        // fallback sederhana kalau header.php belum ada
        echo '<div style="padding:12px 20px; background:#e5edf9; border-radius:8px; margin:12px;">';
        if (isset($_SESSION['username'])) {
            echo 'Halo, ' . esc($_SESSION['username']) . ' | <a href="logout.php">Logout</a>';
            if (is_admin())
                echo ' | <a href="admin.php">Admin Panel</a>';
        } else {
            echo '<a href="login.php">Login</a>';
        }
        echo '</div>';
    }
    ?>

    <div class="container">
        <h2>Search posts (try SQLi)</h2>

        <form method="GET" style="margin-bottom:16px;">
            <input type="text" name="q" placeholder="kata kunci..."
                value="<?= isset($_GET['q']) ? esc($_GET['q']) : '' ?>">
            <input type="submit" value="Search">
        </form>

        <?php
        // jika ada query pencarian — intentionally vulnerable (untuk lab SQLi)
        if (isset($_GET['q']) && $_GET['q'] !== '') {
            $q = $_GET['q'];
            // VULNERABLE: langsung masukkan input ke query (SQL Injection) — untuk latihan
            $sql = "SELECT * FROM posts WHERE title LIKE '%$q%' OR body LIKE '%$q%'";
            try {
                $stmt = $pdo->query($sql);
            } catch (Exception $e) {
                echo '<p class="small-muted">Query error: ' . esc($e->getMessage()) . '</p>';
                $stmt = $pdo->query("SELECT * FROM posts");
            }
        } else {
            $stmt = $pdo->query("SELECT * FROM posts");
        }

        // tampilkan hasil
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='post'>";
            echo "<h3><a href='post.php?id=" . urlencode($row['id']) . "'>" . esc($row['title']) . "</a></h3>";
            echo "<p>" . esc(substr($row['body'], 0, 200)) . "</p>";
            echo "</div>";
        }
        ?>

        <hr>
        <h3>Upload file (insecure)</h3>
        <p><a href="upload.php">Upload a file</a></p>
    </div>

</body>

</html>