<?php
session_start();
$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // E-posta formatı: 0@mail.com, 1@mail.com, ... ve şifre: 123
    if (preg_match('/^(\d+)@mail\.com$/', $email, $eslesen) && $password === '123') {
        $uye_id = (int)$eslesen[1];

        $conn = oci_connect('SEFER', 'Sefer123', '//localhost:1521/KDBS');
        if (!$conn) {
            die('Veritabanı bağlantı hatası.');
        }

        $sql = "
            SELECT 
                u.id AS uye_id,
                i.ad,
                i.soyad,
                u.tckn,
                TO_CHAR(u.dogum_tarihi, 'YYYY-MM-DD') AS dogum_tarihi,
                u.cinsiyet,
                a.sokak,
                a.mahalle,
                a.ilce,
                a.il,
                k.koy_adi
            FROM Uyeler u
            JOIN Isimler i ON u.isim_id = i.id
            JOIN Adresler a ON u.adres_id = a.id
            LEFT JOIN Uye_Koy_Iliskisi uki ON u.id = uki.uye_id
            LEFT JOIN Koyler k ON uki.koy_id = k.id
            WHERE u.id = :uye_id
        ";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':uye_id', $uye_id);
        oci_execute($stmt);
        $uye = oci_fetch_assoc($stmt);

        oci_free_statement($stmt);
        oci_close($conn);

        if ($uye) {
            $_SESSION['uye'] = $uye;

            if ($uye_id === 0) {
                header('Location: admin.php'); // admin paneline yönlendir
            } else {
                header('Location: profile.php'); // normal kullanıcı paneli
            }
            exit;
        } else {
            $hata = "Üye bulunamadı.";
        }
    } else {
        $hata = "Geçersiz e-posta veya şifre.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - KDBS</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/logo.png" alt="KDBS Logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Ana Sayfa</a></li>
                <li><a href="index.php#hakkimizda">Hakkımızda</a></li>
                <li><a href="index.php#projelerimiz">Projelerimiz</a></li>
                <li><a href="index.php#bagis">Bağış Yap</a></li>
                <li><a href="index.php#iletisim">İletişim</a></li>
                <li><a href="login.php" class="login-btn active">Giriş Yap</a></li>
            </ul>
        </nav>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<section class="login-section">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h2>Giriş Yap</h2>
                <p>Bağış yapmak ve etkinliklerimize katılmak için giriş yapın</p>
            </div>
            <div class="login-form-container">
                <?php if (!empty($hata)): ?>
                    <div style="color: red; font-weight: bold; margin-bottom: 1rem;">
                        <?= htmlspecialchars($hata) ?>
                    </div>
                <?php endif; ?>
                <form id="login-form" method="POST" action="">
                    <div class="form-group">
                        <label for="email">E-posta</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Şifre</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div>
                        <label>
                            <input type="checkbox" id="remember-me">
                            <span>Beni hatırla</span>
                        </label>
                        <br>
                    </div>
                    <div style="height: 5vw;"></div>
                    <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
                </form>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <a href="index.php">
                    <img src="assets/logo.png" alt="KDBS Logo" width="350px">
                </a>
                <p>Köyümüzün geleceği için el ele</p>
            </div>
            <div class="footer-links">
                <h3>Hızlı Bağlantılar</h3>
                <ul>
                    <li><a href="index.php">Ana Sayfa</a></li>
                    <li><a href="index.php#hakkimizda">Hakkımızda</a></li>
                    <li><a href="index.php#projelerimiz">Projelerimiz</a></li>
                    <li><a href="index.php#bagis">Bağış Yap</a></li>
                    <li><a href="index.php#iletisim">İletişim</a></li>
                </ul>
            </div>
            <div class="footer-newsletter">
                <h3>Bültenimize Abone Olun</h3>
                <p>Gelişmelerden haberdar olmak için e-posta listemize kaydolun</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="E-posta adresiniz">
                    <button type="submit" class="btn btn-primary">Abone Ol</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&reg;2025 KDBS. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>
