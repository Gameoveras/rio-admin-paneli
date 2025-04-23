<?php
// giris.php: Kullanıcı girişi
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './inc/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Veritabanı bağlantısı kontrolü
if (!isset($conn)) {
    echo json_encode(['hata' => 'Veritabanı bağlantısı başarısız.']);
    exit;
}

// JSON verisini al
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Eposta ve parola kontrolü
if (!isset($data['eposta']) || !isset($data['parola'])) {
    echo json_encode(['hata' => 'E-posta veya parola eksik']);
    exit;
}

$eposta = trim($data['eposta']);
$parola = $data['parola'];

// Geçerli e-posta formatı kontrolü
if (!filter_var($eposta, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['hata' => 'Geçersiz e-posta formatı']);
    exit;
}

try {
    // Kullanıcıyı veritabanında ara
    $stmt = $conn->prepare("SELECT user_id, parola, qr_code, ad_soyad, eposta, telefon_no, yorum_sayisi, yildiz_sayisi FROM kullanicilar WHERE eposta = :eposta");
    $stmt->bindParam(':eposta', $eposta);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Şifreyi doğrula
        if (password_verify($parola, $user['parola'])) {
            // Başarılı yanıt
            echo json_encode([
                'success' => true,
                'message' => 'Giriş başarılı',
                'userId' => $user['user_id'],
                'ad_soyad' => $user['ad_soyad'],
                'eposta' => $user['eposta'],
                'telefon_no' => $user['telefon_no'],
                'yildiz_sayisi' => $user['yildiz_sayisi'],
                'yorum_sayisi' => $user['yorum_sayisi'],
                'qr_code' => $user['qr_code']
            ]);
        } else {
            echo json_encode(['hata' => 'Geçersiz parola']);
        }
    } else {
        echo json_encode(['hata' => 'Kullanıcı bulunamadı']);
    }
} catch (Exception $e) {
    echo json_encode(['hata' => 'Giriş başarısız: ' . $e->getMessage()]);
}
?>
