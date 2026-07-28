<?php

function findKategori($id)
{
  $conn = db();
  $res = $conn->query("SELECT * FROM kategori WHERE id_kategori = $id");
  $result = $res->fetch_assoc();
  return $result;
}
function getAllKategori()
{
  $conn = db();
  $sql = "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC";
  $res = $conn->query($sql);

  $kategori = [];
  while ($row = $res->fetch_assoc()) {
    $kategori[] = $row;
  }

  return $kategori;
}

function getKategoriList($page = 1, $limit = 10, $search = '')
{
  $conn = db();
  $offset = ($page - 1) * $limit;

  $where = "";
  if ($search !== '') {
    $safe = "%" . $conn->real_escape_string($search) . "%";
    $where = "WHERE nama_kategori LIKE '$safe' OR deskripsi LIKE '$safe'";
  }

  // Hitung total
  $resCount = $conn->query("SELECT COUNT(*) AS total FROM kategori $where");
  $total = $resCount->fetch_assoc()['total'];

  // Ambil data
  $res = $conn->query("
    SELECT id_kategori, nama_kategori, deskripsi
    FROM kategori
    $where
    ORDER BY id_kategori DESC
    LIMIT $limit OFFSET $offset
  ");

  $kategori = [];
  while ($row = $res->fetch_assoc()) {
    $kategori[] = $row;
  }

  return [$kategori, $total];
}

function tambahKategori($data)
{
  $conn = db();
  $sql = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $data['nama_kategori'], $data['deskripsi']);
  $result = $stmt->execute();
  $stmt->close();
  return $result;
}

function editKategori($id, $data)
{
  $conn = db();
  $sql = "UPDATE kategori SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssi", $data['nama_kategori'], $data['deskripsi'], $id);
  $result = $stmt->execute();
  $stmt->close();
  return $result;
}

function hapusKategori($id)
{
  $conn = db();
  $stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
  $stmt->bind_param('i', $id);
  $result = $stmt->execute();
  $stmt->close();
  return $result;
}


// Mengambil kategori untuk halaman landing page
function getLandingKategori()
{
  $conn = db();

  $sql = "
      SELECT 
        k.id_kategori,
        k.nama_kategori,

        -- mengambil 1 gambar produk terlaris di kategori
        (
          SELECT p.gambar 
          FROM produk p 
          WHERE p.id_kategori = k.id_kategori 
          ORDER BY p.terjual DESC 
          LIMIT 1
        ) AS image,

        -- total produk dalam kategori
        (
          SELECT COUNT(*) 
          FROM produk p2 
          WHERE p2.id_kategori = k.id_kategori
        ) AS total_produk,

        -- total penjualan kategori
        COALESCE((
          SELECT SUM(p3.terjual) 
          FROM produk p3 
          WHERE p3.id_kategori = k.id_kategori
        ), 0) AS total_penjualan
      FROM kategori k
      ORDER BY total_penjualan DESC 
      LIMIT 8
    ";

  $res = $conn->query($sql);
  $data = [];

  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }

  return $data;
}
