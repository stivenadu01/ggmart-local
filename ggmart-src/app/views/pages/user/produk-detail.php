<div class="py-10" x-data="detailProdukPage()">

  <!-- BACK BUTTON -->
  <div class="px-d mb-6">
    <button @click="window.history.back()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
      <span class="text-2xl">‹</span>
      <span>Kembali</span>
    </button>
  </div>

  <!-- CONTENT -->
  <template x-if="produk">
    <div class="px-2 md:px-8 lg:px-16 max-w-7xl mx-auto">

      <!-- MAIN GRID -->
      <div class="grid md:grid-cols-2 gap-8 mb-16">

        <!-- LEFT: IMAGE -->
        <div class="flex">
          <div class="w-full max-w-lg rounded-3xl overflow-hidden bg-gray-50 flex items-start p-6 glass">
            <img id="img_detail"
              :src="produk.gambar ? BASE_URL + '/uploads/' + produk.gambar : '/assets/no-image.png'"
              class="w-full h-auto object-contain"
              alt="">
          </div>
        </div>

        <!-- RIGHT: DETAILS -->
        <div class="flex flex-col justify-center space-y-6">

          <!-- TITLE & DESCRIPTION -->
          <div>
            <h1 class="text-4xl! mb-3" x-text="produk.nama_produk"></h1>
            <p class="text-gray-600 text-base leading-relaxed whitespace-pre-wrap" x-text="produk.deskripsi"></p>
          </div>

          <!-- PRICE & ORIGIN -->
          <div class="space-y-3">
            <div class="text-4xl font-poppins font-semibold text-primary"
              x-text="$store.utils.formatRupiah(produk.harga_jual)">
            </div>
            <template x-if="produk.asal_produk">
              <div class="inline-block px-4 py-2 bg-green-100/50 rounded-full text-sm text-gray-700 font-medium border border-green-200"
                x-text="'📍 ' + produk.asal_produk">
              </div>
            </template>
          </div>

          <!-- STOCK STATUS -->
          <div class="flex items-center gap-3 p-4 rounded-2xl glass border border-gray-200/50">
            <div class="w-3 h-3 rounded-full"
              :class="produk.stok > 0 ? 'bg-green-500' : 'bg-red-500'">
            </div>
            <span class="text-sm font-medium"
              x-text="produk.stok > 0 ? `Stok Tersedia (${produk.stok})` : 'Stok Habis'">
            </span>
          </div>

          <!-- QUANTITY SELECTOR & ADD TO CART -->
          <div class="space-y-4">
            <div class="flex flex-wrap items-center gap-4">
              <span class="text-sm font-medium text-gray-700">Jumlah:</span>
              <div class="flex items-center gap-3 border border-gray-300 rounded-xl p-2 w-fit">
                <button
                  @click="qty = Math.max(1, qty - 1)"
                  :disabled="qty <= 1"
                  class="w-8 h-8 flex-center hover:bg-gray-100 rounded-lg transition disabled:opacity-50">
                  −
                </button>
                <span class="w-8 text-center font-medium" x-text="qty"></span>
                <button
                  @click="qty = Math.min(produk.stok, qty + 1)"
                  :disabled="qty >= produk.stok"
                  class="w-8 h-8 flex-center hover:bg-gray-100 rounded-lg transition disabled:opacity-50">
                  +
                </button>
              </div>
            </div>
            <div class="flex-center gap-3">
              <a
                :href="`https://wa.me/${NOMOR_WA}?text=Halo, saya ingin bertanya tentang ${produk.nama_produk}`"
                target="_blank"
                class="btn-outline-primary md:py-4 md:text-lg md:rounded-lg flex-center">
                Hubungi Admin
              </a>
              <button
                @click="addToCart()"
                :disabled="produk.stok <= 0"
                class="btn-primary md:py-4 md:text-lg md:rounded-lg">
                <span class="flex-center" x-show="produk.stok > 0">
                  <span class="hidden md:flex" x-text="'Tambah ke '"></span>
                  <span class="md:hidden" x-text="'+ '"></span>
                  Keranjang
                </span>
                <span x-show="produk.stok <= 0">Stok Habis</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- DIVIDER -->
      <div class="h-px bg-linear-to-r from-transparent via-gray-300 to-transparent my-16"></div>

      <!-- RELATED PRODUCTS -->
      <div>
        <h2 class="text-3xl! mb-8">Produk Terkait</h2>

        <!-- LOADING RELATED -->
        <template x-if="loadingRelated">
          <div class="flex-center h-40">
            <div class="w-10 h-10 border-4 border-gray-300 border-t-primary rounded-full animate-spin"></div>
          </div>
        </template>

        <!-- EMPTY -->
        <template x-if="!loadingRelated && produkTerkait.length === 0">
          <div class="text-center py-10 text-gray-500">
            Tidak ada produk terkait
          </div>
        </template>

        <!-- GRID -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
          <template x-for="item in produkTerkait" :key="item.kode_produk">
            <div class="group min-h-48 md:min-h-64">
              <div class="rounded-2xl overflow-hidden shadow hover:shadow-lg transition bg-white flex flex-col h-full">

                <!-- IMAGE -->
                <div class="bg-gray-50 flex items-center justify-center cursor-pointer aspect-square"
                  @click="goDetail(item.kode_produk)">
                  <img
                    loading="lazy"
                    decoding="async"
                    :src="item.gambar ? BASE_URL + '/uploads' + item.gambar : '/assets/no-image.png'"
                    class="w-full h-full object-contain group-hover:scale-105 transition">
                </div>

                <!-- CONTENT -->
                <div class="p-3 space-y-1.5 flex-1">
                  <div class="text-sm font-medium line-clamp-2"
                    x-text="item.nama_produk"></div>

                  <div class="text-primary font-semibold font-poppins"
                    x-text="$store.utils.formatRupiah(item.harga_jual)">
                  </div>

                  <div
                    x-show="item.asal_produk"
                    x-text="item.asal_produk"
                    class="text-xs text-gray-500 italic line-clamp-1">
                  </div>
                </div>

                <!-- ACTION -->
                <div class="flex gap-2 p-3">
                  <button
                    @click="quickAddToCart(item, $event)"
                    class="btn-primary btn-rounded whitespace-nowrap text-xs px-3 py-1 flex-1">
                    + Keranjang
                  </button>

                  <button
                    @click="goDetail(item.kode_produk)"
                    class="btn-outline btn-rounded text-xs px-3 py-1 flex-1">
                    Detail
                  </button>
                </div>

              </div>
            </div>
          </template>
        </div>

      </div>

    </div>
  </template>

