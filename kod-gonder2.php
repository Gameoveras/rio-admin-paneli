<?php
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once './inc/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (empty($data['email'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit();
}

$email = $data['email'];

try {
    $stmt = $conn->prepare("SELECT user_id FROM kullanicilar WHERE eposta = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // E-posta kayıtlı değilse hata döndür
    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Bu e-posta adresi ile kayıtlı kullanıcı bulunamadı']);
        exit();
    }

    $dogrulamaKodu = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);


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

    
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'Doğrulama Kodu';
    $mail->Body    = "Merhaba, şifreyi sıfırlamak için doğrulama kodunuz: <strong>$dogrulamaKodu</strong>.";

    $mail->send();

    // Kullanıcı ID'sini de döndür
    echo json_encode([
        'success' => true,
        'message' => 'Doğrulama kodu gönderildi',
        'userId' => $user['user_id'], // React tarafı için gerekli
        'dogrulamaKodu' => $dogrulamaKodu // Test amaçlı
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'E-posta gönderilemedi: ' . $e->getMessage()]);
}