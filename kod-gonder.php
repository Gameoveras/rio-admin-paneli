<?php
// PHPMailer dosyalarını dahil edin
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set headers for CORS and JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once './inc/db.php'; // PDO bağlantısını içeren dosya

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Get JSON input from the request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (empty($data['email'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Email is required']);
    exit();
}

$email = $data['email'];

try {
    // Veritabanında e-posta adresini kontrol et
    $stmt = $conn->prepare("SELECT * FROM kullanicilar WHERE eposta = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // E-posta kayıtlı ise hata döndür
    if ($user) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Bu e-posta adresi zaten kayıtlı.']);
        exit();
    }

    // Doğrulama kodu oluştur
    $dogrulamaKodu = rand(100000, 999999); // 6 haneli rastgele bir kod

    // PHPMailer ile e-posta gönder
    $mail = new PHPMailer(true);

    // SMTP Sunucu Ayarları
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP Sunucusu
    $mail->SMTPAuth   = true;             // SMTP Kimlik Doğrulama
    $mail->Username   = 'uzamanadamtr@gmail.com'; // Gmail adresiniz
    $mail->Password   = 'rlit tekm htgr sxzi'; // Şifreniz veya uygulama şifresi
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS
    $mail->Port       = 587; // Gmail'in önerdiği port

    // Gönderici ve Alıcı
    $mail->setFrom('uzamanadamtr@gmail.com', 'Rio Coffee'); // Gönderici Gmail adresiniz olmalı
    $mail->addAddress($email); // Kullanıcının e-posta adresi

    // E-posta içeriği
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8'; // E-posta karakter setini UTF-8 olarak ayarla
    $mail->Subject = 'Doğrulama Kodu';
    $mail->Body    = "Merhaba, doğrulama kodunuz: <strong>$dogrulamaKodu</strong>.";

    // E-postayı gönder
    $mail->send();

    // Doğrulama kodunu bir .txt dosyasına yaz
    $filePath = 'verification_codes.txt';
    $fileContent = "Email: $email - Kodu: $dogrulamaKodu\n"; // Dosyaya yazılacak içerik
    file_put_contents($filePath, $fileContent, FILE_APPEND); // Dosyaya ekle

    // Başarılı yanıt (React tarafına doğrulama kodunu gönder)
    echo json_encode(['success' => true, 'dogrulamaKodu' => $dogrulamaKodu, 'message' => 'Doğrulama kodu e-posta adresinize gönderildi.']);
} catch (Exception $e) {
    // Hata durumu
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'E-posta gönderilemedi. Hata: ' . $e->getMessage()]);
}
?>
