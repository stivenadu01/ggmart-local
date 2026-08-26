<?php

function findProduk($kode)
{
  $conn = db();
  $stmt = $conn->prepare("SELECT * FROM produk WHERE kode_produk = ?");
  $stmt->bind_param("s", $kode);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  return $result;
}

function getDropdownProduk($search)
{
  $conn = db();
  $safe = "%" . $conn->real_escape_string($search) . "%";

  $sql = "
    SELECT kode_produk, nama_produk, satuan_dasar
    FROM produk 
    WHERE nama_produk LIKE ? OR kode_produk LIKE ?
    ORDER BY nama_produk ASC 
    LIMIT 10
  ";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $safe, $safe);
  $stmt->execute();

  $res = $stmt->get_result();
  $data = [];
  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }

  $stmt->close();
  return $data;
}

function getProdukList($page = 1, $limit = 10, $search = '', $order_by = 'tanggal_dibuat', $order_dir = 'DESC')
{
  $conn = db();

  $offset = ($page - 1) * $limit;

  $allowed_order = ['tanggal_dibuat', 'nama_produk', 'harga_jual', 'stok', 'terjual'];
  if (!in_array($order_by, $allowed_order)) $order_by = 'tanggal_dibuat';

  $order_dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

  $params = [];
  $types  = '';

  $where = "";

  if ($search !== '') {
    $where = "WHERE (
      p.nama_produk LIKE ? OR
      p.kode_produk LIKE ? OR
      p.deskripsi LIKE ? OR
      k.nama_kategori LIKE ?
    )";

    $safe = "%" . $search . "%";
    $params = [$safe, $safe, $safe, $safe];
    $types = "ssss";
  }

  // COUNT
  $sqlCount = "
    SELECT COUNT(*) AS total
    FROM produk p
    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
    $where
  ";

  $stmt = $conn->prepare($sqlCount);
  if (!empty($params)) $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
  $stmt->close();

  // DATA
  $sqlData = "
    SELECT p.*, k.nama_kategori
    FROM produk p
    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
    $where
    ORDER BY $order_by $order_dir
    LIMIT ? OFFSET ?
  ";

  $params[] = $limit;
  $params[] = $offset;
  $types .= "ii";

  $stmt = $conn->prepare($sqlData);
  if (!empty($params)) $stmt->bind_param($types, ...$params);
  $stmt->execute();

  $res = $stmt->get_result();
  $data = [];

  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }

  $stmt->close();

  return [$data, $total];
}

function tambahProduk($data)
{
  $conn = db();

  $sql = "INSERT INTO produk 
    (kode_produk, id_kategori, nama_produk, deskripsi, harga_jual, stok, gambar, satuan_dasar, asal_produk, tagline)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);

  $stmt->bind_param(
    "sissdissss",
    $data['kode_produk'],
    $data['id_kategori'],
    $data['nama_produk'],
    $data['deskripsi'],
    $data['harga_jual'],
    $data['stok'],
    $data['gambar'],
    $data['satuan_dasar'],
    $data['asal_produk'],
    $data['tagline']
  );

  $res = $stmt->execute();
  $stmt->close();
  return $res;
}

function editProduk($kode, $data)
{
  $conn = db();

  $allowed = [
    'nama_produk',
    'id_kategori',
    'deskripsi',
    'harga_jual',
    'stok',
    'terjual',
    'tagline',
    'gambar',
    'satuan_dasar',
    'asal_produk'
  ];

  $set = [];
  $params = [];
  $types = '';

  foreach ($allowed as $col) {
    if (isset($data[$col])) {
      $set[] = "$col = ?";
      $params[] = $data[$col];

      $types .= in_array($col, ['id_kategori', 'stok', 'terjual']) ? 'i'
        : (in_array($col, ['harga_jual']) ? 'd' : 's');
    }
  }

  if (empty($set)) return false;

  $sql = "UPDATE produk SET " . implode(', ', $set) . " WHERE kode_produk = ?";
  $params[] = $kode;
  $types .= 's';

  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$params);

  $res = $stmt->execute();
  $stmt->close();

  return $res;
}

function hapusProduk($kode)
{
  $conn = db();

  $stmt = $conn->prepare("DELETE FROM produk WHERE kode_produk = ?");
  $stmt->bind_param("s", $kode);

  $res = $stmt->execute();
  $stmt->close();

  return $res;
}

function getProdukTrx($search)
{
  $conn = db();

  $safe = "%" . $search . "%";

  $sql = "
    SELECT
      kode_produk,
      nama_produk,
      harga_jual,
      satuan_dasar,
      stok,
      gambar,
      (
        (CASE WHEN asal_produk IS NOT NULL AND asal_produk != '' THEN 1000 ELSE 0 END) +
        (terjual * 5) +
        (stok * 1)
      ) AS score
    FROM produk
    WHERE nama_produk LIKE ? OR kode_produk LIKE ?
    ORDER BY score DESC
    LIMIT 10
  ";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $safe, $safe);
  $stmt->execute();

  $res = $stmt->get_result();
  $data = [];

  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }

  $stmt->close();
  return $data;
}

