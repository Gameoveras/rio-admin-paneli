<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once './inc/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Veritabanından menü öğelerini çek
    $menuSql = "SELECT id, kategori, ad, kalori, aciklama, fiyat, one_cikan, resim FROM menu ORDER BY id";
    $menuStmt = $conn->query($menuSql);
    $menuItems = $menuStmt->fetchAll(PDO::FETCH_ASSOC);

    // Her bir menü öğesi için yorumları ve ortalama puanı çek
    foreach ($menuItems as &$menuItem) {
        $menuId = $menuItem['id'];

        // Yorumları çek
        $yorumlarSql = "SELECT y.id, y.user_id, y.yildiz_puani, y.yorum, k.ad_soyad 
                        FROM yorumlar y 
                        LEFT JOIN kullanicilar k ON y.user_id = k.user_id 
                        WHERE y.menu_id = :menu_id";
        $yorumlarStmt = $conn->prepare($yorumlarSql);
        $yorumlarStmt->execute(['menu_id' => $menuId]);
        $yorumlar = $yorumlarStmt->fetchAll(PDO::FETCH_ASSOC);

        // Ortalama puanı hesapla
        $ortalamaPuanSql = "SELECT AVG(yildiz_puani) as ortalama_puan FROM yorumlar WHERE menu_id = :menu_id";
        $ortalamaPuanStmt = $conn->prepare($ortalamaPuanSql);
        $ortalamaPuanStmt->execute(['menu_id' => $menuId]);
        $ortalamaPuan = $ortalamaPuanStmt->fetch(PDO::FETCH_ASSOC);

        // Menü öğesine yorumları ve ortalama puanı ekle
        $menuItem['yorumlar'] = $yorumlar;
        $menuItem['ortalama_puan'] = $ortalamaPuan['ortalama_puan'] ?? null;
    }

    // JSON olarak döndür
    echo json_encode([
        'success' => true,
        'data' => $menuItems
    ]);
} catch (PDOException $e) {
    // Hata durumunda hata mesajını döndür
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}