-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               9.7.0 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table ggmart.kategori
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  PRIMARY KEY (`id_kategori`),
  KEY `idx_id_kategori` (`id_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ggmart.kategori: ~8 rows (approximately)
INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`) VALUES
	(76, 'Beras & Pangan Pokok', 'Produk beras lokal, jagung, kacang, dan bahan pokok lainnya.'),
	(77, 'Minyak & Bumbu Dapur', 'Minyak lokal, minyak kelapa, rempah-rempah, garam, gula, dan bumbu.'),
	(78, 'Kopi & Minuman', 'Kopi lokal NTT, teh herbal, minuman tradisional, dan olahan minuman lainnya.'),
	(79, 'Madu & Herbal', 'Madu hutan, jahe, minyak kemiri, dan produk kesehatan tradisional.'),
	(80, 'Camilan & Snack', 'Kerupuk, abon ikan, keripik singkong, dan makanan ringan.'),
	(82, 'Kerajinan & Tenun', 'Tenun ikat, souvenir lokal, dan kerajinan tangan khas NTT.\n\n\nxxdnfsaj'),
	(84, 'Sayur & Produk Segar', 'Sayuran segar dari petani lokal.'),
	(85, 'Frozen Food', 'Makanan beku dan olahan penyimpanan dingin.');

