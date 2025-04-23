<?php
session_start();
require_once '../inc/db.php';

// Eğer kullanıcı giriş yapmışsa yönlendir
if (isset($_SESSION['admin_giris'])) {
    header("Location: dashboard.php");
    exit;
}

$mesaj = '';
$mesaj_tur = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $parola = trim($_POST['parola']);
    $parola_tekrar = trim($_POST['parola_tekrar']);
    $eposta = trim($_POST['eposta']);
    $ad_soyad = trim($_POST['ad_soyad']);

    // Validasyonlar
    if (empty($kullanici_adi) || empty($parola) || empty($parola_tekrar) || empty($eposta) || empty($ad_soyad)) {
        $mesaj = "Tüm alanları doldurunuz!";
        $mesaj_tur = 'danger';
    } elseif ($parola !== $parola_tekrar) {
        $mesaj = "Parolalar eşleşmiyor!";
        $mesaj_tur = 'danger';
    } elseif (strlen($parola) < 6) {
        $mesaj = "Parola en az 6 karakter olmalıdır!";
        $mesaj_tur = 'danger';
    } elseif (!filter_var($eposta, FILTER_VALIDATE_EMAIL)) {
        $mesaj = "Geçersiz e-posta adresi!";
        $mesaj_tur = 'danger';
    } else {
        try {
            // Kullanıcı adı ve e-posta kontrolü
            $check_sql = "SELECT admin_id FROM yoneticiler WHERE kullanici_adi = :kullanici_adi OR eposta = :eposta";
            $stmt = $conn->prepare($check_sql);
            $stmt->bindParam(':kullanici_adi', $kullanici_adi);
            $stmt->bindParam(':eposta', $eposta);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $mesaj = "Bu kullanıcı adı veya e-posta zaten alınmış!";
                $mesaj_tur = 'danger';
            } else {
                // Hash parola
                $parola_hash = password_hash($parola, PASSWORD_DEFAULT);

                // Yeni yönetici ekle
                $insert_sql = "INSERT INTO yoneticiler (kullanici_adi, parola, eposta, ad_soyad) VALUES (:kullanici_adi, :parola, :eposta, :ad_soyad)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bindParam(':kullanici_adi', $kullanici_adi);
                $stmt->bindParam(':parola', $parola_hash);
                $stmt->bindParam(':eposta', $eposta);
                $stmt->bindParam(':ad_soyad', $ad_soyad);

                if ($stmt->execute()) {
                    $mesaj = "Yönetici başarıyla oluşturuldu! Giriş yapabilirsiniz.";
                    $mesaj_tur = 'success';
                } else {
                    $mesaj = "Hata oluştu!";
                    $mesaj_tur = 'danger';
                }
            }
        } catch (PDOException $e) {
            $mesaj = "Hata oluştu: " . $e->getMessage();
            $mesaj_tur = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Kayıt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 
    <style>
        body {
            background: linear-gradient(120deg, #6a11cb, #2575fc);
            height: 100vh;
        }
        .register-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
      <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="register-container p-5">
                    <h2 class="text-center mb-4">Yönetici Kayıt</h2>
                    
                    <?php if($mesaj): ?>
                    <div class="alert alert-<?php echo $mesaj_tur; ?>"><?php echo $mesaj; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="kullanici_adi" class="form-label">Kullanıcı Adı</label>
                            <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" required>
                        </div>
                        <div class="mb-3">
                            <label for="eposta" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="eposta" name="eposta" required>
                        </div>
                        <div class="mb-3">
                            <label for="ad_soyad" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" required>
                        </div>
                        <div class="mb-3">
                            <label for="parola" class="form-label">Parola</label>
                            <input type="password" class="form-control" id="parola" name="parola" required>
                        </div>
                        <div class="mb-4">
                            <label for="parola_tekrar" class="form-label">Parola Tekrar</label>
                            <input type="password" class="form-control" id="parola_tekrar" name="parola_tekrar" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kayıt Ol</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none">Giriş Yap</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>