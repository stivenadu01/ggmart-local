<?php
class UserController
{

  public function home()
  {
    view('user/home', ['title' => 'GGMart Toko Pangan Sinode GMIT']);
  }

  public function keranjang()
  {
    view('user/keranjang', ['title' => 'Keranjang | GGMart']);
  }

  public function transaksi()
  {
    view('user/transaksi', ['title' => 'Transaksi Saya | GGMart']);
  }

  public function produk()
  {
    view('user/produk', ['title' => 'Katalog Produk | GGMart']);
  }

  public function produkDetail()
  {
    view('user/produk-detail', ['title' => 'Detail Produk | GGMart']);
  }

  public function profil()
  {
    view('user/profil', ['title' => 'Profil Saya | GGMart']);
  }
}
