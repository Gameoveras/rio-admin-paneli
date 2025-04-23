<?php
session_start();
require_once '../inc/db.php';
if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}

$mesaj = '';
$mesaj_tur = '';

// Düzenlenecek kampanyanın ID'sini al
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: kampanyalar.php?mesaj=Geçersiz kampanya ID'si!&mesaj_tur=danger");
    exit;
}

// Kampanya bilgilerini çek
$sorgu = $conn->prepare("SELECT * FROM kampanyalar WHERE id = :id");
$sorgu->execute(['id' => $id]);
$kampanya = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$kampanya) {
    header("Location: kampanyalar.php?mesaj=Kampanya bulunamadı!&mesaj_tur=danger");
    exit;
}

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = trim($_POST['baslik']);
    $tur = trim($_POST['tur']);
    $on_aciklama = trim($_POST['on_aciklama']);
    $icerik = trim($_POST['icerik']);
    $on_resim = $_FILES['on_resim'];

    if (!in_array($tur, ['Duyuru', 'Kampanya'])) {
        $mesaj = "Geçersiz tür seçimi!";
        $mesaj_tur = 'danger';
    } elseif (empty($baslik) || empty($on_aciklama) || empty($icerik)) {
        $mesaj = "Tüm alanları doldurunuz!";
        $mesaj_tur = 'danger';
    } else {
        try {
            $conn->beginTransaction();

            // Yeni resim yüklenmişse işlemleri yap
            if ($on_resim['name']) {
                $hedef_klasor = "uploads/";
                $dosya_adi = time() . "_" . uniqid() . "_" . basename($on_resim['name']);
                $hedef_dosya = $hedef_klasor . $dosya_adi;

                // Resim format ve boyut kontrolü
                $izinli_formatlar = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($on_resim['type'], $izinli_formatlar)) {
                    throw new Exception("Sadece JPG, PNG ve GIF formatları kabul edilmektedir!");
                }

                if ($on_resim['size'] > 2 * 1024 * 1024) {
                    throw new Exception("Dosya boyutu 2MB'dan küçük olmalıdır!");
                }

                if (!is_dir($hedef_klasor)) {
                    mkdir($hedef_klasor, 0777, true);
                }

                if (!move_uploaded_file($on_resim['tmp_name'], $hedef_dosya)) {
                    throw new Exception("Dosya yükleme başarısız!");
                }

                // Eski resmi sil
                if ($kampanya['on_resmi'] && file_exists($hedef_klasor . $kampanya['on_resmi'])) {
                    unlink($hedef_klasor . $kampanya['on_resmi']);
                }
            } else {
                // Yeni resim yüklenmemişse eski resmi kullan
                $dosya_adi = $kampanya['on_resmi'];
            }

            // Kampanya bilgilerini güncelle
            $sql = "UPDATE kampanyalar 
                    SET baslik = :baslik, tur = :tur, on_aciklama = :on_aciklama, icerik = :icerik, on_resmi = :on_resmi 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);

            $stmt->bindValue(':baslik', $baslik, PDO::PARAM_STR);
            $stmt->bindValue(':tur', $tur, PDO::PARAM_STR);
            $stmt->bindValue(':on_aciklama', $on_aciklama, PDO::PARAM_STR);
            $stmt->bindValue(':icerik', $icerik, PDO::PARAM_STR);
            $stmt->bindValue(':on_resmi', $dosya_adi, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $conn->commit();
                header("Location: kampanyalar.php?mesaj=Kampanya başarıyla güncellendi!&mesaj_tur=success");
                exit;
            } else {
                throw new Exception("Veritabanı hatası!");
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $mesaj = "Hata: " . $e->getMessage();
            $mesaj_tur = 'danger';

            // Yüklenen dosyayı sil
            if (isset($hedef_dosya) && file_exists($hedef_dosya)) {
                unlink($hedef_dosya);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kampanya Düzenle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-edit mr-2"></i>Kampanya Düzenle</h4>
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

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="baslik">Başlık</label>
                        <input type="text" class="form-control" id="baslik" name="baslik" value="<?= htmlspecialchars($kampanya['baslik']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tur">Tür</label>
                        <select class="form-control" id="tur" name="tur" required>
                            <option value="Duyuru" <?= $kampanya['tur'] == 'Duyuru' ? 'selected' : '' ?>>Duyuru</option>
                            <option value="Kampanya" <?= $kampanya['tur'] == 'Kampanya' ? 'selected' : '' ?>>Kampanya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="on_aciklama">Ön Açıklama</label>
                        <textarea class="form-control" id="on_aciklama" name="on_aciklama" rows="3" required><?= htmlspecialchars($kampanya['on_aciklama']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="icerik">İçerik</label>
                        <textarea class="form-control" id="icerik" name="icerik" rows="5" required><?= htmlspecialchars($kampanya['icerik']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="on_resim">Ön Resim</label>
                        <input type="file" class="form-control-file" id="on_resim" name="on_resim">
                        <small class="form-text text-muted">Mevcut resim: <?= $kampanya['on_resmi'] ?></small>
                    </div>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                    <a href="kampanyalar.php" class="btn btn-secondary">İptal</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>