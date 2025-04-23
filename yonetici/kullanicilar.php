<?php
session_start();
require_once '../inc/db.php';
if (!isset($_SESSION['admin_giris'])) {
    header("Location: ../dashboard.php");
    exit;
}

// Kullanıcıları veritabanından çek
try {
    $sorgu = $conn->prepare("SELECT * FROM kullanicilar");
    $sorgu->execute();
    $kullanicilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

// Kullanıcı silme işlemi
if (isset($_GET['sil'])) {
    $silinecek_id = $_GET['sil'];
    try {
        $sil_sorgu = $conn->prepare("DELETE FROM kullanicilar WHERE user_id = :id");
        $sil_sorgu->execute(['id' => $silinecek_id]);
        header("Location: kullanicilar.php?mesaj=Kullanıcı başarıyla silindi&mesaj_tur=success");
        exit;
    } catch (PDOException $e) {
        header("Location: kullanicilar.php?mesaj=Hata: " . urlencode($e->getMessage()) . "&mesaj_tur=danger");
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
    <title>Kullanıcı Yönetimi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
    <style>
        .card-shadow { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
        .table-hover tbody tr:hover { background-color: rgba(0,123,255,0.05); }
        .sortable { cursor: pointer; position: relative; }
        .sortable::after {
            content: "↕";
            margin-left: 5px;
            opacity: 0.5;
            position: absolute;
        }
        .sortable.asc::after { content: "↑"; opacity: 1; }
        .sortable.desc::after { content: "↓"; opacity: 1; }
    </style>
</head>
<body class="bg-light">
      <?php include 'navbar.php'; ?>
    <div class="container py-5">
        <div class="card card-shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-users mr-2"></i>Kullanıcı Listesi</h4>
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

                <div class="mb-4">
                    <a href="kullanici-ekle.php" class="btn btn-success">
                        <i class="fas fa-user-plus mr-2"></i>Yeni Kullanıcı
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="siralanabilirTablo">
                        <thead class="thead-dark">
                            <tr>
                                <th class="sortable" data-column="id">ID</th>
                                <th class="sortable" data-column="ad">Ad Soyad</th>
                                <th class="sortable" data-column="eposta">E-posta</th>
                                <th class="sortable" data-column="telefon">Telefon</th>
                                <th class="sortable" data-column="yildiz">Yıldız</th>
                                <th class="sortable" data-column="yorum">Yorum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($kullanicilar as $kullanici): ?>
                                <tr>
                                    <td><?= htmlspecialchars($kullanici['user_id']) ?></td>
                                    <td><?= htmlspecialchars($kullanici['ad_soyad']) ?></td>
                                    <td><?= htmlspecialchars($kullanici['eposta']) ?></td>
                                    <td><?= htmlspecialchars($kullanici['telefon_no']) ?></td>
                                    <td><?= htmlspecialchars($kullanici['yildiz_sayisi']) ?></td>
                                    <td><?= htmlspecialchars($kullanici['yorum_sayisi']) ?></td>
                                    <td>
                                        <a href="kullanici-duzenle.php?id=<?= $kullanici['user_id'] ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="kullanicilar.php?sil=<?= $kullanici['user_id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')"
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sıralama Fonksiyonu
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                const table = document.querySelector('#siralanabilirTablo');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const columnIndex = header.cellIndex;
                const isAsc = header.classList.contains('asc');
                
                // Sıralama yönünü değiştir
                document.querySelectorAll('.sortable').forEach(h => h.classList.remove('asc', 'desc'));
                header.classList.add(isAsc ? 'desc' : 'asc');

                // Sıralama işlemi
                rows.sort((a, b) => {
                    const aValue = a.cells[columnIndex].textContent.trim();
                    const bValue = b.cells[columnIndex].textContent.trim();
                    
                    // Sayısal sıralama için kontrol
                    if (!isNaN(aValue) && !isNaN(bValue)) {
                        return isAsc ? bValue - aValue : aValue - bValue;
                    }
                    
                    // Metinsel sıralama
                    return isAsc ? 
                        bValue.localeCompare(aValue, 'tr') : 
                        aValue.localeCompare(bValue, 'tr');
                });

                // Tabloyu yeniden oluştur
                rows.forEach(row => tbody.appendChild(row));
            });
        });
    </script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>