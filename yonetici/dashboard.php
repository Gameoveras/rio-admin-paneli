<?php
session_start();
require_once '../inc/db.php';
if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}

$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Eski URL'ye erişimi engelle
if(strpos($current_url, '.php') !== false) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . str_replace('.php', '', $current_url));
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome (alternatif ikonlar için) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --neon-purple: #8a2be2;
            --neon-blue: #00f7ff;
            --dark-bg: #0a0a1a;
            --card-bg: #13132b;
        }
        body {
            background: var(--dark-bg);
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
        }
        .neon-glow {
            text-shadow: 0 0 10px var(--neon-purple),
                         0 0 20px var(--neon-purple),
                         0 0 30px var(--neon-purple);
        }
        .admin-header {
            background: var(--card-bg);
            border-bottom: 2px solid var(--neon-purple);
            box-shadow: 0 0 30px rgba(138,43,226,0.2);
        }
        .menu-card {
            background: var(--card-bg);
            border: 1px solid rgba(138,43,226,0.3);
            border-radius: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .menu-card:hover {
            transform: translateY(-5px);
            border-color: var(--neon-purple);
            box-shadow: 0 10px 30px rgba(138,43,226,0.3);
        }
        .card-icon {
            font-size: 2.5rem;
            background: linear-gradient(45deg, var(--neon-purple), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-menu {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1rem;
            margin: 2rem auto;
            max-width: 90%;
        }
        .nav-link {
            color: #fff !important;
            position: relative;
            padding: 1rem 2rem !important;
            margin: 0 1rem;
            transition: all 0.3s;
        }
        .nav-link:before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--neon-blue);
            transition: width 0.3s;
        }
        .nav-link:hover:before {
            width: 100%;
        }
        .nav-link.active {
            background: linear-gradient(45deg, var(--neon-purple), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 600;
        }
        .btn-neon {
            background: linear-gradient(45deg, var(--neon-purple), var(--neon-blue));
            border: none;
            color: #fff;
            padding: 12px 25px;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }
        .btn-neon:after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-neon:hover:after {
            left: 100%;
        }
        .welcome-section {
            background: linear-gradient(135deg, var(--neon-purple), var(--dark-bg));
            border-radius: 20px;
            padding: 4rem 2rem;
            margin: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        .welcome-section:after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, 
                rgba(0,247,255,0.1) 0%, 
                rgba(138,43,226,0.05) 50%, 
                transparent 100%
            );
            animation: rotate 20s linear infinite;
        }
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        .online { background: #00ff00; box-shadow: 0 0 10px #00ff00; }
        .offline { background: #ff0000; box-shadow: 0 0 10px #ff0000; }
        .section-title {
            position: relative;
            padding-left: 1.5rem;
            margin: 2rem 0;
        }
        .section-title:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 80%;
            width: 4px;
            background: linear-gradient(to bottom, var(--neon-purple), var(--neon-blue));
            border-radius: 2px;
        }
        .card-title {
             color: #fff;
        }
        .card-text {
             color: #fff;
        }
    </style>
</head>
<body>

    <header class="admin-header py-3">
        <div class="container">
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-auto">
                    <img src="logo.png" alt="Site Logo" class="logo" style="max-height: 60px;">
                </div>
                <div class="col-12 col-md-auto mt-3 mt-md-0">
                    <h4 class="mb-0 fw-bold neon-glow">Yönetim Paneli</h4>
                </div>
                <div class="col-12 col-md-auto mt-3 mt-md-0">
                    <span class="badge bg-secondary">
                        <i class="bi bi-clock me-2"></i> Son Giriş: <?php echo date('d.m.Y H:i'); ?>
                    </span>
                </div>
            </div>
            <!-- Ana Menü -->
            <nav class="nav-menu mt-4 d-flex justify-content-center">
                <a href="dashboard.php" class="nav-link active">
                    <i class="bi bi-house-door menu-icon"></i> Ana Sayfa
                </a>
                <a href="yoneticiler.php" class="nav-link">
                    <i class="bi bi-person menu-icon"></i> Yöneticiler
                </a>
                <a href="qr-tarat.php" class="nav-link">
                    <i class="bi bi-gear menu-icon"></i> QR KOD
                </a>
                <a href="menu.php" class="nav-link">
                    <i class="bi bi-bell menu-icon"></i> Menü
                </a>
                <!-- Çıkış için uygun bir logout dosyası veya işlemi ekleyin -->
                <a href="cikis.php" class="nav-link">
                    <i class="bi bi-box-arrow-right menu-icon"></i> Çıkış
                </a>
            </nav>
        </div>
    </header>

    <div class="container my-4">
        <section class="welcome-section position-relative">
            <div class="row justify-content-center">
                <div class="col-md-10 text-center">
                    <h1 class="display-4 fw-bold mb-3">Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['ad_soyad']); ?></h1>
                    <p class="lead opacity-75">Yönetim panelinden tüm işlemlerinizi gerçekleştirebilirsiniz.</p>
                </div>
            </div>
        </section>

        <!-- Yönetici İşlemleri -->
        <section class="mb-5">
            <h2 class="h4 section-title">Yönetici İşlemleri</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up card-icon"></i>
                            <h5 class="card-title mt-3">QR KOD OKUT!</h5>
                            <p class="card-text">Müşterilerin QR kodlarını okutun.</p>
                            <a href="qr-tarat.php" class="btn btn-info">
                                <i class="bi bi-qr-code"></i> Okut
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-people card-icon"></i>
                            <h5 class="card-title mt-3">Yöneticiler</h5>
                            <p class="card-text">Tüm yöneticileri görüntüle ve düzenle.</p>
                            <a href="yoneticiler.php" class="btn btn-primary">
                                <i class="bi bi-eye-fill"></i> Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-person-plus card-icon"></i>
                            <h5 class="card-title mt-3">Yönetici Ekle</h5>
                            <p class="card-text">Yeni yönetici hesabı oluştur.</p>
                            <a href="yonetici-ekle.php" class="btn btn-success">
                                <i class="bi bi-plus-circle-fill"></i> Ekle
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up card-icon"></i>
                            <h5 class="card-title mt-3">Grafikler</h5>
                            <p class="card-text">İstatistikleri ve grafikleri görüntüle.</p>
                            <a href="grafikler.php" class="btn btn-info">
                                <i class="bi bi-bar-chart-fill"></i> Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Menü İşlemleri -->
        <section class="mb-5">
            <h2 class="h4 section-title">Menü İşlemleri</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-list-ul card-icon"></i>
                            <h5 class="card-title mt-3">Menüyü Görüntüle</h5>
                            <p class="card-text">Mevcut menü öğelerini görüntüle ve düzenle.</p>
                            <a href="menu.php" class="btn btn-primary">
                                <i class="bi bi-eye-fill"></i> Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-plus-circle card-icon"></i>
                            <h5 class="card-title mt-3">Menüye Ekle</h5>
                            <p class="card-text">Yeni menü öğesi ekle.</p>
                            <a href="menu-ekle.php" class="btn btn-success">
                                <i class="bi bi-plus-circle-fill"></i> Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kampanya İşlemleri -->
        <section class="mb-5">
            <h2 class="h4 section-title">Kampanya İşlemleri</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-megaphone card-icon"></i>
                            <h5 class="card-title mt-3">Kampanyalar</h5>
                            <p class="card-text">Tüm kampanyaları görüntüle ve düzenle.</p>
                            <a href="kampanyalar.php" class="btn btn-primary">
                                <i class="bi bi-eye-fill"></i> Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-plus-square card-icon"></i>
                            <h5 class="card-title mt-3">Kampanya Ekle</h5>
                            <p class="card-text">Yeni kampanya oluştur.</p>
                            <a href="kampanya-ekle.php" class="btn btn-success">
                                <i class="bi bi-plus-circle-fill"></i> Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kupon İşlemleri -->
        <section class="mb-5">
            <h2 class="h4 section-title">Kupon İşlemleri</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-megaphone card-icon"></i>
                            <h5 class="card-title mt-3">Kuponlar</h5>
                            <p class="card-text">Tüm kuponları görüntüle ve düzenle.</p>
                            <a href="kuponlar.php" class="btn btn-primary">
                                <i class="bi bi-eye-fill"></i> Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-plus-square card-icon"></i>
                            <h5 class="card-title mt-3">Kupon Ekle</h5>
                            <p class="card-text">Yeni kupon oluştur.</p>
                            <a href="kupon-ekle.php" class="btn btn-success">
                                <i class="bi bi-plus-circle-fill"></i> Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kullanıcı İşlemleri -->
        <section class="mb-5">
            <h2 class="h4 section-title">Kullanıcı İşlemleri</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up card-icon"></i>
                            <h5 class="card-title mt-3">Kullanıcılar</h5>
                            <p class="card-text">Kullanıcıları görüntüle ve düzenle.</p>
                            <a href="kullanicilar.php" class="btn btn-info">
                                <i class="bi bi-eye-fill"></i> Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-envelope card-icon"></i>
                            <h5 class="card-title mt-3">Kullanıcı Ekle</h5>
                            <p class="card-text">Yeni kullanıcı oluştur.</p>
                            <a href="kullanici-ekle.php" class="btn btn-warning">
                                <i class="bi bi-pencil-fill"></i> Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Bootstrap Bundle JS (Popper dahil) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
