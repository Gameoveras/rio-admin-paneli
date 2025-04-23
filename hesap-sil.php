<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once './inc/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// POST verisini al
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['userId']) || empty($data['userId'])) {
    echo json_encode(["success" => false, "message" => "Geçersiz kullanıcı ID."]);
    exit;
}

$user_id = intval($data['userId']);

try {
    $stmt = $conn->prepare("DELETE FROM kullanicilar WHERE user_id = :userId");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Kullanıcı başarıyla silindi."]);
    } else {
        echo json_encode(["success" => false, "message" => "Belirtilen kullanıcı bulunamadı veya zaten silinmiş."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Hata: " . $e->getMessage()]);
}