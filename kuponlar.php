<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './inc/db.php';

try {
    // JSON verisini al
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['userId']) || empty($input['userId'])) {
        echo json_encode(['success' => false, 'error' => 'Geçersiz kullanıcı ID']);
        exit;
    }

    $userId = intval($input['userId']);

    // Kuponları çekme sorgusu
    $stmt = $conn->prepare("SELECT id, kupon_kodu, kazanilan_yildiz, kullanildi_mi, kullanilma_tarihi 
                            FROM kuponlar WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'coupons' => $coupons
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Genel hata: ' . $e->getMessage()
    ]);
}
?>
