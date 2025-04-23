<?php
session_start();
require_once '../inc/db.php';

if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}


$sorgu = $conn->prepare("SELECT * FROM menu");
$sorgu->execute();
$menu_items = $sorgu->fetchAll(PDO::FETCH_ASSOC);

// Arama ve Filtreleme Parametreleri
$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? 'all';

// SQL Sorgusunu Dinamik Olarak Oluştur
$sql = "SELECT * FROM menu WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (ad LIKE :search OR aciklama LIKE :search)";
    $params['search'] = "%$search%";
}

if ($kategori != 'all') {
    $sql .= " AND kategori = :kategori";
    $params['kategori'] = $kategori;
}

$sorgu = $conn->prepare($sql);
$sorgu->execute($params);
$menu_items = $sorgu->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['sil'])) {
    $silinecek_id = $_GET['sil'];
    $sil_sorgu = $conn->prepare("DELETE FROM menu WHERE id = :id");
    $sil_sorgu->execute(['id' => $silinecek_id]);
    header("Location: menu.php?mesaj=Menü öğesi başarıyla silindi.&mesaj_tur=success");
    exit;
}

$mesaj = $_GET['mesaj'] ?? '';
$mesaj_tur = $_GET['mesaj_tur'] ?? '';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Menü Yönetimi</title>
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
                <h4 class="mb-0"><i class="fas fa-utensils mr-2"></i>Menü Yönetimi</h4>
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
                    <a href="menu-ekle.php" class="btn btn-success">
                        <i class="fas fa-plus-circle mr-2"></i>Yeni Menü Öğesi Ekle
                    </a>
                </div>
                
                 <div class="card-body">
                <!-- Arama ve Filtreleme Formu -->
                <form class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Ürün ara..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="kategori" class="form-control">
                                <option value="all" <?= $kategori == 'all' ? 'selected' : '' ?>>Tüm Kategoriler</option>
                                <option value="İçecek" <?= $kategori == 'İçecek' ? 'selected' : '' ?>>İçecek</option>
                                <option value="Yiyecek" <?= $kategori == 'Yiyecek' ? 'selected' : '' ?>>Yiyecek</option>
                                <option value="Tatlı" <?= $kategori == 'Tatlı' ? 'selected' : '' ?>>Tatlı</option>
                                <option value="Atıştırmalık" <?= $kategori == 'Atıştırmalık' ? 'selected' : '' ?>>Atıştırmalık</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Filtrele
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#ID</th>
                                <th>Kategori</th>
                                <th>Ad</th>
                                <th>Kalori</th>
                                <th>Açıklama</th>
                                <th>Fiyat</th>
                                <th>Öne Çıkan</th>
                                <th>Resim</th>
                                <th>İçindekiler</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menu_items as $item): ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= $item['kategori'] ?></td>
                                    <td><?= $item['ad'] ?></td>
                                    <td><?= $item['kalori'] ?></td>
                                    <td><?= $item['aciklama'] ?></td>
                                    <td><?= number_format($item['fiyat'], 2) ?> TL</td>
                                    <td><?= $item['one_cikan'] ? 'Evet' : 'Hayır' ?></td>
                                    <td>
                                        <?php if ($item['resim']): ?>
                                            <img src="https://asebay.com.tr/moods/yonetici/<?= $item['resim'] ?>" alt="<?= $item['ad'] ?>" style="max-width: 100px;">
                                        <?php else: ?>
                                            Resim Yok
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $item['içindekiler'] ?></td>
                                    <td>
                                        <a href="menu-duzenle.php?id=<?= $item['id'] ?>" 
                                           class="btn btn-sm btn-warning"
                                           title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="menu.php?sil=<?= $item['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bu menü öğesini silmek istediğinize emin misiniz?')"
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