-- Dumping structure for table ggmart.produk
CREATE TABLE IF NOT EXISTS `produk` (
  `kode_produk` varchar(15) NOT NULL,
  `id_kategori` int DEFAULT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `harga_jual` decimal(12,2) NOT NULL,
  `deskripsi` text,
  `satuan_dasar` varchar(10) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `asal_produk` varchar(100) DEFAULT NULL,
  `tagline` varchar(100) DEFAULT NULL,
  `stok` int DEFAULT '0',
  `terjual` int DEFAULT '0',
  `tanggal_dibuat` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`kode_produk`),
  KEY `idx_kode_produk` (`kode_produk`),
  KEY `idx_nama_produk` (`nama_produk`),
  KEY `idx_id_kategori` (`id_kategori`),
  KEY `idx_tanggal_dibuat` (`tanggal_dibuat`),
  KEY `idx_produk_penjualan` (`id_kategori`,`terjual`),
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ggmart.produk: ~55 rows (approximately)
INSERT INTO `produk` (`kode_produk`, `id_kategori`, `nama_produk`, `harga_jual`, `deskripsi`, `satuan_dasar`, `gambar`, `asal_produk`, `tagline`, `stok`, `terjual`, `tanggal_dibuat`) VALUES
	('PRD_00016875477', 80, 'CNC COOKIES NASTAR', 65000.00, '', 'Toples', '/produk/PRD_00016875477.png', 'CNC COOKIES, PENFUI KUPANG NTT', 'Nastar CNC renyah, rasa kelapa asli Kupang.', 24, 26, '2025-12-19 12:53:23'),
	('PRD_00877921510', 80, 'CNC COOKIES SAGU KEJU', 45000.00, '', 'Toples', '/produk/PRD_00877921510.png', 'CNC COOKIES, PENFUI KUPANG NTT', 'Rasa gurih keju dengan sentuhan sagu alami, langsung dari Kupang.', 0, 0, '2025-12-19 12:54:49'),
	('PRD_02053183769', 80, 'Kue Coklat Spekuk CNC COOKIES', 45000.00, '', 'Toples', '/produk/PRD_02053183769.png', 'CNC COOKIES, PENFUI KUPANG NTT', 'Spekuk coklat renyah, rasa asli Kupang, cocok ngemil santai.', 0, 0, '2025-12-19 12:56:47'),
	('PRD_02638121216', 78, 'Coca Cola 330 ML', 7000.00, '', 'Kaleng', '/produk/PRD_02638121216.webp', '', NULL, 0, 0, '2025-12-28 14:17:18'),
	('PRD_03566984797', 80, 'Jagung Pulut Pedas', 10000.00, 'Nikmati kelezatan camilan autentik khas Nusa Tenggara Timur dengan Jagung Pulut Pedas. Terbuat dari jagung pulut (jagung ketan) pilihan yang terkenal dengan teksturnya yang unik—lebih empuk, padat, dan sedikit kenyal saat dikunyah, sangat berbeda dari jagung biasa.', 'Pcs', '/produk/PRD_03566984797.png', 'Klasis Kota Kupang Barat', 'Jagung pulut pedas, empuk kenyal, cita rasa khas NTT.', 45, 5, '2025-12-19 12:59:18'),
	('PRD_05304045813', 79, 'Minyak Kemiri', 30000.00, '', 'Botol', '/produk/PRD_05304045813.png', 'Pemuda Amanuban Tengah Selatan', 'Minyak kemiri Pemuda Amanuban, wangi alami, cocok untuk masak sehari-hari.', 0, 0, '2025-12-19 13:02:12'),
	('PRD_06662692105', 78, 'Minuman Perjamuan Perjanjian Baru', 75000.00, '', 'Botol', '/produk/PRD_06662692105.png', 'SINODE GMIT', 'Minuman Perjamuan Perjanjian baru, dari SINODE GMIT.', 0, 0, '2025-12-19 13:04:28'),
	('PRD_07627479596', 79, 'Minyak Kayu Putih Tuamese', 35000.00, '', 'Botol', '/produk/PRD_07627479596.png', 'GMIT EBENHAEZER TUAMESE', 'Minyak kayu putih Tuamese, wangi alami, segar dari Kupang.', 18, 2, '2025-12-19 13:06:04'),
	('PRD_07639457476', 78, 'Pulpy orange 300 ML', 7000.00, '', 'Botol', '/produk/PRD_07639457476.webp', '', NULL, 0, 0, '2025-12-28 16:32:43'),
	('PRD_08361677758', 78, 'Bear Brand 189 ML', 11500.00, '', 'Pcs', '/produk/PRD_08361677758.png', '', NULL, 0, 0, '2025-12-28 16:33:56'),
	('PRD_08959734393', 78, 'Fruit Tea Blaccurent 350 ML', 7000.00, '', 'Pcs', '/produk/PRD_08959734393.jpg', '', NULL, 0, 0, '2025-12-28 16:34:55'),
	('PRD_09433004187', 78, 'Fruit Tea Strobery 350 ML', 7000.00, '', 'Botol', '/produk/PRD_09433004187.jpg', '', NULL, 0, 0, '2025-12-28 16:35:43'),
	('PRD_09992327462', 79, 'Gula Sabu Putih 350ML', 21000.00, 'Kegunaan:\r\n-Mengatasi sakit lambung\r\n-Mengatasi anemia\r\n-Mengatasi nafsu makan yang kurang normal\r\n-Dapat meresahkan lambung, kronis dll', 'Botol', '/produk/PRD_09992327462.png', 'Air Nona, Kota Raja Kota Kupang', 'Gula Sabu alami, bantu atasi lambung dan tambah selera makan.', 65, 3, '2025-12-19 13:10:01'),
	('PRD_10996118655', 79, 'Madu Batu', 40000.00, '', 'Botol', '/produk/PRD_10996118655.png', 'Klasis Semau', 'Madu Batu Klasis Semau, manis alami khas Kupang.', 27, 3, '2025-12-19 13:11:41'),
	('PRD_11650264519', 79, 'Madu Pohon', 40000.00, '', 'Botol', '/produk/PRD_11650264519.png', 'Amfoang Selatan', 'Rasa manis alami khas Amfoang', 17, 3, '2025-12-19 13:12:46'),
	('PRD_11779732260', 78, 'Dancow Coklat', 5000.00, '', 'Sachet', '/produk/PRD_11779732260.jpg', '', NULL, 0, 0, '2025-12-28 16:49:39'),
	('PRD_12267226394', 78, 'Teh Botol Sosro 350 ML', 7000.00, '', 'Botol', '/produk/PRD_12267226394.webp', '', NULL, 0, 0, '2025-12-28 16:40:26'),
	('PRD_12761562947', 78, 'Buavita 245 ML', 8500.00, '', 'Pcs', '/produk/PRD_12761562947.jpg', '', NULL, 0, 0, '2025-12-28 16:41:16'),
	('PRD_13359265350', 78, 'Nipis Madu 330 ML', 5500.00, '', 'Botol', '/produk/PRD_13359265350.png', '', NULL, 0, 0, '2025-12-28 16:42:15'),
	('PRD_13699127602', 78, 'Good Day 250 ML', 7500.00, '', 'Botol', '/produk/PRD_13699127602.jpg', '', NULL, 0, 0, '2025-12-28 16:42:49'),
	('PRD_13763803432', 77, 'Kunyit Asam Instan', 20000.00, '', 'Pcs', '/produk/PRD_13763803432.png', 'Jemaat Fatubena', 'Kunyit Asam Instan Jemaat Fatubena, rasa tradisional siap saji.', 0, 0, '2025-12-19 11:09:25'),
	('PRD_14080726422', 78, 'Floridina Jeruk 350 ML', 5000.00, '', 'Botol', '/produk/PRD_14080726422.jpg', '', NULL, 0, 0, '2025-12-28 16:43:28'),
	('PRD_14954711260', 78, 'Golda Coffee 200 ML', 5500.00, '', 'Botol', '/produk/PRD_14954711260.jpg', '', NULL, 0, 0, '2025-12-28 16:44:55'),
	('PRD_15650276204', 78, 'Chocolatos Drink', 3000.00, '', 'Shachet', '/produk/PRD_15650276204.jpg', '', NULL, 0, 0, '2025-12-28 16:46:05'),
	('PRD_16805975959', 78, 'Good Day', 3500.00, '', 'Sachet', '/produk/PRD_16805975959.png', '', NULL, 0, 0, '2025-12-28 16:48:00'),
	('PRD_17324769388', 78, 'Dancow Putih', 5000.00, '', 'Sachet', '/produk/PRD_17324769388.jpg', '', NULL, 0, 0, '2025-12-28 16:48:52'),
	('PRD_18129502243', 78, 'Energen Coklat', 3000.00, '', 'Sachet', '/produk/PRD_18129502243.jpg', '', NULL, 0, 0, '2025-12-28 16:50:12'),
	('PRD_18617013290', 78, 'Energen Vanila', 3000.00, '', 'Sachet', '/produk/PRD_18617013290.jpg', '', NULL, 0, 0, '2025-12-28 16:51:01'),
	('PRD_18639457606', 78, 'Aguamor', 500.00, '', 'Gelas', '/produk/PRD_18639457606.png', '', '', 100, 0, '2025-12-28 14:04:23'),
	('PRD_18974646805', 78, 'Top Coffee Susu', 2000.00, '', 'Sachet', '/produk/PRD_18974646805.jpg', '', NULL, 0, 0, '2025-12-28 16:51:37'),
	('PRD_19493878708', 78, 'Kapal Api', 2500.00, '', 'Sachet', '/produk/PRD_19493878708.webp', '', NULL, 0, 0, '2025-12-28 16:52:30'),
	('PRD_20037601647', 78, 'Top Coffee Gula Aren', 2500.00, 'Top Coffee Gula Aren adalah varian kopi instan 3-in-1 dari Wings Food yang memadukan kekuatan rasa kopi tubruk asli, kelembutan susu, dan legitnya gula aren alami. Produk inovatif ini dirancang bagi generasi modern yang menyukai tren kopi susu ala kafe, namun menginginkan kepraktisan dan harga yang ekonomis dalam format sachet.Berikut adalah rincian deskripsi produk lengkap untuk kebutuhan penjualan, katalog, atau promosi:🌟 Keunggulan ProdukKombinasi Karakter Kuat: Menggunakan racikan biji kopi Robusta dan Arabika pilihan untuk menghasilkan aroma harum yang kompleks dan rasa kopi yang mantap.Sentuhan Gula Aren Asli: Memberikan sensasi rasa manis yang legit, lembut di tenggorokan, khas, dan tidak membuat enek.Ampas Kopi yang Halus: Memiliki tekstur serbuk kopi tubruk mikro yang halus, sehingga sensasi strong kopinya langsung terasa tanpa perlu menunggu ampas turun lama.Fleksibel Disajikan: Sangat nikmat diseduh hangat sebagai teman beraktivitas maupun disajikan dingin dengan es batu untuk kesegaran maksimal.📝 Spesifikasi & KomposisiJenis Produk: Kopi Instan Susu + Gula Aren.Komposisi: Gula, krimer nabati, kopi bubuk instan (9,6%), susu skim bubuk, perisa sintetik, garam, serta pemanis buatan (Asesulfam-K, Sukralosa).Berat per Sachet: 22 gram.Informasi Nilai Gizi: Mengandung sekitar 100 kalori, 2,5 gram lemak, dan 18 gram karbohidrat per sachet.Kemasan di Pasaran: Tersedia dalam bentuk pouch (isi 6+3 sachet) maupun kemasan renteng (isi 15 sachet).☕ Cara Penyajian ResmiSajian Panas: Tuangkan 1 sachet ke dalam cangkir, seduh dengan 150 ml air panas, lalu aduk rata.Sajian Dingin (Es Kopi): Tuangkan 1 sachet ke dalam gelas, seduh dengan 60 ml air panas hingga larut, kemudian tambahkan sekitar 90 gram es batu.', 'Sachet', '/produk/PRD_20037601647.jpg', '', NULL, 0, 0, '2025-12-28 16:53:23'),
	('PRD_20669949571', 78, 'Top White Coffee', 2500.00, 'Top White Coffee adalah varian kopi instan siap seduh dari Wings Corp yang memadukan kelembutan krimer nabati dengan karakter rasa kopi yang kuat dan harum. Produk ini dirancang khusus untuk para pencinta kopi yang menginginkan sensasi white coffee premium dengan harga ekonomis dan kemasan yang praktis.Berikut adalah rincian deskripsi produk untuk kebutuhan penjualan, katalog, atau promosi Anda:🌟 Keunggulan ProdukPerpaduan Biji Kopi Premium: Mengombinasikan biji kopi Robusta dan Arabika pilihan untuk menciptakan rasa yang seimbang, aroma harum, dan karakter yang kompleks.Tekstur Lembut & Creamy: Menggunakan krimer berkualitas tinggi yang menghasilkan konsistensi lembut di mulut tanpa menutupi rasa asli kopinya.Ramah di Lambung: Ciri khas proses white coffee yang menggunakan suhu pemanggangan lebih rendah membuat tingkat keasamannya cenderung lebih ramah bagi lambung.Praktis & Ekonomis: Dikemas dalam bentuk sachet siap seduh yang sudah mencakup kopi, gula, dan krimer.\r\n\r\n\r\nznfsif', 'Sachet', '/produk/PRD_20669949571.png', '', NULL, 0, 0, '2025-12-28 16:54:27'),
	('PRD_20715061932', 78, 'Aqua 330 ML', 3000.00, '', 'Botol', '/produk/PRD_20715061932.jpg', '', NULL, 0, 0, '2025-12-28 14:07:51'),
	('PRD_21948382265', 78, 'Teh Pucuk 350 ML', 5000.00, '', 'Botol', '/produk/PRD_21948382265.jpg', '', NULL, 0, 0, '2025-12-28 14:09:54'),
	('PRD_23259949343', 78, 'Teh Kotak 300 ML', 5000.00, '', 'Pcs', '/produk/PRD_23259949343.png', '', NULL, 0, 0, '2025-12-28 14:12:05'),
	('PRD_23765443715', 78, 'Sari Kacang Hijau 250 ML', 6000.00, '', 'Pcs', '/produk/PRD_23765443715.jpg', '', NULL, 0, 0, '2025-12-28 14:12:56'),
	('PRD_24254357879', 78, 'You C 1000 ORANGE', 10000.00, '', 'Botol', '/produk/PRD_24254357879.webp', '', NULL, 0, 0, '2025-12-28 14:13:45'),
	('PRD_25312761177', 78, 'Fanta 330 ML', 7000.00, '', 'Kaleng', '/produk/PRD_25312761177.jpg', '', NULL, 0, 0, '2025-12-28 14:15:31'),
	('PRD_25863427374', 78, 'Sprite 330 ML', 7000.00, '', 'Kaleng', '/produk/PRD_25863427374.jpg', '', NULL, 0, 0, '2025-12-28 14:16:26'),
	('PRD_27131014176', 78, 'Pocari Sweat 350 ML', 7500.00, '', 'Botol', '/produk/PRD_27131014176.webp', '', NULL, 0, 0, '2025-12-28 14:18:33'),
	('PRD_27464033182', 78, 'Pocari Sweat 500 ML', 8500.00, '', 'Botol', '/produk/PRD_27464033182.webp', '', NULL, 0, 0, '2025-12-28 14:19:06'),
	('PRD_34331235266', 77, 'Kunyit Instan', 20000.00, 'Kunyit Instan dari Jemaat Fatubena adalah bubuk kunyit siap seduh alami yang diolah secara higienis tanpa bahan pengawet kimia. Produk ini merupakan hasil produksi lokal yang tidak hanya menawarkan kepraktisan untuk kesehatan dan bumbu dapur, tetapi juga mendukung perekonomian komunitas usaha mikro di daerah Fatubena.Berikut adalah draf deskripsi produk yang dioptimalkan untuk halaman penjualan toko online Anda:🌟 Keunggulan ProdukAlami & Higienis: Terbuat dari rimpang kunyit pilihan yang diproses bersih tanpa campuran zat kimia berbahaya.Multifungsi: Dapat diseduh langsung sebagai minuman herbal (jamu) hangat penambah daya tahan tubuh, maupun digunakan sebagai bumbu praktis untuk masakan dapur.Mendukung Produk Lokal: Setiap pembelian produk ini berkontribusi langsung pada pemberdayaan ekonomi Jemaat Fatubena.📝 Spesifikasi ProdukNama Produk: Kunyit Instan Jemaat FatubenaKode Produk: PRD_34331235266Kategori: Minyak & Bumbu DapurHarga: Rp 20.000Status Stok: 1 unit tersedia (Sangat Terbatas)Asal / Produsen: Jemaat Fatubena☕ Cara PenggunaanUntuk Minuman Kesehatan: Ambil 1-2 sendok teh bubuk kunyit instan, seduh dengan segelas air hangat. Bisa ditambahkan madu atau lemon sesuai selera.Untuk Bumbu Masak: Masukkan secukupnya ke dalam masakan seperti soto, gulai, ayam ungkep, atau hidangan lainnya sesuai takaran resep.', 'PCS', '/produk/PRD_34331235266.png', 'Jemaat Fatubena', 'Kunyit Instan siap seduh dan alami. tanpa bahan kimia.', 0, 0, '2025-12-19 11:03:55'),
	('PRD_40477704712', 78, 'Kopi Meto', 55000.00, '', 'Pcs', '/produk/PRD_40477704712.png', 'Kelompok Kofi Metode Bitobe(Sonan, Bioba)', 'Aroma khas Bitobe, rasa kuat alami tiap cangkir.', 0, 0, '2025-12-19 11:14:09'),
	('PRD_55071163152', 77, 'Gula Semut', 10000.00, '', 'Pcs', '/produk/PRD_55071163152.png', 'Imanuel Rote Barat Laut', 'Manis alami gula semut Imanuel, rasa otentik Rote.', 0, 0, '2025-12-19 14:25:09'),
	('PRD_56095135699', 77, 'Garam Yodium Oebelo Tanah Merah', 15000.00, '', 'Bakun', '/produk/PRD_56095135699.png', 'Klasis Kupang Tengah', 'Garam Yodium Oebelo, rasa alami dari Tanah Merah Kupang.', 0, 0, '2025-12-19 14:26:51'),
	('PRD_58480927407', 76, 'Tepung Mokaf Original', 15000.00, '', 'Pcs', '/produk/PRD_58480927407.png', 'Jemaat Sopo, Klasis Amanuban Tengah Uatar', 'Tepung Mokaf asli Sopo, tekstur halus, rasa otentik.', 0, 0, '2025-12-19 14:30:50'),
	('PRD_60274096744', 78, 'Kopi Amfoang', 60000.00, '', 'Pcs', '/produk/PRD_60274096744.png', 'Istana Hijau Lelogama', 'Kopi Amfoang dari Istana Hijau, rasa alami yang memikat.', 0, 0, '2025-12-19 14:33:49'),
	('PRD_61762411140', 77, 'Kecap Manis Sedap', 1.00, '', 'Pcs', '/produk/PRD_61762411140.png', '', NULL, 0, 0, '2025-12-19 14:36:18'),
	('PRD_65671504693', 79, 'Gula Lempeng Tuamese 1KG', 30000.00, '', 'Pcs', '/produk/PRD_65671504693.png', '-', 'Gula lempeng Tuamese, manis pas, serbaguna untuk masak.', 0, 0, '2025-12-19 14:42:49'),
	('PRD_66878007736', 79, 'Gula Lempeng Rote', 10000.00, '', 'Pcs', '/produk/PRD_66878007736.png', 'Imanuel Ndau / RBL', 'Manis alami Gula Lempeng Rote, rasa khas pulau sejuk.', 0, 0, '2025-12-19 14:44:49'),
	('PRD_70956539291', 79, 'Nu Koennu Minyak Herbal', 50000.00, '...', 'Botol', '/produk/PRD_70956539291.png', 'Napago Farm Kupang, NTT', 'Minyak herbal Napago Farm, alami, menenangkan tubuh secara lembut.', 0, 0, '2025-12-19 14:51:37'),
	('PRD_90212598279', 78, 'Aqua 600 ML', 5000.00, '', 'Botol', '/produk/PRD_90212598279.webp', '', NULL, 0, 0, '2025-12-28 14:08:45'),
	('PRD_96761227467', 80, 'Cokelat KELOR-S', 15000.00, '', 'Pcs', '/produk/PRD_96761227467.png', 'Niki Niki Amanuban Tengah, TTS', 'Rasa alami khas Niki niki Amanuban Tengah.', 0, 0, '2025-12-19 12:47:58'),
	('PRD_97964235986', 80, 'Kacang Telur', 12500.00, '', 'Pcs', '/produk/PRD_97964235986.png', 'HomeMade Moms, Penfui Kupang NTT', 'Kacang telur Homemade Moms, gurih alami rasa tradisional Kupang.', 0, 0, '2025-12-19 12:49:58');


