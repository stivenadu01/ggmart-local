<?php

function getTransaksiList(
  $page = 1,
  $limit = 10,
  $search = null,
  $start = null,
  $end = null,
  $metode = null,
  $user = null,
  $status = null
) {
  $conn = db();
  $offset = ($page - 1) * $limit;
  $conditions = [];

  // SEARCH
  if ($search) {
    $safe = "%" . $conn->real_escape_string($search) . "%";

    $conditions[] = "(
      t.kode_transaksi LIKE '$safe'
      OR EXISTS (
        SELECT 1 FROM detail_transaksi d
        LEFT JOIN produk p ON d.kode_produk = p.kode_produk
        WHERE d.kode_transaksi = t.kode_transaksi
        AND p.nama_produk LIKE '$safe'
      )
    )";
  }

  // FILTER TANGGAL
  if ($start && $end) {
    $safeStart = $conn->real_escape_string($start);
    $safeEnd   = $conn->real_escape_string($end);
    $conditions[] = "t.tanggal_transaksi BETWEEN '$safeStart 00:00:00' AND '$safeEnd 23:59:59'";
  } elseif ($start) {
    $safeStart = $conn->real_escape_string($start);
    $conditions[] = "t.tanggal_transaksi >= '$safeStart 00:00:00'";
  } elseif ($end) {
    $safeEnd = $conn->real_escape_string($end);
    $conditions[] = "t.tanggal_transaksi <= '$safeEnd 23:59:59'";
  }

  // METODE BAYAR
  if ($metode) {
    $safeMetode = $conn->real_escape_string($metode);
    $conditions[] = "t.metode_bayar = '$safeMetode'";
  }

  // FILTER USER
  if ($user) {
    $safeUser = intval($user);
    $conditions[] = "t.id_pengguna = $safeUser";
  }

  //untuk summary hitung hanya yang selesai
  $whereSum = "WHERE t.status='selesai'";
  if (count($conditions) > 0) {
    $whereSum .= " AND " . implode(" AND ", $conditions);
  }

  // FILTER STATUSa
  if ($status) {
    $safeStatus = $conn->real_escape_string($status);
    // support multiple status
    if (str_contains($safeStatus, ',')) {
      $statuses = explode(',', $safeStatus);
      $statuses = array_map(fn($s) => "'" . $conn->real_escape_string(trim($s)) . "'", $statuses);
      $conditions[] = "t.status IN (" . implode(',', $statuses) . ")";
    } else {
      $conditions[] = "t.status = '$safeStatus'";
    }
  }

  $where = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

  // COUNT
  $sqlCount = "SELECT COUNT(*) AS total FROM transaksi t $where";
  $total = $conn->query($sqlCount)->fetch_assoc()['total'] ?? 0;

  // DATA
  $sqlData = "
    SELECT t.*, u.nama AS user, u.role AS user_role
    FROM transaksi t
    LEFT JOIN pengguna u ON t.id_pengguna = u.id_pengguna
    $where
    ORDER BY t.tanggal_transaksi DESC
    LIMIT $limit OFFSET $offset
  ";

  $res = $conn->query($sqlData);

  $data = [];
  while ($row = $res->fetch_assoc()) $data[] = $row;

  // SUMMARY
  $sqlSum = "
    SELECT 
      SUM(t.total_pokok) AS pokok,
      SUM(t.total_harga) AS jual,
      SUM(t.total_harga - t.total_pokok) AS laba
    FROM transaksi t
    $whereSum
  ";

  $totalSummary = $conn->query($sqlSum)->fetch_assoc() ?? [
    'pokok' => 0,
    'jual' => 0,
    'laba' => 0
  ];

  return [$data, $total, $totalSummary];
}


function findTransaksi($kode_transaksi)
{
  $conn = db();
  $stmt = $conn->prepare("SELECT t.*, u.nama AS user, u.role AS user_role FROM transaksi t LEFT JOIN pengguna u ON t.id_pengguna=u.id_pengguna WHERE kode_transaksi = ?");
  $stmt->bind_param("s", $kode_transaksi);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  return $res;
}

