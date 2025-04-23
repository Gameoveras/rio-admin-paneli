<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once './inc/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // En çok yorumlanan ürünleri ve ortalama yıldız puanını çek
    $sql = "
        SELECT 
            m.id AS menu_id,
            m.ad AS menu_adi,
            m.kalori,
            m.fiyat,
            m.resim,
            m.aciklama,
            m.kategori,
            COUNT(y.id) AS yorum_sayisi,
            COALESCE(AVG(y.yildiz_puani), 0) AS ortalama_yildiz
        FROM 
            menu m
        LEFT JOIN 
            yorumlar y ON m.id = y.menu_id
        GROUP BY 
            m.id
        ORDER BY 
            yorum_sayisi DESC
    ";
    $stmt = $conn->query($sql);
    $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON olarak döndür
    echo json_encode([
        'success' => true,
        'data' => $urunler
    ]);
} catch (PDOException $e) {
    // Hata mesajını döndür
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}
?>
