<?php
require_once __DIR__ . '/config.php';

// MODE: false = intentionally vulnerable (SQL injection practice)
//       true  = secure mode (prepared statements + password hashing)
$secure_mode = false;

$msg = '';
$username = '';

// jika secure_mode=true dan ingin password hashed, gunakan password_hash saat membuat akun.
// (note: DB sample menggunakan plain text 'admin123' — jika pakai secure_mode=true, update DB atau set password_verify accordingly)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $msg = 'Masukkan username dan password.';
    } else {
        if ($secure_mode) {
            // SECURE: prepared statement + password_verify (assumes passwords hashed in DB)
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($password, $row['password'])) {
                // login sukses
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                header('Location: index.php');
                exit;
            } else {
                $msg = 'Login gagal. Cek kredensial.';
            }
        } else {
            // VULNERABLE: intentionally building query with direct input (for SQLi practice)
            // Keep this only for lab; do NOT use on production.
            $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";
            try {
                $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $row = false;
            }

            if ($row) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                header('Location: index.php');
                exit;
            } else {
                $msg = 'Login gagal.';
            }
        }
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Login — Vuln PHP Lab</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* tambahan kecil spesifik login */
        .login-card {
            max-width: 420px;
            margin: 30px auto;
            padding: 18px;
            border-radius: 12px;
            background: linear-gradient(180deg, #fff 0, #f6fbff 100%);
            box-shadow: 0 10px 30px rgba(2, 30, 58, 0.06);
        }

        .login-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .login-header h2 {
            margin: 0;
            color: #023e8a;
        }

        .small {
            font-size: 0.9rem;
            color: #6b7280;
        }

        .error {
            background: #fff3f3;
            border: 1px solid #ffd2d2;
            padding: 8px 10px;
            border-radius: 8px;
            color: #8b0000;
            margin-bottom: 12px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-top: 6px;
            color: #044a6f;
        }

        .hint {
            margin-top: 8px;
            font-size: 0.9rem;
            color: #075985;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <h2>Masuk</h2>
                <div style="flex:1"></div>
                <div class="small">Vuln PHP Lab</div>
            </div>

            <?php if ($msg): ?>
                <div class="error"><?= esc($msg) ?></div>
            <?php endif; ?>

            <form method="POST" autocomplete="off" novalidate>
                <label for="username">Username</label>
                <input id="username" name="username" type="text" value="<?= esc($username) ?>"
                    placeholder="masukkan username" required>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="masukkan password" required>

                <div style="display:flex; gap:8px; align-items:center; margin-top:10px;">
                    <input type="submit" value="Login">
                    <a href="index.php" style="margin-left:auto; align-self:center; color:#064ea2;">Kembali</a>
                </div>

                <p class="hint">
                    Sampel akun: <strong>admin</strong> / <strong>admin123</strong> (DB contoh).<br>
                    Mode saat ini: <strong><?= $secure_mode ? 'SECURE' : 'VULNERABLE (SQLi allowed)' ?></strong>
                </p>
            </form>

            <?php if (!$secure_mode): ?>
                <p class="small">Catatan: mode <em>vulnerable</em> sengaja mengizinkan SQL Injection untuk latihan. Ubah
                    <code>$secure_mode = true</code> di file <code>login.php</code> untuk mengaktifkan mode aman.
                </p>
            <?php else: ?>
                <p class="small">Mode aman: prepared statements + password hashing. Pastikan password di DB sudah di-hash
                    (password_hash) sebelum login.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>