function findTransaksiForUpdate($kode_transaksi)
{
  $conn = db();
  $stmt = $conn->prepare("SELECT t.*, u.nama AS user, u.role AS user_role FROM transaksi t LEFT JOIN pengguna u ON t.id_pengguna=u.id_pengguna WHERE t.kode_transaksi = ? FOR UPDATE");
  $stmt->bind_param("s", $kode_transaksi);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  return $res;
}

function tambahTransaksi($data)
{
  $conn = db();
  $sql = "INSERT INTO transaksi (kode_transaksi, id_pengguna, total_harga, total_pokok, status, metode_bayar)
          VALUES (?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "siddss",
    $data['kode_transaksi'],
    $data['id_pengguna'],
    $data['total_harga'],
    $data['total_pokok'],
    $data['status'],
    $data['metode_bayar']
  );
  $res = $stmt->execute();
  $stmt->close();
  return $res;
}

function hapusTransaksi($kode_transaksi)
{
  $conn = db();
  $stmt = $conn->prepare("DELETE FROM transaksi WHERE kode_transaksi = ?");
  $stmt->bind_param("s", $kode_transaksi);
  $res = $stmt->execute();
  $stmt->close();
  return $res;
}

function updateStatusTransaksi($kode_transaksi, $status, $total_pokok = null)
{
  $conn = db();
  if ($total_pokok === null) {
    $stmt = $conn->prepare("UPDATE transaksi SET status = ? WHERE kode_transaksi = ?");
    $stmt->bind_param("ss", $status, $kode_transaksi);
  } else {
    $stmt = $conn->prepare("UPDATE transaksi SET status = ?, total_pokok = ? WHERE kode_transaksi = ?");
    $stmt->bind_param("sds", $status, $total_pokok, $kode_transaksi);
  }
  $res = $stmt->execute();
  $stmt->close();
  return $res;
}





// DASHBOARD
function sumPenjualanByDate($date)
{
  return db()->query("
    SELECT SUM(total_harga) as total 
    FROM transaksi 
    WHERE DATE(tanggal_transaksi) = '$date' AND status='selesai'
  ")->fetch_assoc()['total'] ?? 0;
}

function sumLabaByDate($date)
{
  return db()->query("
    SELECT SUM((dt.harga_satuan - dt.harga_pokok) * dt.jumlah) as laba
    FROM detail_transaksi dt
    JOIN transaksi t ON t.kode_transaksi = dt.kode_transaksi
    WHERE DATE(t.tanggal_transaksi) = '$date' AND t.status='selesai'
  ")->fetch_assoc()['laba'] ?? 0;
}

function countTransaksiByDate($date)
{
  return db()->query("
    SELECT COUNT(*) as total 
    FROM transaksi 
    WHERE DATE(tanggal_transaksi) = '$date' AND status='selesai'
  ")->fetch_assoc()['total'] ?? 0;
}

function getPenjualanTrend7Hari()
{
  $conn = db();

  return $conn->query("
    SELECT 
      DATE(tanggal_transaksi) as tanggal,
      SUM(total_harga) as penjualan,
      SUM(total_harga - total_pokok) as laba
    FROM transaksi
    WHERE tanggal_transaksi >= DATE(NOW() - INTERVAL 6 DAY)
      AND status='selesai'
    GROUP BY DATE(tanggal_transaksi)
    ORDER BY tanggal ASC
  ")->fetch_all(MYSQLI_ASSOC);
}

function sumOmzetByMonth($month)
{
  return db()->query("
    SELECT SUM(total_harga) as total
    FROM transaksi
    WHERE DATE_FORMAT(tanggal_transaksi,'%Y-%m')='$month'
    AND status='selesai'
  ")->fetch_assoc()['total'] ?? 0;
}

function sumLabaByMonth($month)
{
  return db()->query("
    SELECT SUM(total_harga - total_pokok) as laba
    FROM transaksi
    WHERE DATE_FORMAT(tanggal_transaksi,'%Y-%m')='$month'
    AND status='selesai'
  ")->fetch_assoc()['laba'] ?? 0;
}

function getJamRamaiTransaksi()
{
  return db()->query("
    SELECT 
      HOUR(tanggal_transaksi) as jam,
      COUNT(*) as total
    FROM transaksi
    WHERE status='selesai'
    GROUP BY HOUR(tanggal_transaksi)
    ORDER BY total DESC
  ")->fetch_all(MYSQLI_ASSOC);
}
