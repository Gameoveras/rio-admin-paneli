<?php
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

// Gerekli alan kontrolü
if (empty($data['userId']) || empty($data['newPassword'])) {
    http_response_code(400);
    echo json_encode(['error' => 'userId ve newPassword alanları zorunludur']);
    exit();
}

$userId = $data['userId'];
$newPassword = $data['newPassword'];

try {
    // 1. Kullanıcıyı kontrol et
    $stmt = $conn->prepare("SELECT user_id FROM kullanicilar WHERE user_id = :userId");
    $stmt->execute(['userId' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Kullanıcı bulunamadı']);
        exit();
    }

    // 2. Şifreyi hashle ve güncelle
    $hashedSifre = password_hash($newPassword, PASSWORD_BCRYPT);
    
    $updateStmt = $conn->prepare("UPDATE kullanicilar 
                                SET parola = :sifre 
                                WHERE user_id = :userId");
    $updateStmt->execute([
        'sifre' => $hashedSifre,
        'userId' => $userId
    ]);

    // 3. Başarılı yanıt
    echo json_encode([
        'success' => true,
        'message' => 'Şifreniz başarıyla güncellendi'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Veritabanı hatası: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Beklenmeyen hata: ' . $e->getMessage()]);
}