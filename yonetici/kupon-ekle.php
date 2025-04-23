<?php
session_start();
require_once '../inc/db.php';

if (!isset($_SESSION['admin_giris'])) {
    header("Location: dashboard.php");
    exit;
}

$mesaj = '';
$mesaj_tur = '';

// Kullanıcıları getir
try {
    $stmt = $conn->query("SELECT user_id, ad_soyad, eposta FROM kullanicilar ORDER BY ad_soyad");
    $kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mesaj = "Kullanıcılar yüklenirken hata oluştu: " . $e->getMessage();
    $mesaj_tur = 'danger';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $kazanilan_yildiz = filter_input(INPUT_POST, 'kazanilan_yildiz', FILTER_VALIDATE_INT);
    $kupon_kodu = trim($_POST['kupon_kodu']);

    if (!$user_id || !$kazanilan_yildiz || empty($kupon_kodu)) {
        $mesaj = "Tüm alanları doldurunuz!";
        $mesaj_tur = 'danger';
    } elseif ($kazanilan_yildiz < 1) {
        $mesaj = "Kazanılan yıldız sayısı pozitif olmalıdır!";
        $mesaj_tur = 'danger';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO kuponlar (user_id, kupon_kodu, kazanilan_yildiz) VALUES (:user_id, :kupon_kodu, :kazanilan_yildiz)");
            
            if ($stmt->execute([
                ':user_id' => $user_id,
                ':kupon_kodu' => $kupon_kodu,
                ':kazanilan_yildiz' => $kazanilan_yildiz
            ])) {
                $mesaj = "Kupon başarıyla oluşturuldu: " . $kupon_kodu;
                $mesaj_tur = 'success';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $mesaj = "Bu kupon kodu zaten kullanımda!";
            } else {
                $mesaj = "Hata: " . $e->getMessage();
            }
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
    <title>Kupon Oluştur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>
<body>
      <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="card-title h4 mb-0">
                            <i class="fas fa-ticket-alt me-2"></i>Kupon Oluştur
                        </h2>
                    </div>
                    <div class="card-body">
                        <?php if($mesaj): ?>
                        <div class="alert alert-<?php echo $mesaj_tur; ?> alert-dismissible fade show" role="alert">
                            <?php echo $mesaj; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" id="kuponForm" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Kullanıcı</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Kullanıcı seçin</option>
                                    <?php foreach ($kullanicilar as $kullanici): ?>
                                    <option value="<?php echo htmlspecialchars($kullanici['user_id']); ?>">
                                        <?php echo htmlspecialchars($kullanici['ad_soyad'] . ' '  . ' (' . $kullanici['eposta'] . ')'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Lütfen bir kullanıcı seçin.</div>
                            </div>

                            <div class="mb-3">
                                <label for="kupon_kodu" class="form-label">Kupon Kodu</label>
                                <input type="text" class="form-control" id="kupon_kodu" name="kupon_kodu" 
                                       required maxlength="50" pattern="[A-Za-z0-9-_]+" 
                                       title="Sadece harf, rakam, tire ve alt çizgi kullanabilirsiniz">
                                <div class="invalid-feedback">
                                    Lütfen geçerli bir kupon kodu girin (sadece harf, rakam, tire ve alt çizgi).
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="kazanilan_yildiz" class="form-label">Kazanılacak Yıldız Sayısı</label>
                                <input type="number" class="form-control" id="kazanilan_yildiz" name="kazanilan_yildiz" 
                                       min="1" required>
                                <div class="invalid-feedback">Lütfen geçerli bir yıldız sayısı girin.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus-circle me-2"></i>Kupon Oluştur
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('kuponForm');
            const kuponKoduInput = document.getElementById('kupon_kodu');

            // Form doğrulama
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });

            // Kupon kodu büyük harfe çevirme
            kuponKoduInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>