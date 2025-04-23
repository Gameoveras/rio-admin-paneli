<?php
session_start();
require_once '../inc/db.php';
if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}



$sorgu = $conn->prepare("SELECT * FROM yoneticiler");
$sorgu->execute();
$yoneticiler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['sil'])) {
    $silinecek_id = $_GET['sil'];
    if ($silinecek_id != 1) {
        $sil_sorgu = $conn->prepare("DELETE FROM yoneticiler WHERE admin_id = :id");
        $sil_sorgu->execute(['id' => $silinecek_id]);
        header("Location: yoneticiler.php?mesaj=Yönetici başarıyla silindi.&mesaj_tur=success");
        exit;
    } else {
        header("Location: yoneticiler.php?mesaj=Bu yönetici silinemez.&mesaj_tur=danger");
        exit;
    }
}

$mesaj = $_GET['mesaj'] ?? '';
$mesaj_tur = $_GET['mesaj_tur'] ?? '';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetici Yönetimi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 
    <style>
        .card-shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .disabled-operation {
            opacity: 0.5;
            cursor: not-allowed !important;
        }
    </style>
</head>
<body class="bg-light">
      <?php include 'navbar.php'; ?>
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-users-cog mr-2"></i>Yönetici Listesi</h4>
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

                <div class="d-flex justify-content-between mb-4">
                    <a href="yonetici-ekle.php" class="btn btn-success">
                        <i class="fas fa-plus-circle mr-2"></i>Yeni Yönetici
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#ID</th>
                                <th>Kullanıcı Adı</th>
                                <th>Ad Soyad</th>
                                <th>E-posta</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($yoneticiler as $yonetici): ?>
                                <tr>
                                    <td><?= $yonetici['admin_id'] ?></td>
                                    <td><?= $yonetici['kullanici_adi'] ?></td>
                                    <td><?= $yonetici['ad_soyad'] ?></td>
                                    <td><?= $yonetici['eposta'] ?></td>
                                    <td>
                                        <?php if ($yonetici['admin_id'] == 1): ?>
                                            <span class="text-muted" title="Bu yönetici düzenlenemez">
                                                <i class="fas fa-lock mr-2"></i>Korumalı
                                            </span>
                                        <?php else: ?>
                                            <a href="yonetici_duzenle.php?id=<?= $yonetici['admin_id'] ?>" 
                                               class="btn btn-sm btn-warning"
                                               title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="yoneticiler.php?sil=<?= $yonetici['admin_id'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Bu yöneticiyi silmek istediğinize emin misiniz?')"
                                               title="Sil">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>