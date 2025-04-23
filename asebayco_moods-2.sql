-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 23 Nis 2025, 15:53:02
-- Sunucu sürümü: 10.6.21-MariaDB
-- PHP Sürümü: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `asebayco_moods`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `favoriler`
--

CREATE TABLE `favoriler` (
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Tablo döküm verisi `favoriler`
--

INSERT INTO `favoriler` (`user_id`, `menu_id`) VALUES
(3, 22),
(3, 52),
(9, 3),
(9, 10),
(22, 1),
(25, 10);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kampanyalar`
--

CREATE TABLE `kampanyalar` (
  `id` int(11) NOT NULL,
  `on_resmi` varchar(255) DEFAULT NULL,
  `baslik` varchar(255) NOT NULL,
  `on_aciklama` mediumtext NOT NULL,
  `icerik` mediumtext NOT NULL,
  `tur` enum('duyuru','kampanya') NOT NULL DEFAULT 'duyuru'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Tablo döküm verisi `kampanyalar`
--

INSERT INTO `kampanyalar` (`id`, `on_resmi`, `baslik`, `on_aciklama`, `icerik`, `tur`) VALUES
(1, '1743086019_67e561c3b443d_image-36.jpg', 'Büyük Fırsat!', 'Seçili ürünlerimizde xx indirim.', 'Şimdi alışverişin tam zamanı! Tüm ürünlerimizde xxye varan indirim fırsatını kaçırmayın. Stoklarla sınırlı bu kampanya ile en sevdiğiniz ürünlere yarı fiyatına sahip olabilirsiniz.\r\n\r\n✔ Tüm kategorilerde geçerli\r\n✔ Sınırlı süre için özel indirim\r\n✔ Stoklarla sınırlıdır, acele edin!', 'duyuru');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `user_id` int(11) NOT NULL,
  `ad_soyad` varchar(100) NOT NULL,
  `eposta` varchar(100) NOT NULL,
  `telefon_no` varchar(15) NOT NULL,
  `parola` varchar(255) NOT NULL,
  `yildiz_sayisi` int(11) DEFAULT 0,
  `yorum_sayisi` int(11) DEFAULT 0,
  `qr_code` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`user_id`, `ad_soyad`, `eposta`, `telefon_no`, `parola`, `yildiz_sayisi`, `yorum_sayisi`, `qr_code`) VALUES
(2, 'Sungu Erdem', 'sunguerd@icloud.com', '5442513566', '$2y$10$fHQS3SafRBkQLMqILJy8n.y.ITek4a01rJQ36lGp1gadOslnWBihm', 0, 0, ''),
(3, 'Sungu Erdem', 'sunguerd@icloud.coms', '54425135661', '$2y$10$ZFg3moC3XgCHeVdJ0i6GwOvWk4D1V8RO3omCj7aNpux0SUzdhDgJG', 25, 0, ''),
(9, 'Sungu Erdem', 'sunguerd@gmail.coma', '5312057881', '$2y$10$UdLjHBWPqwlyhsIRyE.FJuPNpz72t52KkkpoXeYt5hSsHg0b1TuZa', 22, 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAADNAQMAAAAhcmYqAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABuklEQVRYhe2XO46EMBBEGxEQcgRuAhdDAomLMTfxERw6QNRWt/GgndlN2wkdMAzPQYn+FSJPPPFv9ABOmUUa2F2rP6s/4s/YH0PSP0n6LeRn3mhDGmUB+RY6YAs4ayEq5O08GK+HpA1AtFTVQpYve0er8OlXKp2Q1iwfpPG6fBS2EyqBFy6ZX+GBqDBNQLDaSVOch+4V/ZHIkBoc16EGG7pSNq5I25j9k5rYBhbQOnQoCj0RO1hnq0xR+I60id5Jc0T9IWxj40cetdhL2XgiBOpC0J1jCqn13V9+iGuOhUuOuOR8MXPijxZQF9/WrlPWZKIGEulOWbSX1yHnqwYq+RLJ4loVvNZAmi9OkzirVl7uoeeILFW6/jnjdKgN9/L1RJyouZep0E6mkkdXtOp+WbRwdbLpodseuCLuukX3LufKzp7SQ/6Im38X9QAsFuwsoLcR8EXZH6bRmoiRpPdHRaYdgrbxb2PmhLI/PNSP2N6lSZxus+SHqIYvSv2IZHE6av1R+YS5PMBnKl2ReoC8aWjRJtRCh9gnJccb7eJdTI5ozV9SVy+reR5rIKteWC8LPRnjFf3RE0/8GT+XQ9gdRHyxNgAAAABJRU5ErkJggg=='),
(10, 'deneme', 'deneme@hotmail.com', '433231313', '$2y$10$ZbjHoTRuMn2VG7a1T/aNX.evBePsGUp3.N55bUwpekEch.YiOedhO', 7, 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAADNAQMAAAAhcmYqAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABxElEQVRYhe2XO46DQBBE23JAyBG4CVwMCSQuBjfhCIQTINdW9ZiPd71pkzCBP/MIWtPVRY3Zve717yoBvKw1e2A1TBj4H3084lddrlUycSuHOe9FowGptg7kfVWwwhmvqxAr5C+W2SzPC5FxA4tV+FJ8EPJ+DXNBtWj3TyuDkNTLjVS/P34JOwhtC9NiRtl87oYhVpganhEV01Y+ToUeikZUDOtaZSk8I1lKsckmFHV5iJI/9FL7sFUYibxLcEsxWq1VxUk2YajEbA2ypZjbG8ZdNoFogE5LltJW6bE8wSf7cGRP6QSyFLpJzYOyx67eSDRDE+yzjJEFF7gCdcoAZvssGy5BHJ2R/eIsc03s12FvgYijw37x1edHploP0wtEko3q8lnmQzTYw/TiEK3MGiEGs4q/Wkvv+mIRZ9kTqqJInuVTPoxDuV9bFGGZ3rRwNCCHU2lWY3zy+UjUe2i3/OpjGqCOy3i0c49oo159H8EsCOV82LtistnT4ywe6aB0hWm2VqX6fIZRyK8w0CQxDXhc3N568UgXl+xxrbTTX4R8frrZb3WHmCJRn29SjGNKA4tS6xXI1euznJsGTEs8ute9vq4f6T6/baXE4jQAAAAASUVORK5CYII='),
(22, 'Fulya Hatipdir', 'erdemfulya2@gmail.com', '531205788845', '$2y$10$dkVQUaLGksU.wX7awsiUHemdpezEH7vdD781YTjW.z9JW8ORbUnmu', 1, 0, 'iVBORw0KGgoAAAANSUhEUgAAAOEAAADhAQMAAAAEfUA5AAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAACCUlEQVRYhe2YMbKDMAxElaGg5Ai+CVyMmTDDxchNOAKlCyb6u+sk8JM2/4sCF56Y50KxtGuB2TnO8Sejcfe7VbO11lt9Wyq3Do9yMB2sgMkqzxdfU27xOJqOnttmSHZZsMIvw9LnI1DE7FMzzvWd4R6LDqnk9whU+UVqOx9S7pY+2Uf2Ayi1gEPE+T2nd6UE0EfgrLornlIL9jYCKHKJZ1CANStkin3FRoLpmlBwmG6+Gs50wIQizMFUtaaqG2dlunbWXyy164xwGSnOtGZ+/bZsCY6iUCPFaRAn7iMcJyztutNCCEWtZcoAtJqZaaa2iqfK78xbkhc4t1ifgqn6CC++Aa1iwuaXx8ZRpBYyyLbJ1A5B0UwkCYLXNrTKwGMpTb/lBc7AywWpLcEU9sUWh77htUOmasEslsLN4GEjFdqzz+mV353HxtCKBeekzCrCReBb1QVR+v2dgU+Y1Of4tHOzIEoPuyx98Q3t05kGU93dD98w9Rb1zjeiKCpMMcs3Pv9REFVjw+WrzxEIpugeEK4X32D/bPwL0ZTO1aHn0lt+uY+0JZY+tpTuy0zNxPa2GEWf72VP34A0NAXTQRcQBOESp2JeLZrq2wiOrrhZo/xi8wEoIi3fRibaCPuwI1A20QycmfZsu/xG0ZJfnN+iD2+ykS3mKCotsDEEUNVhuf5Wyv/Tc5zj6+MH33hhAfMGjxkAAAAASUVORK5CYII='),
(23, 'Gag Bag', 'loren.iy0vu27.paid@icloud.com', '6693334215', '$2y$10$FIP12bjHFSzTuz4YJiNrQuQExeoSRHHU3zOVQnuhFJFocLoGFR412', 0, 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAADNAQMAAAAhcmYqAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABv0lEQVRYhe2XsY3DMAxFGbhQ6RG0SbyYAQvwYvYmGsGlCiO8T8pKcrlc+92YReDwNYT+J0WJXHHFv9Gr6kNGkXu/R111xn9NfJRqtgzblIv0c645Npq13GVSuUuXAyrM+jgLocKA48HP1p2IUJwuh14nIdcLKg26H19yBjL3IlHux8+HsUmoBbJJ/mRpCBWWQRXFtYYO68ZHIrEIsgFmqSMlNNtQUZe9iUymHBaTT1uFVKRqCRmQwKgV72o66neYZRsjLLxH6IXTWpptmKiqdPTPunWo9XndENGcA45H4FlTDl/l1ozNRMnci7AsjAv36gkItoFKaokxNvn4CN9yUzgGDR1dL9zAiY+StfFuKtnVhybS9XWGPIQBa5OtmGeD3cDPycZEOB70j5qFcVAQTYq0ICIbJKYXhtp09PJzB2Ai62VU6HpV7zxahVSkUCnZtix+777NeSZKUrNmYcxWc1HPR5Ujs9iFt3xbixio7ofJenmqbwcrmI+Sj/joN40X51905E8YHM+xA7xLSUfHqw7uHeNHhUyU6g7g27K81gMiSr6qu0r2rpz0t5Qs5O71HXnKGLWq77ceDV1xxdf4AYvCq3zcGPANAAAAAElFTkSuQmCC'),
(24, 'Uzman Adam', 'uzamanadamtr@gmail.coms', '531 205 7881', '$2y$10$BCqSGS3E0cemtTzW1TUv/edJEspfXYQR3sYhqzhjqBoT..G4clA2S', 6, 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAADNAQMAAAAhcmYqAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABwElEQVRYhe2XMa6EMAxEvaKg3CNwE7jYSiBxMbgJR0iZAuE/Y2DZz/7fDg0pTJRHYcXjwZjd617/rqe7L3j6wt3oPaJ3eoRH/ewneySzbNzFmRr1nmtuaiumEhlOyPUihDsqPb0qhOJCZDgdrGCpvpIXoajXbO4ptPNVShGienGQ6y2chC1C62qn3OCOyt+nQoQMcTpbuaBoyHCuyjFdgaZQDLTT8qXey7dslKizuCMf0T9r+XzPUIh4PTC1NSBXhHfRlAinvCMczGynrvLhLRwdYhuP3lXhKzD7wq3Z+0uIsKJAuUntBAlbFR8eNYJOEFuqNyyeDX0Fore6h7tXh9XKEfvHQywvuknh+AJ3ckQ3CZ006WXRRL47mxQZ84LP19wtNNjd2ZQo9pEmMow0856fFEGuUaWtf9DLx3woRFDM2jqQcPhKDM96RGfjF5gT0Zg+fF6Ket9NjQbLlz6HdhVaOewtZrKBCZ/HIgVa50PffhsY4srkqOPfCz43DGFquTlKqUPbL4x7DInPUym1qPcwF6iXs+peRTlCSg8O7QP4MQMoURfjmK297Kn1I0MlCvWG2dPnWbcx6dG97vXn+gE/pdBSaB2iIgAAAABJRU5ErkJggg=='),
(25, 'Berksu Ertuğrul', 'berksu_94@hotmail.com', '', '$2y$10$RH5DlJuIdh4cABE7H9yDVuBYpvRJpZyyLLs/jHMN11.VEUOxxVSoO', 25, 0, 'iVBORw0KGgoAAAANSUhEUgAAAOEAAADhAQMAAAAEfUA5AAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAACG0lEQVRYhe2YMY6DQAxFP6KgzBG4yXKxSIyUiyU3mSNQUqB4//+TaNmk3Y1TxAUC3hSWx/72DPCxj/2LHSLiyscQy1z5tgETf63JtABfhzKiW44Yzjhi/eLvbHoKfhrMclz0EPUtKMa1W0znwLtQRnLtojB+2t93oKV9MuGKvvuKp91PoK6FU6Xj98dTpbyetiUbs44A09IHHi2BMogD1WKMM1MPXZyYesHtzqUsTksGs24bh8vCqpj0SKZzZV0CIU9DxiTckE17ZdhxpHj0AipTfiKZ0qbl5rjyT5Hs65pL1SAvwVoIlgHfGM4LF2fTgrjey4DgqB6w17ocyjRzmaoM3AiGq7Y7l/KNPvdh3ZC72t90ygY5eEM7ySvDWb04mdI83VAwpGba5OCSZMqthdx1XUKRHLQOybSAaSbd0Jxz399dpaRQD6lawnBSMhhJzjm7nMyiY5tP2SpbEDWz1mTKjmhR5VTPWpiosQzsj5olUZ4wOASyAs5sRdAQjb2qpNHapL7zOBHW2J3POVSecpioEVJ+uWvvkynchUqbI6Cxpw2GudQH/MVCC7h3ezpELm1LQmom0Xdx6gSZS30u85zjLtnO+xuyaWlnWOnG/fh6KLt+lEN9CzFLNwrcJdXFf9/YZFGfy24F4SEab0A1RLspTaFG8OBzBi3tzk3nsmpdW9FuSVLp7W5Eeq+23etupDze2Lyafuxjf27fvGAn2+7DyEwAAAAASUVORK5CYII='),
(26, 'Aleyna Akkoca', 'aleynakkoca@gmail.com', '0000000000', '$2y$10$2uypD219OiTPQMaS1ne8guKmPSvGRPK/vQoejRJ448BeIiD4WTh0e', 0, 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAADNAQMAAAAhcmYqAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABwElEQVRYhe2YQY6DMAxFXbHIkiPkJvRiSETiYuUmOQJLFog/386Qomln62zwglIelaz4+zupyB13/Bs9gKNHxrJ2eXtg5nckf8SPgSgcMgGvfs7lmTeasQ288G4XHNJlJtwK7ZEZdgiaYTukN491FK1XI2T1ShEvZrjJZymdkKl3zlo0u/wVtg8qwTWihFkv+QgfVDQrKpsxaqliqMk7Ij6lYqa8PXnBgl0CVvFHo1heANVLe5vAN1ugqJpFWEB74xpFdlLyRyKmXiZn9VLHb4EmJseVYXLI5isynL9xRGyd7Uk/M+1ocrHKxhXtNDV2UjH7MoEbIKpX2D8Ih750qLOdPu+J+GE6sUkTtJOqvbkiChfYtZcnvUuRCTdAtLIF1svk2klbnXqeSIe+mKnxgpUT+LQUV8TFoa1m6+WgvBqsJ+K4Yf+kuiPao7zDEXVl8tNXoJ109XlHpHsADS7U793FbfzQyXWNpLT2If7I9oe6GVJ3P+dfA5TKbtl6mWcHNboWqJykMlQ7HDysV10+b8RjnMmGncSpNzRDY3mqm8T4lo0nsnrZHkCP2tcDhCsq/zmUPQB9JWnRkju6446v8QP7nR/QcmEKPAAAAABJRU5ErkJggg=='),
(27, 'Onursal', 'onursalrio@gmail.com', '00000000', '$2y$10$u8OGWawWEJUj3RtBCkkwL.P0Ovm1HbDrc36RVYCXhO6qZ.gNK3cha', 0, 0, 'iVBORw0KGgoAAAANSUhEUgAAAM0AAADNAQMAAAAhcmYqAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABxElEQVRYhe2XPY6EMAyFjShSzhFyE7gYEkhcDG7CEVKmGOF9z+FnZ9htTUMKYPw1Vmw/vxF5znP+PS9VXaVeVFMXddYRv3XwR3g1r3EJiCxZ8FVi3mjU3OCtc6qXgAwXXe9CLyTXJolBwe9D0olU+o65uibvhKxeiAp5cy2lE2L3IpCb7fHV2E6onF6R3CBfUUeEDHOLUuHrXa4szMkfScdSmaSItY2GvW280SEpyJDl+pWhF2KUN6P2mIQDvXeUI5Kas9xTUrrI3onk7gjfZYwr2zSpVmmPdeOITFuRpvXOikiujsZ2RG8JrJLNclitge5AUNQ2QVLYx5xl0VuQqvFiRVgvbODBH41UNptl6DwCUQ/Rc0RoXEwNOoZ5hYkCeyqbH0KB0L09Fx42MAZa8pafLxp4M12pXJnl0x44olIqxQTXC+8t2kDfgAQaEmXzh58rwBGp+SD6Q4G2whJ8mHYnVLiatkZ079UWuaDdH+KO+vLfgfbdHyGbhrM84Ycll8+F6IisXhhozrI5onPr3YDELHMFsb9k6IfgRzjL0Fb65r2ZPNFQrDqKJvTrffm3646se4eIO+ohLhTZOfmj5zznz/MD1DqxqNdlJNUAAAAASUVORK5CYII=');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kuponlar`
--

CREATE TABLE `kuponlar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kupon_kodu` varchar(50) NOT NULL,
  `kazanilan_yildiz` int(11) NOT NULL,
  `kullanildi_mi` tinyint(1) DEFAULT 0,
  `kullanilma_tarihi` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Tablo döküm verisi `kuponlar`
--

INSERT INTO `kuponlar` (`id`, `user_id`, `kupon_kodu`, `kazanilan_yildiz`, `kullanildi_mi`, `kullanilma_tarihi`) VALUES
(1, 9, 'ODUL22', 22, 1, '2025-01-31 18:23:04'),
(2, 25, 'BERKSU', 25, 1, '2025-04-08 14:46:17'),
(3, 3, 'SUNGU22', 25, 1, '2025-03-27 13:29:12'),
(4, 26, 'ALEYNA22', 35, 0, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `kategori` enum('İçecek','Yiyecek','Tatlı','Atıştırmalık') NOT NULL,
  `ad` varchar(255) NOT NULL,
  `kalori` int(11) NOT NULL,
  `aciklama` mediumtext DEFAULT NULL,
  `fiyat` decimal(10,2) NOT NULL,
  `one_cikan` tinyint(1) DEFAULT 0,
  `resim` varchar(255) DEFAULT NULL,
  `içindekiler` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`içindekiler`))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Tablo döküm verisi `menu`
