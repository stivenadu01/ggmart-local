<div class="space-y-4 p-4" x-data="transaksiDetailPage()">

  <!-- HEADER -->
  <div class="flex-between flex-col md:flex-row gap-4">
    <div>
      <h1>Detail Transaksi</h1>
      <p class="text-gray-500 text-sm">Informasi lengkap transaksi</p>
    </div>

    <a href="#" @click.prevent="history.back()" class="btn-secondary w-auto">
      ← Kembali
    </a>
  </div>

  <!-- LOADING -->
  <template x-if="loading">
    <div class="text-center py-10 text-gray-400">Memuat...</div>
  </template>

  <!-- CONTENT -->
  <template x-if="!loading && trx">
    <div class="grid md:grid-cols-3 gap-4">
      <!-- ================= LEFT ================= -->
      <div class="md:col-span-2 space-y-4 max-h-[85dvh] overflow-y-auto">
        <!-- PRODUK -->
        <div class="bg-white rounded-xl shadow border overflow-hidden">
          <div class="p-4 border-b font-semibold">Daftar Produk</div>

          <div class="divide-y">
            <template x-for="item in trx.detail" :key="item.kode_produk">
              <div class="p-4 flex gap-3 items-center">

                <img
                  :src="item.gambar ? BASE_URL + '/uploads/' + item.gambar : '/assets/no-image.png'"
                  class="w-14 h-14 object-cover rounded-lg">

                <div class="flex-1">
                  <div class="font-medium" x-text="item.nama_produk"></div>
                  <div class="text-sm text-gray-500">
                    <span x-text="utils.formatRupiah(item.harga_satuan)"></span>
                    × <span x-text="item.jumlah"></span>
                  </div>
                </div>

                <div class="font-semibold text-right"
                  x-text="utils.formatRupiah(item.harga_satuan * item.jumlah)">
                </div>

              </div>
            </template>
          </div>
        </div>
      </div>

      <!-- ================= RIGHT ================= -->
      <div class="space-y-4">
        <!-- INFO -->
        <div class="bg-white p-4 rounded-xl shadow border space-y-2">
          <div class="flex-between">
            <div>
              <div class="text-sm text-gray-500">Kode Transaksi</div>
              <div class="font-bold text-lg" x-text="trx.kode_transaksi"></div>
            </div>

            <span
              :class="{
                'bg-blue-100 text-blue-700': trx.status === 'pending',
                'bg-yellow-100 text-yellow-700': trx.status === 'diproses',
                'bg-green-100 text-green-700': trx.status === 'selesai',
                'bg-red-100 text-red-700': trx.status === 'dibatalkan'
              }"
              class=" px-3 py-1 rounded-lg text-sm font-semibold"
              x-text="trx.status">
            </span>

          </div>

          <div class="text-sm text-gray-600">
            Tanggal:
            <span x-text="utils.formatDateTime(trx.tanggal_transaksi)"></span>
          </div>

          <div class="text-sm text-gray-600">
            Pelanggan:
            <span x-text="trx.user || '-'"></span>
          </div>

          <div class="text-sm text-gray-600">
            Metode:
            <span class="font-medium uppercase" x-text="trx.metode_bayar"></span>
          </div>
        </div>

        <!-- TOTAL -->
        <div class="bg-white p-4 rounded-xl shadow border space-y-2">
          <div class="flex-between text-sm">
            <span>Total Harga</span>
            <span class="font-bold text-lg text-primary"
              x-text="utils.formatRupiah(trx.total_harga)">
            </span>
          </div>
          <template x-if="trx.status === 'selesai'">
            <div class="flex-between text-sm">
              <span>Total Modal</span>
              <span x-text="utils.formatRupiah(trx.total_pokok)"></span>
            </div>
          </template>

          <template x-if="trx.status === 'selesai'">
            <div class="flex-between text-sm font-semibold text-green-600">
              <span>Laba</span>
              <span x-text="utils.formatRupiah(trx.total_harga - trx.total_pokok)"></span>
            </div>
          </template>
        </div>

        <!-- AKSI -->
        <template x-if="trx.status !== 'dibatalkan'">
          <div class="bg-white p-4 rounded-xl shadow border space-y-3">
            <template x-if="trx.status === 'pending'">
              <button class="flex-center gap-2 btn-outline-primary" @click="proses()">
                <svg class=' w-6 h-6' xmlns="http://w3.org" viewBox="0 0 24 28">
                  <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.5 3h6L19 6.5v17c0 1.1-.9 2-2 2H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2z" />
                    <path d="M15.5 3v3.5H19" />
                    <path opacity=".5" d="M8.5 10.5h5" />
                    <circle cx="12" cy="17.5" r="2" />
                    <path d="M12 14.5v1m0 4v1m-3-3h1m4 0h1m-5-2 .6.6m2.8 2.8.6.6m-4 0 .6-.6m2.8-2.8.6-.6" />
                    <path d="M7.5 17.5a4.5 4.5 0 1 1 7 3.5" opacity=".7" />
                    <path d="m12 22 2.5-1-.5-2.5" opacity=".7" />
                  </g>
                </svg>
                Proses Pesanan
              </button>
            </template>
            <template x-if="trx.status === 'diproses'">
              <button class="flex-center gap-2 btn-outline-primary" @click="konfirmasi()">
                <svg class='h-6 w-6' xmlns="http://w3.org" viewBox="0 0 24 28">
                  <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.5 3h6L19 6.5V15m-6 10.5H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h2.5M5 23.5V5" />
                    <path d="M15.5 3v3.5H19m-10.5 4h7m-7 3.5h5" />
                    <path opacity=".6" d="M8.5 17.5h3" />
                    <circle cx="17.5" cy="20.5" r="4" fill="currentColor" fill-opacity=".05" />
                    <path d="M15.5 20.5 17 22l2.5-3" stroke-width="1.5" />
                  </g>
                </svg>
                Konfirmasi Pesanan
              </button>
            </template>
            <button
              @click="batal()"
              class="w-full flex-center btn-danger gap-2">
              <svg class='w-6 h-6' xmlns="http://w3.org" viewBox="0 0 24 28">
                <g fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9.5 3h6L19 6.5V15m-6 10.5H7c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h2.5" />
                  <path d="M15.5 3v3.5H19m-10.5 4h7m-7 3.5h5" />
                  <path opacity=".6" d="M8.5 17.5h3" />
                  <circle cx="17.5" cy="20.5" r="4" fill="currentColor" fill-opacity=".05" />
                  <path d="m15.5 18.5 4 4m0-4-4 4" stroke-width="1.5" />
                </g>
              </svg>
              Batalkan Transaksi
            </button>
          </div>
        </template>
      </div>

    </div>
  </template>

