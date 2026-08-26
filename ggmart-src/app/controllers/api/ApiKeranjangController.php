<?php


class ApiKeranjangController
{
  public function __construct()
  {
    model('Keranjang');
  }

  public function list()
  {
    $id_pengguna = query('u');
    if ($id_pengguna != $_SESSION['user']['id_pengguna']) return response(['success' => 'false'], 400);

    $data = getKeranjangByUser($id_pengguna);

    return response([
      'success' => true,
      'data' => $data
    ]);
  }

  public function tambah()
  {
    $input = input();
    $id_pengguna = $input['id_pengguna'];
    $kode_produk = $input['kode_produk'];
    $jumlah = $input['jumlah'];

    if ($id_pengguna != $_SESSION['user']['id_pengguna']) return response(['success' => 'false'], 400);
    model('Produk');

    $cek = cekKeranjang($id_pengguna, $kode_produk);
    if ($cek) {
      $stok = cekStokProduk($kode_produk);
      if ($jumlah + $cek['jumlah'] > $stok) return response(['message' => "Stok tidak mencukupi"], 400);
      updateKeranjang($cek['id_keranjang'], $jumlah + $cek['jumlah']);
    } else {
      tambahKeranjang(
        $id_pengguna,
        $kode_produk,
        intval($jumlah)
      );
    }

    return response(['success' => true]);
  }

  public function update()
  {
    $input = input();
    if ($input['id_pengguna'] != $_SESSION['user']['id_pengguna']) return response(['success' => 'false'], 400);

    updateKeranjang(
      $input['id_keranjang'],
      intval($input['jumlah'])
    );

    return response(['success' => true]);
  }

  public function hapus()
  {
    $id = input('id_keranjang');
    $id_pengguna = input('id_pengguna');

    if ($id_pengguna !== $_SESSION['user']['id_pengguna']) return response(['success' => false], 400);

    hapusItemKeranjang($id);

    return response(['success' => true]);
  }

  public function clear()
  {
    $id_pengguna = input('id_pengguna');
    if (input('id_pengguna') != $_SESSION['user']['id_pengguna']) return response(['success' => 'false'], 400);

    clearKeranjang($id_pengguna);

    return response(['success' => true]);
  }
}
