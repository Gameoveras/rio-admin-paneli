<?php
session_start();
require_once '../inc/db.php';
if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}

$sorgu = $conn->prepare("SELECT * FROM kampanyalar");
$sorgu->execute();
$kampanyalar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['sil'])) {
    $silinecek_id = $_GET['sil'];
    $sil_sorgu = $conn->prepare("DELETE FROM kampanyalar WHERE id = :id");
    $sil_sorgu->execute(['id' => $silinecek_id]);
    header("Location: kampanyalar.php?mesaj=Kampanya başarıyla silindi.&mesaj_tur=success");
    exit;
}

$mesaj = $_GET['mesaj'] ?? '';
$mesaj_tur = $_GET['mesaj_tur'] ?? '';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kampanya Yönetimi</title>
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
    </style>
</head>
<body class="bg-light">
    
    <?php include 'navbar.php'; ?>
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-bullhorn mr-2"></i>Kampanya Listesi</h4>
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
                    <a href="kampanya-ekle.php" class="btn btn-success">
                        <i class="fas fa-plus-circle mr-2"></i>Yeni Kampanya
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#ID</th>
                                <th>Ön Resim</th>
                                <th>Başlık</th>
                                <th>Ön Açıklama</th>
                                <th>İçerik</th>
                                <th>Tür</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kampanyalar as $kampanya): ?>
                                <tr>
                                    <td><?= $kampanya['id'] ?></td>
<td>
    <img src="https://asebay.com.tr/moods/yonetici/uploads/<?= htmlspecialchars($kampanya['on_resmi']) ?>" alt="Ön Resim" style="width:100px; height:auto;">
</td>
                                    <td><?= $kampanya['baslik'] ?></td>
                                    <td><?= $kampanya['on_aciklama'] ?></td>
                                    <td><?= $kampanya['icerik'] ?></td>
                                    <td><?= $kampanya['tur'] ?></td>
                                    <td>
                                        <a href="kampanya_duzenle.php?id=<?= $kampanya['id'] ?>" 
                                           class="btn btn-sm btn-warning"
                                           title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="kampanyalar.php?sil=<?= $kampanya['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bu kampanyayı silmek istediğinize emin misiniz?')"
                                           title="Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
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