</div>

<script>
  function detailProdukPage() {
    return {
      produk: null,
      produkTerkait: [],
      loadingRelated: false,
      qty: 1,

      async init() {
        const kode = window.location.pathname.split('/').pop()
        await this.loadDetail(kode)
      },

      async loadDetail(kode) {
        try {
          const res = await API.get(`/produk/detail?k=${kode}`)
          this.produk = res.data

          // Reset quantity
          this.qty = 1

          // Load related products
          await this.loadRelated(kode)
        } catch (err) {
          console.error(err)
          this.produk = null
        }
      },

      async loadRelated(kodeProduk) {
        this.loadingRelated = true
        try {
          const res = await API.get(`/produk/terkait?k=${kodeProduk}`, false)
          this.produkTerkait = res.data || []
        } catch (err) {
          console.error(err)
          this.produkTerkait = []
        } finally {
          this.loadingRelated = false
        }
      },

      async addToCart() {
        if (!this.produk || this.produk.stok <= 0) return
        const imgEL = document.getElementById('img_detail')
        await Alpine.store('cart').add({
          ...this.produk,
          jumlah: this.qty,
        }, imgEL)

        // Reset quantity
        this.qty = 1
      },

      async quickAddToCart(item, e) {
        const card = e.target.closest('.group')
        const imgEl = card.querySelector('img')
        await Alpine.store('cart').add({
          ...item,
          jumlah: 1
        }, imgEl)
      },

      goDetail(kode) {
        window.location.href = `${BASE_URL}/produk/${kode}`
      }
    }
  }
</script>