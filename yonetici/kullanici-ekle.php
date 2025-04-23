<?php
session_start();
require_once '../inc/db.php';
require_once '../phpqrcode/qrlib.php'; // QR kodu oluşturmak için gerekli kütüphane

if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}

$mesaj = '';
$mesaj_tur = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = trim($_POST['ad_soyad']);
    $eposta = trim($_POST['eposta']);
    $telefon_no = trim($_POST['telefon_no']);
    $parola = trim($_POST['parola']);

    // Gerekli alanların kontrolü
    if (empty($ad_soyad) || empty($eposta) || empty($telefon_no) || empty($parola)) {
        $mesaj = "Tüm alanları doldurunuz!";
        $mesaj_tur = 'danger';
    } else {
        try {
            $conn->beginTransaction();

            // Parolayı hashle
            $parola_hash = password_hash($parola, PASSWORD_DEFAULT);

            // Kullanıcıyı veritabanına ekle
            $sql = "INSERT INTO kullanicilar (ad_soyad, eposta, telefon_no, parola, yildiz_sayisi, yorum_sayisi) 
                    VALUES (:ad_soyad, :eposta, :telefon_no, :parola, 0, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':ad_soyad', $ad_soyad, PDO::PARAM_STR);
            $stmt->bindValue(':eposta', $eposta, PDO::PARAM_STR);
            $stmt->bindValue(':telefon_no', $telefon_no, PDO::PARAM_STR);
            $stmt->bindValue(':parola', $parola_hash, PDO::PARAM_STR);
            $stmt->execute();

            // Eklenen kullanıcının ID'sini al
            $userId = $conn->lastInsertId();

            // QR Kodu oluşturma
            $qrDir = $_SERVER['DOCUMENT_ROOT'] . '/moods/qr_codes/'; // Mutlak yol
            if (!is_dir($qrDir) && !mkdir($qrDir, 0755, true) && !is_dir($qrDir)) {
                throw new Exception('QR dizini oluşturulamadı', 500);
            }

            $qrData = json_encode([
                'user_id' => $userId,
                'ad_soyad' => $ad_soyad,
                'yildiz_sayisi' => 0
            ]);

            $qrPath = $qrDir . "user_$userId.png";
            QRcode::png($qrData, $qrPath, QR_ECLEVEL_L, 5);

            if (!file_exists($qrPath)) {
                throw new Exception('QR kodu oluşturulamadı', 500);
            }

            $qrBase64 = base64_encode(file_get_contents($qrPath));

            // Veritabanı güncelleme (QR kodu ekleme)
            $stmt = $conn->prepare("UPDATE kullanicilar SET qr_code = ? WHERE user_id = ?");
            $stmt->execute([$qrBase64, $userId]);

            // Temizlik (QR dosyasını sil)
            if (!unlink($qrPath)) {
                error_log("QR dosyası silinemedi: $qrPath");
            }

            $conn->commit();

            $mesaj = "Kullanıcı başarıyla eklendi!";
            $mesaj_tur = 'success';
        } catch (Exception $e) {
            $conn->rollBack();
            $mesaj = "Hata: " . $e->getMessage();
            $mesaj_tur = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Ekle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
</head>
<body class="bg-light">
      <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus mr-2"></i>Yeni Kullanıcı Ekle</h4>
            </div>
            
            <div class="card-body">
                <?php if ($mesaj): ?>
                    <div class="alert alert-<?= $mesaj_tur ?> alert-dismissible fade show">
                        <?= $mesaj ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="ad_soyad">Ad Soyad</label>
                        <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" required>
                    </div>
                    <div class="form-group">
                        <label for="eposta">E-posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" required>
                    </div>
                    <div class="form-group">
                        <label for="telefon_no">Telefon Numarası</label>
                        <input type="text" class="form-control" id="telefon_no" name="telefon_no" required>
                    </div>
                    <div class="form-group">
                        <label for="parola">Parola</label>
                        <input type="password" class="form-control" id="parola" name="parola" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Kullanıcı Ekle</button>
                    <a href="kullanicilar.php" class="btn btn-secondary">İptal</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>