--

INSERT INTO `menu` (`id`, `kategori`, `ad`, `kalori`, `aciklama`, `fiyat`, `one_cikan`, `resim`, `içindekiler`) VALUES
(3, 'İçecek', 'Türk Kahvesi', 15, 'Geleneksel yöntemlerle hazırlanan yoğun ve aromatik Türk kahvesi.\r\n\r\n30 mL 65₺\r\n60 mL 90₺', 65.00, 0, 'uploads/67e5267d627b9.jpg', NULL),
(57, 'İçecek', 'Limonata', 150, 'Limonata, taze sıkılmış limon suyu, su ve şekerin karıştırılmasıyla yapılan ferahlatıcı bir içecektir. Genellikle yaz aylarında serinletici bir içecek olarak tercih edilir, çünkü limonun asidik ve ekşi tadı, şekerle dengeleyerek tatlı ve asidik bir harmoni oluşturur. Limonata, sadece birkaç malzeme ile hızlıca hazırlanan, serinletici ve lezzetli bir içecektir. 300mL', 110.00, 0, 'uploads/67e54b3fda772.jpg', NULL),
(4, 'İçecek', 'Ristretto', 30, 'Yoğun aroması ve zengin tadıyla kısa çekim espresso. Daha az su kullanılarak hazırlanır, güçlü ve dengeli bir lezzet sunar. Sıcak içecektir. 20 mL', 75.00, 0, 'uploads/67e54c817461c.jpg', NULL),
(5, 'İçecek', 'Espresso', 3, 'İnce çekilmiş kahve çekirdeklerinden yüksek basınçla hazırlanan yoğun ve aromatik kahve. Dengeli bir asidite ve zengin bir crema tabakası sunar. Sıcak içecektir. 30 mL', 75.00, 0, 'uploads/67e54cbf03e50.jpg', NULL),
(6, 'İçecek', 'Lungo', 5, 'Espressoya göre daha fazla su ile hazırlanan, daha hafif ancak aromatik bir kahve çeşidi. Daha uzun sürede demlendiği için 40 mL hacmindedir. Sıcak içecektir.\r\n', 80.00, 0, 'uploads/67e54cf1a2485.jpg', NULL),
(7, 'İçecek', 'Doppio', 10, 'İki shot espresso kullanılarak hazırlanan, yoğun aromalı ve güçlü bir kahve çeşidi. Sıcak servis edilir ve 60 mL hacmindedir.', 110.00, 0, 'uploads/67e54d27cf7b1.jpg', NULL),
(8, 'İçecek', 'Romano', 5, 'Klasik espressonun içinde bir dilim limon ile servis edilen versiyonu. Limonun hafif asidik dokunuşu, kahvenin yoğun aromasını dengeler. Sıcak servis edilir ve 40 mL hacmindedir.\r\n', 85.00, 0, 'uploads/67e54d884e0e5.jpg', NULL),
(9, 'İçecek', 'Macchiato', 8, 'Klasik İtalyan espressosu üzerine eklenen bir miktar süt köpüğüyle hazırlanan, yoğun ve hafif tatlı bir içecek. Espresso\'nun güçlü tadı, süt ile dengelenerek mükemmel bir uyum oluşturur. 40 mL’lik porsiyonuyla hem enerjik hem de hafif bir seçenek arayanlar için ideal. Sade veya vanilyalı seçeneklerle de sunulabilir.', 100.00, 0, 'uploads/67e55113ecaf8.jpg', NULL),
(10, 'İçecek', 'Americano', 23, 'Americano, zengin espresso tadının sıcak su ile seyreltilmesiyle elde edilen, hafif ve içimi kolay bir kahve içeceğidir. Farklı boyutlarda sunulabilen Americano, yoğun kahve aromasıyla mükemmel bir deneyim sunar.\r\n\r\n210 ml: 10.5 kalori, 100 TL\r\n310 ml: 15.5 kalori, 110 TL\r\n450 ml: 22.5 kalori, 130 TL\r\n', 130.00, 1, 'uploads/67e54dcd5aaff.jpg', NULL),
(11, 'İçecek', 'Long Black ', 11, 'Long Black, klasik Americano\'dan farklı olarak, double espresso\'nun üzerine sıcak su eklenerek yapılan bir içecektir. Espresso\'nun güçlü aroması, sıcak suyla yumuşatılır, ancak kahvenin yoğunluğu hala korunur. Bu içecek, zengin ve derin bir tat arayan kahve severler için mükemmel bir tercihtir. 210 mL', 115.00, 0, 'uploads/67e54e042f0ed.jpg', NULL),
(12, 'İçecek', 'Filtre Kahve', 23, 'Filtre kahve, kahve çekirdeklerinin sıcak suyla demlenerek elde edilen klasik ve zengin bir içecektir. Uzun süreli demleme işlemi, kahvenin aromalarını derinlemesine açığa çıkarır. Farklı boyut seçenekleriyle sunulan filtre kahve, kahve severlere yoğun ve keyifli bir deneyim sunar.\r\n\r\n210 mL: 10.5 kalori, 120 TL  \r\n310 mL: 15.5 kalori, 130 TL  \r\n450 mL: 22.5 kalori, 140 TL  ', 140.00, 0, 'uploads/67e5520dc6d71.jpg', NULL),
(13, 'İçecek', 'Cortado', 20, 'Cortado, espresso ile eşit miktarda sıcak süt karıştırılarak hazırlanan, dengeli ve yoğun bir kahve içeceğidir. Süt, espressonun acılığını hafifletirken kahvenin zenginliğini korur. Cortado, hem espresso severler hem de sütlü kahve sevenler için mükemmel bir seçenek olup, düşük kalorisiyle keyifli bir içim deneyimi sağlar.\r\n210 mL: 20 kalori, 100 TL  \r\n', 100.00, 0, 'uploads/67e53457e7fe9.jpg', NULL),
(14, 'İçecek', 'Cappucino', 180, 'Cappuccino, espresso, sıcak süt ve süt köpüğünün mükemmel uyumuyla hazırlanan, hafif tatlı ve kremamsı bir içecektir. Üstündeki yoğun süt köpüğü, kahvenin zenginliğini yumuşatarak dengeli bir lezzet ortaya çıkarır. Farklı boyut seçenekleriyle sunulan cappuccino, her bir yudumda keyifli bir kahve deneyimi sağlar.\r\n210 mL: 90 kalori, 120 TL  \r\n310 mL: 130 kalori, 130 TL  \r\n450 mL: 180 kalori, 140 TL  ', 140.00, 1, 'uploads/67e552f443bc5.jpg', NULL),
(15, 'İçecek', 'Caffe Latte', 190, 'Caffè Latte, espresso ve bol miktarda sıcak sütün birleşiminden oluşan, yumuşak ve kremamsı bir içecektir. Süt, espressonun yoğunluğunu hafifletirken kahvenin zengin tadını korur. Üst kısmında ince bir süt köpüğü tabakası bulunur, bu da latte\'yi dengeli ve içimi kolay hale getirir. Farklı boyut seçenekleriyle sunulabilen Caffè Latte, sütlü kahve sevenler için ideal bir tercihtir.\r\n210 mL: 100 kalori, 120 TL  \r\n310 mL: 140 kalori, 130 TL  \r\n450 mL: 190 kalori, 140 TL  ', 140.00, 0, 'uploads/67e53457e8567.jpg', NULL),
(16, 'İçecek', 'Flat White', 160, 'Flat White, double espresso ve buharda ısıtılmış sütle yapılan, kremamsı ama yoğun bir içecektir. Süt, kahvenin acılığını yumuşatarak daha pürüzsüz ve dengeli bir lezzet oluşturur. Üstünde minimal bir köpük tabakası bulunur, bu da içeceğe daha pürüzsüz bir yapı kazandırır. Flat White, daha yoğun kahve tadı isteyenler için ideal bir tercihtir.\r\n210 mL: 120 kalori, 130 TL  \r\n310 mL: 160 kalori, 140 TL  ', 140.00, 0, 'uploads/67e54e4639acf.jpg', NULL),
(17, 'İçecek', 'Caramel Macchiato', 270, 'Caramel Macchiato, tatlı bir dokunuş arayan kahve severler için mükemmel bir tercihtir. Bu içecek, karamel şurubu, buharda ısıtılmış süt ve üzerine eklenen espresso ile hazırlanır. Üstünde zengin ve akışkan karamel sosu ile tatlılık katılır. Hem kremamsı hem de kahvenin yoğunluğunu barındıran bu içecek, dengeli ve lezzetli bir deneyim sunar.\r\n210 mL: 150 kalori, 125 TL  \r\n310 mL: 200 kalori, 140 TL  \r\n450 mL: 270 kalori, 155 TL  ', 155.00, 0, 'uploads/67e53457e89e0.jpg', NULL),
(18, 'İçecek', 'Lotus Latte', 280, 'Lotus Latte, tatlı ve aromatik bir kahve deneyimi sunar. Bu içecek, espresso, sıcak süt ve Lotus bisküvi şurubunun birleşiminden oluşur. Şurubun bisküvi tadı, süt ve kahve ile mükemmel bir uyum yakalar. Üstünde ince bir süt köpüğü tabakası ile sonlanır, bu da içeceğe kremamsı bir yapı katarken, aynı zamanda tatlı ve baharatlı bir aroma sunar.\r\n210 mL: 160 kalori, 125 TL  \r\n310 mL: 210 kalori, 140 TL  \r\n450 mL: 280 kalori, 155 TL  ', 155.00, 0, 'uploads/67e53457e8c61.jpg', NULL),
(19, 'İçecek', 'Irish Coffee', 400, 'Irish Coffee, sıcak kahve, taze çekilmiş espresso içerisinde irish whiskey aromalı şurup ve sıcak su eklenerek yapılır. Fincan üzerine son olarak süt köpüğü eklenerek servise hazır hale gelir.\r\n210 mL: 220 kalori, 125 TL  \r\n310 mL: 300 kalori, 140 TL  \r\n450 mL: 400 kalori, 155 TL  ', 155.00, 0, 'uploads/67e53457e8ecc.jpg', NULL),
(20, 'İçecek', 'Mocha', 320, 'Mocha, espresso, sıcak süt ve çikolata şurubunun birleşiminden oluşan tatlı ve zengin bir içecektir. Çikolatanın hafif tatlılığı, kahvenin yoğunluğu ile mükemmel bir uyum yakalar. Üstünde yoğun süt köpüğü ve bazen çikolata sosu ile süslenerek tatlı bir kahve deneyimi sunar.\r\n210 mL: 180 kalori, 125 TL  \r\n310 mL: 240 kalori, 140 TL  \r\n450 mL: 320 kalori, 155 TL  ', 155.00, 0, 'uploads/67e53457e90c4.jpg', NULL),
(21, 'İçecek', 'Toffie Nut Latte', 380, 'Toffee Nut Latte, espresso, sıcak süt, karamel ve fındık şurubunun birleşimiyle hazırlanan tatlı ve aromatik bir içecektir. Karamel ve fındığın zengin tadı, kahve ile mükemmel bir uyum sağlar. Üstündeki ince süt köpüğü katmanı, içeceği kremamsı yaparak lezzetini tamamlar. Bu içecek, tatlı ve yoğun kahve tadını sevenler için ideal bir tercihtir.\r\n210 mL: 210 kalori, 125 TL  \r\n310 mL: 280 kalori, 140 TL  \r\n450 mL: 380 kalori, 155 TL  ', 155.00, 0, 'uploads/67e53457e92ec.jpg', NULL),
(22, 'İçecek', 'Carnaval Latte', 270, 'Carnaval Latte, espresso, sıcak süt ve meyve şuruplarının birleşimiyle hazırlanan eğlenceli ve tatlı bir içecektir. Çilek ve muz meyve aromaları, kahveyle mükemmel bir uyum yakalar. Üstünde yoğun süt köpüğü ile süslenerek tatlı ve aromatik bir kahve deneyimi sunar.\r\n210 mL: 125 TL, 180 kalori  \r\n310 mL: 140 TL, 210 kalori  \r\n450 mL: 155 TL, 270 kalori  ', 155.00, 0, 'uploads/67e555e033160.jpg', NULL),
(23, 'İçecek', 'Vanilla Latte', 190, 'Vanilla Latte, espresso, sıcak süt ve vanilya şurubunun birleşiminden oluşan tatlı ve kremamsı bir içecektir. Vanilyanın hafif tatlılığı, kahvenin zenginliğini yumuşatarak dengeli ve lezzetli bir deneyim sunar. Üstünde ince bir süt köpüğü tabakası bulunur, bu da içeceğe pürüzsüz bir yapı katarken, aromatik vanilya tadı ile içimi kolaylaştırır.\r\n210 mL: 125 TL, 150 kalori  \r\n310 mL: 140 TL, 170 kalori  \r\n450 mL: 155 TL, 190 kalori  ', 155.00, 0, 'uploads/67e537cee020a.jpg', NULL),
(24, 'İçecek', 'Walnut Latte', 270, 'Walnut Latte, espresso, sıcak süt ve ceviz şurubunun birleşiminden oluşan aromatik bir kahve içeceğidir. Cevizin zengin tadı, kahvenin yoğunluğuyla mükemmel bir denge yakalar. Üstündeki yoğun süt köpüğü, içeceği kremamsı hale getirir, ceviz aroması ise kendine özgü bir tat katmıştır. Hem kahve hem de ceviz severler için ideal bir tercihtir.\r\n210 mL: 125 TL, 180 kalori \r\n310 mL: 140 TL, 210 kalori  \r\n450 mL: 155 TL, 270 kalori  ', 155.00, 0, 'uploads/67e537cee04c4.jpg', NULL),
(25, 'İçecek', 'Rio Special', 382, 'Rio Special, tatlı ve meyvemsi bir kahve deneyimi sunar. Beyaz çikolata şurubunun kremamsı tatlılığı, frambuazın meyvemsi asidik dokusu ile mükemmel bir uyum yakalar. Espresso ve sıcak sütle harmanlanan bu içecek, yoğun ve zengin bir lezzet profili sunar. Üstünde ince bir süt köpüğü katmanı ile sonlanır, bu da içeceğe pürüzsüz bir yapı katarken, her yudumda tatlı ve aromatik bir deneyim sağlar.\r\n210 mL: 212 kalori, 120 TL  \r\n310 mL: 282 kalori, 130 TL  \r\n450 mL: 382 kalori, 150 TL  ', 155.00, 0, 'uploads/67e537cee075c.jpg', NULL),
(26, 'İçecek', 'V60', 3, 'V60, el ile yapılan filtre kahve demleme yöntemlerinden biridir ve kahvenin tatlarını en iyi şekilde ortaya çıkarmak için tercih edilir. İnce öğütülmüş kahve, sıcak suyun dikkatlice dökülmesiyle yavaşça demlenir. Bu yöntem, kahvenin zengin aromalarını ve doğal tatlarını derinlemesine açığa çıkarır. V60, kahve severlere daha kontrollü ve yoğun bir kahve deneyimi sunar. 210 mL', 145.00, 0, 'uploads/67e53bb4a1cb6.jpg', NULL),
(27, 'İçecek', 'Chemex', 0, 'Chemex, filtre kahve demleme yöntemlerinden biri olup, şıklığı ve mükemmel kahve demleme tekniğiyle dikkat çeker. Kalın kâğıt filtreler sayesinde, kahvenin yağı ve kısımlarından arındırılmış, temiz ve berrak bir tat elde edilir. Chemex, kahve çekirdeklerinin zengin aromalarını ve tatlarını en iyi şekilde ortaya çıkarmak için ideal bir yöntemdir. Estetik bir tasarıma sahip bu kahve demleme aparatı, kahve tutkunları için hem görsel hem de tat olarak doyurucu bir deneyim sunar.\r\n- Kahve, sıcak suyun yavaşça dökülmesiyle demlenir.\r\n- Filtrelerin kalınlığı, kahvenin daha pürüzsüz ve saf bir tat almasını sağlar.\r\n- Chemex, genellikle daha hafif ve asidik kahve tercih edenler için ideal bir tercihtir.\r\nChemex ile yapılan kahve, her bir yudumda daha net ve saf bir tat deneyimi sunar, böylece kahve severler daha derin bir lezzet keşfi yapabilirler. 210 mL', 145.00, 0, 'uploads/67e53bb4a21cf.jpg', NULL),
(28, 'İçecek', 'Aeropress', 0, 'Aeropress, hızlı ve pratik bir şekilde filtre kahve yapmak için kullanılan bir kahve demleme aracıdır. Bu yöntem, kahve çekirdeklerini sıcak su ile karıştırarak basınçla süzülmesini sağlar. Aeropress, hızlı demlenmesi ve yoğun kahve tadı ile tanınır. Kullanımı kolay olan bu alet, kahveye daha zengin, pürüzsüz ve lezzetli bir tat kazandırır. Aeropress, özellikle zaman kısıtlaması olanlar için ideal bir seçenektir.\r\n- Demleme Süresi: Yaklaşık 2-3 dakika.\r\n- Daha yoğun ve zengin bir kahve deneyimi sunar. 210 mL', 145.00, 0, 'uploads/67e53bb4a24f9.jpg', NULL),
(29, 'İçecek', 'Syphon', 0, 'Syphon, etkileyici bir demleme tekniği sunan ve kahve severlere hem görsel hem de tat açısından zengin bir deneyim sağlayan bir kahve demleme aracıdır. Bu yöntem, vakum basıncı kullanarak kahveyi demler ve her bir yudumda zengin bir aroma ile berrak bir kahve sunar. Syphon, genellikle özel kahve dükkanlarında veya kahve tutkunlarının evlerinde kullanılan, görsel olarak da cezbedici bir demleme yöntemidir.\r\n- Demleme Süresi: Yaklaşık 5-7 dakika.\r\n- Kahve Tadı: Yumuşak, zengin ve berrak bir tat deneyimi sağlar. 210 mL', 200.00, 0, 'uploads/67e53bb4a2888.jpg', NULL),
(30, 'İçecek', 'Affogato', 310, 'Affogato, İtalyanca’da “boğulmuş” anlamına gelen bir tatlı-kahve kombinasyonudur. Genellikle bir top vanilyalı dondurmanın üzerine sıcak espresso dökülerek hazırlanır. Hem tatlı hem de kahve severler için mükemmel bir lezzet sunar. 300 mL\r\n', 120.00, 0, 'uploads/67e5414376c4b.jpg', NULL),
(31, 'İçecek', 'Iced Americano', 7, 'Iced Americano, klasik Americano’nun soğuk versiyonudur. Espresso shot’larının üzerine soğuk su eklenerek hazırlanır ve genellikle buz ile servis edilir. Sade ve düşük kalorili bir içecektir, çünkü içinde süt veya şeker bulunmaz. 300mL', 125.00, 1, 'uploads/67e541437705e.jpg', NULL),
(32, 'İçecek', 'Freddo Espresso', 10, 'Freddo Espresso, Yunanistan kökenli soğuk bir kahve türüdür. Çift shot espresso hazırlanıp buzla karıştırılarak soğutulur ve genellikle kremsi bir köpük oluşturmak için blender veya shaker ile çırpılır. Klasik Iced Americano’dan farklı olarak daha yoğun bir kıvama sahiptir. 300mL', 130.00, 0, 'uploads/67e541437729e.jpg', NULL),
(33, 'İçecek', 'Iced Latte', 120, 'Iced Latte, soğuk süt ve espresso karışımından oluşan hafif ve kremsi bir kahve içeceğidir. Genellikle 1-2 shot espresso üzerine bol miktarda soğuk süt eklenerek hazırlanır ve buzla servis edilir. Iced Americano’dan farklı olarak sütten gelen tatlılık ve kremamsı dokuyla daha yumuşak bir içime sahiptir. 300mL', 140.00, 1, 'uploads/67e54143774c6.jpg', NULL),
(34, 'İçecek', 'Iced Mocha', 250, 'Iced mocha, soğuk kahve, süt ve çikolata karışımından yapılan bir içecektir. Genellikle espresso, soğuk süt, çikolata şurubu ve buz kullanılarak hazırlanır. Bu içecek, sıcak yaz günlerinde ferahlatıcı bir seçenek olarak popülerdir. 300 mL', 160.00, 0, 'uploads/67e541437762a.jpg', NULL),
(35, 'İçecek', 'Iced Toffie Nut Latte', 320, 'Iced Toffee Nut Latte, soğuk bir kahve içeceği olup, özellikle tatlı bir lezzet arayanlar için harika bir seçenektir. Bu içecek, soğuk espresso, toffee nut (karamel fındık) şurubu, buz ve süt karışımından oluşur. Toffee nut şurubu, kahveye tatlı, fındıklı ve hafif karamelimsi bir tat verir. 300mL', 160.00, 1, 'uploads/67e54143777d5.jpg', NULL),
(36, 'İçecek', 'Iced Carnaval Latte', 320, 'Iced Carnaval Latte, tatlı bir kahve içeceği olup, muz ve çilek şuruplarıyla hazırlanır. İçeceğin adı \"Carnaval\" (karnaval) tarzında eğlenceli ve tatlı bir deneyim çağrıştırır. 300 mL', 160.00, 0, 'uploads/67e5414377976.jpg', NULL),
(37, 'İçecek', 'Iced Vanilla Latte', 170, 'Iced Vanilla Latte, vanilya aromalı tatlı bir kahve içeceğidir ve genellikle soğuk kahve sevenler tarafından tercih edilir. Bu içecek, soğuk espresso, süt ve vanilya şurubunun birleşiminden oluşur. Vanilya şurubu, içeceğe tatlı bir aroma katarak kahvenin acılığını dengeler. 300mL', 160.00, 0, 'uploads/67e5414377afc.jpg', NULL),
(38, 'İçecek', 'Iced Walnut Latte', 320, 'Iced Walnut Latte, ceviz aromalı tatlı ve ferahlatıcı bir kahve içeceğidir. Soğuk espresso, süt ve ceviz şurubunun karışımı ile yapılır. Ceviz şurubu, içeceğe zengin ve hafif bir tat katarak kahvenin acılığını dengeler ve kremamsı bir lezzet sunar. Bu içecek, özellikle ceviz sevenler için mükemmel bir tercihtir. 300 mL', 160.00, 0, 'uploads/67e5414377c8d.jpg', NULL),
(39, 'İçecek', 'Frappe', 120, 'Frappe, genellikle buzlu kahve içeceklerini tanımlamak için kullanılan bir terimdir ve özellikle sıcak havalarda ferahlatıcı bir seçenek olarak popülerdir. Frappe, genellikle kahve, süt, buz ve tatlandırıcıların karıştırılarak bir çeşit kıvamlı, soğuk içecek haline getirilmesiyle hazırlanır. Frappe, Yunanistan\'dan dünyaya yayılmış ve farklı varyasyonlarla hazırlanabilmektedir. 300mL', 160.00, 0, 'uploads/67e5414377e53.jpg', NULL),
(40, 'İçecek', 'Frappucino', 250, 'Frappuccino, genellikle kahve, süt, buz, dondurma ve tatlandırıcıların karışımından oluşur. Frappuccino, genellikle soğuk, tatlı ve kremsi bir içecek olarak bilinir ve birçok farklı çeşidi vardır. İçeceğin adı, \"frozen\" (buzlu) ve \"cappuccino\" kelimelerinin birleşiminden türetilmiştir. 300mL', 175.00, 0, 'uploads/67e5414378064.jpg', NULL),
(41, 'İçecek', 'Iced Rio Special', 260, 'Rio Special, tatlı bir meyvemsi bir kahve deneyimi sunar. Beyaz çikolata şurubunun kremamsı tatlılığı, frambuazın meyvemsi asidik dokusu ile mükemmel bir uyum yakalar. Espresso ve soğuk sütle harmanlanan bu içecek, yoğun ve zengin bir lezzet profili sunar. Üstünde ince bir süt köpüğü katmanıyla sonlanır, bu da içeceğe pürüzsüz bir yapı katarken her yudumda tatlı ve aromatik bir deneyim sağlar. 300mL', 160.00, 0, 'uploads/67e5414378205.jpg', NULL),
(42, 'İçecek', 'Cold Brew', 10, 'Cold brew, soğuk kahve yapım yöntemidir ve sıcak su yerine soğuk su kullanılarak yapılan bir kahve türüdür. Bu yöntem, kahvenin daha yumuşak ve düşük asidik bir tada sahip olmasını sağlar. Cold brew, kahve çekirdeklerinin soğuk suda uzun süre (genellikle 12-24 saat) demlenmesiyle elde edilir, bu yüzden sıcak kahveye göre daha az acıdır ve daha pürüzsüz bir lezzet sunar. 300mL', 155.00, 0, 'uploads/67e5417c1feeb.jpg', NULL),
(43, 'İçecek', 'Sıcak Çikolata', 300, 'Sıcak çikolata, kremamsı ve tatlı bir içecek olup, kakao ve süt ile yapılan, soğuk günlerde içimi keyifli bir içecektir. Özellikle kış mevsiminde sıcak tutan ve ferahlatıcı bir etki bırakan sıcak çikolata, çikolata severler için ideal bir tercihtir. 310mL', 125.00, 0, 'uploads/67e542d348738.jpg', NULL),
(44, 'İçecek', 'Salep', 350, 'Salep, özellikle kış aylarında tercih edilen, sütle karıştırılarak yapılan sıcak bir içecektir. Geleneksel olarak, orkide bitkisinin köklerinden elde edilen salep tozu ile yapılan bu içecek, kremamsı kıvamı ve hafif tatlılığı ile içimi son derece keyiflidir. Baharatlı bir dokunuşla, üzerine tarçın serpiştirilebilir ve bu da içeceğe ekstra bir aroma katabilir. Salep, soğuk kış günlerinde vücut ısısını artırarak, rahatlatıcı bir deneyim sunar. 310mL', 125.00, 0, 'uploads/67e542d348b42.jpg', NULL),
(45, 'İçecek', 'Çay', 3, 'Çay, dünyanın en popüler içeceklerinden biridir ve farklı türleriyle her damak zevkine hitap eder. Genellikle sıcak içilen çay, yumuşak, ferahlatıcı bir içim sağlar. Siyah çay, yeşil çay, beyaz çay ve bitki çayları gibi çeşitli seçenekler bulunur. Çay, hafif asidik yapısıyla sindirimi kolaylaştıran ve sakinleştirici etkisiyle bilinen bir içecektir. 100mL', 30.00, 0, 'uploads/67e542d348e74.jpg', NULL),
(46, 'İçecek', 'Fincan Çay', 10, 'Fincan çay, dünyanın en popüler içeceklerinden biridir ve farklı türleriyle her damak zevkine hitap eder. Genellikle sıcak içilen çay, yumuşak, ferahlatıcı bir içim sağlar. Siyah çay, yeşil çay, beyaz çay ve bitki çayları gibi çeşitli seçenekler bulunur. Çay, hafif asidik yapısıyla sindirimi kolaylaştıran ve sakinleştirici etkisiyle bilinen bir içecektir. 210mL', 50.00, 0, 'uploads/67e542d3491f0.jpg', NULL),
(47, 'İçecek', 'Meyve Çayı', 30, 'Meyve çayı, çeşitli meyve aromalarıyla hazırlanan, ferahlatıcı ve tatlı bir içecektir. Çeşitli meyve parçacıkları, çiçekler ve bitkisel içerikler ile zenginleştirilen meyve çayı, hem sağlıklı hem de lezzetli bir alternatif sunar. Yumuşak ve doğal tatları ile rahatlatıcı bir içim sağlar. 210mL', 80.00, 0, 'uploads/67e542d349566.jpg', NULL),
(48, 'İçecek', 'Kış Çayı', 30, 'Kış çayı, soğuk hava şartlarında içimizi ısıtan ve vücuda canlılık katan, baharatlı ve meyveli bir içecektir. Genellikle tarçın, zencefil, karanfil, narenciye kabukları ve bitkisel özlerle zenginleştirilir. Hem sıcak hem de soğuk olarak tüketilebilen kış çayı, soğuk algınlığına karşı da rahatlatıcı etkiler sağlar. Baharatlar, doğal bir şekilde bağışıklık sistemini desteklerken, kış çayı içimi aynı zamanda aromatik bir deneyim sunar. 310mL', 115.00, 0, 'uploads/67e542d349907.jpg', NULL),
(49, 'İçecek', 'Ihlamur', 10, 'Ihlamur, özellikle kış aylarında soğuk algınlıklarına karşı sıkça tercih edilen, rahatlatıcı ve yatıştırıcı etkisiyle bilinen bir bitki çayıdır. Ihlamur çayı, doğal içerikleri sayesinde vücuda rahatlık sağlar, sindirimi destekler ve stresi azaltabilir. Yumuşak, hafif ve aromatik bir tat sunar. Genellikle sıcak içilen bu içecek, içerdiği doğal yağlar sayesinde rahatlatıcı ve uyku düzenine yardımcı olur. 310mL', 115.00, 0, 'uploads/67e542d349ccc.jpg', NULL),
(50, 'İçecek', 'Bitki Çayı', 10, 'Bitki çayı, farklı bitkilerden elde edilen özlerle yapılan, sağlıklı ve rahatlatıcı bir içecektir. Çeşitli bitkiler (nane, papatya, melisa, kuşburnu vb.) kullanılarak yapılan bu çaylar, sindirim sistemine yardımcı olabilir, stresi azaltabilir ve rahatlatıcı etkiler sunabilir. Bitki çayları genellikle sıcak olarak tüketilir ve içerdiği doğal bileşenler sayesinde vücudu dinlendirici bir etki bırakır. 210mL', 115.00, 0, 'uploads/67e542d34a023.jpg', NULL),
(51, 'İçecek', 'Su', 0, 'Su, vücudun en temel ihtiyacıdır ve sağlıklı bir yaşam için vazgeçilmezdir. Hiçbir kalori içermeyen su, vücudun su dengesini sağlamak, sindirimi düzenlemek ve toksinlerden arınmak için gereklidir. Su, aynı zamanda cilt sağlığını destekler ve genel canlılık için önemlidir. Gün boyu yeterli su tüketimi, sağlığın korunmasına yardımcı olur. 500mL', 15.00, 0, 'uploads/67e542d34a35f.jpg', NULL),
(52, 'Tatlı', 'İzmir Bombası', 400, 'İzmir Bombası, Türkiye\'nin İzmir şehrine özgü bir tatlıdır ve sokak lezzetlerinin en sevilenlerinden biridir. Dışı çıtır çıtır, içi ise sıcacık, akışkan çikolata dolu olan bu tatlı, tatlı severlerin vazgeçilmezi haline gelmiştir. 60gr', 60.00, 0, 'uploads/67eff7acc965d.jpg', NULL),
(53, 'Tatlı', 'Donut', 300, 'Donut, tatlı bir hamur işidir ve genellikle yuvarlak şekilli olup ortasında bir delik bulunur. Farklı aromalar ve üst malzemelerle çeşitlendirilen donutlar, sıcak servis edilir ve genellikle şekerli, çikolatalı veya meyve soslarıyla kaplanır. 60gr', 60.00, 0, 'uploads/67e54910e9ca2.jpg', NULL),
(54, 'Tatlı', 'Çikolatalı Pasta', 450, 'Çikolatalı pasta, yoğun çikolata lezzetiyle yapılan, genellikle kat kat ve krema ile kaplanmış bir tatlıdır. Zengin, pürüzsüz çikolata kreması, şekerli pastaban ve bazen çikolata parçacıklarıyla süslenmiş bu pasta, tatlı krizlerine mükemmel bir çözüm sunar. 105gr', 155.00, 0, 'uploads/67e54910e9f51.jpg', NULL),
(55, 'Tatlı', 'Fıstık Rüyası', 500, 'Fıstık Rüyası, kadife gibi yumuşak bir kek tabanı, antep fıstığı, çikolata ile birleşerek her lokmada yoğun bir tat deneyimi sunar. Üzerine fıstık kırıkları eklenerek ekstra fıstık aroması sağlanır. 105gr', 155.00, 1, 'uploads/67e55dc8be740.jpg', NULL),
(56, 'Tatlı', 'Yabanmersinli Şeftalili Pasta', 300, 'Yabanmersini Şeftali Pasta, meyve aromalarının mükemmel bir birleşimiyle yapılan ferahlatıcı bir tatlıdır. Şeftali ve yabanmersini, pastanın üzerine taze meyve olarak eklenerek doğal tatlar sunar. Genellikle hafif bir kek tabanı üzerinde, şeftali ve yabanmersini sosları ile zenginleştirilir. Hem tatlı hem de hafif ekşi bir lezzet sunarak, tatlı severlere ferahlatıcı bir deneyim sağlar. 105gr', 155.00, 0, 'uploads/67e54910ea650.jpg', NULL),
(58, 'İçecek', 'İtalyan Sodası', 150, 'İtalyan soda, meyve şurubu, soda ve buzdan yapılan ferahlatıcı bir içecektir. Genellikle tatlandırıcı olarak meyve şurupları (örneğin çilek, lime, vişne, şeftali vb.) kullanılır ve içecek, sodanın gazlı yapısı ile canlandırıcı bir deneyim sunar. İtalyan sodası, özellikle yaz aylarında, serinletici ve lezzetli bir alternatif olarak popülerdir. 300mL', 110.00, 0, 'uploads/67e54b3fdac3d.jpg', NULL),
(59, 'İçecek', 'Frozen', 200, 'Frozen, buzlu ve karıştırılarak yapılan içecekleri tanımlar. Özellikle yaz aylarında serinletici ve ferahlatıcı bir seçenek olarak popülerdir. Frozen içecekler meyve aromaları ve buzla karıştırılarak yoğun, kıvamlı bir içecek haline gelir. Bu tür içecekler \"frozen\" veya \"slushie\" olarak da adlandırılabilir. 300mL', 130.00, 0, 'uploads/67e54b3fdae79.jpg', NULL),
(60, 'İçecek', 'Cool Lime', 150, 'Cool Lime, özellikle yaz aylarında popüler olan, ferahlatıcı bir içecektir. Lime (yeşil limon) suyu, şeker ve soda gibi malzemelerle yapılır. Bu içecek, limonun asidik ve tazeleyici tadı ile serinletici bir deneyim sunar. 300mL\r\n\r\n', 120.00, 0, 'uploads/67e54b3fdb0f0.jpg', NULL),
(61, 'İçecek', 'Milkshake', 550, 'Milkshake, süt ve tatlandırıcılar (şeker, şurup, vb.) ile yapılan kremamsı, yoğun ve tatlı bir içecektir. Çoğunlukla bir tatlı veya atıştırmalık olarak tüketilen milkshake, özellikle yaz aylarında serinletici ve lezzetli bir seçenektir. Farklı tatlar ve malzemelerle çeşitlendirilebilir, bu da milkshake\'i herkesin damak zevkine göre özelleştirilebilir hale getirir. 300mL', 120.00, 0, 'uploads/67e54bb079125.jpg', NULL),
(62, 'İçecek', 'Rio Special', 100, 'Rio Special, ferahlatıcı ve lezzetli bir içecek olup, nar ve mavi portakalın birleşimiyle eşsiz bir tat sunar. Bu içecek, meyve sularının tazeleyici özelliklerinin yanı sıra, eklenen aromalarla da damakta hoş bir iz bırakır. Özellikle yaz aylarında serinletici bir seçenek olarak tercih edilebilir. 300mL', 125.00, 0, 'uploads/67e54b3fdb496.jpg', NULL),
(63, 'İçecek', 'Hibiscus', 100, 'Hibiscus İçeceği, hibiscus çiçeğinden yapılan ve soğuk servis edilen ferahlatıcı bir içecektir. Hibiscus, kırmızı renkteki çiçekleriyle tanınan bir bitkidir ve genellikle sağlık yararları ile bilinir. Soğuk hibiscus içeceği, hafif ekşi ve tatlı bir tat sunar, aynı zamanda antioksidanlar bakımından zengindir ve serinletici bir etkiye sahiptir. 300mL', 120.00, 0, 'uploads/67e54b3fdb6d5.jpg', NULL),
(64, 'İçecek', 'Sade/Meyveli Soda', 100, 'Sade/Meyveli Soda, gazlı su ve farklı tatların birleşimiyle yapılan, ferahlatıcı ve serinletici bir içecektir. Sade soda, yalnızca gazlı su ile yapılırken, meyveli soda, meyve şurupları veya taze meyve eklenerek tatlandırılır. Genellikle yaz aylarında tercih edilen bu içecekler, sağlıklı ve hafif seçenekler olarak da öne çıkar. 200mL\r\nSade soda Fiyat(TL): 40 TL', 60.00, 0, 'uploads/67e54b3fdb872.jpg', NULL),
(65, 'İçecek', 'Boba Latte', 248, 'Boba Latte, tatlı ve kremsi bir içecek olup, boba (veya bubble tea) ile yapılan bir kahve içeceğidir. Bu içecek, kahve, süt ve boba\'nın birleşiminden oluşur ve tatlı bir deneyim sunar. Genellikle, tatlılık ve doku bakımından farklı türlerde sunulabilir. 300mL', 180.00, 1, 'uploads/67e54b3fdba6a.jpg', NULL),
(66, 'İçecek', 'Bubble İtalyan Soda', 250, 'Bubble İtalyan Soda, İtalyan sodası ve boba birleşiminden oluşan, ferahlatıcı ve tatlı bir içecektir. İtalyan soda, genellikle soda ve meyve şurubunun karıştırılmasıyla yapılan gazlı bir içecektir. Bubble kısmı ise, içine eklenen boba sayesinde içeceğe farklı bir doku ve eğlenceli bir özellik katar. Bu içecek, gazlı ve meyvemsi bir tat sunar. 300mL', 155.00, 0, 'uploads/67e54b3fdbc7a.jpg', NULL),
(67, 'İçecek', 'Bubble Tea', 300, 'Bubble Tea, aynı zamanda Boba Tea olarak da bilinir, Tayvan kökenli popüler bir içecektir. Bu içecek, boba ve soğuk çay ile yapılan bir karışımdan oluşur. Boba, içeceğin alt kısmına eklenen, patlatılabilir ve tatlı toplardır. Bubble tea, tatlılık, ferahlık ve patlatılabilirlik gibi özellikleri birleştirerek eğlenceli ve benzersiz bir içecek deneyimi sunar. 300mL', 130.00, 0, 'uploads/67e54b3fdbe17.jpg', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `qr_islemleri`
--

CREATE TABLE `qr_islemleri` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `islem_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `islem_tipi` varchar(50) NOT NULL DEFAULT 'okuma'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Tablo döküm verisi `qr_islemleri`
--

INSERT INTO `qr_islemleri` (`id`, `user_id`, `islem_tarihi`, `islem_tipi`) VALUES
(1, 9, '2025-02-01 11:13:06', 'ekle'),
(2, 10, '2025-02-01 19:34:32', 'ekle'),
(3, 10, '2025-02-01 19:43:38', 'ekle'),
(4, 22, '2025-02-02 08:56:54', 'ekle'),
(5, 9, '2025-03-03 08:37:26', 'ekle'),
(6, 24, '2025-03-16 17:34:30', 'ekle'),
(7, 24, '2025-03-16 17:35:09', 'kullan'),
(8, 24, '2025-03-16 17:43:01', 'ekle'),
(9, 24, '2025-03-16 17:43:32', 'ekle'),
(10, 24, '2025-03-16 17:43:59', 'kullan'),
(11, 24, '2025-03-16 17:45:38', 'ekle'),
(12, 24, '2025-03-16 17:47:19', 'ekle'),
(13, 24, '2025-03-16 17:47:33', 'ekle'),
(14, 9, '2025-03-16 17:50:38', 'ekle'),
(15, 9, '2025-03-16 17:52:15', 'ekle');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yoneticiler`
--

CREATE TABLE `yoneticiler` (
  `admin_id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `ad_soyad` varchar(100) NOT NULL,
  `eposta` varchar(100) NOT NULL,
  `parola` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Tablo döküm verisi `yoneticiler`
--

INSERT INTO `yoneticiler` (`admin_id`, `kullanici_adi`, `ad_soyad`, `eposta`, `parola`) VALUES
(1, 'sunguerd', 'Sungu Erdem', 'sunguerd@icloud.com', '$2y$10$Z4veb.YnhYSwN6H38F/x9.ZmLWCWsrrYRM3FXgwcdh15N0nPdfFQy');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yorumlar`
--

CREATE TABLE `yorumlar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `yildiz_puani` int(11) DEFAULT NULL CHECK (`yildiz_puani` between 1 and 5),
  `yorum` mediumtext DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Tablo döküm verisi `yorumlar`
--

INSERT INTO `yorumlar` (`id`, `user_id`, `menu_id`, `yildiz_puani`, `yorum`) VALUES
(1, 9, 1, 4, 'Güzel bir kahve'),
(2, 22, 1, 5, 'Çok güzel bie kahve'),
(3, 9, 1, 4, 'Test'),
(4, 3, 22, 5, 'Gayet güzel bir içecek');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `favoriler`
--
ALTER TABLE `favoriler`
  ADD PRIMARY KEY (`user_id`,`menu_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Tablo için indeksler `kampanyalar`
--
ALTER TABLE `kampanyalar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `eposta` (`eposta`),
  ADD UNIQUE KEY `telefon_no` (`telefon_no`);

--
-- Tablo için indeksler `kuponlar`
--
ALTER TABLE `kuponlar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kupon_kodu` (`kupon_kodu`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `qr_islemleri`
--
ALTER TABLE `qr_islemleri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `yoneticiler`
--
ALTER TABLE `yoneticiler`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `eposta` (`eposta`),
  ADD UNIQUE KEY `kullanici_adi` (`kullanici_adi`);

--
-- Tablo için indeksler `yorumlar`
--
ALTER TABLE `yorumlar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `kampanyalar`
--
ALTER TABLE `kampanyalar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Tablo için AUTO_INCREMENT değeri `kuponlar`
--
ALTER TABLE `kuponlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- Tablo için AUTO_INCREMENT değeri `qr_islemleri`
--
ALTER TABLE `qr_islemleri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Tablo için AUTO_INCREMENT değeri `yoneticiler`
--
ALTER TABLE `yoneticiler`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `yorumlar`
--
ALTER TABLE `yorumlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
