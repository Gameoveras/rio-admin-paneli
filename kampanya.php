<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once './inc/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Veritabanından öne çıkan ürünleri çek
    $stmt = $conn->query("SELECT * FROM kampanyalar ");
    $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ürünleri JSON olarak döndür
    echo json_encode([
        'success' => true,
        'data' => $urunler
    ]);
} catch (PDOException $e) {
    // Hata durumunda hata mesajını döndür
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}