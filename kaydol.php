<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
require_once './inc/db.php';
require_once './phpqrcode/qrlib.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed', 405);
    }
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    // Gerekli alan kontrolü - telefon kaldırıldı
    $requiredFields = ['ad', 'soyad', 'email', 'sifre'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception('Eksik zorunlu bilgi gönderildi', 400);
        }
    }
    // Veri temizleme
    $ad = filter_var(trim($data['ad']), FILTER_SANITIZE_STRING);
    $soyad = filter_var(trim($data['soyad']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $sifre = trim($data['sifre']);
    // Validasyon
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Geçersiz email adresi', 400);
    }
    if (strlen($sifre) < 6) {
        throw new Exception('Şifre en az 6 karakter olmalıdır', 400);
    }
    // Veritabanı kontrolü - sadece email kontrolü yapılıyor
    $conn->beginTransaction();
    $stmt = $conn->prepare("SELECT user_id FROM kullanicilar WHERE eposta = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('Bu email adresi zaten kayıtlı', 400);
    }
    // Kullanıcı oluşturma - telefon alanı kaldırıldı
    $sifreHash = password_hash($sifre, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO kullanicilar (ad_soyad, eposta, parola) VALUES (?, ?, ?)");
    $stmt->execute(["$ad $soyad", $email, $sifreHash]);
    $userId = $conn->lastInsertId();
    // QR Kodu oluşturma
    $qrDir = $_SERVER['DOCUMENT_ROOT'] . '/moods/qr_codes/'; // Mutlak yol
    if (!is_dir($qrDir) && !mkdir($qrDir, 0755, true) && !is_dir($qrDir)) {
        throw new Exception('QR dizini oluşturulamadı', 500);
    }
    $qrData = json_encode([
        'user_id'      => $userId,
        'ad_soyad'     => "$ad $soyad",
        'yildiz_sayisi'=> 0,
        'yorum_sayisi' => 0
    ]);
    $qrPath = $qrDir . "user_$userId.png";
    QRcode::png($qrData, $qrPath, QR_ECLEVEL_L, 5);
    if (!file_exists($qrPath)) {
        throw new Exception('QR kodu oluşturulamadı', 500);
    }
    $qrBase64 = base64_encode(file_get_contents($qrPath));
    
    // Veritabanı güncelleme
    $stmt = $conn->prepare("UPDATE kullanicilar SET qr_code = ? WHERE user_id = ?");
    $stmt->execute([$qrBase64, $userId]);
    
    // Oluşturulan QR dosyasını sil
    if (!unlink($qrPath)) {
        error_log("QR dosyası silinemedi: $qrPath");
    }
    $conn->commit();
    // Başarılı yanıt - telefon alanı kaldırıldı
    http_response_code(201);
    echo json_encode([
        'success'       => true,
        'message'       => 'Kayıt başarılı',
        'userId'        => $userId,
        'ad_soyad'      => "$ad $soyad",
        'eposta'        => $email,
        'yildiz_sayisi' => 0,
        'yorum_sayisi'  => 0,
        'qr_code'       => $qrBase64
    ]);
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'hata' => $e->getMessage()
    ]);
    
    error_log("Hata: [" . $e->getCode() . "] " . $e->getMessage());
}