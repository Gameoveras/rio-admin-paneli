<?php
session_start();
require_once '../inc/db.php';

if (!isset($_SESSION['admin_giris'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $response = ['success' => false, 'message' => ''];
        
        $conn->beginTransaction();
        
        for ($i = 0; $i < count($_POST["ad"]); $i++) {
            $kategori = $_POST["kategori"][$i];
            $ad = $_POST["ad"][$i];
            $aciklama = $_POST["aciklama"][$i];
            $fiyat = $_POST["fiyat"][$i];
            $kalori = $_POST["kalori"][$i];
            $one_cikan = isset($_POST["one_cikan"][$i]) ? 1 : 0;
            
            // Base64 resim verisi kontrolü
            $resim_yolu = NULL;
            if (!empty($_POST["resim_data"][$i])) {
                $img_data = $_POST["resim_data"][$i];
                $img_data = str_replace('data:image/png;base64,', '', $img_data);
                $img_data = str_replace('data:image/jpeg;base64,', '', $img_data);
                $img_data = str_replace(' ', '+', $img_data);
                $img_data = base64_decode($img_data);
                
                $upload_dir = "uploads/";
                $dosya_adi = uniqid() . ".jpg";
                $resim_yolu = $upload_dir . $dosya_adi;
                
                file_put_contents($resim_yolu, $img_data);
            }
            
            $sql = "INSERT INTO menu (kategori, ad, aciklama, fiyat, kalori, resim, one_cikan) 
                    VALUES (:kategori, :ad, :aciklama, :fiyat, :kalori, :resim, :one_cikan)";
            $stmt = $conn->prepare($sql);
            
            $stmt->bindParam(":kategori", $kategori);
            $stmt->bindParam(":ad", $ad);
            $stmt->bindParam(":aciklama", $aciklama);
            $stmt->bindParam(":fiyat", $fiyat);
            $stmt->bindParam(":kalori", $kalori);
            $stmt->bindParam(":resim", $resim_yolu);
            $stmt->bindParam(":one_cikan", $one_cikan, PDO::PARAM_INT);
            
            $stmt->execute();
        }
        
        $conn->commit();
        $response['success'] = true;
        $response['message'] = 'Ürünler başarıyla eklendi!';
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode($response);
            exit;
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $response['message'] = "Hata: " . $e->getMessage();
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode($response);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menü Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
    <style>
        .product-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .remove-product {
            color: #dc3545;
            cursor: pointer;
        }
        .preview-image {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
        }
        .drop-zone {
            width: 100%;
            height: 150px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .drop-zone:hover, .drop-zone.dragover {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
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
        .progress-container {
            display: none;
            margin-top: 10px;
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

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Menü Yönetimi</h2>
                    </div>
                    <div class="card-body">
                        <form action="menu-ekle.php" method="POST" enctype="multipart/form-data" id="menuForm">
                            <div id="products-container">
                                <div class="product-form">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4>Ürün #1</h4>
                                        <i class="fas fa-times remove-product"></i>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Kategori:</label>
                                            <select name="kategori[]" class="form-select" required>
                                                <option value="İçecek">İçecek</option>
                                                <option value="Yiyecek">Yiyecek</option>
                                                <option value="Tatlı">Tatlı</option>
                                                <option value="Atıştırmalık">Atıştırmalık</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Ad:</label>
                                            <input type="text" name="ad[]" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Açıklama:</label>
                                        <textarea name="aciklama[]" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Fiyat (TL):</label>
                                            <input type="number" step="0.01" name="fiyat[]" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Kalori:</label>
                                            <input type="number" name="kalori[]" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Resim:</label>
                                            <div class="drop-zone">
                                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                                <span>Sürükle bırak veya tıkla</span>
                                                <input type="hidden" name="resim_data[]" class="image-data">
                                            </div>
                                            <img class="preview-image d-none">
                                            <div class="progress-container">
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="one_cikan[]" class="form-check-input" value="1">
                                            <label class="form-check-label">Öne Çıkar</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" id="addProduct" class="btn btn-success me-2">
                                    <i class="fas fa-plus"></i> Yeni Ürün Ekle
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Tümünü Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('products-container');
            const addButton = document.getElementById('addProduct');
            const form = document.getElementById('menuForm');
            const loading = document.querySelector('.loading');
            let productCount = 1;

            // Sürükle-bırak ve dosya yükleme işlemleri
            function handleDragDrop(dropZone) {
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('dragover');
                });

                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('dragover');
                });

                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    if (files.length) {
                        handleFile(files[0], dropZone);
                    }
                });

                dropZone.addEventListener('click', () => {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/*';
                    input.onchange = (e) => {
                        if (e.target.files.length) {
                            handleFile(e.target.files[0], dropZone);
                        }
                    };
                    input.click();
                });
            }

            function handleFile(file, dropZone) {
                if (!file.type.startsWith('image/')) {
                    alert('Lütfen sadece resim dosyası yükleyin!');
                    return;
                }

                const reader = new FileReader();
                const preview = dropZone.parentElement.querySelector('.preview-image');
                const progressContainer = dropZone.parentElement.querySelector('.progress-container');
                const progressBar = progressContainer.querySelector('.progress-bar');
                const imageData = dropZone.parentElement.querySelector('.image-data');

                progressContainer.style.display = 'block';
                
                reader.onprogress = (e) => {
                    if (e.lengthComputable) {
                        const percentLoaded = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentLoaded + '%';
                    }
                };

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    dropZone.style.display = 'none';
                    imageData.value = e.target.result;
                    progressContainer.style.display = 'none';
                };

                reader.readAsDataURL(file);
            }

            // Yeni ürün ekleme
            addButton.addEventListener('click', function() {
                productCount++;
                const template = document.querySelector('.product-form').cloneNode(true);
                template.querySelector('h4').textContent = `Ürün #${productCount}`;
                
                // Form elemanlarını temizle
                template.querySelectorAll('input:not([type="checkbox"])').forEach(input => input.value = '');
                template.querySelectorAll('textarea').forEach(textarea => textarea.value = '');
                template.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);
                template.querySelector('.preview-image').classList.add('d-none');
                template.querySelector('.preview-image').src = '';
                template.querySelector('.drop-zone').style.display = 'flex';
                
                // Yeni drop zone için event listener'ları ekle
                handleDragDrop(template.querySelector('.drop-zone'));
                
                container.appendChild(template);
            });

            // İlk drop zone için event listener'ları ekle
            document.querySelectorAll('.drop-zone').forEach(dropZone => {
                handleDragDrop(dropZone);
            });

            // Ürün silme
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-product')) {
                    const productForms = document.querySelectorAll('.product-form');
                    if (productForms.length > 1) {
                        e.target.closest('.product-form').remove();
                        document.querySelectorAll('.product-form h4').forEach((header, index) => {
                            header.textContent = `Ürün #${index + 1}`;
                        });
                        productCount = document.querySelectorAll('.product-form').length;
                    } else {
                        alert('En az bir ürün formu bulunmalıdır!');
                    }
                }
            });

            // AJAX form gönderimi
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                loading.style.display = 'flex';

                const formData = new FormData(form);
                
                fetch('menu-ekle.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';
                    
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        
                        // Reset the form
                        const firstProduct = document.querySelector('.product-form');
                        const productsContainer = document.getElementById('products-container');
                        
                        // Clear all products except the first one
                        while (productsContainer.children.length > 1) {
                            productsContainer.removeChild(productsContainer.lastChild);
                        }
                        
                        // Reset the first product form
                        firstProduct.querySelectorAll('input:not([type="checkbox"])').forEach(input => input.value = '');
                        firstProduct.querySelectorAll('textarea').forEach(textarea => textarea.value = '');
                        firstProduct.querySelectorAll('input[type="checkbox"]').forEach(checkbox => checkbox.checked = false);
                        firstProduct.querySelector('.preview-image').classList.add('d-none');
                        firstProduct.querySelector('.preview-image').src = '';
                        firstProduct.querySelector('.drop-zone').style.display = 'flex';
                        firstProduct.querySelector('.image-data').value = '';
                        
                        // Reset product counter
                        productCount = 1;
                    } else {
                        // Show error message
                        alert(data.message || 'Bir hata oluştu!');
                    }
                })
                .catch(error => {
                    loading.style.display = 'none';
                    alert('Bir hata oluştu: ' + error.message);
                });
            });
        });
    </script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>