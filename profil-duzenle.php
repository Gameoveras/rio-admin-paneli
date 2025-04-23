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

if (!isset($data['userId'])) {
    echo json_encode(["error" => "Kullanıcı ID gerekli."]);
    exit;
}

$userId = $data['userId'];
$email = isset($data['email']) ? trim($data['email']) : null;
$phone = isset($data['phone']) ? trim($data['phone']) : null;

if (!$email && !$phone) {
    echo json_encode(["error" => "Güncellenecek en az bir alan (eposta veya telefon) belirtilmelidir."]);
    exit;
}

try {
    // E-posta veya telefon başka bir kullanıcıda var mı kontrolü
    if ($email) {
        $stmt = $conn->prepare("SELECT user_id FROM kullanicilar WHERE eposta = :email AND user_id != :userId");
        $stmt->execute([':email' => $email, ':userId' => $userId]);
        if ($stmt->fetch()) {
            echo json_encode(["error" => "Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor."]);
            exit;
        }
    }

    if ($phone) {
        $stmt = $conn->prepare("SELECT user_id FROM kullanicilar WHERE telefon_no = :phone AND user_id != :userId");
        $stmt->execute([':phone' => $phone, ':userId' => $userId]);
        if ($stmt->fetch()) {
            echo json_encode(["error" => "Bu telefon numarası başka bir kullanıcı tarafından kullanılıyor."]);
            exit;
        }
    }

    // Güncelleme işlemi
    $updates = [];
    $params = [];

    if ($email) {
        $updates[] = "eposta = :email";
        $params[':email'] = $email;
    }
    if ($phone) {
        $updates[] = "telefon_no = :phone";
        $params[':phone'] = $phone;
    }

    $params[':userId'] = $userId;

    $sql = "UPDATE kullanicilar SET " . implode(", ", $updates) . " WHERE user_id = :userId";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Güncellenmiş kullanıcı bilgilerini al
    $stmt = $conn->prepare("SELECT ad_soyad, eposta, telefon_no FROM kullanicilar WHERE user_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            "success" => true,
            "message" => "Profil başarıyla güncellendi.",
            "ad_soyad" => $user['ad_soyad'],
            "eposta" => $user['eposta'],
            "telefon_no" => $user['telefon_no']
        ]);
    } else {
        echo json_encode(["error" => "Kullanıcı bulunamadı."]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Güncelleme başarısız: " . $e->getMessage()]);
}
?>
