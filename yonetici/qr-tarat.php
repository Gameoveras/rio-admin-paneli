<?php
session_start();
require_once '../inc/db.php';

if (!isset($_SESSION['admin_giris'])) {
    header("Location: dashboard.php");
    exit;
}

$mesaj = '';
$mesajTipi = '';
$hata = '';
$kullanici = null;
$qrDataOriginal = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_data'])) {
    try {
        $qrData = json_decode($_POST['qr_data'], true, 512, JSON_THROW_ON_ERROR);
        $qrDataOriginal = $qrData;
        
        $requiredFields = ['user_id', 'ad_soyad', 'yildiz_sayisi', 'timestamp'];
        foreach ($requiredFields as $field) {
            if (!isset($qrData[$field])) {
                throw new Exception("Eksik QR verisi: $field alanı bulunamadı!");
            }
        }
        
        $qrTime = new DateTime($qrData['timestamp']);
        $currentTime = new DateTime();
        $timeDiff = $currentTime->getTimestamp() - $qrTime->getTimestamp();
        
        if ($timeDiff > 300) {
            throw new Exception("QR kodu süresi dolmuş! Lütfen yeniden taratın.");
        }
        
        $stmt = $conn->prepare("SELECT * FROM kullanicilar WHERE user_id = ?");
        $stmt->execute([$qrData['user_id']]);
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$kullanici) {
            throw new Exception("Kullanıcı bulunamadı: ID {$qrData['user_id']}");
        }
        
        if ($kullanici['ad_soyad'] !== $qrData['ad_soyad']) {
            throw new Exception("Kullanıcı adı uyuşmuyor!");
        }
        
        if ((int)$kullanici['yildiz_sayisi'] !== (int)$qrData['yildiz_sayisi']) {
            throw new Exception("Yıldız sayısı uyuşmuyor!");
        }
        
    } catch (Exception $e) {
        $hata = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['islem_tipi'])) {
    try {
        if (!$kullanici) {
            throw new Exception("Kullanıcı bilgileri yüklenemedi!");
        }
        
        $islemTipi = $_POST['islem_tipi'];
        $mevcutYildiz = (int)$kullanici['yildiz_sayisi'];
        
        if (!in_array($islemTipi, ['kullan', 'ekle'])) {
            throw new Exception("Geçersiz işlem tipi!");
        }
        
        if ($islemTipi === 'kullan') {
            if ($mevcutYildiz < 10) {
                throw new Exception("En az 10 yıldız gereklidir!");
            }
            $yeniYildiz = $mevcutYildiz - 10;
            $mesaj = "<strong>{$kullanici['ad_soyad']}</strong> için bedava kahve hazırlandı!";
            $mesajTipi = "coffee";
        } else {
            $miktar = (int)($_POST['yildiz_miktari'] ?? 0);
            if ($miktar < 1) {
                throw new Exception("En az 1 yıldız eklemelisiniz!");
            }
            $yeniYildiz = $mevcutYildiz + $miktar;
            $mesaj = "<strong>{$kullanici['ad_soyad']}</strong> için <strong>$miktar</strong> yıldız başarıyla eklendi!";
            $mesajTipi = "star";
        }
        
        if ($yeniYildiz < 0) {
            throw new Exception("Yıldız sayısı negatif olamaz!");
        }
        
        $conn->beginTransaction();
        
        $stmt = $conn->prepare("UPDATE kullanicilar SET yildiz_sayisi = ? WHERE user_id = ?");
        $stmt->execute([$yeniYildiz, $kullanici['user_id']]);
        
        $stmt = $conn->prepare("INSERT INTO qr_islemleri (user_id, islem_tipi) VALUES (?, ?)");
        $stmt->execute([$kullanici['user_id'], $islemTipi]);
        
        $conn->commit();
        
        // İşlem başarılı bilgisini session'a kaydedelim, sayfa yenilendikten sonra bu bilgiyi gösterelim
        $_SESSION['islem_basarili'] = true;
        $_SESSION['mesaj'] = $mesaj;
        $_SESSION['mesaj_tipi'] = $mesajTipi;
        $_SESSION['yeni_yildiz'] = $yeniYildiz;
        
        header("Refresh:0");
        exit;
        
    } catch (Exception $e) {
        $conn->rollBack();
        $hata = $e->getMessage();
    }
}

