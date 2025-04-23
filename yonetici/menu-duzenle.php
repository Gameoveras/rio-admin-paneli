<?php
session_start();
require_once '../inc/db.php';

if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}


$item_id = $_GET['id'] ?? null;

// Mevcut ürün bilgilerini çek
$sorgu = $conn->prepare("SELECT * FROM menu WHERE id = :id");
$sorgu->execute(['id' => $item_id]);
$item = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    // Ürün bulunamadıysa hata mesajı gösterebilirsiniz ya da başka bir sayfaya yönlendirebilirsiniz.
    die("Ürün bulunamadı!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = ['success' => false, 'message' => ''];
    try {
        $conn->beginTransaction();

        // Form verilerini al
        $kategori   = $_POST["kategori"];
        $ad         = $_POST["ad"];
        $aciklama   = $_POST["aciklama"];
        $fiyat      = $_POST["fiyat"];
        $kalori     = $_POST["kalori"];
        $one_cikan  = isset($_POST["one_cikan"]) ? 1 : 0;
        $icindekiler = json_encode($_POST["icindekiler"]);
        
        // Resim işlemleri
        $resim_yolu = $item['resim'];
        if (!empty($_POST["resim_data"])) {
            $img_data = $_POST["resim_data"];
            $img_data = str_replace(['data:image/png;base64,', 'data:image/jpeg;base64,', ' '], ['', '', '+'], $img_data);
            $decoded_data = base64_decode($img_data);

            if ($decoded_data === false) {
                throw new Exception("Geçersiz resim formatı!");
            }

            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $dosya_adi = uniqid() . ".jpg";
            $resim_yolu = $upload_dir . $dosya_adi;
            
            if (!file_put_contents($resim_yolu, $decoded_data)) {
                throw new Exception("Resim yüklenemedi!");
            }

            // Eski resmi sil
            if ($item['resim'] && file_exists($item['resim'])) {
                unlink($item['resim']);
            }
        }

        // Veritabanını güncelle
        $guncelle = $conn->prepare("UPDATE menu SET 
    kategori   = :kategori,
    ad         = :ad,
    aciklama   = :aciklama,
    fiyat      = :fiyat,
    kalori     = :kalori,
    resim      = :resim,
    one_cikan  = :one_cikan,
    `içindekiler` = :icindekiler
    WHERE id = :id
");

$guncelle->execute([
    ':kategori'    => $kategori,
    ':ad'          => $ad,
    ':aciklama'    => $aciklama,
    ':fiyat'       => $fiyat,
    ':kalori'      => $kalori,
    ':resim'       => $resim_yolu,
    ':one_cikan'   => $one_cikan,
    ':icindekiler' => $içindekiler, // Formdan gelen değeri burada atıyoruz.
    ':id'          => $item_id
]);

        $conn->commit();
        $response = ['success' => true, 'message' => 'Ürün başarıyla güncellendi!'];
    } catch (PDOException $e) {
        $conn->rollBack();
        $response['message'] = "Veritabanı hatası: " . $e->getMessage();
    } catch (Exception $e) {
        $conn->rollBack();
        $response['message'] = $e->getMessage();
    }

    // Eğer AJAX isteği ise JSON yanıtı dönün
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // İşlem tamamlandığında yönlendirme yapın
    if ($response['success']) {
        header("Location: menu.php?mesaj=" . urlencode($response['message']) . "&mesaj_tur=success");
    } else {
        header("Location: menu-duzenle.php?id=$item_id&mesaj=" . urlencode($response['message']) . "&mesaj_tur=danger");
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Düzenle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('resim-preview').src = e.target.result;
                document.getElementById('resim_data').value = e.target.result;
                document.getElementById('resim-preview').style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-edit mr-2"></i>Ürün Düzenle</h3>
            </div>
            <div class="card-body">
                <?php if(isset($_GET['mesaj'])): ?>
                <div class="alert alert-<?= $_GET['mesaj_tur'] ?>"><?= $_GET['mesaj'] ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori" class="form-control" required>
                                    <option value="İçecek" <?= $item['kategori'] == 'İçecek' ? 'selected' : '' ?>>İçecek</option>
                                    <option value="Yiyecek" <?= $item['kategori'] == 'Yiyecek' ? 'selected' : '' ?>>Yiyecek</option>
                                    <option value="Tatlı" <?= $item['kategori'] == 'Tatlı' ? 'selected' : '' ?>>Tatlı</option>
                                    <option value="Atıştırmalık" <?= $item['kategori'] == 'Atıştırmalık' ? 'selected' : '' ?>>Atıştırmalık</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Ürün Adı</label>
                                <input type="text" name="ad" class="form-control" value="<?= htmlspecialchars($item['ad']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Fiyat (TL)</label>
                                <input type="number" step="0.01" name="fiyat" class="form-control" value="<?= htmlspecialchars($item['fiyat']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kalori</label>
                                <input type="number" name="kalori" class="form-control" value="<?= htmlspecialchars($item['kalori']) ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Öne Çıkan</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="one_cikan" class="custom-control-input" id="one_cikan" <?= $item['one_cikan'] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="one_cikan">Öne Çıkar</label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Resim</label>
                                <input type="file" class="form-control-file" onchange="previewImage(event)">
                                <input type="hidden" name="resim_data" id="resim_data">
                                <?php if($item['resim']): ?>
                                <img src="<?= $item['resim'] ?>" id="resim-preview" class="img-thumbnail mt-2" style="max-width: 200px;">
                                <?php else: ?>
                                <img src="" id="resim-preview" class="img-thumbnail mt-2" style="max-width: 200px; display: none;">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Açıklama</label>
                        <textarea name="aciklama" class="form-control" rows="3"><?= htmlspecialchars($item['aciklama']) ?></textarea>
                    </div>

                   <div class="form-group">
    <label>İçindekiler</label>
    <textarea name="icindekiler" class="form-control" rows="3"><?= htmlspecialchars($item['içindekiler'] ?? '') ?></textarea>
</div>


                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save mr-2"></i>Kaydet
                    </button>
                    <a href="menu.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times mr-2"></i>İptal
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
