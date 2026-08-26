<?php



class ApiTransaksiController
{
  public function __construct()
  {
    model('Transaksi');
    model('MutasiStok');
    model('DetailTransaksi');
    model('Produk');
  }

  // LIST TRANSAKSI
  public function list()
  {
    try {
      $page   = max(1, intval($_GET['page'] ?? 1));
      $limit  = max(1, intval($_GET['limit'] ?? 10));
      $search = trim($_GET['search'] ?? '');
      $start  = $_GET['start'] ?? null;
      $end    = $_GET['end'] ?? null;
      $metode = $_GET['metode'] ?? null;
      $user   = $_GET['user'] ?? null;
      $status = $_GET['status'] ?? null;
      $currentUser = $_SESSION['user'] ?? null;

      if (!$currentUser) throw new Exception('Unauthorized', 401);
      if (!in_array($currentUser['role'], ['admin', 'pimpinan'], true)) {
        // User biasa selalu dibatasi ke transaksi miliknya sendiri.
        $user = (int)$currentUser['id_pengguna'];
      }

      [$data, $total, $summary] = getTransaksiList(
        $page,
        $limit,
        $search,
        $start,
        $end,
        $metode,
        $user,
        $status
      );

      return response([
        'success' => true,
        'data' => $data,
        'totalSummary' => $summary,
        'pagination' => [
          'page' => $page,
          'limit' => $limit,
          'total' => $total,
          'total_pages' => ceil($total / $limit)
        ]
      ]);
    } catch (Exception $e) {
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], $e->getCode() ?: 500);
    }
  }

  // DETAIL TRANSAKSI
  public function detail()
  {

    try {
      $kode = trim($_GET['k'] ?? '');
      if (!$kode) throw new Exception("Kode transaksi tidak valid", 400);

      $data = findTransaksi($kode);
      if (!$data) throw new Exception("Transaksi tidak ditemukan", 404);

      $currentUser = $_SESSION['user'] ?? null;
      if (!$currentUser) throw new Exception('Unauthorized', 401);
      if ($currentUser['role'] === 'pelanggan' && (int)$data['id_pengguna'] !== (int)$currentUser['id_pengguna']) {
        throw new Exception('Anda tidak memiliki akses ke transaksi ini', 403);
      }

      $data['detail'] = getDetailTransaksi($kode);

      return response([
        'success' => true,
        'data' => $data
      ]);
    } catch (Exception $e) {
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], $e->getCode() ?: 500);
    }
  }

  // CREATE TRANSAKSI (ADMIN)
  public function tambah_transaksi()
  {
    $conn = db();
    $conn->begin_transaction();

    try {
      $input = input();
      $input['id_pengguna'] = $_SESSION['user']['id_pengguna'];
      $input['metode_bayar'] = $input['metode_bayar'] ?? 'tunai';
      if (!in_array($input['metode_bayar'], ['qris', 'tunai'], true)) {
        throw new Exception('Metode pembayaran tidak valid', 422);
      }
      if (!isset($input['detail']) || !is_array($input['detail']) || count($input['detail']) === 0) {
        throw new Exception('Detail transaksi wajib diisi', 422);
      }
      if (!isset($input['total_harga']) || !is_numeric($input['total_harga'])) {
        throw new Exception('Total harga tidak valid', 422);
      }

      $kode_transaksi = "GG-" . substr(str_replace('.', '', microtime(true)), -8) . random_int(100, 999);
      $input['kode_transaksi'] = $kode_transaksi;

      $detail_insert = [];
      $total_pokok = 0;
      $total_harga_db = 0;

      $seenProducts = [];
      foreach ($input['detail'] as $item) {

        $kodeProduk = trim((string)($item['kode_produk'] ?? ''));
        $jumlah = filter_var($item['jumlah'] ?? null, FILTER_VALIDATE_INT);
        if ($kodeProduk === '' || $jumlah === false || $jumlah <= 0) {
          throw new Exception('Produk dan jumlah transaksi tidak valid', 422);
        }
        if (isset($seenProducts[$kodeProduk])) {
          throw new Exception('Produk yang sama tidak boleh muncul lebih dari satu kali dalam transaksi', 422);
        }
        $seenProducts[$kodeProduk] = true;

        $produk = findProduk($kodeProduk);
        if (!$produk) throw new Exception("Produk tidak ditemukan", 404);

        $db_harga = (float)$produk['harga_jual'];
        $total_harga_db += $db_harga * $jumlah;

        $stok = getMutasiByProdukForUpdate($kodeProduk);
        if (!$stok) throw new Exception("Stok " . $produk['nama_produk'] . " tidak tersedia", 422);

        $ambil = $jumlah;
        $subtotal_pokok = 0;

        foreach ($stok as $s) {
          if ($ambil <= 0) break;

          $ambil_dari_batch = min($s['sisa_stok'], $ambil);
          $ambil -= $ambil_dari_batch;

          $sisa = $s['sisa_stok'] - $ambil_dari_batch;
          $subtotal_pokok += $ambil_dari_batch * $s['harga_pokok'];

          if (!ubahSisaStokMutasi($s['id_mutasi'], $sisa)) {
            throw new Exception("Gagal update stok mutasi", 500);
          }
        }

        if ($ambil > 0) {
          throw new Exception("Stok tidak cukup untuk {$item['kode_produk']}", 422);
        }

        $total_pokok += $subtotal_pokok;

        //detail transaksi 
        $detail_insert[] = [
          'kode_transaksi' => $kode_transaksi,
          'kode_produk' => $kodeProduk,
          'jumlah' => $jumlah,
          'harga_satuan' => $db_harga,
          'harga_pokok' => $subtotal_pokok / $jumlah
        ];

        updateStokProduk($kodeProduk);
        ubahTerjualProduk($kodeProduk, $jumlah);
      }

      if (abs(round($total_harga_db, 2) - round((float)$input['total_harga'], 2)) > 0.01) {
        throw new Exception("Total harga tidak valid", 400);
      }

      $input['total_harga'] = round($total_harga_db, 2);
      $input['total_pokok'] = round($total_pokok, 2);
      $input['status'] = 'selesai';

      tambahTransaksi($input);

      foreach ($detail_insert as $d) {
        tambahDetailTransaksi($d);
      }

      $conn->commit();

      return response([
        'success' => true,
        'message' => 'Transaksi berhasil',
        'data' => $kode_transaksi
      ], 201);
    } catch (Exception $e) {
      $conn->rollback();
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], $e->getCode() ?: 500);
    }
  }

  // CREATE TRANSAKSI (USER)
  public function tambah_transaksi_user()
  {
    $conn = db();
    $conn->begin_transaction();

    try {
      $input = input();
      $input['id_pengguna'] = (int)$_SESSION['user']['id_pengguna'];
      $input['metode_bayar'] = $input['metode_bayar'] ?? 'tunai';
      if (!in_array($input['metode_bayar'], ['qris', 'tunai'], true)) {
        throw new Exception('Metode pembayaran tidak valid', 422);
      }
      if (!isset($input['detail']) || !is_array($input['detail']) || count($input['detail']) === 0) {
        throw new Exception('Detail pesanan wajib diisi', 422);
      }
      if (!isset($input['total_harga']) || !is_numeric($input['total_harga'])) {
        throw new Exception('Total harga tidak valid', 422);
      }

      $kode_transaksi = "GG-" . substr(str_replace('.', '', microtime(true)), -8) . random_int(100, 999);
      $input['kode_transaksi'] = $kode_transaksi;

      $total_harga_db = 0;
      $detail_insert = [];

      $seenProducts = [];
      foreach ($input['detail'] as $item) {
        $kodeProduk = trim((string)($item['kode_produk'] ?? ''));
        $jumlah = filter_var($item['jumlah'] ?? null, FILTER_VALIDATE_INT);
        if ($kodeProduk === '' || $jumlah === false || $jumlah <= 0) {
          throw new Exception('Produk dan jumlah pesanan tidak valid', 422);
        }
        if (isset($seenProducts[$kodeProduk])) {
          throw new Exception('Produk yang sama tidak boleh muncul lebih dari satu kali dalam pesanan', 422);
        }
        $seenProducts[$kodeProduk] = true;

        $produk = findProduk($kodeProduk);
        if (!$produk) throw new Exception("Produk tidak ditemukan", 404);
        if ((int)$produk['stok'] < $jumlah) {
          throw new Exception("Stok tidak cukup untuk {$produk['nama_produk']}", 422);
        }

        $db_harga = (float)$produk['harga_jual'];
        $total_harga_db += $db_harga * $jumlah;

        $detail_insert[] = [
          'kode_transaksi' => $kode_transaksi,
          'kode_produk' => $kodeProduk,
          'jumlah' => $jumlah,
          'harga_satuan' => $db_harga,
          'harga_pokok' => 0
        ];
      }

      if (abs(round($total_harga_db, 2) - round((float)$input['total_harga'], 2)) > 0.01) {
        throw new Exception("Total harga tidak valid", 400);
      }

      $input['total_harga'] = round($total_harga_db, 2);
      $input['total_pokok'] = 0;
      $input['status'] = 'pending';

      tambahTransaksi($input);

      foreach ($detail_insert as $d) {
        tambahDetailTransaksi($d);
      }

      $conn->commit();

      return response([
        'success' => true,
        'message' => 'Pesanan dibuat, menunggu diproses',
        'data' => $kode_transaksi
      ], 201);
    } catch (Exception $e) {
      $conn->rollback();
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], $e->getCode() ?: 500);
    }
  }

  // PROSES TRANSAKSI USER
  public function proses_transaksi()
  {
    $conn = db();
    $conn->begin_transaction();
    try {
      $kode_transaksi = request('kode_transaksi');
      if (!$kode_transaksi) throw new Exception('Kode transaksi wajib diisi', 400);
      $trx = findTransaksiForUpdate($kode_transaksi);
      if (!$trx) throw new Exception("Transaksi tidak ditemukan", 404);

      if ($trx['status'] !== 'pending') {
        throw new Exception("Transaksi sudah diproses", 400);
      }

      // update transaksi
      updateStatusTransaksi($kode_transaksi, 'diproses');
      $conn->commit();

      return response([
        'success' => true,
        'message' => 'Transaksi berhasil diproses'
      ]);
    } catch (Exception $e) {
      $conn->rollback();
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], $e->getCode() ?: 500);
    }
  }

  // KONFIRMASI TRANSAKSI (PROSES PESANAN USER)
  public function konfirmasi_transaksi()
  {
    $conn = db();
    $conn->begin_transaction();

    try {
      $kode_transaksi = request('kode_transaksi');
      $trx = findTransaksiForUpdate($kode_transaksi);
      if (!$trx) throw new Exception("Transaksi tidak ditemukan", 404);

      if ($trx['status'] !== 'diproses') {
        throw new Exception("Transaksi sudah selesai", 400);
      }

      $details = getDetailTransaksi($kode_transaksi);

      $total_pokok = 0;

      foreach ($details as $item) {

        $stok = getMutasiByProdukForUpdate($item['kode_produk']);
        if (!$stok) throw new Exception("Stok tidak tersedia", 422);

        $ambil = $item['jumlah'];
        $subtotal_pokok = 0;

        foreach ($stok as $s) {
          if ($ambil <= 0) break;

          $ambil_dari_batch = min($s['sisa_stok'], $ambil);
          $ambil -= $ambil_dari_batch;

          $sisa = $s['sisa_stok'] - $ambil_dari_batch;
          $subtotal_pokok += $ambil_dari_batch * $s['harga_pokok'];

          if (!ubahSisaStokMutasi($s['id_mutasi'], $sisa)) {
            throw new Exception("Gagal update stok mutasi", 500);
          }
        }

        if ($ambil > 0) {
          throw new Exception("Stok tidak cukup", 422);
        }

        $total_pokok += $subtotal_pokok;

        // update detail (karena sebelumnya 0)
        updateHargaPokokDetail($kode_transaksi, $item['kode_produk'], $subtotal_pokok / $item['jumlah']);

        updateStokProduk($item['kode_produk']);
        ubahTerjualProduk($item['kode_produk'], $item['jumlah']);
      }

      // update transaksi
      updateStatusTransaksi($kode_transaksi, 'selesai', $total_pokok);

      $conn->commit();

      return response([
        'success' => true,
        'message' => 'Transaksi berhasil dikonfirmasi'
      ]);
    } catch (Exception $e) {
      $conn->rollback();
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], $e->getCode() ?: 500);
    }
  }

  // BATAL TRANSAKSI (OLEH ADMIN)
  public function batal_transaksi()
  {
    $conn = db();
    $conn->begin_transaction();

    try {
      $kode = input()['kode_transaksi'] ?? null;
      if (!$kode) throw new Exception("Kode transaksi wajib", 400);

      $transaksi = findTransaksiForUpdate($kode);
      if (!$transaksi) throw new Exception("Transaksi tidak ditemukan", 404);

      // hanya boleh batalkan yang belum dibatalkan
      if ($transaksi['status'] == 'dibatalkan') {
        throw new Exception("Hanya transaksi yang bukan dibatalkan yang bisa dibatalkan", 400);
      }
      // jika selesai balikan stok
      if ($transaksi['status'] == 'selesai') {

        // jika sudah lewat 24 jam tidak bisa dibatalkan
        $waktu_transaksi = strtotime($transaksi['tanggal_transaksi']);
        if (time() - $waktu_transaksi > 24 * 60 * 60) {
          throw new Exception("Transaksi sudah lebih dari 24 jam, tidak bisa dibatalkan", 400);
        }

        $detail = getDetailTransaksi($kode);
        foreach ($detail as $d) {

          $jumlah_kembali = (int)$d['jumlah'];
          $batch = getMutasiByProdukForReturn($d['kode_produk']);

          foreach ($batch as $m) {
            if ($jumlah_kembali <= 0) break;

            $max = $m['stok_masuk'] - $m['sisa_stok'];
            if ($max <= 0) continue;

            $kembali = min($jumlah_kembali, $max);
            $new_sisa = $m['sisa_stok'] + $kembali;

            if (!ubahSisaStokMutasi($m['id_mutasi'], $new_sisa)) {
              throw new Exception("Gagal rollback stok", 500);
            }

            $jumlah_kembali -= $kembali;
          }

          updateStokProduk($d['kode_produk']);
          ubahTerjualProduk($d['kode_produk'], -$d['jumlah']);
        }
      }

      // ubah status
      updateStatusTransaksi($kode, 'dibatalkan');

      $conn->commit();

      return response([
        'success' => true,
        'message' => 'Transaksi berhasil dibatalkan'
      ]);
    } catch (Exception $e) {
      $conn->rollback();
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], $e->getCode() ?: 500);
    }
  }

  // BATAL TRANSAKSI (YANG MASIH PENDING)
  public function batal_transaksi_pending()
  {
    $conn = db();
    $conn->begin_transaction();

    try {
      $kode = input()['kode_transaksi'] ?? null;
      if (!$kode) throw new Exception("Kode transaksi wajib", 400);

      $transaksi = findTransaksiForUpdate($kode);
      if (!$transaksi) throw new Exception("Transaksi tidak ditemukan", 404);
      if ((int)$transaksi['id_pengguna'] !== (int)$_SESSION['user']['id_pengguna']) {
        throw new Exception('Anda tidak memiliki akses ke pesanan ini', 403);
      }

      if ($transaksi['status'] !== 'pending') {
        throw new Exception("Hanya transaksi pending yang bisa dibatalkan", 400);
      }

      // langsung ubah status
      updateStatusTransaksi($kode, 'dibatalkan');

      $conn->commit();

      return response([
        'success' => true,
        'message' => 'Pesanan dibatalkan'
      ]);
    } catch (Exception $e) {
      $conn->rollback();
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], $e->getCode() ?: 500);
    }
  }

  public function badge()
  {
    try {
      $conn = db();

      $res = $conn->query("
      SELECT COUNT(*) as total
      FROM transaksi
      WHERE status IN ('pending','diproses')
    ");

      $total = $res->fetch_assoc()['total'] ?? 0;

      return response([
        'success' => true,
        'data' => [
          'total' => (int)$total
        ]
      ]);
    } catch (Exception $e) {
      return response([
        'success' => false,
        'message' => $e->getMessage()
      ], 500);
    }
  }
}