// Session'dan işlem başarılı bilgisini alalım ve temizleyelim
if (isset($_SESSION['islem_basarili']) && $_SESSION['islem_basarili']) {
    $mesaj = $_SESSION['mesaj'];
    $mesajTipi = $_SESSION['mesaj_tipi'];
    $yeniYildiz = $_SESSION['yeni_yildiz'];
    
    unset($_SESSION['islem_basarili']);
    unset($_SESSION['mesaj']);
    unset($_SESSION['mesaj_tipi']);
    unset($_SESSION['yeni_yildiz']);
}
?>
<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rio - Yönetici Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Instascan kütüphanesi -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <style>
        body {
            background: #121212;
            min-height: 100vh;
            font-family: 'Space Grotesk', sans-serif;
        }
        .qr-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(40, 40, 40, 0.8);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .user-card {
            background: rgba(20, 20, 20, 0.7);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255,255,255,0.05);
        }
        .star {
            color: #ffd700;
            font-size: 1.5rem;
            margin: 0 2px;
        }
        .pulse {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            border: 3px solid #4CAF50;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(76, 175, 80, 0.3);
            pointer-events: none;
        }
        .camera-box {
            background: rgba(20, 20, 20, 0.7);
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255,255,255,0.05);
        }
        .success-alert {
            background: rgba(25, 135, 84, 0.2);
            border-left: 5px solid #198754;
            color: #fff;
            padding: 1.2rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.5s;
        }
        .success-coffee {
            background: rgba(13, 110, 253, 0.2);
            border-left: 5px solid #0d6efd;
            color: #fff;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-add {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            border: none;
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
        }
        .btn-add:hover {
            background: linear-gradient(45deg, #45a049, #3d8b3d);
            box-shadow: 0 6px 12px rgba(76, 175, 80, 0.4);
        }
        .btn-coffee {
            background: linear-gradient(45deg, #0d6efd, #0b5ed7);
            border: none;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }
        .btn-coffee:hover {
            background: linear-gradient(45deg, #0b5ed7, #0a58ca);
            box-shadow: 0 6px 12px rgba(13, 110, 253, 0.4);
        }
        .alert-icon {
            font-size: 2.5rem;
            margin-right: 1rem;
        }
        .app-header {
            position: relative;
            display: inline-block;
        }
        .app-header:after {
            content: '';
            position: absolute;
            width: 50%;
            height: 3px;
            background: linear-gradient(90deg, #ffd700, transparent);
            bottom: -10px;
            left: 25%;
        }
        .success-title {
            font-weight: 600;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }
        .success-detail {
            opacity: 0.9;
        }
        .result-info {
            margin-top: 0.5rem;
            display: inline-block;
            background: rgba(255,255,255,0.1);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Her zaman mevcut olan gizli form -->
    <form method="post" id="qrForm" style="display: none;">
        <input type="hidden" name="qr_data" id="qrDataInput">
    </form>

    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="qr-container">
            <h1 class="text-center mb-4 text-white app-header">
                <i class="fas fa-coffee me-2"></i>Rio - Yönetici Paneli
            </h1>
            
            <?php if($hata): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($hata) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if($mesaj): ?>
            <div class="success-alert <?= $mesajTipi === 'coffee' ? 'success-coffee' : '' ?> d-flex align-items-start">
                <div class="alert-icon">
                    <?php if($mesajTipi === 'coffee'): ?>
                        <i class="fas fa-mug-hot"></i>
                    <?php else: ?>
                        <i class="fas fa-star"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="success-title">
                        <?php if($mesajTipi === 'coffee'): ?>
                            Bedava Kahve Hazırlandı!
                        <?php else: ?>
                            Yıldız Başarıyla Eklendi!
                        <?php endif; ?>
                    </div>
                    <div class="success-detail"><?= $mesaj ?></div>
                    <div class="result-info">
                        <i class="fas fa-star text-warning me-1"></i> Mevcut Yıldız: <?= $yeniYildiz ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="camera-box mb-4">
                <div class="position-relative" style="border: 3px solid #333; border-radius: 10px;">
                    <video id="qrScanner" width="100%" style="border-radius: 8px;"></video>
                    <div class="scanner-overlay"></div>
                </div>
                <div class="text-center mt-3">
                    <button id="startScanner" class="btn btn-outline-primary">
                        <i class="fas fa-video me-2"></i>Kamerayı Aç
                    </button>
                    <button id="stopScanner" class="btn btn-outline-danger d-none">
                        <i class="fas fa-stop-circle me-2"></i>Durdur
                    </button>
                </div>
            </div>

            <?php if($kullanici): 
                // Bedava kahve bilgisi: her 10 yıldızda 1 bedava kahve kazanılıyor
                $bedavaKahve = floor($kullanici['yildiz_sayisi'] / 10);
            ?>
            <div class="user-card">
                <div class="row align-items-center mb-3">
                    <div class="col-md-8">
                    <h2 class="text-warning mb-1">
                        <?= htmlspecialchars($kullanici['ad_soyad']) ?>
                    </h2>
                    <div class="mb-2">
                        <?php for($i=0; $i<min($kullanici['yildiz_sayisi'], 10); $i++): ?>
                            <i class="fas fa-star star"></i>
                        <?php endfor; ?>
                        <?php if($kullanici['yildiz_sayisi'] > 10): ?>
                            <span class="badge bg-warning text-dark ms-2">+<?= $kullanici['yildiz_sayisi'] - 10 ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted">
                        Toplam Yıldız: <?= $kullanici['yildiz_sayisi'] ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-user-circle fa-4x text-secondary"></i>
                </div>
            </div>
            
            <div class="row g-3 mt-3">
                <?php if($kullanici['yildiz_sayisi'] >= 10): ?>
                <div class="col-12">
                    <div class="alert alert-info border-start border-4 border-primary">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-gift fa-2x me-3 text-primary"></i>
                            <div>
                                <h5 class="mb-1">Bedava Kahve Hakkı</h5>
                                <p class="mb-0">Kullanılabilir Bedava Kahve: <strong><?= floor($kullanici['yildiz_sayisi'] / 10) ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <form method="post">
                        <input type="hidden" name="qr_data" 
                               value="<?= htmlspecialchars(json_encode($qrDataOriginal ?? [])) ?>">
                        <button type="submit" 
                                name="islem_tipi" 
                                value="kullan"
                                class="btn btn-coffee btn-lg w-100 pulse">
                            <i class="fas fa-mug-hot me-2"></i>
                            Bedava Kahve Hazırla
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning border-start border-4 border-warning">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
                            <div>
                                <h5 class="mb-1">Daha Fazla Yıldız Gerekli</h5>
                                <p class="mb-0">Bedava kahve için en az 10 yıldız gerekli! Şu an: <strong><?= $kullanici['yildiz_sayisi'] ?></strong> yıldız</p>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: <?= ($kullanici['yildiz_sayisi'] / 10) * 100 ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="col-md-6">
                    <form method="post">
                        <input type="hidden" name="qr_data" 
                               value="<?= htmlspecialchars(json_encode($qrDataOriginal ?? [])) ?>">
                        <div class="input-group">
                            <input type="number" 
                                   name="yildiz_miktari" 
                                   class="form-control form-control-lg" 
                                   min="1" 
                                   value="1"
                                   required>
                            <button type="submit" 
                                    name="islem_tipi" 
                                    value="ekle"
                                    class="btn btn-add btn-lg">
                                <i class="fas fa-plus-circle me-2"></i>
                                Yıldız Ekle
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="col-12 mt-4">
                    <div class="card bg-dark">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Müşteri Bilgileri
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush bg-transparent">
                                <li class="list-group-item bg-transparent d-flex justify-content-between">
                                    <span>Toplam Yıldız:</span>
                                    <span class="fw-bold"><?= $kullanici['yildiz_sayisi'] ?></span>
                                </li>
                                <li class="list-group-item bg-transparent d-flex justify-content-between">
                                    <span>Kazanılan Kahve:</span>
                                    <span class="fw-bold"><?= floor($kullanici['yildiz_sayisi'] / 10) ?></span>
                                </li>
                                <li class="list-group-item bg-transparent d-flex justify-content-between">
                                    <span>Bir Sonraki Kahve İçin:</span>
                                    <span class="fw-bold"><?= 10 - ($kullanici['yildiz_sayisi'] % 10) ?> yıldız daha</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Instascan ile kamera ve QR tarama işlemleri
let scanner = null;
const videoElement = document.getElementById('qrScanner');
const startBtn = document.getElementById('startScanner');
const stopBtn = document.getElementById('stopScanner');

// Kamera başlatma fonksiyonu
async function startCamera() {
    try {
        const cameras = await Instascan.Camera.getCameras();
        if (cameras.length === 0) {
            showError('Hiç kamera bulunamadı!');
            return;
        }

        const backCamera = cameras.find(c => c.name.toLowerCase().includes('back')) || cameras[0];

        scanner = new Instascan.Scanner({
            video: videoElement,
            mirror: false,
            backgroundScan: true,
            refractoryPeriod: 5000,
            scanPeriod: 3
        });

        scanner.addListener('scan', content => {
            handleQRScan(content);
        });

        await scanner.start(backCamera);
        toggleCameraButtons(true);
        console.log('Kamera başlatıldı:', backCamera.name);
        
        // Hata mesajlarını temizle
        const existingError = document.querySelector('.alert-danger');
        if (existingError) existingError.remove();
    } catch (error) {
        handleCameraError(error);
    }
}

// QR tarandığında çalışacak fonksiyon
function handleQRScan(content) {
    console.log('QR Okundu:', content);
    try {
        // Tarama başarılı animasyonu
        const overlay = document.querySelector('.scanner-overlay');
        overlay.style.borderColor = '#4CAF50';
        overlay.style.boxShadow = '0 0 30px rgba(76, 175, 80, 0.7)';
        
        // Titreşim efekti (destekleyen cihazlarda)
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }
        
        // QR verilerini forma ekle ve gönder
        document.getElementById('qrDataInput').value = content;
        document.getElementById('qrForm').submit();
        
        // Tarama başarılıysa kamerayı durdur
        stopCamera();
        
        // Yeniden başlat butonunu göster
        showRestartButton();
    } catch (error) {
        showError('QR işlenirken hata oluştu: ' + error.message);
    }
}

// Kamera durdurma fonksiyonu
function stopCamera() {
    if (scanner) {
        scanner.stop();
        toggleCameraButtons(false);
        console.log('Kamera durduruldu.');
    }
}

// Kamera butonlarını güncelleme
function toggleCameraButtons(isScanning) {
    startBtn.classList.toggle('d-none', isScanning);
    stopBtn.classList.toggle('d-none', !isScanning);
}

// Hata gösterimi
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger mt-3';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
    `;
    document.querySelector('.camera-box').appendChild(errorDiv);
}

// Kamera yeniden başlatma butonunu gösterme
function showRestartButton() {
    const cameraBox = document.querySelector('.camera-box');
    const existingRestart = document.querySelector('.restart-btn');
    if (!existingRestart) {
        const restartDiv = document.createElement('div');
        restartDiv.className = 'text-center mt-3';
        restartDiv.innerHTML = `
            <button class="btn btn-outline-primary restart-btn">
                <i class="fas fa-sync-alt me-2"></i>Yeni Tarama Başlat
            </button>
        `;
        cameraBox.appendChild(restartDiv);
        
        // Butona tıklama olayını ekle
        const restartBtn = restartDiv.querySelector('.restart-btn');
        restartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // Mevcut butonu kaldır
            restartDiv.remove();
            // Kamerayı yeniden başlat
            startCamera();
        });
    }
}

// Kamera hatası işleme
function handleCameraError(error) {
    console.error('Kamera hatası:', error);
    showError('Kamera başlatılırken hata oluştu: ' + error.message);
}

// Başarılı işlem bildirimini otomatik kapat
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.success-alert');
    if (successAlert) {
        setTimeout(() => {
            new bootstrap.Alert(successAlert).close();
        }, 10000);
    }
});

// Buton event listener'ları
startBtn.addEventListener('click', (e) => {
    e.preventDefault();
    startCamera();
});

stopBtn.addEventListener('click', (e) => {
    e.preventDefault();
    stopCamera();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bu bölüm yönetici olmayan kullanıcılar için sayfa yüklenirken çalışır -->
<?php if (!isset($_SESSION['admin_giris'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('Bu sayfaya erişmek için yönetici olarak giriş yapmanız gerekmektedir!');
        window.location.href = 'dashboard.php';
    });
</script>
<?php endif; ?>
</body>
</html>