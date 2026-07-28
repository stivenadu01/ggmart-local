<?php

class ApiMutasiStokController
{
  public function __construct()
  {
    model('MutasiStok');
    model('Produk');
  }

  // === LIST MUTASI ===
  public function list()
  {
    try {
      $page   = max(1, intval($_GET['page'] ?? 1));
      $limit  = max(1, intval($_GET['limit'] ?? 10));
      $search = trim($_GET['search'] ?? '');
      $type   = trim($_GET['type'] ?? '');

      [$data, $total] = getMutasiStokList($page, $limit, $type, $search);

      return response([
        'success' => true,
        'data' => $data,
        'pagination' => [
          'page' => $page,
          'limit' => $limit,
          'total' => intval($total),
          'total_pages' => ($limit > 0) ? ceil($total / $limit) : 1
        ]
      ]);
    } catch (Exception $e) {
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }

  // === DETAIL ===
  public function detail()
  {
    try {
      $id = $_GET['id'] ?? null;
      if (!$id) throw new Exception('ID mutasi tidak valid', 400);

      $mutasi = findMutasiStok($id);
      if (!$mutasi) throw new Exception('Mutasi stok tidak ditemukan', 404);

      return response(['success' => true, 'data' => $mutasi]);
    } catch (Exception $e) {
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }

  // === DROPDOWN (BATCH STOK) ===
  public function dropdown()
  {
    try {
      $kode = $_GET['kode'] ?? null;
      if (!$kode) throw new Exception('Kode produk tidak valid', 400);

      $data = getMutasiByProduk($kode);
      if (!$data) throw new Exception('Mutasi List stok tidak ditemukan', 404);

      return response(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }

  // === TAMBAH MUTASI ===
  public function tambah()
  {
    $conn = db();
    $conn->begin_transaction();

    try {
      $input = input();

      $kode_produk = $input['kode_produk'] ?? null;
      $jumlah      = $input['jumlah'] ?? null;
      $type        = $input['type'] ?? null;
      $id_mutasi   = $input['id_mutasi'] ?? null;

      if (!$kode_produk || !$type || !$jumlah) {
        throw new Exception('Produk dan Type wajib di isi.', 422);
      }

      if ($type == 'masuk') {
        if (empty($input['harga_pokok'])) {
          throw new Exception('Harga pokok wajib di isi.', 422);
        }

        $input['sisa_stok'] = $jumlah;

        if (!tambahMutasiStok($input)) throw new Exception("Gagal Tambah", 500);
        if (!updateStokProduk($kode_produk)) throw new Exception("Gagal Update", 500);
      } elseif ($type == 'keluar') {
        $mutasi = findMutasiStok($id_mutasi);

        if (!$id_mutasi || !$mutasi) {
          throw new Exception('Mutasi/Batch Stok Tidak Ditemukan!', 422);
        }

        if (!ubahSisaStokMutasi($id_mutasi, $mutasi['sisa_stok'] - $jumlah)) {
          throw new Exception("Gagal Ubah Stok", 500);
        }

        $input['sisa_stok'] = null;

        if (!tambahMutasiStok($input)) throw new Exception("Gagal Tambah", 500);
        if (!updateStokProduk($kode_produk)) throw new Exception("Gagal Update", 500);
      }

      $conn->commit();

      return response([
        'success' => true,
        'message' => 'Perubahan Stok berhasil',
        'data' => $input
      ], 201);
    } catch (Exception $e) {
      $conn->rollback();
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }

  // === DELETE ===
  public function hapus()
  {
    $conn = db();
    $conn->begin_transaction();

    try {
      $id = request('id');
      if (!$id) throw new Exception('ID Mutasi wajib diisi.', 400);

      $mutasi = findMutasiStok($id);
      if (!$mutasi) throw new Exception('Mutasi tidak ditemukan.', 404);

      $tanggal_mutasi = new DateTime($mutasi['tanggal']);
      $sekarang = new DateTime();

      $interval = $sekarang->getTimestamp() - $tanggal_mutasi->getTimestamp();

      if ($interval >= 86400) {
        throw new Exception('Mutasi stok yang sudah lebih dari 1 hari tidak bisa dihapus', 400);
      }

      if ($mutasi['type'] == 'keluar') {
        throw new Exception("Stok Keluar Tidak Bisa Dihapus", 400);
      }

      if ($mutasi['type'] == 'masuk' && $mutasi['jumlah'] != $mutasi['sisa_stok']) {
        throw new Exception('Batch Stok ini sudah digunakan, silahkan kurangi.', 400);
      }

      if (!hapusMutasiStok($id)) {
        throw new Exception('Mutasi Stok gagal dihapus.', 500);
      }

      if (!updateStokProduk($mutasi['kode_produk'])) {
        throw new Exception("Gagal update stok produk", 500);
      }

      $conn->commit();

      return response(['success' => true, 'message' => 'Mutasi Stok berhasil dihapus']);
    } catch (Exception $e) {
      $conn->rollback();
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }
}