-- Dumping structure for table ggmart.mutasi_stok
CREATE TABLE IF NOT EXISTS `mutasi_stok` (
  `id_mutasi` int NOT NULL AUTO_INCREMENT,
  `kode_produk` varchar(15) DEFAULT NULL,
  `type` enum('masuk','keluar') NOT NULL,
  `jumlah` int NOT NULL,
  `harga_pokok` decimal(12,2) DEFAULT NULL,
  `keterangan` text,
  `sisa_stok` int DEFAULT NULL,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mutasi`),
  KEY `kode_produk` (`kode_produk`),
  KEY `idx_tanggal` (`tanggal`),
  CONSTRAINT `mutasi_stok_ibfk_1` FOREIGN KEY (`kode_produk`) REFERENCES `produk` (`kode_produk`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ggmart.mutasi_stok: ~12 rows (approximately)
INSERT INTO `mutasi_stok` (`id_mutasi`, `kode_produk`, `type`, `jumlah`, `harga_pokok`, `keterangan`, `sisa_stok`, `tanggal`) VALUES
	(57, 'PRD_09992327462', 'masuk', 50, 15000.00, '', 45, '2026-06-07 14:01:04'),
	(58, 'PRD_07627479596', 'masuk', 20, 30000.00, '', 18, '2026-06-07 14:01:20'),
	(59, 'PRD_10996118655', 'masuk', 30, 30000.00, '', 27, '2026-06-07 14:01:37'),
	(60, 'PRD_11650264519', 'masuk', 20, 30000.00, '', 17, '2026-06-07 14:02:03'),
	(61, 'PRD_00016875477', 'masuk', 20, 55000.00, '', 0, '2026-06-07 14:02:21'),
	(62, 'PRD_09992327462', 'keluar', 2, 15000.00, 'hilang saat pengantaran', NULL, '2026-06-07 15:18:26'),
	(63, 'PRD_03566984797', 'masuk', 50, 9000.00, '', 45, '2026-07-07 16:38:04'),
	(64, 'PRD_09992327462', 'masuk', 20, 15000.00, 'stok masuk', 20, '2026-07-07 16:41:35'),
	(66, 'PRD_18639457606', 'masuk', 100, 400.00, '', 100, '2026-08-21 12:22:27'),
	(67, 'PRD_00016875477', 'masuk', 10, 40001.00, '', 4, '2026-08-21 12:27:30'),
	(68, 'PRD_00016875477', 'masuk', 10, 40000.00, '', 10, '2026-08-21 12:27:47'),
	(69, 'PRD_00016875477', 'masuk', 10, 40000.00, '', 10, '2026-08-21 12:28:03');


-- Dumping structure for table ggmart.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id_pengguna` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pelanggan','admin','kasir','pimpinan') NOT NULL,
  `tanggal_dibuat` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) DEFAULT '0',
  `verify_token` varchar(255) DEFAULT NULL,
  `token_expired` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expired` datetime DEFAULT NULL,
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_id_user` (`id_pengguna`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ggmart.pengguna: ~3 rows (approximately)
INSERT INTO `pengguna` (`id_pengguna`, `nama`, `email`, `no_hp`, `alamat`, `password`, `role`, `tanggal_dibuat`, `is_verified`, `verify_token`, `token_expired`, `reset_token`, `reset_expired`) VALUES
	(1, 'stiven', 'stivenadu01@gmail.com', '081338609228', 'Jln Nasution II, Kayu Puith, Kupang NTT', '$2y$10$.XpEs.z7h5xt68V8cua9o.c6iu5fBbfZvd6giRTExPT6GWS7.5zlS', 'admin', '2025-10-21 20:01:46', 1, NULL, '2026-05-22 15:58:20', NULL, NULL),
	(12, 'Stiven M Adu', 'stivenadu9@gmail.com', '081338609228', 'Jln A.H Nasution 3', '$2y$10$Qb6CXUHFwA2DcPTKb7bq9OeEjZUpLbTdlVxLjd.9.wfWRknXv4bzq', 'pimpinan', '2026-06-01 23:07:02', 1, NULL, '2026-06-02 23:07:02', NULL, NULL),
	(22, 'budi santoso', 'budi@gmail.com', '085829230787', 'Jl Perintis Kemerdekaan 1', '$2y$10$SoOgXtN1GUDpg5szQJmT.OM97MleZeG8ZgsKIx78vHU33or.bxiqu', 'pelanggan', '2026-08-21 23:39:59', 1, NULL, NULL, NULL, NULL);


-- Dumping structure for table ggmart.transaksi
CREATE TABLE IF NOT EXISTS `transaksi` (
  `kode_transaksi` varchar(15) NOT NULL,
  `id_pengguna` int DEFAULT NULL,
  `tanggal_transaksi` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_harga` decimal(12,2) NOT NULL,
  `total_pokok` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','diproses','dibatalkan','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pending',
  `metode_bayar` enum('qris','tunai') DEFAULT 'tunai',
  PRIMARY KEY (`kode_transaksi`),
  KEY `idx_kode_transaksi` (`kode_transaksi`),
  KEY `idx_tanggal_transaksi` (`tanggal_transaksi`),
  KEY `idx_metode_bayar` (`metode_bayar`),
  KEY `idx_id_user` (`id_pengguna`),
  CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ggmart.transaksi: ~16 rows (approximately)
INSERT INTO `transaksi` (`kode_transaksi`, `id_pengguna`, `tanggal_transaksi`, `total_harga`, `total_pokok`, `status`, `metode_bayar`) VALUES
	('GG-04594905709', 22, '2026-08-26 18:34:19', 40000.00, 30000.00, 'selesai', 'tunai'),
	('GG-22236629598', 12, '2026-06-07 14:03:43', 61000.00, 45000.00, 'dibatalkan', NULL),
	('GG-34342366855', 1, '2026-07-07 16:37:14', 80000.00, 60000.00, 'dibatalkan', 'tunai'),
	('GG-34412527401', 1, '2026-07-07 16:37:21', 56000.00, 45000.00, 'selesai', 'tunai'),
	('GG-34450706130', 1, '2026-07-07 16:37:25', 200000.00, 150000.00, 'dibatalkan', 'tunai'),
	('GG-34931808444', 1, '2026-07-07 16:38:13', 30000.00, 27000.00, 'selesai', 'tunai'),
	('GG-34981397590', 1, '2026-07-07 16:38:18', 45000.00, 39000.00, 'selesai', 'tunai'),
	('GG-35021925657', 1, '2026-07-07 16:38:22', 80000.00, 60000.00, 'selesai', 'tunai'),
	('GG-42301889815', 12, '2026-06-07 14:37:10', 86000.00, 70000.00, 'dibatalkan', NULL),
	('GG-48969104116', 1, '2026-06-07 14:48:16', 61000.00, 45000.00, 'selesai', 'tunai'),
	('GG-49144627776', 1, '2026-05-07 14:48:34', 40000.00, 30000.00, 'selesai', 'tunai'),
	('GG-59882919237', 1, '2026-08-21 12:19:48', 10000.00, 9000.00, 'selesai', 'tunai'),
	('GG-65986668915', 22, '2026-08-25 19:16:38', 126000.00, 85001.00, 'selesai', 'tunai'),
	('GG-68311993314', 22, '2026-08-21 23:40:31', 42000.00, 30000.00, 'dibatalkan', 'tunai'),
	('GG-86545325378', 1, '2026-08-21 12:29:05', 975000.00, 825000.00, 'selesai', 'tunai'),
	('GG-86603743348', 1, '2026-08-21 12:30:03', 650000.00, 475005.00, 'selesai', 'tunai');


-- Dumping structure for table ggmart.detail_transaksi
CREATE TABLE IF NOT EXISTS `detail_transaksi` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(15) NOT NULL,
  `kode_produk` varchar(15) DEFAULT NULL,
  `jumlah` int DEFAULT '1',
  `harga_satuan` decimal(12,2) DEFAULT NULL,
  `harga_pokok` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `idx_kode_transaksi` (`kode_transaksi`),
  KEY `idx_kode_produk` (`kode_produk`),
  CONSTRAINT `fk_detail_produk` FOREIGN KEY (`kode_produk`) REFERENCES `produk` (`kode_produk`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`kode_transaksi`) REFERENCES `transaksi` (`kode_transaksi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ggmart.detail_transaksi: ~25 rows (approximately)
INSERT INTO `detail_transaksi` (`id_detail`, `kode_transaksi`, `kode_produk`, `jumlah`, `harga_satuan`, `harga_pokok`) VALUES
	(60, 'GG-22236629598', 'PRD_09992327462', 1, 21000.00, 15000.00),
	(61, 'GG-22236629598', 'PRD_10996118655', 1, 40000.00, 30000.00),
	(62, 'GG-42301889815', 'PRD_09992327462', 1, 21000.00, 15000.00),
	(63, 'GG-42301889815', 'PRD_00016875477', 1, 65000.00, 55000.00),
	(64, 'GG-48969104116', 'PRD_10996118655', 1, 40000.00, 30000.00),
	(65, 'GG-48969104116', 'PRD_09992327462', 1, 21000.00, 15000.00),
	(66, 'GG-49144627776', 'PRD_10996118655', 1, 40000.00, 30000.00),
	(67, 'GG-34342366855', 'PRD_11650264519', 1, 40000.00, 30000.00),
	(68, 'GG-34342366855', 'PRD_10996118655', 1, 40000.00, 30000.00),
	(69, 'GG-34412527401', 'PRD_09992327462', 1, 21000.00, 15000.00),
	(70, 'GG-34412527401', 'PRD_07627479596', 1, 35000.00, 30000.00),
	(71, 'GG-34450706130', 'PRD_10996118655', 5, 40000.00, 30000.00),
	(72, 'GG-34931808444', 'PRD_03566984797', 3, 10000.00, 9000.00),
	(73, 'GG-34981397590', 'PRD_07627479596', 1, 35000.00, 30000.00),
	(74, 'GG-34981397590', 'PRD_03566984797', 1, 10000.00, 9000.00),
	(75, 'GG-35021925657', 'PRD_11650264519', 1, 40000.00, 30000.00),
	(76, 'GG-35021925657', 'PRD_10996118655', 1, 40000.00, 30000.00),
	(77, 'GG-59882919237', 'PRD_03566984797', 1, 10000.00, 9000.00),
	(78, 'GG-86545325378', 'PRD_00016875477', 15, 65000.00, 55000.00),
	(79, 'GG-86603743348', 'PRD_00016875477', 10, 65000.00, 47500.50),
	(80, 'GG-68311993314', 'PRD_09992327462', 2, 21000.00, 15000.00),
	(81, 'GG-65986668915', 'PRD_00016875477', 1, 65000.00, 40001.00),
	(82, 'GG-65986668915', 'PRD_11650264519', 1, 40000.00, 30000.00),
	(83, 'GG-65986668915', 'PRD_09992327462', 1, 21000.00, 15000.00),
	(84, 'GG-04594905709', 'PRD_11650264519', 1, 40000.00, 30000.00);


-- Dumping structure for table ggmart.keranjang
CREATE TABLE IF NOT EXISTS `keranjang` (
  `id_keranjang` int NOT NULL AUTO_INCREMENT,
  `id_pengguna` int NOT NULL,
  `kode_produk` varchar(15) DEFAULT NULL,
  `jumlah` int DEFAULT '1',
  PRIMARY KEY (`id_keranjang`),
  KEY `fk_keranjang_user` (`id_pengguna`),
  KEY `fk_keranjang_produk` (`kode_produk`),
  CONSTRAINT `fk_keranjang_produk` FOREIGN KEY (`kode_produk`) REFERENCES `produk` (`kode_produk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_keranjang_user` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table ggmart.keranjang: ~2 rows (approximately)
INSERT INTO `keranjang` (`id_keranjang`, `id_pengguna`, `kode_produk`, `jumlah`) VALUES
	(232, 22, 'PRD_03566984797', 1),
	(233, 22, 'PRD_09992327462', 1);


/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