</div>

<script>
  function transaksiDetailPage() {
    return {
      trx: null,
      loading: true,
      kode: <?= json_encode(params('kode')) ?>,

      async init() {
        await this.load()
      },

      async load() {
        try {
          if (!this.kode) {
            Alpine.store('ui').toast('Kode transaksi tidak valid')
            return history.back()
          }

          const res = await API.get('/transaksi/detail?k=' + this.kode)

          if (res.success) {
            this.trx = res.data
            console.log(this.trx);
          } else {
            Alpine.store('ui').toast(res.message || 'Gagal load data')
          }

        } catch (err) {
          console.error(err)
        } finally {
          this.loading = false
        }
      },

      async proses() {
        const ok = await Alpine.store('ui').confirm('Proses pesanan ini?')
        if (!ok) return
        try {
          await API.post('/transaksi/proses', {
            kode_transaksi: this.trx.kode_transaksi
          })
          Alpine.store('ui').toast('Pesanan diproses')
          this.load()
        } catch (err) {
          console.error(err)
        }
      },


      async konfirmasi() {
        const ok = await Alpine.store('ui').confirm('Konfirmasi pesanan ini?')
        if (!ok) return
        try {
          const res = await API.post('/transaksi/konfirmasi', {
            kode_transaksi: this.trx.kode_transaksi
          })
          if (res.success) {
            Alpine.store('ui').toast('Pesanan dikonfirmasi')
            await this.load()
          }
        } catch (err) {
          console.error(err)
        }
      },

      async batal() {
        const ok = await Alpine.store('ui').confirm(`Yakin ingin membatalkan transaksi ${this.trx.status=='selesai'?'yang telah selesai' : ''} ini ?`)
        if (!ok) return
        try {
          const res = await API.post('/transaksi/batal', {
            kode_transaksi: this.trx.kode_transaksi
          })
          if (res.success) {
            Alpine.store('ui').toast('transaksi dibatalkan')
            await this.load()
          }
        } catch (err) {
          console.error(err)
        }
      }
    }
  }
</script>