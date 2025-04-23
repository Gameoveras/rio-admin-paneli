<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once './inc/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Sadece POST isteklerini kabul et
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Geçersiz istek metodu');
    }

    // Gelen veriyi al
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Kullanıcı ID kontrolü
    if (!isset($input['userId']) || !is_numeric($input['userId'])) {
        throw new Exception('Geçersiz kullanıcı ID');
    }
    $userId = (int)$input['userId'];


    // Favori menü öğelerini getir
    $stmt = $conn->prepare("
        SELECT 
            m.kategori,
            m.ad as menu_adi,
            m.resim,
            m.kalori,
            m.fiyat,
            m.aciklama,
            m.id as menu_id
        FROM favoriler f
        INNER JOIN menu m ON f.menu_id = m.id
        WHERE f.user_id = :user_id
    ");
    
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Yanıtı hazırla
    if (count($favorites) > 0) {
        echo json_encode([
            'success' => true,
            'favorites' => $favorites
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Favori bulunamadı',
            'favorites' => []
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}