<?php
session_start();
require_once '../inc/db.php';

if (!isset($_SESSION['admin_giris'])) {
    header("Location: dashboard.php");
    exit;
}

$mesaj = '';
$mesaj_tur = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $baslik = trim($_POST['baslik']);
    $tur = trim($_POST['tur']);
    $on_aciklama = trim($_POST['on_aciklama']);
    $icerik = trim($_POST['icerik']);
    $on_resim = $_FILES['on_resim'];

    if (!in_array($tur, ['Duyuru', 'Kampanya'])) {
        $mesaj = "Geçersiz tür seçimi!";
        $mesaj_tur = 'danger';
    } elseif (empty($baslik) || empty($on_aciklama) || empty($icerik) || !$on_resim['name']) {
        $mesaj = "Tüm alanları doldurunuz!";
        $mesaj_tur = 'danger';
    } else {
        try {
            $hedef_klasor = "uploads/";
            $dosya_adi = time() . "_" . uniqid() . "_" . basename($on_resim['name']);
            $hedef_dosya = $hedef_klasor . $dosya_adi;
            
            // Resim format ve boyut kontrolü
            $izinli_formatlar = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($on_resim['type'], $izinli_formatlar)) {
                throw new Exception("Sadece JPG, PNG ve GIF formatları kabul edilmektedir!");
            }
            
            if ($on_resim['size'] > 2 * 1024 * 1024) {
                throw new Exception("Dosya boyutu 2MB'dan küçük olmalıdır!");
            }

            if (!is_dir($hedef_klasor)) {
                mkdir($hedef_klasor, 0777, true);
            }

            if (!move_uploaded_file($on_resim['tmp_name'], $hedef_dosya)) {
                throw new Exception("Dosya yükleme başarısız!");
            }

            $conn->beginTransaction();

            $sql = "INSERT INTO kampanyalar (baslik, tur, on_aciklama, icerik, on_resmi, olusturma_tarihi) 
                    VALUES (:baslik, :tur, :on_aciklama, :icerik, :on_resmi, NOW())";
            $stmt = $conn->prepare($sql);
            
            $stmt->bindValue(':baslik', $baslik, PDO::PARAM_STR);
            $stmt->bindValue(':tur', $tur, PDO::PARAM_STR);
            $stmt->bindValue(':on_aciklama', $on_aciklama, PDO::PARAM_STR);
            $stmt->bindValue(':icerik', $icerik, PDO::PARAM_STR);
            $stmt->bindValue(':on_resmi', $dosya_adi, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $conn->commit();
                $mesaj = "Kampanya başarıyla eklendi!";
                $mesaj_tur = 'success';
                
                // Form verilerini temizle
                $baslik = $tur = $on_aciklama = $icerik = '';
            } else {
                throw new Exception("Veritabanı hatası!");
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $mesaj = "Hata: " . $e->getMessage();
            $mesaj_tur = 'danger';
            
            // Yüklenen dosyayı sil
            if (file_exists($hedef_dosya)) {
                unlink($hedef_dosya);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kampanya Ekle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 
    
    
    <style>
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            display: none;
            margin-top: 10px;
        }
        .char-counter {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .form-floating textarea {
            height: auto !important;
        }
        .drop-zone {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .drop-zone:hover {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }
        .drop-zone i {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>
    
    <div class="loading">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h2 class="card-title h4 mb-0">
                            <i class="fas fa-bullhorn me-2"></i>Kampanya Ekle
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <?php if($mesaj): ?>
                        <div class="alert alert-<?php echo $mesaj_tur; ?> alert-dismissible fade show" role="alert">
                            <?php echo $mesaj; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate id="kampanyaForm">
                            <div class="mb-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="baslik" name="baslik" 
                                           placeholder="Başlık" required maxlength="100"
                                           value="<?php echo isset($baslik) ? htmlspecialchars($baslik) : ''; ?>">
                                    <label for="baslik">Başlık</label>
                                    <div class="invalid-feedback">Başlık alanı zorunludur.</div>
                                    <div class="char-counter mt-1">0/100</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="tur" class="form-label">Tür</label>
                                <select class="form-select" id="tur" name="tur" required>
                                    <option value="">Seçiniz</option>
                                    <option value="Duyuru" <?php echo (isset($tur) && $tur == 'Duyuru') ? 'selected' : ''; ?>>Duyuru</option>
                                    <option value="Kampanya" <?php echo (isset($tur) && $tur == 'Kampanya') ? 'selected' : ''; ?>>Kampanya</option>
                                </select>
                                <div class="invalid-feedback">Lütfen bir tür seçiniz.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Ön Resim</label>
                                <div class="drop-zone">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p class="mb-0">Resim yüklemek için tıklayın veya sürükleyin</p>
                                    <input type="file" class="d-none" id="on_resim" name="on_resim" accept="image/*" required>
                                </div>
                                <img id="imagePreview" class="preview-image img-thumbnail">
                                <div class="invalid-feedback">Lütfen bir resim seçiniz.</div>
                                <small class="text-muted">Maximum boyut: 2MB, İzin verilen formatlar: JPG, PNG, GIF</small>
                            </div>

                            <div class="mb-4">
                                <div class="form-floating">
                                    <textarea class="form-control" id="on_aciklama" name="on_aciklama" 
                                              placeholder="Ön Açıklama" style="height: 100px" required maxlength="250"
                                              ><?php echo isset($on_aciklama) ? htmlspecialchars($on_aciklama) : ''; ?></textarea>
                                    <label for="on_aciklama">Ön Açıklama</label>
                                    <div class="invalid-feedback">Ön açıklama alanı zorunludur.</div>
                                    <div class="char-counter mt-1">0/250</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-floating">
                                    <textarea class="form-control" id="icerik" name="icerik" 
                                              placeholder="İçerik" style="height: 200px" required
                                              ><?php echo isset($icerik) ? htmlspecialchars($icerik) : ''; ?></textarea>
                                    <label for="icerik">İçerik</label>
                                    <div class="invalid-feedback">İçerik alanı zorunludur.</div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Kampanya Ekle
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
            const form = document.getElementById('kampanyaForm');
            const dropZone = document.querySelector('.drop-zone');
            const fileInput = document.getElementById('on_resim');
            const imagePreview = document.getElementById('imagePreview');
            const loading = document.querySelector('.loading');

            // Karakter sayacı
            document.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(element => {
                const counter = element.parentElement.querySelector('.char-counter');
                if (counter) {
                    const updateCounter = () => {
                        counter.textContent = `${element.value.length}/${element.maxLength}`;
                    };
                    element.addEventListener('input', updateCounter);
                    updateCounter();
                }
            });

            // Sürükle-bırak işlemleri
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#0d6efd';
                dropZone.style.backgroundColor = 'rgba(13, 110, 253, 0.1)';
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.style.borderColor = '#ccc';
                dropZone.style.backgroundColor = '';
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#ccc';
                dropZone.style.backgroundColor = '';
                
                const files = e.dataTransfer.files;
                if (files.length) {
                    handleFile(files[0]);
                }
            });

            dropZone.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length) {
                    handleFile(e.target.files[0]);
                }
            });

            function handleFile(file) {
                if (!file.type.startsWith('image/')) {
                    alert('Lütfen sadece resim dosyası yükleyin!');
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('Dosya boyutu 2MB\'dan küçük olmalıdır!');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    dropZone.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            // Form doğrulama
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    loading.style.display = 'flex';
                }
                form.classList.add('was-validated');
            });

            // Resmi kaldır
            imagePreview.addEventListener('click', () => {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
                dropZone.style.display = 'block';
                fileInput.value = '';
            });
        });
    </script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>