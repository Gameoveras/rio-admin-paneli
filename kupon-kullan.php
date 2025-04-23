<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './inc/db.php';

try {
    // JSON verisini al
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['userId'], $input['couponId']) || empty($input['userId']) || empty($input['couponId'])) {
        echo json_encode(['success' => false, 'error' => 'Geçersiz giriş verisi!']);
        exit;
    }

    $userId = intval($input['userId']);
    $couponId = intval($input['couponId']);

    // Kuponun mevcut olup olmadığını ve kullanılmamış olduğunu kontrol et
    $stmt = $conn->prepare("SELECT kazanilan_yildiz, kullanildi_mi FROM kuponlar WHERE id = :coupon_id AND user_id = :user_id");
    $stmt->bindParam(':coupon_id', $couponId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$coupon) {
        echo json_encode(['success' => false, 'error' => 'Kupon bulunamadı!']);
        exit;
    }

    if ($coupon['kullanildi_mi']) {
        echo json_encode(['success' => false, 'error' => 'Bu kupon zaten kullanılmış!']);
        exit;
    }

    // Kullanıcının mevcut yıldız sayısını al
    $stmt = $conn->prepare("SELECT yildiz_sayisi FROM kullanicilar WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Kullanıcı bulunamadı!']);
        exit;
    }

    $newStarCount = $user['yildiz_sayisi'] + $coupon['kazanilan_yildiz'];

    // Kuponu kullanılmış olarak işaretle ve kullanıcının yıldız sayısını güncelle
    $conn->beginTransaction();

    $stmt = $conn->prepare("UPDATE kuponlar SET kullanildi_mi = 1, kullanilma_tarihi = NOW() WHERE id = :coupon_id");
    $stmt->bindParam(':coupon_id', $couponId, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE kullanicilar SET yildiz_sayisi = :new_star_count WHERE user_id = :user_id");
    $stmt->bindParam(':new_star_count', $newStarCount, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Kupon başarıyla kullanıldı!',
        'new_star_count' => $newStarCount
    ]);

} catch (PDOException $e) {
    $conn->rollBack();
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
