<?php
session_start();
require_once './inc/db.php'; // PDO bağlantısı için db.php dosyası

// Eğer kullanıcı zaten giriş yapmışsa direkt dashboard'a yönlendir
if (isset($_SESSION['admin_giris'])) {
    header("Location: ./yonetici/dashboard.php");
    exit;
}

$hata_mesaji = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $parola = trim($_POST['parola']);

    if (empty($kullanici_adi) || empty($parola)) {
        $hata_mesaji = "Lütfen tüm alanları doldurunuz!";
    } else {
        try {
            // Kullanıcı adına göre kullanıcıyı bul
            $sorgu = "SELECT * FROM yoneticiler WHERE kullanici_adi = :kullanici_adi";
            $stmt = $conn->prepare($sorgu);
            $stmt->bindParam(':kullanici_adi', $kullanici_adi);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($parola, $row['parola'])) {
                    // Giriş başarılı, session oluştur
                    $_SESSION['admin_giris'] = true;
                    $_SESSION['admin_id'] = $row['admin_id'];
                    $_SESSION['ad_soyad'] = $row['ad_soyad'];
                    $_SESSION['kullanici_adi'] = $row['kullanici_adi'];
                    header("Location: ./yonetici/dashboard.php");
                    exit;
                } else {
                    $hata_mesaji = "Hatalı şifre!";
                }
            } else {
                $hata_mesaji = "Kullanıcı bulunamadı!";
            }
        } catch (PDOException $e) {
            $hata_mesaji = "Hata oluştu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #7f7fd5, #86a8e7, #91eae4);
            height: 100vh;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #7f7fd5;
            box-shadow: 0 0 0 0.2rem rgba(127, 127, 213, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="login-container p-5">
                    <h2 class="text-center mb-4">Moods Yönetici Girişi</h2>
                    
                    <?php if(!empty($hata_mesaji)): ?>
                    <div class="alert alert-danger"><?php echo $hata_mesaji; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="kullanici_adi" class="form-label">Kullanıcı Adı</label>
                            <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" required>
                        </div>
                        <div class="mb-4">
                            <label for="parola" class="form-label">Parola</label>
                            <input type="password" class="form-control" id="parola" name="parola" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>