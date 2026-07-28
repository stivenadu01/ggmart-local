<div class="p-4 space-y-4" x-data="kasirPage()">

  <!-- HEADER -->
  <div class="flex-between">
    <div>
      <h1>Kasir</h1>
      <p class="text-gray-500 text-sm">Input transaksi penjualan</p>
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-4">

    <!-- ================= LEFT: PRODUK ================= -->
    <div class="bg-white rounded-xl shadow border p-4 flex flex-col">
      <!-- SEARCH -->
      <input
        type="text"
        x-model="search"
        @input.debounce.300="loadProduk()"
        @keydown.enter.prevent="tambahDariInput()"
        placeholder="Cari produk..."
        class="input text-lg mb-4"
        x-ref="searchInput">

      <!-- LIST -->
      <div class="grid grid-cols-2 md:grid-cols-3 gap-3 overflow-auto">

        <template x-for="p in produk" :key="p.kode_produk">
          <div
            @click="tambah(p)"
            class="border rounded-lg p-2 cursor-pointer hover:bg-gray-50">

            <div class="text-sm font-semibold truncate" x-text="p.nama_produk"></div>

            <div class="text-xs text-gray-400" x-text="p.nama_kategori"></div>

            <div class="text-green-600 font-bold text-sm mt-1"
              x-text="utils.formatRupiah(p.harga_jual)"></div>

            <div class="text-xs mt-1"
              :class="p.stok <= 0 ? 'text-red-500' : 'text-gray-500'"
              x-text="p.stok > 0 ? 'Stok: ' + p.stok : 'Habis'">
            </div>

          </div>
        </template>

        <template x-if="produk.length === 0">
          <div class="col-span-full text-center text-gray-400 py-6">
            Produk tidak ditemukan
          </div>
        </template>

      </div>

    </div>

    <!-- ================= RIGHT: KERANJANG ================= -->
    <div class="bg-white rounded-xl shadow border p-4 flex flex-col">

      <div class="flex-between mb-3">
        <h2 class="font-bold">Keranjang</h2>
        <button @click="reset()" class="text-red-500 text-sm">Reset</button>
      </div>

      <!-- LIST -->
      <div class="flex-1 overflow-auto space-y-2">

        <template x-for="(item, i) in keranjang" :key="item.kode_produk">
          <div class="flex-between border p-2 rounded-lg">

            <div>
              <div class="font-medium text-sm" x-text="item.nama_produk"></div>
              <div class="text-xs text-gray-400"
                x-text="utils.formatRupiah(item.harga_satuan)">
              </div>
            </div>

            <div class="flex items-center gap-2">

              <input type="number"
                min="1"
                class="input w-16"
                x-model.number="item.jumlah"
                @input="update(i)">

              <div class="text-sm font-semibold"
                x-text="utils.formatRupiah(item.subtotal)">
              </div>

              <button @click="hapus(i)" class="text-red-500 text-xs">✕</button>

            </div>

          </div>
        </template>

        <template x-if="keranjang.length === 0">
          <div class="text-center text-gray-400 py-10">
            Keranjang kosong
          </div>
        </template>

      </div>

      <!-- TOTAL -->
      <div class="border-t pt-3 space-y-2 mt-3">

        <div class="flex-between text-lg font-bold">
          <span>Total</span>
          <span x-text="utils.formatRupiah(total)"></span>
        </div>

        <!-- METODE -->
        <div class="flex gap-2">
          <button
            @click="metode='tunai'"
            :class="metode==='tunai' ? 'bg-green-500 text-white' : 'border'"
            class="flex-1 p-2 rounded-lg border">
            Tunai
          </button>

          <button
            @click="metode='qris'"
            :class="metode==='qris' ? 'bg-green-500 text-white' : 'border'"
            class="flex-1 p-2 rounded-lg border">
            QRIS
          </button>
        </div>

        <!-- BUTTON -->
        <div class="space-y-2">
          <button
            @click="submit(true)"
            class="btn-primary w-full">
            Simpan & Cetak
          </button>

          <button
            @click="submit(false)"
            class="btn-secondary w-full">
            Simpan Saja
          </button>
        </div>

      </div>

    </div>

  </div>

</div>

<script>
  function kasirPage() {
    return {

      produk: [],
      keranjang: [],
      search: '',
      total: 0,
      metode: 'tunai',

      // ================= INIT =================
      async init() {
        await this.loadProduk()

        // shortcut
        window.addEventListener('keydown', (e) => {
          if (e.ctrlKey && e.key === 'k') {
            e.preventDefault()
            this.$refs.searchInput.focus()
          }

          if (e.ctrlKey && e.key === 'Enter') {
            this.submit(true)
          }

          if (e.ctrlKey && e.key === 's') {
            e.preventDefault()
            this.submit(false)
          }
        })
      },

      // ================= PRODUK =================
      async loadProduk() {
        const res = await API.get('/produk/trx?search=' + encodeURIComponent(this.search), false)
        this.produk = res.data || []
      },

      tambahDariInput() {
        if (this.produk.length === 1) {
          this.tambah(this.produk[0])
        } else {
          Alpine.store('ui').toast('Produk tidak ditemukan')
        }
      },

      // ================= KERANJANG =================
      tambah(p) {
        if (p.stok <= 0) {
          Alpine.store('ui').toast('Stok habis')
          return
        }

        let idx = this.keranjang.findIndex(i => i.kode_produk === p.kode_produk)

        if (idx >= 0) {
          if (this.keranjang[idx].jumlah < p.stok) {
            this.keranjang[idx].jumlah++
            this.update(idx)
          }
        } else {
          this.keranjang.push({
            kode_produk: p.kode_produk,
            nama_produk: p.nama_produk,
            harga_satuan: Number(p.harga_jual),
            jumlah: 1,
            subtotal: Number(p.harga_jual),
            stok: p.stok
          })
        }

        this.search = ''
        this.hitung()
      },

      update(i) {
        let item = this.keranjang[i]

        if (item.jumlah > item.stok) {
          item.jumlah = item.stok
        }

        item.subtotal = item.jumlah * item.harga_satuan
        this.hitung()
      },

      hapus(i) {
        this.keranjang.splice(i, 1)
        this.hitung()
      },

      reset() {
        this.keranjang = []
        this.total = 0
      },

      hitung() {
        this.total = this.keranjang.reduce((s, i) => s + i.subtotal, 0)
      },

      // ================= SUBMIT =================
      async submit(cetak = false) {
        try {
          if (this.keranjang.length === 0) {
            Alpine.store('ui').toast('Keranjang kosong')
            return
          }

          const payload = {
            total_harga: this.total,
            metode_bayar: this.metode,
            detail: this.keranjang.map(i => ({
              kode_produk: i.kode_produk,
              jumlah: i.jumlah,
              harga_satuan: i.harga_satuan,
              subtotal: i.subtotal
            }))
          }

          const res = await API.post('/transaksi', payload)

          if (res.success) {
            Alpine.store('ui').toast('Transaksi berhasil')

            const kode = res.data

            this.reset()

            if (cetak) {
              window.open(`/admin/transaksi/print?k=${kode}`, '_blank')
            }

          }

        } catch (err) {
          console.error(err)
        }
      }

    }
  }
</script>