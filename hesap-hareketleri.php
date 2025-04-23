<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once './inc/db.php';  // Veritabanı bağlantı dosyası

$response = ['success' => false, 'movements' => [], 'error' => ''];

// POST isteği kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON verisini al ve decode et
    $jsonInput = file_get_contents('php://input');
    $data = json_decode($jsonInput, true);

    // Gerekli parametre kontrolü
    if (isset($data['userId'])) {
        $userId = intval($data['userId']);

        try {
            // Kullanıcının QR işlemlerini çek
            $stmt = $conn->prepare("SELECT id, islem_tarihi, islem_tipi 
                                  FROM qr_islemleri 
                                  WHERE user_id = :user_id 
                                  ORDER BY islem_tarihi DESC");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($movements) {
                $response['success'] = true;
                $response['movements'] = $movements;
            } else {
                $response['error'] = 'Hesap hareketi bulunamadı.';
            }
        } catch (PDOException $e) {
            $response['error'] = 'Veritabanı hatası: ' . $e->getMessage();
        }
    } else {
        $response['error'] = 'Geçersiz kullanıcı ID.';
    }
} else {
    $response['error'] = 'Geçersiz istek yöntemi.';
}

// JSON çıktısı
echo json_encode($response, JSON_UNESCAPED_UNICODE);