function getProdukPublic($limit = 12, $offset = 0, $search = '', $kategori = null, $lokal = null, $sort = 'rekomendasi')
{
  $conn = db();

  $params = [];
  $types  = '';

  $where = "WHERE stok > -1";

  if ($search !== '') {
    $where .= " AND nama_produk LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= 's';
  }

  if (!is_null($kategori)) {
    $where .= " AND id_kategori = ?";
    $params[] = $kategori;
    $types .= 'i';
  }

  if (!is_null($lokal)) {
    if ($lokal == 1) {
      $where .= " AND asal_produk IS NOT NULL AND asal_produk != ''";
    } else {
      $where .= " AND (asal_produk IS NULL OR asal_produk = '')";
    }
  }

  switch ($sort) {
    case 'terlaris':
      $order = "ORDER BY terjual DESC";
      break;
    case 'terbaru':
      $order = "ORDER BY tanggal_dibuat DESC";
      break;
    case 'harga_asc':
      $order = "ORDER BY harga_jual ASC";
      break;
    case 'harga_desc':
      $order = "ORDER BY harga_jual DESC";
      break;
    default:
      $order = "ORDER BY score DESC";
      break;
  }

  $sql = "
    SELECT
      kode_produk,
      nama_produk,
      harga_jual,
      gambar,
      asal_produk,
      stok,
      (
        (CASE WHEN asal_produk IS NOT NULL AND asal_produk != '' THEN 5000 ELSE 0 END) +
        (terjual * 5) +
        (stok * 1)
      ) AS score
    FROM produk
    $where
    $order
    LIMIT ? OFFSET ?
  ";

  $params[] = $limit;
  $params[] = $offset;
  $types .= 'ii';

  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();

  $res = $stmt->get_result();
  $data = [];

  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }

  $stmt->close();
  return $data;
}

function getProdukTerkait($kode_produk, $id_kategori, $limit = 24)
{
  $conn = db();

  $sql = "
    SELECT 
    kode_produk,
    nama_produk,
    harga_jual,
    gambar,
    asal_produk,
    stok,
      (
        (CASE WHEN asal_produk IS NOT NULL AND asal_produk != '' THEN 2000 ELSE 0 END) +
        (terjual * 5) +
        (stok * 1)
      ) AS score,
      (
        (id_kategori = ?) * 1000 +
        (CASE WHEN asal_produk IS NOT NULL AND asal_produk != '' THEN 500 ELSE 0 END)
      ) AS priority
    FROM produk
    WHERE stok > -1 AND kode_produk != ?
    ORDER BY priority DESC, score DESC
    LIMIT ?
  ";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("isi", $id_kategori, $kode_produk, $limit);
  $stmt->execute();

  $res = $stmt->get_result();
  $data = [];

  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }

  $stmt->close();
  return $data;
}

// tambahan
function updateStokProduk($kode_produk)
{
  $conn = db();
  $res = $conn->query("SELECT SUM(sisa_stok) AS total FROM mutasi_stok WHERE kode_produk='$kode_produk' AND type='masuk'");
  $stok = $res->fetch_assoc()['total'];

  if (!$stok) $stok = 0;
  // Pastikan integer
  $stok = (int)$stok;

  return $conn->query("UPDATE produk SET stok=$stok WHERE kode_produk='$kode_produk'");
}

function ubahTerjualProduk($kode, $jumlah)
{
  $conn = db();
  return $conn->query("UPDATE produk SET terjual = terjual + '$jumlah' WHERE kode_produk='$kode'");
}

function getProdukLanding()
{
  $conn = db();

  $sql = "
    SELECT
      kode_produk,
      nama_produk,
      harga_jual,
      gambar,
      tagline
    FROM produk
    WHERE asal_produk IS NOT NULL AND asal_produk != ''
    ORDER BY (terjual * 5 + (stok * 1) + (CASE WHEN asal_produk IS NOT NULL AND asal_produk != '' THEN 1000 ELSE 0 END)) DESC
    LIMIT 10
  ";

  $res = $conn->query($sql);
  $data = [];

  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }

  return $data;
}

function cekStokProduk($kode)
{
  $conn = db();
  $stmt = $conn->prepare("SELECT stok FROM produk WHERE kode_produk = ?");
  $stmt->bind_param("s", $kode);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  return $result['stok'];
}


// DASHBOARD
function countAllProduk()
{
  return db()->query("
    SELECT COUNT(*) as total 
    FROM produk
  ")->fetch_assoc()['total'] ?? 0;
}

function getTopProdukTerlaris($limit = 5)
{
  return db()->query("
    SELECT p.nama_produk, SUM(dt.jumlah) as total_terjual
    FROM detail_transaksi dt
    JOIN produk p ON p.kode_produk = dt.kode_produk
    JOIN transaksi t ON t.kode_transaksi = dt.kode_transaksi
    WHERE t.status = 'selesai'
    GROUP BY dt.kode_produk
    ORDER BY total_terjual DESC
    LIMIT $limit
  ")->fetch_all(MYSQLI_ASSOC);
}

function getProdukSlowMoving($limit = 5)
{
  return db()->query("
    SELECT nama_produk, terjual, stok
    FROM produk
    ORDER BY terjual ASC
    LIMIT $limit
  ")->fetch_all(MYSQLI_ASSOC);
}

function getProdukHampirHabis($limit = 10)
{
  return db()->query("
    SELECT nama_produk, stok
    FROM produk
    WHERE stok <= 5
    ORDER BY stok ASC
    LIMIT $limit
  ")->fetch_all(MYSQLI_ASSOC);
}

function getProdukPalingUntung($limit = 5)
{
  return db()->query("
    SELECT p.nama_produk, SUM(dt.jumlah * (dt.harga_satuan - dt.harga_pokok)) as profit
    FROM detail_transaksi dt
    JOIN produk p ON p.kode_produk = dt.kode_produk
    JOIN transaksi t ON t.kode_transaksi = dt.kode_transaksi
    WHERE t.status = 'selesai'
    GROUP BY dt.kode_produk, p.nama_produk
    ORDER BY profit DESC
    LIMIT $limit
  ")->fetch_all(MYSQLI_ASSOC);
}
