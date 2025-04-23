<?php
session_start();
require_once '../inc/db.php';

// Eğer kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}



// Düzenlenecek yöneticinin ID'sini al
$yonetici_id = $_GET['id'] ?? null;

// Yönetici bilgilerini veritabanından çek
$yonetici = null;
if ($yonetici_id) {
    $sorgu = $conn->prepare("SELECT * FROM yoneticiler WHERE admin_id = :id");
    $sorgu->execute(['id' => $yonetici_id]);
    $yonetici = $sorgu->fetch(PDO::FETCH_ASSOC);

    // Eğer yönetici bulunamazsa veya admin_id 1 ise düzenleme yapılamaz
    if (!$yonetici || $yonetici['admin_id'] == 1) {
        header("Location: yoneticiler.php?mesaj=Bu yönetici düzenlenemez.&mesaj_tur=danger");
        exit;
    }
}

// Form gönderildiğinde güncelleme işlemi yap
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = $_POST['kullanici_adi'];
    $ad_soyad = $_POST['ad_soyad'];
    $eposta = $_POST['eposta'];
    $parola = $_POST['parola'];

    // Parola güncellenmişse hash'le, değilse eski parolayı kullan
    if (!empty($parola)) {
        $parola = password_hash($parola, PASSWORD_DEFAULT);
    } else {
        $parola = $yonetici['parola'];
    }

    // Veritabanında güncelleme yap
    $guncelle_sorgu = $conn->prepare("UPDATE yoneticiler SET kullanici_adi = :kullanici_adi, ad_soyad = :ad_soyad, eposta = :eposta, parola = :parola WHERE admin_id = :id");
    $guncelle_sorgu->execute([
        'kullanici_adi' => $kullanici_adi,
        'ad_soyad' => $ad_soyad,
        'eposta' => $eposta,
        'parola' => $parola,
        'id' => $yonetici_id
    ]);

    // Başarı mesajı ile yöneticiler sayfasına yönlendir
    header("Location: yoneticiler.php?mesaj=Yönetici başarıyla güncellendi.&mesaj_tur=success");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetici Düzenle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .card-shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-edit mr-2"></i>Yönetici Düzenle</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="kullanici_adi">Kullanıcı Adı</label>
                        <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" value="<?= $yonetici['kullanici_adi'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ad_soyad">Ad Soyad</label>
                        <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" value="<?= $yonetici['ad_soyad'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="eposta">E-posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" value="<?= $yonetici['eposta'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="parola">Parola (Değiştirmek istemiyorsanız boş bırakın)</label>
                        <input type="password" class="form-control" id="parola" name="parola">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Güncelle
                    </button>
                    <a href="yoneticiler.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Geri Dön
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>