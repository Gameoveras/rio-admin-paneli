<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './inc/db.php';

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input === null) {
        throw new Exception("Geçersiz JSON verisi.");
    }

    // Validate input
    if (!isset($input['userId']) || !isset($input['productId'])) {
        throw new Exception("Kullanıcı kimliği ve ürün kimliği zorunludur.");
    }

    $userId = (int)$input['userId'];
    $productId = (int)$input['productId'];

    if ($userId <= 0 || $productId <= 0) {
        throw new Exception("Geçersiz kimlik formatı.");
    }

    // Check if the favorite already exists
    $checkQuery = "SELECT user_id, menu_id FROM favoriler WHERE user_id = :userId AND menu_id = :productId LIMIT 1";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute([':userId' => $userId, ':productId' => $productId]);
    $exists = $stmt->rowCount() > 0;

    // Perform the action
    if ($exists) {
        // Remove from favorites
        $deleteQuery = "DELETE FROM favoriler WHERE user_id = :userId AND menu_id = :productId";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->execute([':userId' => $userId, ':productId' => $productId]);
        $action = 'removed';
    } 
    // Get the new status
    $newStatus = !$exists;

    // Return success response
    echo json_encode([
        'success' => true,
        'action' => $action,
        'is_favori' => $newStatus,
        'message' => $action === 'added' ? 'Ürün favorilere eklendi.' : 'Ürün favorilerden kaldırıldı.'
    ]);

} catch (PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage(),
        'is_favori' => null,
        'productId' => $productId ?? null
    ]);
} catch (Exception $e) {
    // Handle other errors
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'is_favori' => null,
        'productId' => $productId ?? null
    ]);
} finally {
    // Close the connection
    $conn = null;
}
?>