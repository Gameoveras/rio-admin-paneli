<?php
session_start();
require_once '../inc/db.php'; // PDO bağlantılı

if(!isset($_SESSION['admin_giris'])) {
    header("Location: ../index.php");
    exit;
}

// Veri çekme fonksiyonları (Mevcut tablolara uygun)
function getToplamKullanici($conn) {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM kullanicilar");
    return $stmt->fetchColumn();
}

function getMenuDagilimi($conn) {
    $stmt = $conn->query("SELECT kategori, COUNT(*) as count FROM menu GROUP BY kategori");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getYildizDagilimi($conn) {
    $stmt = $conn->query("
        SELECT 
            yildiz_puani,
            COUNT(*) as adet 
        FROM yorumlar 
        GROUP BY yildiz_puani 
        ORDER BY yildiz_puani
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPopulerUrunler($conn) {
    $stmt = $conn->query("
        SELECT m.ad, COUNT(f.menu_id) as favori_sayisi 
        FROM favoriler f
        JOIN menu m ON f.menu_id = m.id
        GROUP BY f.menu_id 
        ORDER BY favori_sayisi DESC 
        LIMIT 5
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getKampanyaDagilimi($conn) {
    $stmt = $conn->query("SELECT tur, COUNT(*) as sayi FROM kampanyalar GROUP BY tur");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Verileri al
$menu_dagilim = getMenuDagilimi($conn);
$yildiz_dagilim = getYildizDagilimi($conn);
$populer_urunler = getPopulerUrunler($conn);
$kampanya_dagilim = getKampanyaDagilimi($conn);
$toplam_kullanici = getToplamKullanici($conn);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Analitik Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .chart-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
        }
    </style>
</head>

  <?php include 'navbar.php'; ?>


<body class="bg-light">
    <div class="container py-4">
        <h2 class="mb-4 text-center">☕ Mevcut Verilerle Analitikler</h2>
        
        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <h5>Toplam Kullanıcı</h5>
                    <h1 class="text-primary"><?= $toplam_kullanici ?></h1>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <h5>Toplam Menü Öğesi</h5>
                    <h1 class="text-success"><?= array_sum(array_column($menu_dagilim, 'count')) ?></h1>
                </div>
            </div>
        </div>

        <!-- Grafikler -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-card">
                    <canvas id="menuChart"></canvas>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="chart-card">
                    <canvas id="yildizChart"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-card">
                    <canvas id="kampanyaChart"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-card">
                    <canvas id="populerChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Menü Dağılımı
        new Chart(document.getElementById('menuChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($menu_dagilim, 'kategori')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($menu_dagilim, 'count')) ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            },
            options: {
                plugins: {
                    title: {display: true, text: '🍴 Menü Kategori Dağılımı'}
                }
            }
        });

        // Yıldız Dağılımı
        new Chart(document.getElementById('yildizChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($yildiz_dagilim, 'yildiz_puani')) ?>,
                datasets: [{
                    label: 'Yıldız Sayısı',
                    data: <?= json_encode(array_column($yildiz_dagilim, 'adet')) ?>,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)'
                }]
            },
            options: {
                plugins: {
                    title: {display: true, text: '⭐ Yıldız Puanı Dağılımı'}
                },
                scales: {
                    y: {beginAtZero: true, ticks: {precision: 0}}
                }
            }
        });

        // Kampanya Dağılımı
        new Chart(document.getElementById('kampanyaChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($kampanya_dagilim, 'tur')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($kampanya_dagilim, 'sayi')) ?>,
                    backgroundColor: ['#FF6384', '#36A2EB']
                }]
            },
            options: {
                plugins: {
                    title: {display: true, text: '📢 Kampanya Türleri'}
                }
            }
        });

        // Popüler Ürünler
        new Chart(document.getElementById('populerChart'), {
            type: 'horizontalBar',
            data: {
                labels: <?= json_encode(array_column($populer_urunler, 'ad')) ?>,
                datasets: [{
                    label: 'Favori Sayısı',
                    data: <?= json_encode(array_column($populer_urunler, 'favori_sayisi')) ?>,
                    backgroundColor: '#4BC0C0'
                }]
            },
            options: {
                plugins: {
                    title: {display: true, text: '🏆 En Popüler Ürünler'}
                },
                scales: {
                    x: {beginAtZero: true}
                }
            }
        });
    </script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>