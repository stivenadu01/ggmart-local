<?php

get('/', 'UserController@home');

// Auth 
get('/login', 'AuthController@login');
get('/register', 'AuthController@register');
get('/auth/verify', 'AuthController@verify');
get('/auth/reset-password', 'AuthController@resetPassword');

get('/keranjang', 'UserController@keranjang');
get('/transaksi', 'UserController@transaksi', ['auth']);
get('/produk', 'UserController@produk');
get('/produk/{id}', 'UserController@produkDetail');

get('/profil', 'UserController@profil', ['auth']);

// ADMIN
get('/admin', 'AdminController@dashboard', ['role:admin,pimpinan']);
get('/admin/kasir', 'AdminController@kasir', ['role:admin']);
get('/admin/pesanan', 'AdminController@pesanan', ['role:admin']);
get('/admin/kategori', 'AdminController@kategori', ['role:admin,pimpinan']);
get('/admin/produk', 'AdminController@produk', ['role:admin,pimpinan']);
get('/admin/produk/form', 'AdminController@produkForm', ['role:admin']);
get('/admin/stok', 'AdminController@stok', ['role:admin,pimpinan']);
get('/admin/stok/form', 'AdminController@stokForm', ['role:admin']);
get('/admin/user', 'AdminController@user', ['role:admin']);
get('/admin/transaksi', 'AdminController@transaksi', ['role:admin,pimpinan']);
get('/admin/transaksi/{kode}', 'AdminController@transaksiDetail', ['role:admin,pimpinan']);
get('/admin/laporan', 'AdminController@laporan', ['role:pimpinan']);
get('/admin/pengaturan', 'AdminController@pengaturan', ['role:admin']);
