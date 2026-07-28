<div class="py-10 max-w-7xl mx-auto" x-data="transaksiPage()">

  <!-- HEADER -->
  <div class="mb-5 px-d">
    <h1>Transaksi Saya</h1>
    <p class="text-gray-500 text-sm">Riwayat pembelian Anda</p>
  </div>

  <!-- TAB STATUS -->
  <div class="sticky md:block top-11 md:top-auto z-49 md:mt-10">
    <div class="flex gap-2 mb-6 pb-1.5 px-d glass py-2 border-b border-black/10 overflow-hidden overflow-x-auto">
      <template x-for="tab in tabs" :key="tab">
        <button
          @click="filter.status = tab; load(); window.scrollTo({ top: 125, behavior: 'smooth' });"
          class="px-4 py-2 rounded-full text-sm whitespace-nowrap transition"
          :class="filter.status === tab
          ? 'bg-primary text-white'
          : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">

          <span x-text="tab === '' ? 'Semua' : tab"></span>
        </button>
      </template>
    </div>
  </div>

  <!-- LIST -->
  <div class="space-y-8 px-d min-h-[75dvh]">
    <!-- LOADING -->
    <template x-if="loading">
      <div class="flex-center h-[60dvh]">
        <div class="w-10 h-10 border-4 border-gray-300 border-t-primary rounded-full animate-spin"></div>
      </div>
    </template>
    <!-- EMPTY -->
    <template x-if="!loading && transaksi.length === 0">
      <div class="text-center py-10 text-gray-500">
        Belum ada transaksi<span x-text="filter.status === '' ? '' : ` dengan status ${filter.status}`"></span>.
      </div>
    </template>
    <!-- CARD -->
    <template x-for="t in transaksi" :key="t.kode_transaksi">
      <div class="card py-3 space-y-4 border border-gray-200 bg-gray-100/50 relative">
        <!-- STATUS -->
        <div class="flex justify-end absolute top-2 right-2">
          <span class="px-2 py-1 rounded-full"
            :class="{
              'bg-yellow-100 text-yellow-700': t.status === 'diproses',
              'bg-green-100 text-green-700': t.status === 'selesai',
              'bg-red-100 text-red-700': t.status === 'dibatalkan',
              'bg-blue-100 text-blue-700': t.status === 'pending'
            }"
            x-text="t.status">
          </span>
        </div>


        <div class="flex flex-col md:flex-row md:items-center gap-3 mt-5 md:mt-3">
          <!-- LEFT -->
          <div class="text-sm md:text-base w-full">
            <p>Kode Transaksi:
              <span class="font-semibold font-poppins wrap-break-word" x-text="t.kode_transaksi"></span>
            </p>

            <div class="text-gray-500 text-sm mt-1"
              x-text="`Waktu Pemesanan: ${Alpine.store('utils').formatDateTime(t.tanggal_transaksi)}`">
            </div>
          </div>

          <!-- RIGHT -->
          <div class="text-sm md:text-base w-full">
            <p>Total Pembayaran:
              <span class="font-semibold font-poppins"
                x-text="' '+$store.utils.formatRupiah(t.total_harga)"></span>
            </p>
          </div>
        </div>

        <!-- DESKTOP GRID -->
        <div class="hidden md:grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">

          <template x-for="d in t.detail" :key="d.kode_produk">
            <div class="flex items-center gap-3 p-2 border border-gray-300 rounded-lg">
              <img
                loading="lazy"
                decoding="async"
                @click="window.location.href = `${BASE_URL}/produk/${d.kode_produk}`"
                :src="d.gambar ? BASE_URL + '/uploads/' + d.gambar : '/assets/no-image.png'"
                class="w-12 h-12 object-cover cursor-pointer" />

              <div class="min-w-0 flex-1">
                <div class="text-sm font-medium truncate"
                  x-text="d.nama_produk"></div>

                <div class="text-xs text-gray-500">
                  <span x-text="d.jumlah"></span> ×
                  <span x-text="$store.utils.formatRupiah(d.harga_satuan)"></span>
                </div>

                <div class="text-sm font-light text-gray-900"
                  x-text="$store.utils.formatRupiah(d.harga_satuan * d.jumlah)">
                </div>
              </div>
            </div>
          </template>

        </div>
        <!-- MOBILE LIST (FLEX COL FIX) -->
        <div class="md:hidden space-y-3">
          <template x-for="d in t.detail" :key="d.kode_produk">

            <div class="flex justify-between items-center gap-3 p-2 border-gray-200 border-b rounded-lg">

              <!-- LEFT -->
              <div class="flex items-center gap-3 min-w-0">

                <img
                  loading="lazy"
                  decoding="async"
                  @click="window.location.href = `${BASE_URL}/produk/${d.kode_produk}`"
                  :src="d.gambar ? BASE_URL + '/uploads/' + d.gambar : '/assets/no-image.png'"
                  class="w-10 h-10 object-cover rounded-md border shrink-0" />

                <div class="min-w-0">
                  <div class="text-sm font-medium truncate"
                    x-text="d.nama_produk"></div>

                  <div class="text-xs text-gray-500">
                    <span x-text="d.jumlah"></span> ×
                    <span x-text="$store.utils.formatRupiah(d.harga_satuan)"></span>
                  </div>
                </div>

              </div>

              <!-- RIGHT -->
              <div class="text-sm font-light whitespace-nowrap"
                x-text="$store.utils.formatRupiah(d.harga_satuan * d.jumlah)">
              </div>

            </div>

          </template>
        </div>

        <div class="flex justify-end gap-3">
          <button :disabled="t.status != 'pending'" class="btn-outline-danger btn-rounded" @click="batal(t.kode_transaksi)">Batalkan Pesanan</button>
          <a
            target="_blank"
            class="btn-primary btn-rounded"
            :href="`https://wa.me/${NOMOR_WA}?text=Halo, saya ${Alpine.store('auth').user.nama} ingin menanyakan transaksi ${t.kode_transaksi}`">
            Hubungi Admin
          </a>
        </div>
      </div>
    </template>

  </div>
</div>

<script>
  function transaksiPage() {
    return {
      transaksi: [],
      loading: false,

      tabs: ['', 'pending', 'diproses', 'selesai', 'dibatalkan'],

      filter: {
        status: ''
      },

      async init() {
        await this.load()
      },

      async load() {
        this.loading = true
        try {
          const query = new URLSearchParams()

          query.append('user', Alpine.store('auth').user.id_user)

          if (this.filter.status) {
            query.append('status', this.filter.status)
          }

          const res = await API.get('/transaksi/list?' + query.toString(), false)
          this.transaksi = res.data

          // load detail tiap transaksi (karena UI butuh produk list)
          for (let t of this.transaksi) {
            const d = await API.get('/transaksi/detail?k=' + t.kode_transaksi, false)
            t.detail = d.data.detail
          }

        } finally {
          this.loading = false
        }
      },

      async batal(kode_transaksi) {
        const ok = await Alpine.store('ui').confirm(`Yakin ingin membatalkan pesanan ${kode_transaksi}`)
        if (ok) {
          await API.post('/transaksi/batal-pending', {
            kode_transaksi
          })
          this.load();
        }
      }
    }
  }
</script>