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
      $input['id_user'] = $_SESSION['user']['id_user'];
      $kode_transaksi = "GG-" . substr(str_replace('.', '', microtime(true)), -8) . random_int(100, 999);
      $input['kode_transaksi'] = $kode_transaksi;

      $detail_insert = [];
      $total_pokok = 0;
      $total_harga_db = 0;

      foreach ($input['detail'] as $item) {

        $produk = findProduk($item['kode_produk']);
        if (!$produk) throw new Exception("Produk tidak ditemukan", 404);

        $db_harga = $produk['harga_jual'];
        $total_harga_db += $db_harga * $item['jumlah'];

        $stok = getMutasiByProduk($item['kode_produk']);
        if (!$stok) throw new Exception("Stok " . $produk['nama_produk'] . " tidak tersedia", 422);

        $ambil = intval($item['jumlah']);
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
          'kode_produk' => $item['kode_produk'],
          'jumlah' => $item['jumlah'],
          'harga_satuan' => $item['harga_satuan'],
          'harga_pokok' => $subtotal_pokok / $item['jumlah']
        ];

        updateStokProduk($item['kode_produk']);
        ubahTerjualProduk($item['kode_produk'], $item['jumlah']);
      }

      if ($total_harga_db != $input['total_harga']) {
        throw new Exception("Total harga tidak valid", 400);
      }

      $input['total_pokok'] = $total_pokok;
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
      if ($input['id_user'] != $_SESSION['user']['id_user']) {
        throw new Exception("User Tidak Valid", 400);
      }

      $kode_transaksi = "GG-" . substr(str_replace('.', '', microtime(true)), -8) . random_int(100, 999);
      $input['kode_transaksi'] = $kode_transaksi;

      $total_harga_db = 0;
      $detail_insert = [];

      foreach ($input['detail'] as $item) {

        $produk = findProduk($item['kode_produk']);
        if (!$produk) throw new Exception("Produk tidak ditemukan", 404);
        if ($produk['stok'] < $item['jumlah']) {
          throw new Exception("Stok tidak cukup untuk {$produk['nama_produk']}", 422);
        }

        $db_harga = $produk['harga_jual'];
        $total_harga_db += $db_harga * $item['jumlah'];


        $detail_insert[] = [
          'kode_transaksi' => $kode_transaksi,
          'kode_produk' => $item['kode_produk'],
          'jumlah' => $item['jumlah'],
          'harga_satuan' => $item['harga_satuan'],
          'harga_pokok' => 0 // belum dihitung
        ];
      }

      if ($total_harga_db != $input['total_harga']) {
        throw new Exception("Total harga tidak valid", 400);
      }

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
    try {
      $kode_transaksi = request('kode_transaksi');
      $trx = findTransaksi($kode_transaksi);
      if (!$trx) throw new Exception("Transaksi tidak ditemukan", 404);

      if ($trx['status'] !== 'pending') {
        throw new Exception("Transaksi sudah diproses", 400);
      }

      // update transaksi
      updateStatusTransaksi($kode_transaksi, 'diproses');

      return response([
        'success' => true,
        'message' => 'Transaksi berhasil diproses'
      ]);
    } catch (Exception $e) {
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
      $trx = findTransaksi($kode_transaksi);
      if (!$trx) throw new Exception("Transaksi tidak ditemukan", 404);

      if ($trx['status'] !== 'diproses') {
        throw new Exception("Transaksi sudah selesai", 400);
      }

      $details = getDetailTransaksi($kode_transaksi);

      $total_pokok = 0;

      foreach ($details as $item) {

        $stok = getMutasiByProduk($item['kode_produk']);
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

      $transaksi = findTransaksi($kode);
      if (!$transaksi) throw new Exception("Transaksi tidak ditemukan", 404);

      // hanya boleh batalkan yang belum dibatalkan
      if ($transaksi['status'] == 'dibatalkan') {
        throw new Exception("Hanya transaksi yang bukan dibatalkan yang bisa dibatalkan", 400);
      }
      // jika selesai balikan stok
      if ($transaksi['status'] == 'selesai') {
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

      $transaksi = findTransaksi($kode);
      if (!$transaksi) throw new Exception("Transaksi tidak ditemukan", 404);

      if ($transaksi['status'] !== 'pendig') {
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
