<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once './inc/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Geçersiz istek yöntemi!']);
    exit;
}

// JSON verisini al ve çözümle
$data = json_decode(file_get_contents("php://input"), true);

// Gerekli alanları kontrol et
if (!isset($data['userId'], $data['productId'], $data['comment'], $data['rating'])) {
    echo json_encode(['success' => false, 'error' => 'Eksik veri gönderildi!']);
    exit;
}

// Verileri değişkenlere ata
$userId = intval($data['userId']);
$productId = intval($data['productId']);
$comment = trim($data['comment']);
$rating = intval($data['rating']);

// Yıldız puanı 1-5 aralığında mı?
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'error' => 'Yıldız puanı 1 ile 5 arasında olmalıdır!']);
    exit;
}

try {
    // PDO ile veri ekleme
    $stmt = $conn->prepare("INSERT INTO yorumlar (user_id, menu_id, yildiz_puani, yorum) VALUES (:userId, :productId, :rating, :comment)");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Yorum başarıyla eklendi!',
            'comment' => [
                'id' => $conn->lastInsertId(),
                'userId' => $userId,
                'productId' => $productId,
                'rating' => $rating,
                'comment' => $comment
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Yorum eklenirken hata oluştu!']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>
