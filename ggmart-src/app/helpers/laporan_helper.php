<?php


function getRekapTransaksiHarian($tanggal, $metode = '')
{
  $conn = db();
  $tanggal = $conn->escape_string($tanggal);
  $metode = $conn->escape_string($metode);
  $query = "
        SELECT 
        kode_transaksi,
        tanggal_transaksi,
        metode_bayar
        FROM transaksi
        WHERE DATE(tanggal_transaksi) = DATE(?)
        AND status='selesai'
    ";

  if (!empty($metode)) {
    $query .= " AND metode_bayar = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $tanggal, $metode);
  } else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tanggal);
  }

  $stmt->execute();
  $res = $stmt->get_result();
  $data = [];
  while ($r = $res->fetch_assoc()) {
    $data[] = $r;
  }
  return $data;
}

function getRekapTransaksiBulanan($bulan, $metode = '')
{
  $conn = db();
  $bulan = $conn->escape_string($bulan);
  $metode = $conn->escape_string($metode);
  $q = "
        SELECT 
            DATE(t.tanggal_transaksi) AS tanggal,
            COUNT(DISTINCT t.kode_transaksi) AS jumlah_transaksi,
            SUM(d.jumlah) AS total_produk,
            SUM(d.harga_pokok * d.jumlah) AS total_pokok,
            SUM(d.harga_satuan * d.jumlah) AS total_jual
        FROM transaksi t
        JOIN detail_transaksi d ON t.kode_transaksi = d.kode_transaksi
        WHERE DATE_FORMAT(t.tanggal_transaksi, '%Y-%m') = '$bulan'
        AND t.status='selesai'
    ";

  if (!empty($metode)) {
    $q .= " AND t.metode_bayar = '$metode'";
  }
  $q .= " GROUP BY DATE(t.tanggal_transaksi)";
  $res = $conn->query($q);
  $data = [];
  while ($r = $res->fetch_assoc()) {
    $data[] = $r;
  }
  return $data;
}

function getRekapTransaksiTahunan($tahun, $metode = '')
{
  $conn = db();
  $tahun = $conn->escape_string($tahun);
  $metode = $conn->escape_string($metode);

  $q = "
        SELECT 
            DATE_FORMAT(t.tanggal_transaksi, '%M') AS bulan,
            COUNT(DISTINCT t.kode_transaksi) AS jumlah_transaksi,
            SUM(d.jumlah) AS total_produk,
            SUM(d.harga_pokok * d.jumlah) AS total_pokok,
            SUM(d.harga_satuan * d.jumlah) AS total_jual
        FROM transaksi t
        JOIN detail_transaksi d ON t.kode_transaksi = d.kode_transaksi
        WHERE YEAR(t.tanggal_transaksi) = '$tahun'
        AND t.status='selesai'
    ";

  if (!empty($metode)) {
    $q .= " AND t.metode_bayar = '$metode'";
  }

  $q .= " GROUP BY bulan";

  $res = $conn->query($q);
  $data = [];
  while ($r = $res->fetch_assoc()) {
    $data[] = $r;
  }

  return $data;
}


function findDetailTransaksi($kode_transaksi)
{
  $conn = db();
  $kode_transaksi = $conn->escape_string($kode_transaksi);
  $res = $conn->query("
    SELECT dt.*, p.nama_produk
    FROM detail_transaksi dt 
    LEFT JOIN produk p ON p.kode_produk = dt.kode_produk  
    WHERE dt.kode_transaksi = '$kode_transaksi'
  ");


  $data = [];
  while ($r = $res->fetch_assoc()) {
    $data[] = $r;
  }
  return $data;
}

// ============================================
// Fungsi untuk mutasi stok
function getLaporanStokProduk($kode_produk = '', $tglMulai = '', $tglSelesai = '')
{
  $conn = db();
  $where = [];
  if ($kode_produk) $where[] = "m.kode_produk = '" . $conn->escape_string($kode_produk) . "'";
  if ($tglMulai) $where[] = "DATE(m.tanggal) >= '" . $conn->escape_string($tglMulai) . "'";
  if ($tglSelesai) $where[] = "DATE(m.tanggal) <= '" . $conn->escape_string($tglSelesai) . "'";

  $whereSql = $where ? "WHERE " . implode(' AND ', $where) : '';

  $q = "
        SELECT m.*, p.satuan_dasar, p.nama_produk
        FROM mutasi_stok m JOIN produk p ON p.kode_produk = m.kode_produk
        $whereSql
        ORDER BY m.tanggal ASC
    ";

  $res = $conn->query($q);
  $data = [];
  while ($r = $res->fetch_assoc()) {
    $data[] = $r;
  }
  return $data;
}

function nf($number)
{
  return number_format($number, 0, ',', '.');
}

function date_indo($format = 'j F Y', $tanggal = null)
{
  // Tentukan timestamp
  if (is_null($tanggal)) {
    $timestamp = time(); // default: waktu sekarang
  } elseif (is_numeric($tanggal)) {
    $timestamp = (int)$tanggal; // kalau sudah timestamp
  } elseif (is_string($tanggal)) {
    $timestamp = strtotime($tanggal);
    if ($timestamp === false) $timestamp = time(); // fallback
  } elseif ($tanggal instanceof DateTime) {
    $timestamp = $tanggal->getTimestamp(); // dukung objek DateTime
  } else {
    $timestamp = time();
  }

  // Daftar nama hari dan bulan Indonesia
  $hari_indo = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
  ];

  $bulan_indo = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
  ];

  // Format tanggal sesuai input
  $hasil = date($format, $timestamp);

  // Ganti nama hari & bulan Inggris ke Indonesia
  $hasil = strtr($hasil, $hari_indo);
  $hasil = strtr($hasil, $bulan_indo);

  return $hasil;
}
