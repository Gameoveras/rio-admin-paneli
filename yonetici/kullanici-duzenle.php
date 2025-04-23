<?php
session_start();
require_once '../inc/db.php';
if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}

$mesaj = '';
$mesaj_tur = '';

// Düzenlenecek kullanıcının ID'sini al
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: kullanicilar.php?mesaj=Geçersiz kullanıcı ID'si!&mesaj_tur=danger");
    exit;
}

// Kullanıcı bilgilerini çek
try {
    $sorgu = $conn->prepare("SELECT * FROM kullanicilar WHERE user_id = :id");
    $sorgu->execute(['id' => $id]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

    if (!$kullanici) {
        header("Location: kullanicilar.php?mesaj=Kullanıcı bulunamadı!&mesaj_tur=danger");
        exit;
    }
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = trim($_POST['ad_soyad']);
    $eposta = trim($_POST['eposta']);
    $telefon_no = trim($_POST['telefon_no']);
    $parola = trim($_POST['parola']);

    // Gerekli alanların kontrolü
    if (empty($ad_soyad) || empty($eposta) || empty($telefon_no)) {
        $mesaj = "Ad Soyad, E-posta ve Telefon alanları zorunludur!";
        $mesaj_tur = 'danger';
    } else {
        try {
            $conn->beginTransaction();

            // Parola güncellenmişse hashle
            $parola_sql = '';
            if (!empty($parola)) {
                $parola_hash = password_hash($parola, PASSWORD_DEFAULT);
                $parola_sql = ", parola = :parola";
            }

            // Kullanıcı bilgilerini güncelle
            $sql = "UPDATE kullanicilar 
                    SET ad_soyad = :ad_soyad, eposta = :eposta, telefon_no = :telefon_no $parola_sql 
                    WHERE user_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':ad_soyad', $ad_soyad, PDO::PARAM_STR);
            $stmt->bindValue(':eposta', $eposta, PDO::PARAM_STR);
            $stmt->bindValue(':telefon_no', $telefon_no, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            if (!empty($parola)) {
                $stmt->bindValue(':parola', $parola_hash, PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                $conn->commit();
                header("Location: kullanicilar.php?mesaj=Kullanıcı başarıyla güncellendi!&mesaj_tur=success");
                exit;
            } else {
                throw new Exception("Veritabanı güncelleme hatası!");
            }
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
    <title>Kullanıcı Düzenle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .card-shadow { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-edit mr-2"></i>Kullanıcı Düzenle</h4>
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
                        <input type="text" class="form-control" id="ad_soyad" name="ad_soyad" 
                               value="<?= htmlspecialchars($kullanici['ad_soyad']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="eposta">E-posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" 
                               value="<?= htmlspecialchars($kullanici['eposta']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefon_no">Telefon Numarası</label>
                        <input type="text" class="form-control" id="telefon_no" name="telefon_no" 
                               value="<?= htmlspecialchars($kullanici['telefon_no']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="parola">Parola (Değiştirmek istemiyorsanız boş bırakın)</label>
                        <input type="password" class="form-control" id="parola" name="parola">
                    </div>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                    <a href="kullanicilar.php" class="btn btn-secondary">İptal</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>