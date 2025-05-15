<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['uye']['UYE_ID'])) {
    header("Location: login.php");
    exit;
}
?>

<!-- profile.php -->
<?php
// Oracle bağlantı bilgileri
$conn = oci_connect('SEFER', 'Sefer123', 'localhost/KDBS');

if (!$conn) {
    $e = oci_error();
    $baglantiMesaji = [
        'tip' => 'danger',
        'icerik' => "Bağlantı hatası: " . $e['message']
    ];
} else {
    $baglantiMesaji = [
        'tip' => 'success',
        'icerik' => "Oracle'a başarıyla bağlanıldı!"
    ];
}
?>

<!-- HTML kodların yer aldığı bölüm burada başlar -->
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - KDBS</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <!-- Sol Sidebar -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <a href="index.php"><img src="assets/logo.png" class="rounded-circle img-fluid mb-3" style="width: 150px;"></a>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Hızlı Menü</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#kisisel-bilgiler" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Kişisel Bilgiler
                        </a>
                        <a href="#aidat-bilgileri" class="list-group-item list-group-item-action">
                            <i class="fas fa-money-bill me-2"></i> Aidat Bilgileri
                        </a>
                        <a href="#bagis-islemleri" class="list-group-item list-group-item-action">
                            <i class="fas fa-hand-holding-heart me-2"></i> Bağış İşlemleri
                        </a>
                        <a href="#etkinlikler" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-alt me-2"></i> Etkinlikler
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ana İçerik -->
            <div class="col-md-9">
                <div class="container mt-4">
                    <?php if (isset($baglantiMesaji)): ?>
                        <div class="alert alert-<?php echo $baglantiMesaji['tip']; ?> alert-dismissible fade show"
                            role="alert">
                            <?php echo $baglantiMesaji['icerik']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Kişisel Bilgiler -->
                <?php
                $uye_id = $_SESSION['uye']['UYE_ID']; // Dinamik olarak oturumdan al

                $conn = oci_connect('SEFER', 'Sefer123', '//localhost:1521/KDBS');
                if (!$conn) {
                    die("Veritabanı bağlantısı sağlanamadı.");
                }

                // Üye bilgilerini getir
                $kisisel = "
                    SELECT
                        u.id AS uye_id,
                        i.ad AS ad,
                        i.soyad AS soyad,
                        u.tckn,
                        TO_CHAR(u.dogum_tarihi, 'YYYY-MM-DD') AS dogum_tarihi,
                        u.cinsiyet,
                        TO_CHAR(u.kayit_tarihi, 'YYYY-MM-DD') AS kayit_tarihi,
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
                $kisiselext = oci_parse($conn, $kisisel);
                oci_bind_by_name($kisiselext, ":uye_id", $uye_id);
                oci_execute($kisiselext);
                $uyeBilgileri = oci_fetch_assoc($kisiselext);
                oci_free_statement($kisiselext);

                if (!$uyeBilgileri) {
                    die("Üye bilgileri bulunamadı.");
                }

                extract($uyeBilgileri);

                ?>
                <div id="kisisel-bilgiler" class="card mb-4">
                    <?php
                    if (isset($_POST['profil_guncelle']) && isset($_SESSION['uye']['UYE_ID'])) {
                        // Bağlantı
                        $conn = oci_connect('SEFER', 'Sefer123', '//localhost:1521/KDBS');
                        if (!$conn) {
                            $e = oci_error();
                            die("Bağlantı hatası: " . htmlentities($e['message']));
                        }

                        // Giriş yapan üyenin ID’si
                        $uye_id = $_SESSION['uye']['UYE_ID'];

                        // Formdan gelen veriler
                        $ad             = $_POST['ad'];
                        $soyad          = $_POST['soyad'];
                        $tckn           = $_POST['tckn'];
                        $dogum_tarihi   = $_POST['dogum_tarihi'];
                        $cinsiyet       = $_POST['cinsiyet'];
                        $koy_adi        = trim($_POST['koy_adi']);
                        $sokak          = $_POST['sokak'];
                        $mahalle        = $_POST['mahalle'];
                        $ilce           = $_POST['ilce'];
                        $il             = $_POST['il'];

                        // isim_id ve adres_id al
                        $sorgu = "SELECT isim_id, adres_id FROM Uyeler WHERE id = :uye_id";
                        $stmt = oci_parse($conn, $sorgu);
                        oci_bind_by_name($stmt, ":uye_id", $uye_id);
                        oci_execute($stmt);
                        $ids = oci_fetch_assoc($stmt);
                        oci_free_statement($stmt);

                        if (!$ids) {
                            die("Üye bulunamadı.");
                        }

                        $isim_id = $ids['ISIM_ID'];
                        $adres_id = $ids['ADRES_ID'];

                        // Isimler güncelle
                        $sql1 = "UPDATE Isimler SET ad = :ad, soyad = :soyad WHERE id = :isim_id";
                        $stmt1 = oci_parse($conn, $sql1);
                        oci_bind_by_name($stmt1, ":ad", $ad);
                        oci_bind_by_name($stmt1, ":soyad", $soyad);
                        oci_bind_by_name($stmt1, ":isim_id", $isim_id);
                        oci_execute($stmt1);

                        // Adresler güncelle
                        $sql2 = "UPDATE Adresler SET sokak = :sokak, mahalle = :mahalle, ilce = :ilce, il = :il WHERE id = :adres_id";
                        $stmt2 = oci_parse($conn, $sql2);
                        oci_bind_by_name($stmt2, ":sokak", $sokak);
                        oci_bind_by_name($stmt2, ":mahalle", $mahalle);
                        oci_bind_by_name($stmt2, ":ilce", $ilce);
                        oci_bind_by_name($stmt2, ":il", $il);
                        oci_bind_by_name($stmt2, ":adres_id", $adres_id);
                        oci_execute($stmt2);

                        // Uyeler güncelle
                        $sql3 = "UPDATE Uyeler SET tckn = :tckn, dogum_tarihi = TO_DATE(:dogum_tarihi, 'YYYY-MM-DD'), cinsiyet = :cinsiyet WHERE id = :uye_id";
                        $stmt3 = oci_parse($conn, $sql3);
                        oci_bind_by_name($stmt3, ":tckn", $tckn);
                        oci_bind_by_name($stmt3, ":dogum_tarihi", $dogum_tarihi);
                        oci_bind_by_name($stmt3, ":cinsiyet", $cinsiyet);
                        oci_bind_by_name($stmt3, ":uye_id", $uye_id);
                        oci_execute($stmt3);

                        // Köy kontrolü
                        $sql4 = "SELECT id FROM Koyler WHERE LOWER(koy_adi) = LOWER(:koy_adi)";
                        $stmt4 = oci_parse($conn, $sql4);
                        oci_bind_by_name($stmt4, ":koy_adi", $koy_adi);
                        oci_execute($stmt4);
                        $koy = oci_fetch_assoc($stmt4);
                        oci_free_statement($stmt4);

                        if ($koy) {
                            $koy_id = $koy['ID'];
                        } else {
                            $get_koy_seq = oci_parse($conn, "SELECT NVL(MAX(id), 0) + 1 AS yeni_id FROM Koyler");
                            oci_execute($get_koy_seq);
                            $row = oci_fetch_assoc($get_koy_seq);
                            $koy_id = $row['YENI_ID'];
                            oci_free_statement($get_koy_seq);

                            $ekle_koy = oci_parse($conn, "INSERT INTO Koyler (id, koy_adi, il, ilce) VALUES (:id, :koy_adi, :il, :ilce)");
                            oci_bind_by_name($ekle_koy, ":id", $koy_id);
                            oci_bind_by_name($ekle_koy, ":koy_adi", $koy_adi);
                            oci_bind_by_name($ekle_koy, ":il", $il);
                            oci_bind_by_name($ekle_koy, ":ilce", $ilce);
                            oci_execute($ekle_koy);
                            oci_free_statement($ekle_koy);
                        }

                        // Uye_Koy_Iliskisi güncelle/ekle
                        $kontrol = oci_parse($conn, "SELECT * FROM Uye_Koy_Iliskisi WHERE uye_id = :uye_id");
                        oci_bind_by_name($kontrol, ":uye_id", $uye_id);
                        oci_execute($kontrol);
                        $varmi = oci_fetch_assoc($kontrol);
                        oci_free_statement($kontrol);

                        if ($varmi) {
                            $guncelle = oci_parse($conn, "UPDATE Uye_Koy_Iliskisi SET koy_id = :koy_id WHERE uye_id = :uye_id");
                        } else {
                            $guncelle = oci_parse($conn, "INSERT INTO Uye_Koy_Iliskisi (uye_id, koy_id, kayit_tarihi) VALUES (:uye_id, :koy_id, SYSDATE)");
                        }
                        oci_bind_by_name($guncelle, ":uye_id", $uye_id);
                        oci_bind_by_name($guncelle, ":koy_id", $koy_id);
                        oci_execute($guncelle);
                        oci_free_statement($guncelle);

                        // Temizlik
                        oci_free_statement($stmt1);
                        oci_free_statement($stmt2);
                        oci_free_statement($stmt3);

                        echo "<div class='alert alert-success'>Profil başarıyla güncellendi.</div>";
                    }
                    ?>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i> Kişisel Bilgiler</h5>
                        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#kisiselBilgilerForm">
                            <i class="fas fa-edit me-1"></i> Düzenle
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="collapse" id="kisiselBilgilerForm">
                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Ad:</label>
                                        <input type="text" name="ad" class="form-control"
                                            value="<?php echo $AD ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Soyad:</label>
                                        <input type="text" name="soyad" class="form-control"
                                            value="<?php echo $SOYAD ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">TCKN:</label>
                                        <input type="text" name="tckn" class="form-control"
                                            value="<?php echo $TCKN ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Doğum Tarihi:</label>
                                        <input type="text" name="dogum_tarihi" class="form-control"
                                            value="<?php echo $DOGUM_TARIHI ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Cinsiyet:</label>
                                        <input type="text" name="cinsiyet" class="form-control"
                                            value="<?php echo $CINSIYET ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Köy:</label>
                                        <input type="text" name="koy_adi" class="form-control"
                                            value="<?php echo $KOY_ADI ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sokak:</label>
                                        <input type="text" name="sokak" class="form-control"
                                            value="<?php echo $SOKAK ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mahalle:</label>
                                        <input type="text" name="mahalle" class="form-control"
                                            value="<?php echo $MAHALLE ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">İlçe:</label>
                                        <input type="text" name="ilce" class="form-control"
                                            value="<?php echo $ILCE ?>" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">İl:</label>
                                        <input type="text" name="il" class="form-control"
                                            value="<?php echo $IL ?>" required>
                                    </div>
                                </div>

                                <button type="submit" name="profil_guncelle" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Kaydet
                                </button>
                            </form>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Ad Soyad:</strong>
                                    <?php echo "$AD $SOYAD" ?>
                                </p>
                                <p><strong>TCKN:</strong>
                                    <?php echo $TCKN ?>
                                </p>
                                <p><strong>Doğum Tarihi:</strong>
                                    <?php echo $DOGUM_TARIHI ?>
                                </p>
                                <p><strong>Cinsiyet:</strong>
                                    <?php echo $CINSIYET ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Köy:</strong>
                                    <?php echo $KOY_ADI ?>
                                </p>
                                <p><strong>Adres:</strong>
                                    <?php echo "$MAHALLE $SOKAK" ?>
                                </p>
                                <p><strong>İlçe/İl:</strong>
                                    <?php echo "$ILCE, $IL" ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="uye-ekle" class="card mb-4">
                    <?php
                    if (isset($_POST['uye_ekle'])) {
                        $conn = oci_connect('SEFER', 'Sefer123', '//localhost:1521/KDBS');
                        if (!$conn) {
                            $e = oci_error();
                            die("Bağlantı hatası: " . htmlentities($e['message']));
                        }

                        // Form verileri
                        $ad = trim($_POST['ad']);
                        $soyad = trim($_POST['soyad']);
                        $tckn = trim($_POST['tckn']);
                        $dogum_tarihi = $_POST['dogum_tarihi'];
                        $cinsiyet = $_POST['cinsiyet'];
                        $sokak = trim($_POST['sokak']);
                        $mahalle = trim($_POST['mahalle']);
                        $ilce = trim($_POST['ilce']);
                        $il = trim($_POST['il']);
                        $koy_adi = trim($_POST['koy_adi']);

                        // 1. isim_id üret ve ekle
                        $isim_id_sorgu = oci_parse($conn, "SELECT NVL(MAX(id), 0) + 1 AS yeni_id FROM Isimler");
                        oci_execute($isim_id_sorgu);
                        $isim_row = oci_fetch_assoc($isim_id_sorgu);
                        $isim_id = $isim_row['YENI_ID'];
                        oci_free_statement($isim_id_sorgu);

                        $isim_ekle = oci_parse($conn, "INSERT INTO Isimler (id, ad, soyad) VALUES (:id, :ad, :soyad)");
                        oci_bind_by_name($isim_ekle, ":id", $isim_id);
                        oci_bind_by_name($isim_ekle, ":ad", $ad);
                        oci_bind_by_name($isim_ekle, ":soyad", $soyad);
                        oci_execute($isim_ekle);
                        oci_free_statement($isim_ekle);

                        // 2. adres_id üret ve ekle
                        $adres_id_sorgu = oci_parse($conn, "SELECT NVL(MAX(id), 0) + 1 AS yeni_id FROM Adresler");
                        oci_execute($adres_id_sorgu);
                        $adres_row = oci_fetch_assoc($adres_id_sorgu);
                        $adres_id = $adres_row['YENI_ID'];
                        oci_free_statement($adres_id_sorgu);

                        $adres_ekle = oci_parse($conn, "
                            INSERT INTO Adresler (id, sokak, mahalle, ilce, il)
                            VALUES (:id, :sokak, :mahalle, :ilce, :il)
                        ");
                        oci_bind_by_name($adres_ekle, ":id", $adres_id);
                        oci_bind_by_name($adres_ekle, ":sokak", $sokak);
                        oci_bind_by_name($adres_ekle, ":mahalle", $mahalle);
                        oci_bind_by_name($adres_ekle, ":ilce", $ilce);
                        oci_bind_by_name($adres_ekle, ":il", $il);
                        oci_execute($adres_ekle);
                        oci_free_statement($adres_ekle);

                        // 3. Üye ekle
                        $uye_id_sorgu = oci_parse($conn, "SELECT NVL(MAX(id), 0) + 1 AS yeni_id FROM Uyeler");
                        oci_execute($uye_id_sorgu);
                        $uye_row = oci_fetch_assoc($uye_id_sorgu);
                        $uye_id = $uye_row['YENI_ID'];
                        oci_free_statement($uye_id_sorgu);

                        $uye_ekle = oci_parse($conn, "
                            INSERT INTO Uyeler (id, isim_id, adres_id, tckn, dogum_tarihi, cinsiyet, kayit_tarihi)
                            VALUES (:id, :isim_id, :adres_id, :tckn, TO_DATE(:dogum_tarihi, 'YYYY-MM-DD'), :cinsiyet, SYSDATE)
                        ");
                        oci_bind_by_name($uye_ekle, ":id", $uye_id);
                        oci_bind_by_name($uye_ekle, ":isim_id", $isim_id);
                        oci_bind_by_name($uye_ekle, ":adres_id", $adres_id);
                        oci_bind_by_name($uye_ekle, ":tckn", $tckn);
                        oci_bind_by_name($uye_ekle, ":dogum_tarihi", $dogum_tarihi);
                        oci_bind_by_name($uye_ekle, ":cinsiyet", $cinsiyet);
                        oci_execute($uye_ekle);
                        oci_free_statement($uye_ekle);

                        // 4. Köy kontrol ve ekle
                        $koy_sorgu = oci_parse($conn, "SELECT id FROM Koyler WHERE LOWER(koy_adi) = LOWER(:koy_adi)");
                        oci_bind_by_name($koy_sorgu, ":koy_adi", $koy_adi);
                        oci_execute($koy_sorgu);
                        $koy = oci_fetch_assoc($koy_sorgu);
                        oci_free_statement($koy_sorgu);

                        if ($koy) {
                            $koy_id = $koy['ID'];
                        } else {
                            $get_koy_seq = oci_parse($conn, "SELECT NVL(MAX(id), 0) + 1 AS yeni_id FROM Koyler");
                            oci_execute($get_koy_seq);
                            $row = oci_fetch_assoc($get_koy_seq);
                            $koy_id = $row['YENI_ID'];
                            oci_free_statement($get_koy_seq);

                            $ekle_koy = oci_parse($conn, "
                                INSERT INTO Koyler (id, koy_adi, il, ilce)
                                VALUES (:id, :koy_adi, :il, :ilce)
                            ");
                            oci_bind_by_name($ekle_koy, ":id", $koy_id);
                            oci_bind_by_name($ekle_koy, ":koy_adi", $koy_adi);
                            oci_bind_by_name($ekle_koy, ":il", $il);
                            oci_bind_by_name($ekle_koy, ":ilce", $ilce);
                            oci_execute($ekle_koy);
                            oci_free_statement($ekle_koy);
                        }

                        // 5. Üye - Köy ilişkisi ekle
                        $ekle_iliski = oci_parse($conn, "
                            INSERT INTO Uye_Koy_Iliskisi (uye_id, koy_id, kayit_tarihi)
                            VALUES (:uye_id, :koy_id, SYSDATE)
                        ");
                        oci_bind_by_name($ekle_iliski, ":uye_id", $uye_id);
                        oci_bind_by_name($ekle_iliski, ":koy_id", $koy_id);
                        oci_execute($ekle_iliski);
                        oci_free_statement($ekle_iliski);

                        echo "<div class='alert alert-success'>Yeni üye başarıyla eklendi.</div>";
                    }
                    ?>

                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i> Yeni Üye Ekle</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ad:</label>
                                <input type="text" name="ad" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Soyad:</label>
                                <input type="text" name="soyad" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TCKN:</label>
                                <input type="text" name="tckn" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Doğum Tarihi:</label>
                                <input type="date" name="dogum_tarihi" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cinsiyet:</label>
                                <input type="text" name="cinsiyet" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sokak:</label>
                                <input type="text" name="sokak" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mahalle:</label>
                                <input type="text" name="mahalle" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">İlçe:</label>
                                <input type="text" name="ilce" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">İl:</label>
                                <input type="text" name="il" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Köy:</label>
                                <input type="text" name="koy_adi" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="uye_ekle" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i> Üye Ekle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Aidat Bilgileri -->
                <div id="aidat-bilgileri" class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-money-bill me-2"></i> Aidat Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            // Session'dan giriş yapan üyenin id'sini al
                            if (!isset($_SESSION['uye']['UYE_ID'])) {
                                die("Oturum bulunamadı.");
                            }
                            $uye_id = $_SESSION['uye']['UYE_ID'];           

                            $aidat = "
                                SELECT 
                                    A.id AS aidat_id,
                                    A.donem,
                                    A.tutar,
                                    A.odeme_durumu,
                                    TO_CHAR(AOD.odeme_tarihi, 'DD.MM.YYYY') AS odeme_tarihi,
                                    OY.yontem_adi
                                FROM 
                                    Aidatlar A
                                LEFT JOIN 
                                    Aidat_Odeme_Detay AOD ON A.id = AOD.aidat_id
                                LEFT JOIN 
                                    Odeme_Yontemi OY ON AOD.yontem_id = OY.id
                                WHERE 
                                    A.uye_id = :uye_id
                                ORDER BY 
                                    A.donem DESC
                            ";

                            $aidatext = oci_parse($conn, $aidat);
                            oci_bind_by_name($aidatext, ":uye_id", $uye_id);
                            oci_execute($aidatext);
                            ?>

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Aidat ID</th>
                                        <th>Dönem</th>
                                        <th>Tutar</th>
                                        <th>Ödeme Durumu</th>
                                        <th>Ödeme Yöntemi</th>
                                        <th>Ödeme Tarihi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = oci_fetch_assoc($aidatext)): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($row['AIDAT_ID']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['DONEM']) ?>
                                            </td>
                                            <td>
                                                <?= number_format($row['TUTAR'], 2, ',', '.') ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['ODEME_DURUMU']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['YONTEM_ADI'] ?? '') ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['ODEME_TARIHI'] ?? '') ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Bağış İşlemleri -->
                <div id="bagis-islemleri" class="card mb-4">
                    <?php
                    if (isset($_POST['bagis_ekle'])) {
                        // Giriş yapmış üyenin ID'sini al
                        if (!isset($_SESSION['uye']['UYE_ID'])) {
                            die("Oturum bulunamadı.");
                        }
                        $uye_id = $_SESSION['uye']['UYE_ID'];

                        // Oracle bağlantısı
                        $conn = oci_connect('SEFER', 'Sefer123', '//localhost:1521/KDBS');
                        if (!$conn) {
                            $e = oci_error();
                            die("Bağlantı hatası: " . htmlentities($e['message']));
                        }

                        // Form verileri
                        $tutar = $_POST['tutar'];
                        $yontem_id = $_POST['yontem_id'];

                        // 1. Yeni bağış ID'si al
                        $stmt1 = oci_parse($conn, "SELECT NVL(MAX(id), 0) + 1 AS yeni_id FROM Bagislar");
                        oci_execute($stmt1);
                        $row = oci_fetch_assoc($stmt1);
                        $bagis_id = $row['YENI_ID'];
                        oci_free_statement($stmt1);

                        // 2. Bagislar tablosuna ekle
                        $sql2 = "INSERT INTO Bagislar (id, uye_id, tutar, bagis_tarihi)
                                VALUES (:id, :uye_id, :tutar, SYSDATE)";
                        $stmt2 = oci_parse($conn, $sql2);
                        oci_bind_by_name($stmt2, ":id", $bagis_id);
                        oci_bind_by_name($stmt2, ":uye_id", $uye_id);
                        oci_bind_by_name($stmt2, ":tutar", $tutar);
                        oci_execute($stmt2);
                        oci_free_statement($stmt2);

                        // Ödeme detayı zaten var mı kontrol et (önlem amaçlı)
                        $kontrol = oci_parse($conn, "
                            SELECT COUNT(*) AS SAYI FROM Bagis_Odeme_Detay 
                            WHERE bagis_id = :bagis_id AND yontem_id = :yontem_id
                        ");
                        oci_bind_by_name($kontrol, ":bagis_id", $bagis_id);
                        oci_bind_by_name($kontrol, ":yontem_id", $yontem_id);
                        oci_execute($kontrol);
                        $row = oci_fetch_assoc($kontrol);
                        oci_free_statement($kontrol);

                        if ($row['SAYI'] == 0) {
                            $sql3 = "INSERT INTO Bagis_Odeme_Detay (bagis_id, yontem_id, odeme_tarihi)
                                    VALUES (:bagis_id, :yontem_id, SYSDATE)";
                            $stmt3 = oci_parse($conn, $sql3);
                            oci_bind_by_name($stmt3, ":bagis_id", $bagis_id);
                            oci_bind_by_name($stmt3, ":yontem_id", $yontem_id);
                            oci_execute($stmt3);
                            oci_free_statement($stmt3);
                        }

                        echo "<div class='alert alert-success'>Bağış başarıyla kaydedildi.</div>";
                    }
                    ?>

                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-hand-holding-heart me-2"></i> Bağış Yap</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Bağış Tutarı:</label>
                                <div class="input-group">
                                    <input type="number" name="tutar" class="form-control" step="0.01" required>
                                    <span class="input-group-text">TL</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ödeme Yöntemi:</label>
                                <select name="yontem_id" class="form-select" required>
                                    <option value="1">Nakit</option>
                                    <option value="2">Kredi Kartı</option>
                                    <option value="3">Havale/EFT</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="bagis_ekle" class="btn btn-success">
                                    <i class="fas fa-heart me-1"></i> Bağış Yap
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bağış Geçmişi -->
                <div class="card mb-4">
                    <?php
                    if (isset($_GET['bagis_sil_id'])) {
                        // Giriş yapmış kullanıcıdan üye ID'yi al
                        if (!isset($_SESSION['uye']['UYE_ID'])) {
                            die("Oturum bulunamadı.");
                        }
                        $uye_id = $_SESSION['uye']['UYE_ID'];

                        $bagis_id = (int)$_GET['bagis_sil_id'];

                        // Veritabanı bağlantısı
                        $conn = oci_connect('SEFER', 'Sefer123', '//localhost:1521/KDBS');
                        if (!$conn) {
                            $e = oci_error();
                            die("Bağlantı hatası: " . htmlentities($e['message']));
                        }

                        // Silmeye çalışılan bağış, gerçekten bu üyeye mi ait?
                        $kontrol = oci_parse($conn, "
                            SELECT COUNT(*) AS SAYI FROM Bagislar
                            WHERE id = :bagis_id AND uye_id = :uye_id
                        ");
                        oci_bind_by_name($kontrol, ":bagis_id", $bagis_id);
                        oci_bind_by_name($kontrol, ":uye_id", $uye_id);
                        oci_execute($kontrol);
                        $row = oci_fetch_assoc($kontrol);
                        oci_free_statement($kontrol);

                        if ($row['SAYI'] == 0) {
                            oci_close($conn);
                            die("Bu bağış size ait değil veya bulunamadı.");
                        }

                        // 1. Ödeme detayını sil
                        $stmt1 = oci_parse($conn, "DELETE FROM Bagis_Odeme_Detay WHERE bagis_id = :bagis_id");
                        oci_bind_by_name($stmt1, ":bagis_id", $bagis_id);
                        oci_execute($stmt1);
                        oci_free_statement($stmt1);

                        // 2. Bağışı sil
                        $stmt2 = oci_parse($conn, "DELETE FROM Bagislar WHERE id = :bagis_id");
                        oci_bind_by_name($stmt2, ":bagis_id", $bagis_id);
                        oci_execute($stmt2);
                        oci_free_statement($stmt2);

                        oci_close($conn);

                        echo "<div class='alert alert-success'>Bağış başarıyla silindi.</div>";
                    }
                    ?>
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> Bağış Geçmişi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            // Oturumdan giriş yapan kullanıcının üye ID'sini al
                            if (!isset($_SESSION['uye']['UYE_ID'])) {
                                die("Oturum bulunamadı.");
                            }
                            $uye_id = $_SESSION['uye']['UYE_ID'];

                            // Sorgu
                            $bagis = "
                                SELECT 
                                    B.id AS bagis_id,
                                    B.tutar,
                                    TO_CHAR(B.bagis_tarihi, 'DD.MM.YYYY') AS bagis_tarihi,
                                    OY.yontem_adi,
                                    TO_CHAR(BOD.odeme_tarihi, 'DD.MM.YYYY') AS odeme_tarihi
                                FROM 
                                    Bagislar B
                                LEFT JOIN 
                                    Bagis_Odeme_Detay BOD ON B.id = BOD.bagis_id
                                LEFT JOIN 
                                    Odeme_Yontemi OY ON BOD.yontem_id = OY.id
                                WHERE 
                                    B.uye_id = :uye_id
                                ORDER BY B.bagis_tarihi DESC
                            ";

                            // Sorguyu hazırla ve çalıştır
                            $bagisext = oci_parse($conn, $bagis);
                            oci_bind_by_name($bagisext, ":uye_id", $uye_id);
                            oci_execute($bagisext);
                            ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bağış Numarası</th>
                                        <th>Bağış Tutarı</th>
                                        <th>Bağış Tarihi</th>
                                        <th>Ödeme Yöntemi</th>
                                        <th>Ödeme Tarihi</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = oci_fetch_assoc($bagisext)): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($row['BAGIS_ID']) ?>
                                            </td>
                                            <td>
                                                <?= number_format($row['TUTAR'], 2, ',', '.') ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['BAGIS_TARIHI']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['YONTEM_ADI'] ?? '') ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['ODEME_TARIHI'] ?? '') ?>
                                            </td>
                                            <td>
                                                <a href="?bagis_sil_id=<?= $row['BAGIS_ID'] ?>"
                                                    onclick="return confirm('Bu bağışı silmek istediğinize emin misiniz?')"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Etkinlik Katılımları -->
                <div id="etkinlikler" class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Etkinlik Katılımları</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            // Oturumdan giriş yapan kullanıcının üye ID'sini al
                            if (!isset($_SESSION['uye']['UYE_ID'])) {
                                die("Oturum bulunamadı.");
                            }
                            $uye_id = $_SESSION['uye']['UYE_ID'];

                            // Sorgu
                            $etkinlik = "
                                SELECT 
                                    E.id AS etkinlik_id,
                                    E.etkinlik_adi,
                                    TO_CHAR(E.baslangic_tarihi, 'DD.MM.YYYY') AS baslangic_tarihi,
                                    TO_CHAR(E.bitis_tarihi, 'DD.MM.YYYY') AS bitis_tarihi,
                                    E.mekan,
                                    TO_CHAR(UEK.kayit_tarihi, 'DD.MM.YYYY') AS kayit_tarihi
                                FROM 
                                    Etkinlikler E
                                LEFT JOIN 
                                    Uye_Etkinlik_Katilim UEK 
                                    ON E.id = UEK.etkinlik_id AND UEK.uye_id = :uye_id
                                ORDER BY 
                                    E.baslangic_tarihi DESC
                            ";

                            // Sorguyu hazırla ve çalıştır
                            $etkinlikext = oci_parse($conn, $etkinlik);
                            oci_bind_by_name($etkinlikext, ":uye_id", $uye_id);
                            oci_execute($etkinlikext);
                            ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Etkinlik ID</th>
                                        <th>Etkinlik Adı</th>
                                        <th>Başlangıç Tarihi</th>
                                        <th>Bitiş Tarihi</th>
                                        <th>Mekan</th>
                                        <th>Kayıt Tarihi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = oci_fetch_assoc($etkinlikext)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['ETKINLIK_ID']) ?></td>
                                            <td><?= htmlspecialchars($row['ETKINLIK_ADI']) ?></td>
                                            <td><?= htmlspecialchars($row['BASLANGIC_TARIHI']) ?></td>
                                            <td><?= htmlspecialchars($row['BITIS_TARIHI']) ?></td>
                                            <td><?= htmlspecialchars($row['MEKAN']) ?></td>
                                            <td><?= $row['KAYIT_TARIHI'] ? htmlspecialchars($row['KAYIT_TARIHI']) : '<span style="color:gray;">Katılmadı</span>' ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form doğrulama
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault()
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                })
            })
        })
    </script>
</body>

</html>