<aside x-data="{ active: location.pathname }" class="w-72 min-h-screen border-r border-gray-200 bg-white" x-init="$store.pesananBadge.start()">
  <div class="flex h-full flex-col">
    <div class="py-2 px-4 border-b border-gray-200">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gray-200 text-gray-700 flex-center text-lg font-semibold">
          <span x-text="$store.auth.user?.email ? $store.auth.user.email.charAt(0).toUpperCase() : 'A'"></span>
        </div>
        <div class="min-w-0 gap-1 flex flex-col">
          <p class="text-xs uppercase tracking-[0.2em] text-gray-400">Admin</p>
          <p class="truncate font-semibold text-sm" x-text="$store.auth.user?.email"></p>
        </div>
      </div>
    </div>

    <!-- menu -->
    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-4 pb-4">
      <div>
        <p class="text-xs uppercase tracking-[0.24em] text-gray-400 mb-3">Menu Utama</p>
        <div>
          <a href="<?= BASE_URL ?>/admin" class="flex items-center rounded-xl px-4 py-3 text-sm gap-2 transition hover:bg-gray-100" :class="active === '/admin' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3.5 6c0-1.4 1.1-2.5 2.5-2.5h4c1.4 0 2.5 1.1 2.5 2.5v6c0 1.4-1.1 2.5-2.5 2.5H6c-1.4 0-2.5-1.1-2.5-2.5Z" />
                <path d="M14 6c0-1.4 1.1-2.5 2.5-2.5H18c1.4 0 2.5 1.1 2.5 2.5v3c0 1.4-1.1 2.5-2.5 2.5h-1.5c-1.4 0-2.5-1.1-2.5-2.5Z" fill="currentColor" fill-opacity=".1" />
                <path d="M3.5 17c0-1.4 1.1-2.5 2.5-2.5h4c1.4 0 2.5 1.1 2.5 2.5v4c0 1.4-1.1 2.5-2.5 2.5H6c-1.4 0-2.5-1.1-2.5-2.5ZM14 14c0-1.4 1.1-2.5 2.5-2.5H18c1.4 0 2.5 1.1 2.5 2.5v7c0 1.4-1.1 2.5-2.5 2.5h-1.5c-1.4 0-2.5-1.1-2.5-2.5Z" />
              </g>
            </svg>
            Dashboard
          </a>
          <a href="<?= BASE_URL ?>/admin/kasir" class="flex gap-2 items-center rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/kasir' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.5 5.5v-3h5v3zm2.5 0V8M5 8h14v9H5Z" />
                <rect x="7" y="10" width="5" height="2.5" rx=".3" />
                <path d="M14 11h1m1.5 0h1M14 14h1m1.5 0h1m-14 3h17v4.5h-17Z" />
                <circle cx="12" cy="19.25" r=".75" fill="currentColor" />
              </g>
            </svg>
            Kasir
          </a>
          <a href="<?= BASE_URL ?>/admin/pesanan" class="relative flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/pesanan' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.5 3h6L19 6.5v15c0 2.1-.9 2-4 2h-2.5M5 13.5V5c0-1.1.9-2 2-2h2.5" />
                <path d="M15.5 3v3.5H19" />
                <path opacity=".6" d="M8 10h5m-5 3h6" />
                <path d="M3 14h2l1.8 7.5h5.7l1-5.5h-8" />
                <path opacity=".4" d="m7.5 16 1 5.5m1-5.5 1 5.5m1-5.5.3 5.5" />
                <circle cx="7.5" cy="24" r="1" />
                <circle cx="11.5" cy="24" r="1" />
              </g>
            </svg>
            Pesanan

            <!-- BADGE -->
            <span
              x-show="$store.pesananBadge.total > 0"
              x-text="$store.pesananBadge.total"
              class="absolute top-0 left-0 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
            </span>
          </a>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-[0.24em] text-gray-400 mb-3">Data Master</p>
        <div class="">
          <a href="<?= BASE_URL ?>/admin/kategori" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/kategori' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2.5 6c0-1.4 1.1-2.5 2.5-2.5h14c1.4 0 2.5 1.1 2.5 2.5v5.5c0 1.4-1.1 2.5-2.5 2.5H5c-1.4 0-2.5-1.1-2.5-2.5Z" />
                <path stroke-width="1.2" opacity=".8" d="M6 8.7h6" />
                <circle cx="17.5" cy="8.7" r=".8" fill="currentColor" />
                <path d="M4.5 14v3.5c0 1.1.9 2 2 2h8" />
                <path d="M19.5 14v1.5" opacity=".5" />
                <path d="M6.5 19.5v3c0 1.1.9 2 2 2h3" />
                <circle cx="18" cy="22.5" r="1.8" />
                <path d="M18 19.7v-1m0 6.6v1m-2.8-3.8h-1m6.6 0h1m-5.8-2-.7-.7m4.7 4.7.7.7m-4.7-.7-.7.7m4.7-4.7.7-.7" />
              </g>
            </svg>
            Kelola Kategori
          </a>
          <a href="<?= BASE_URL ?>/admin/produk" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/produk' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 2.5 9.5 5V17L12 22l-9.5-5V7.5Zm0 0v9.7" />
                <path d="m2.5 7.5 9.5 4.7 9.5-4.7" />
                <circle cx="17.5" cy="21.5" r="1.8" />
                <path d="M17.5 18.7v-1m0 6.6v1m-2.8-3.8h-1m6.6 0h1m-5.8-2-.7-.7m4.7 4.7.7.7m-4.7-.7-.7.7m4.7-4.7.7-.7" />
              </g>
            </svg>
            Kelola Produk
          </a>
          <a href="<?= BASE_URL ?>/admin/stok" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/stok' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 10.5v-5C4 4.1 5.1 3 6.5 3h11C18.9 3 20 4.1 20 5.5v5" opacity=".8" />
                <path d="M2.5 11.5C2.5 10.1 3.6 9 5 9h14c1.4 0 2.5 1.1 2.5 2.5v9c0 1.4-1.1 2.5-2.5 2.5H5c-1.4 0-2.5-1.1-2.5-2.5Z" />
                <path d="M10.5 9v5.5h3V9" fill="currentColor" fill-opacity=".1" />
                <path stroke-width="1" opacity=".6" d="M5 18h3.5" />
                <circle cx="18" cy="21.5" r="1.8" />
                <path d="M18 18.7v-1m0 6.6v1m-2.8-3.8h-1m6.6 0h1m-5.8-2-.7-.7m4.7 4.7.7.7m-4.7-.7-.7.7m4.7-4.7.7-.7" />
              </g>
            </svg>
            Kelola Stok
          </a>
          <a href="<?= BASE_URL ?>/admin/user" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/user' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="7.5" cy="8.5" r="2.5" opacity=".6" />
                <path d="M2.5 17.5c0-3 2-4 5-4 1 0 2 .2 2.8.6" opacity=".6" />
                <circle cx="12" cy="7" r="3" />
                <path d="M5.5 17c0-3 2.5-4.5 6.5-4.5 2.2 0 4 .6 5.2 1.6M5.5 17v2H12" />
                <circle cx="18" cy="21.5" r="1.8" />
                <path d="M18 18.7v-1m0 6.6v1m-2.8-3.8h-1m6.6 0h1m-5.8-2-.7-.7m4.7 4.7.7.7m-4.7-.7-.7.7m4.7-4.7.7-.7" />
              </g>
            </svg>
            Kelola User
          </a>
        </div>
      </div>

      <div>
        <p class="text-xs uppercase tracking-[0.24em] text-gray-400 mb-3">Monitoring</p>
        <div class="">
          <a href="<?= BASE_URL ?>/admin/transaksi" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/transaksi' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.5 3h6L19 6.5v15c0 1.1-.9 2-2 2H9.5m-4.5-6V5c0-1.1.9-2 2-2h2.5" />
                <path d="M15.5 3v3.5H19m-10.5 3h7m-7 3h7m-4.5 3h4.5" />
                <circle cx="7.5" cy="20.5" r="3.5" />
                <path d="M7.5 18.5v2H9" />
              </g>
            </svg>
            Riwayat Transaksi
          </a>
          <a href="<?= BASE_URL ?>/admin/laporan" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/laporan' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.5 3h6L19 6.5v17c0 1.1-.9 2-2 2H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2z" />
                <path d="M15.5 3v3.5H19m-10.5 4h5" />
                <path d="M7.5 21h9" opacity=".4" />
                <path d="m8.5 19.5 2.5-3 2.5 1.5 3-4" />
                <path d="M14.5 14h2v2" />
              </g>
            </svg>
            Laporan
          </a>
        </div>
      </div>

      <div class="pt-4 border-t border-gray-200">
        <div class="">
          <a :href="BASE_URL + '/admin/pengaturan'" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100" :class="active === '/admin/pengaturan' ? 'bg-gray-100 font-semibold' : ''">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="14" r="3.5" />
                <path d="M12 6.5V4c0-.6.5-1 1-1h-2c.5 0 1 .4 1 1" />
                <path d="M10.5 4h3v2.5h-3Zm0 17.5h3V24h-3Zm-6.5-9h2.5v3H4Z" fill="currentColor" fill-opacity=".05" />
                <path d="M17.5 12.5H20Z" />
                <path d="M17.5 12.5H20v3h-2.5Zm.51-6.632 2.122 2.122-1.768 1.767-2.121-2.121ZM5.636 18.243l2.121 2.121-1.767 1.768-2.122-2.122Zm14.496 1.767-2.122 2.122-1.767-1.768 2.121-2.121ZM7.757 7.636 5.636 9.757 3.868 7.99 5.99 5.868Z" fill="currentColor" fill-opacity=".05" />
              </g>
            </svg>
            Pengaturan
          </a>
          <a :href="BASE_URL" class="flex items-center gap-2 rounded-xl px-4 py-3 text-sm transition hover:bg-gray-100">
            <svg class='w-6 h-6' xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3.5 11.5 7.2-6.7c.7-.7 1.9-.7 2.6 0l7.2 6.7c.6.6 1 1.4 1 2.3v7.7c0 1.4-1.1 2.5-2.5 2.5H5c-1.4 0-2.5-1.1-2.5-2.5v-7.7c0-.9.4-1.7 1-2.3" />
                <path d="M9.5 24v-7.5c0-1.4 1.1-2.5 2.5-2.5s2.5 1.1 2.5 2.5V24" />
              </g>
            </svg>
            Kembali ke Beranda
          </a>
          <button type="button" @click="$store.auth.logout()" class="w-full flex items-center text-left rounded-xl px-4 py-3 text-sm text-red-600 transition hover:bg-red-50">
            <svg class="w-6 h-6" xmlns="http://w3.org" viewBox="0 0 24 28">
              <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 4.5H5C3.6 4.5 2.5 5.6 2.5 7v14c0 1.4 1.1 2.5 2.5 2.5h5" />
                <path stroke-width="1.35" d="M8.5 14h12" />
                <path d="m16.5 10 4 4-4 4" />
              </g>
            </svg>
            Logout
          </button>
        </div>
      </div>
    </nav>
  </div>
</aside>