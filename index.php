<?php session_start(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KDBS - Yönetim Sistemi</title>
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
                <li><a href="index.php" class="active">Ana Sayfa</a></li>
                <li><a href="#hakkimizda">Hakkımızda</a></li>
                <li><a href="#projelerimiz">Projelerimiz</a></li>
                <li><a href="#bagis">Bağış Yap</a></li>
                <li><a href="#iletisim">İletişim</a></li>
                <?php if (isset($_SESSION['uye'])): ?>
                    <li>
                        <a href="profile.php" class="login-btn">
                            <?= htmlspecialchars($_SESSION['uye']['AD'] . ' ' . $_SESSION['uye']['SOYAD']) ?>
                            <i class="fas fa-user-circle"></i>
                        </a>
                    </li>
                    <li><a href="logout.php" class="login-btn">Çıkış Yap</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="login-btn">Giriş Yap</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Köyümüzün Geleceğine Katkıda Bulunun</h1>
                <p>Köy Derneği olarak köyümüzün kalkınması ve gelişmesi için çalışıyoruz.</p>
            </div>
        </div>
    </section>

    <section id="hakkimizda" class="about">
        <div class="container">
            <div class="section-header">
                <h2>Hakkımızda</h2>
                <p>Köyümüzün değerlerini korumak ve geliştirmek için çalışıyoruz</p>
            </div>
            <div class="about-content">
                <div class="about-text">
                    <p>KDBS, 2025 yılında köyümüzün gelişimi ve kalkınması amacıyla kurulmuştur. Derneğimiz, köy sakinlerinin yaşam kalitesini artırmak, köyün altyapı sorunlarını çözmek ve gelecek nesillere daha güzel bir köy bırakmak için çalışmaktadır.</p>
                    <p>Amacımız, köyümüzün doğal güzelliklerini koruyarak, sürdürülebilir kalkınma modelleri ile köyümüzü modern imkanlara kavuşturmaktır.</p>
                </div>
                <div class="stats">
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p>Üye</p>
                    </div>
                    <div class="stat-item">
                        <h3>25+</h3>
                        <p>Tamamlanan Proje</p>
                    </div>
                    <div class="stat-item">
                        <h3>1M+</h3>
                        <p>Toplanan Bağış</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="projelerimiz" class="projects">
        <div class="container">
            <div class="section-header">
                <h2>Projelerimiz</h2>
                <p>Köyümüzün gelişimi için yürüttüğümüz projeler</p>
            </div>
            <div class="project-cards">
                <div class="project-card">
                    <div class="project-img">
                        <img src="https://images.unsplash.com/photo-1518791841217-8f162f1e1131?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60" alt="Köy Okulu Yenileme Projesi">
                    </div>
                    <div class="project-content">
                        <h3>Köy Okulu Yenileme Projesi</h3>
                        <p>Köyümüzdeki ilkokulun tadilatı ve modern eğitim araçlarıyla donatılması.</p>
                        <div class="progress">
                            <div class="progress-bar" style="width: 75%;">75%</div>
                        </div>
                        <a href="#" class="btn btn-secondary">Detaylar</a>
                    </div>
                </div>
                <div class="project-card">
                    <div class="project-img">
                        <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60" alt="İçme Suyu Altyapı Projesi">
                    </div>
                    <div class="project-content">
                        <h3>İçme Suyu Altyapı Projesi</h3>
                        <p>Köyümüzün içme suyu şebekesinin yenilenmesi ve su arıtma tesisi kurulumu.</p>
                        <div class="progress">
                            <div class="progress-bar" style="width: 45%;">45%</div>
                        </div>
                        <a href="#" class="btn btn-secondary">Detaylar</a>
                    </div>
                </div>
                <div class="project-card">
                    <div class="project-img">
                        <img src="https://images.unsplash.com/photo-1470770841072-f978cf4d019e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60" alt="Köy Meydanı Düzenleme">
                    </div>
                    <div class="project-content">
                        <h3>Köy Meydanı Düzenleme</h3>
                        <p>Köy meydanının yeniden düzenlenmesi ve sosyal alan oluşturulması.</p>
                        <div class="progress">
                            <div class="progress-bar" style="width: 90%;">90%</div>
                        </div>
                        <a href="#" class="btn btn-secondary">Detaylar</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="iletisim" class="about">
        <div class="container">
            <div class="section-header">
                <h2>İletişim</h2>
                <p>Bize ulaşın, sorularınızı cevaplayalım</p>
            </div>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Adres</h3>
                            <p>Köy Muhtarlık Binası, No:1<br>34000 İstanbul</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Telefon</h3>
                            <p>+90 (216) 123 45 67</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>E-posta</h3>
                            <p>info@kdbs.org</p>
                        </div>
                    </div>
                    <div class="social-media">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="contact-form">
                    <form id="contact-form">
                        <div class="form-group">
                            <label for="contact-name">Ad Soyad</label>
                            <input type="text" id="contact-name" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-email">E-posta</label>
                            <input type="email" id="contact-email" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-subject">Konu</label>
                            <input type="text" id="contact-subject" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-message">Mesaj</label>
                            <textarea id="contact-message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gönder</button>
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