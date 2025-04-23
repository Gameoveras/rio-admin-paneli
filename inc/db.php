<?php

// Çevre değişkenlerinden veritabanı bilgilerini al
$servername = "localhost";
$username = "asebayco_moods";
$password = "!tQR2@BiN-k9";
$dbname = "asebayco_moods";

try {
    // PDO ile veritabanı bağlantısı oluştur
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Hataları log dosyasına yaz ve kullanıcıya genel bir mesaj göster
    error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
    die("Bir hata oluştu. Lütfen daha sonra tekrar deneyin.");
}
