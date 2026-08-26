<div class="mx-auto max-w-7xl px-d py-10 pb-36 md:pb-10" x-data>

  <div class="mb-6">
    <h2 class="tracking-tight">Keranjang Belanja</h2>
    <p class="form-help">Periksa jumlah dan produk sebelum melanjutkan ke checkout.</p>
  </div>

  <!-- BELUM LOGIN -->
  <template x-if="!$store.auth.user">
    <div class="user-empty">
      <p class="font-semibold text-slate-800">Login untuk melihat keranjang</p>
      <p class="form-help">Keranjang tersimpan di akun agar pesanan dapat diproses dan dilacak.</p>
      <a :href="BASE_URL + '/login'" class="user-action-primary mt-4 w-auto">Login</a>
    </div>
  </template>

  <!-- SUDAH LOGIN -->
  <template x-if="$store.auth.user">

    <div class="space-y-6">

      <!-- EMPTY -->
      <template x-if="$store.cart.items.length === 0">
        <div class="user-empty">
          <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-xl" aria-hidden="true">🛒</div>
          <p class="mt-4 font-semibold text-slate-800">Keranjang kamu masih kosong</p>
          <p class="form-help">Pilih produk dari katalog untuk mulai berbelanja.</p>
          <a :href="BASE_URL + '/produk'" class="user-action-primary mt-4 w-auto">Lihat Produk</a>
        </div>
      </template>

      <!-- LIST -->
      <div class="space-y-3 min-h-75">

        <template x-for="item in $store.cart.items" :key="item.id_keranjang">

          <article class="user-card flex flex-col gap-4 p-3 transition hover:shadow-sm sm:flex-row sm:items-center sm:p-4">
            <div class="flex min-w-0 flex-1 items-center gap-3 sm:gap-4">
              <button
                type="button"
                @click="window.location.href = BASE_URL + '/produk/' + encodeURIComponent(item.kode_produk)"
                class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-50 sm:h-20 sm:w-20"
                :aria-label="'Lihat ' + item.nama_produk">
                <img
                  loading="lazy"
                  :src="item.gambar ? `${BASE_URL}/uploads${item.gambar}` : `${BASE_URL}/assets/no-image.png`"
                  :alt="item.nama_produk"
                  class="h-full w-full object-contain">
              </button>

              <div class="min-w-0 flex-1">
                <h3 class="line-clamp-2 text-sm font-semibold text-slate-900 sm:text-base" x-text="item.nama_produk"></h3>
                <p class="mt-1 text-sm font-semibold text-primary" x-text="$store.utils.formatRupiah(item.harga_jual)"></p>
                <p class="mt-1 text-xs text-slate-500">
                  Stok tersedia: <span class="font-medium" x-text="item.stok"></span>
                </p>
              </div>
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3 sm:border-0 sm:pt-0">
              <div class="user-quantity-control" :aria-label="'Jumlah ' + item.nama_produk">
                <button
                  type="button"
                  @click="if (item.jumlah > 1) { item.jumlah--; $store.cart.update(item); }"
                  :disabled="item.jumlah <= 1"
                  class="user-quantity-button"
                  aria-label="Kurangi jumlah">−</button>
                <span class="w-8 text-center text-sm font-semibold text-slate-700" x-text="item.jumlah"></span>
                <button
                  type="button"
                  @click="if (item.jumlah < item.stok) { item.jumlah++; $store.cart.update(item); }"
                  :disabled="item.jumlah >= item.stok"
                  class="user-quantity-button"
                  aria-label="Tambah jumlah">+</button>
              </div>

              <div class="min-w-24 text-right">
                <p class="text-xs text-slate-500">Subtotal</p>
                <p class="mt-0.5 text-sm font-semibold text-slate-900" x-text="$store.utils.formatRupiah(item.harga_jual * item.jumlah)"></p>
              </div>

              <button
                type="button"
                @click="$store.cart.remove(item.id_keranjang)"
                class="user-action-danger h-10 w-10 shrink-0 p-0"
                aria-label="Hapus produk dari keranjang">
                <span aria-hidden="true">×</span>
              </button>
            </div>
          </article>
        </template>

      </div>

      <!-- TOTAL -->
      <div x-show="$store.cart.items.length > 0" x-cloak class="fixed bottom-0 left-0 z-40 w-full border-t border-slate-200 bg-white/95 p-3 shadow-[0_-8px_24px_rgba(15,23,42,0.08)] backdrop-blur md:relative md:bottom-auto md:left-auto md:z-auto md:mt-3 md:rounded-2xl md:border md:p-4 md:shadow-sm">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div>
            <p class="text-xs text-slate-500">Total belanja</p>
            <p class="text-xl font-semibold text-primary" x-text="$store.utils.formatRupiah($store.cart.totalHarga)"></p>
          </div>

          <div class="grid grid-cols-2 gap-2 md:flex md:w-auto">
            <button
              type="button"
              @click="$store.cart.clear()"
              :disabled="$store.cart.items.length === 0"
              class="user-action-danger w-full md:w-auto"
              >
              Hapus semua
            </button>
            <button
              type="button"
              @click="$store.cart.checkout()"
              :disabled="$store.cart.items.length === 0 || $store.cart.checkingOut"
              class="user-action-primary w-full md:w-auto disabled:cursor-not-allowed disabled:opacity-50"
              >
              <span x-text="$store.cart.checkingOut ? 'Memproses...' : 'Checkout'"></span>
            </button>
          </div>
        </div>
      </div>
    </div>

  </template>

</div>