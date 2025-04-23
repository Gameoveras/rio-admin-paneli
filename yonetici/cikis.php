<?php
// Oturum yönetimi
session_start();

// Güvenlik başlıkları
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Çıkış işlemleri
function secureLogout() {
    // Oturum değişkenlerini temizle
    $_SESSION = array();

    // Çerezleri geçersiz kıl
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), 
            '', 
            time() - 86400, // 1 gün öncesine ayarla
            $params["path"], 
            $params["domain"],
            $params["secure"], 
            $params["httponly"]
        );
    }

    // Oturumu yok et
    session_destroy();

    // CSRF token temizleme
    if (isset($_COOKIE['csrf_token'])) {
        setcookie('csrf_token', '', time() - 86400, '/', '', true, true);
    }

    // Cache kontrolü
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}

// Çıkış işlemini gerçekleştir
secureLogout();

// Çıkış sonrası yönlendirme
$redirect_url = "../index.php?logout=success";
header("Refresh: 3; url=$redirect_url");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çıkış Yapılıyor - Moods Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --neon-purple: #8a2be2;
            --neon-blue: #00f7ff;
            --dark-bg: #0a0a1a;
        }

        body {
            background: var(--dark-bg);
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .logout-container {
            text-align: center;
            max-width: 600px;
            padding: 2rem;
            background: rgba(19, 19, 43, 0.9);
            border-radius: 15px;
            border: 1px solid rgba(138,43,226,0.3);
            box-shadow: 0 0 30px rgba(138,43,226,0.2);
        }

        .logout-icon {
            font-size: 4rem;
            background: linear-gradient(45deg, var(--neon-purple), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .logout-message {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .progress-bar {
            height: 4px;
            background: rgba(255,255,255,0.1);
            border-radius: 2px;
            overflow: hidden;
            margin: 0 auto;
            width: 80%;
        }

        .progress-bar-inner {
            height: 100%;
            width: 0;
            background: linear-gradient(90deg, var(--neon-purple), var(--neon-blue));
            animation: progress 3s linear forwards;
        }

        @keyframes progress {
            0% { width: 0; }
            100% { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h1 class="mb-4">Güvenli Çıkış Yapılıyor</h1>
        <div class="logout-message">
            <p>Oturumunuz güvenli bir şekilde sonlandırılıyor...</p>
            <p>Lütfen bekleyiniz, giriş sayfasına yönlendiriliyorsunuz.</p>
        </div>
        <div class="progress-bar">
            <div class="progress-bar-inner"></div>
        </div>
    </div>

    <script>
        // Tarayıcı geçmişini temizle
        history.replaceState(null, document.title, location.href);
    </script>
</body>
</html>