<div class="px-d py-10 max-w-7xl mx-auto" x-data>

  <h2 class="mb-6 tracking-tight">Keranjang Belanja</h2>

  <!-- BELUM LOGIN -->
  <template x-if="!$store.auth.user">
    <div class="section-center text-center">
      <div class="space-y-4">
        <p class="text-gray-500">Silakan login untuk melihat keranjang</p>
        <a :href="BASE_URL + '/login'" class="btn-primary w-auto px-6 inline-flex">
          Login
        </a>
      </div>
    </div>
  </template>

  <!-- SUDAH LOGIN -->
  <template x-if="$store.auth.user">

    <div class="space-y-6">

      <!-- EMPTY -->
      <template x-if="$store.cart.items.length === 0">
        <div class="card text-center py-10 text-gray-500">
          🛒 Keranjang kamu masih kosong
        </div>
      </template>

      <!-- LIST -->
      <div class="space-y-3 min-h-75">

        <template x-for="item in $store.cart.items" :key="item.id_keranjang">

          <div class="card flex-between gap-3 md:gap-4 hover:shadow-sm transition border-b border-gray-200">

            <!-- PRODUCT INFO -->
            <div class="flex gap-3 md:gap-4 items-center min-w-0 flex-1">

              <img
                loading="lazy"
                :src="`${BASE_URL}/uploads${item.gambar}`"
                class="w-12 h-12 md:w-16 md:h-16 object-cover rounded-lg shrink-0">

              <div class="min-w-0 flex-1">
                <div class="font-medium text-sm md:text-base truncate"
                  x-text="item.nama_produk"></div>

                <div class="text-xs md:text-sm text-gray-500 mt-0.5"
                  x-text="$store.utils.formatRupiah(item.harga_jual)">
                </div>
              </div>

            </div>

            <!-- QUANTITY CONTROL -->
            <div class="flex items-center gap-1 md:gap-2 shrink-0">

              <!-- MINUS -->
              <button
                @click="
                  if (item.jumlah > 1) {
                    item.jumlah--;
                    $store.cart.update(item);
                  }
                "
                class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex-center transition text-sm md:text-lg">
                −
              </button>

              <!-- QTY -->
              <div
                class="w-6 md:w-10 text-center text-xs md:text-sm font-medium text-gray-700 select-none">
                <span x-text="item.jumlah"></span>
              </div>

              <!-- PLUS -->
              <button
                @click="
                item.jumlah++;
                $store.cart.update(item);
                "
                class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex-center transition text-sm md:text-lg">
                +
              </button>

              <!-- DELETE -->
              <button
                @click="$store.cart.remove(item.id_keranjang)"
                class="ml-2 md:ml-3 text-lg md:text-2xl text-red-500 hover:text-red-600 font-light transition">
                ✕
              </button>
            </div>
          </div>
        </template>

      </div>

      <!-- TOTAL -->
      <div class="card mt-3 flex-between flex-col md:flex-row gap-4 fixed bottom-0 left-0 w-full md:relative md:bottom-auto md:left-auto md:w-auto">

        <div>
          <div class="text-xl font-medium" x-show="$store.cart.items.length > 0">
            Total:
            <span class="text-primary font-poppins"
              x-text="$store.utils.formatRupiah($store.cart.totalHarga)">
            </span>
          </div>
        </div>

        <div class="flex-between w-full md:w-auto px-0 gap-2">
          <button @click=" $store.cart.clear()"
            :disabled="$store.cart.items.length === 0"
            class="btn-outline-danger md:w-auto">
            Hapus semua
          </button>
          <button
            @click="$store.cart.checkout()"
            :disabled="$store.cart.items.length === 0"
            class="btn-primary md:w-auto">
            Checkout
          </button>
        </div>

      </div>

    </div>

  </template>

</div>