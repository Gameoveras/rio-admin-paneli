<?php
session_start();
require_once '../inc/db.php';
if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}

// Filtreleme için kullanılan parametre
$filtre_kullanildi_mi = $_GET['filtre_kullanildi_mi'] ?? null;

// Kuponları çekme sorgusu
$sql = "SELECT * FROM kuponlar";
if ($filtre_kullanildi_mi !== null) {
    $sql .= " WHERE kullanildi_mi = :kullanildi_mi";
}
$sorgu = $conn->prepare($sql);

if ($filtre_kullanildi_mi !== null) {
    $sorgu->bindValue(':kullanildi_mi', $filtre_kullanildi_mi, PDO::PARAM_BOOL);
}
$sorgu->execute();
$kuponlar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

// Tekil silme işlemi
if (isset($_GET['sil'])) {
    $silinecek_id = $_GET['sil'];
    $sil_sorgu = $conn->prepare("DELETE FROM kuponlar WHERE id = :id");
    $sil_sorgu->execute(['id' => $silinecek_id]);
    header("Location: kuponlar.php?mesaj=Kupon başarıyla silindi.&mesaj_tur=success");
    exit;
}

// Toplu silme işlemi
if (isset($_POST['toplu_sil'])) {
    try {
        $conn->beginTransaction();
        $sil_sorgu = $conn->prepare("DELETE FROM kuponlar WHERE kullanildi_mi = 1");
        $sil_sorgu->execute();
        $conn->commit();
        header("Location: kuponlar.php?mesaj=Kullanılmış kuponlar başarıyla silindi.&mesaj_tur=success");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: kuponlar.php?mesaj=Hata: " . $e->getMessage() . "&mesaj_tur=danger");
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
    <title>Kupon Yönetimi</title>
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
                <h4 class="mb-0"><i class="fas fa-ticket-alt mr-2"></i>Kupon Listesi</h4>
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

                <!-- Filtreleme Formu -->
                <form method="GET" class="mb-4">
                    <div class="form-row align-items-center">
                        <div class="col-auto">
                            <label class="sr-only" for="filtre_kullanildi_mi">Kullanım Durumu</label>
                            <select class="form-control" id="filtre_kullanildi_mi" name="filtre_kullanildi_mi">
                                <option value="">Tüm Kuponlar</option>
                                <option value="1" <?= $filtre_kullanildi_mi === '1' ? 'selected' : '' ?>>Kullanılmış Kuponlar</option>
                                <option value="0" <?= $filtre_kullanildi_mi === '0' ? 'selected' : '' ?>>Kullanılmamış Kuponlar</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Filtrele</button>
                        </div>
                    </div>
                </form>

                <!-- Toplu Silme Butonu -->
                <form method="POST" class="mb-4" onsubmit="return confirm('Kullanılmış tüm kuponları silmek istediğinize emin misiniz?');">
                    <button type="submit" name="toplu_sil" class="btn btn-danger">
                        <i class="fas fa-trash-alt mr-2"></i>Kullanılmış Kuponları Toplu Sil
                    </button>
                </form>

                <!-- Kupon Listesi -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>#ID</th>
                                <th>Kullanıcı ID</th>
                                <th>Kupon Kodu</th>
                                <th>Kazanılan Yıldız</th>
                                <th>Kullanım Durumu</th>
                                <th>Kullanılma Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kuponlar as $kupon): ?>
                                <tr>
                                    <td><?= $kupon['id'] ?></td>
                                    <td><?= $kupon['user_id'] ?></td>
                                    <td><?= $kupon['kupon_kodu'] ?></td>
                                    <td><?= $kupon['kazanilan_yildiz'] ?></td>
                                    <td><?= $kupon['kullanildi_mi'] ? 'Kullanıldı' : 'Kullanılmadı' ?></td>
                                    <td><?= $kupon['kullanilma_tarihi'] ?? '-' ?></td>
                                    <td>
                                        <a href="kuponlar.php?sil=<?= $kupon['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bu kuponu silmek istediğinize emin misiniz?')"
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