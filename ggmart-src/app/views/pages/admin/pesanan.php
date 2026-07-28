<div class="space-y-4 p-4" x-data="pesananPage()">

  <!-- HEADER -->
  <div class="flex-between flex-col md:flex-row gap-4">
    <div>
      <h1>Pesanan User</h1>
      <p class="text-gray-500 text-sm">Daftar transaksi dari pelanggan yang menunggu diproses</p>
    </div>
  </div>

  <!-- FILTER -->
  <div class="flex flex-col md:flex-row gap-2">

    <div>
      <label class="label">Cari</label>
      <input
        type="text"
        x-model="search"
        @input.debounce.500="pagination.page=1; load()"
        class="input min-w-64"
        placeholder="Kode / produk...">
    </div>

    <div>
      <label class="label">Metode</label>
      <select x-model="metode" @change="load()" class="input">
        <option value="">Semua</option>
        <option value="tunai">Tunai</option>
        <option value="qris">QRIS</option>
      </select>
    </div>

  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto max-h-[75dvh] overflow-y-auto">
    <table class="table">
      <thead>
        <tr class="bg-gray-50">
          <th>No</th>
          <th>Kode</th>
          <th>Tanggal</th>
          <th>Nama Pelanggan</th>
          <th>Metode</th>
          <th>Total</th>
          <th>Status</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>

      <tbody>

        <!-- EMPTY -->
        <template x-if="data.length === 0">
          <tr>
            <td colspan="8" class="text-center py-8 text-gray-500">
              Tidak ada pesanan
            </td>
          </tr>
        </template>

        <!-- DATA -->
        <template x-for="(item, idx) in data" :key="item.kode_transaksi">
          <tr class="hover:bg-gray-50">

            <td x-text="(pagination.page-1)*pagination.limit + idx + 1"></td>

            <td class="font-medium" x-text="item.kode_transaksi"></td>

            <td class="text-sm text-gray-500"
              x-text="utils.formatDateTime(item.tanggal_transaksi)">
            </td>

            <td x-text="item.user || '-'"></td>

            <td class="uppercase text-sm" x-text="item.metode_bayar"></td>

            <td class="font-semibold text-green-600"
              x-text="utils.formatRupiah(item.total_harga)">
            </td>

            <td>
              <span class="px-2 py-1 rounded-full"
                :class="{
                  'bg-yellow-100 text-yellow-700': item.status === 'diproses',
                  'bg-blue-100 text-blue-700': item.status === 'pending'
                }"
                x-text="item.status">
              </span>
            </td>

            <td class="text-right space-x-2">

              <button
                @click="detail(item.kode_transaksi)"
                class="text-blue-600 text-sm hover:underline">
                Detail
              </button>

              <button
                @click="item.status === 'pending' ? proses(item.kode_transaksi) : (item.status === 'diproses' ? konfirmasi(item.kode_transaksi) : null)"
                class="text-green-600 text-sm hover:underline">
                <span x-text="item.status === 'pending' ? 'Proses' : 'Konfirmasi'"></span>
              </button>

              <button
                @click="batal(item.kode_transaksi)"
                class="text-red-600 text-sm hover:underline">
                Batalkan
              </button>

            </td>

          </tr>
        </template>

      </tbody>
    </table>
  </div>

  <!-- PAGINATION -->
  <div class="flex-between flex-col md:flex-row gap-4 text-sm text-gray-600">

    <div>
      Menampilkan
      <select x-model.number="pagination.limit" @change="pagination.page=1; load()" class="input w-auto">
        <option value="10">10</option>
        <option value="25">25</option>
      </select>
      dari <span x-text="pagination.total"></span>
    </div>

    <div class="flex gap-2">
      <button @click="pagination.page--; load()"
        :disabled="pagination.page==1"
        class="px-3 py-1 border rounded">
        ←
      </button>

      <button @click="pagination.page++; load()"
        :disabled="pagination.page==pagination.total_pages"
        class="px-3 py-1 border rounded">
        →
      </button>
    </div>

  </div>

</div>

<script>
  function pesananPage() {
    return {
      data: [],
      search: utils.getQuery('search') || '',
      metode: utils.getQuery('metode') || '',

      pagination: {
        page: utils.getQuery('page') || 1,
        limit: utils.getQuery('limit') || 10,
        total: 0,
        total_pages: 1
      },

      async init() {
        await this.load()
      },

      async load() {
        try {
          const res = await API.get(
            '/transaksi/list?' +
            'status=diproses,pending' +
            '&search=' + this.search +
            '&metode=' + this.metode +
            '&page=' + this.pagination.page +
            '&limit=' + this.pagination.limit
          )
          if (res.success) {
            this.data = res.data
            this.pagination.total = res.pagination.total
            this.pagination.total_pages = res.pagination.total_pages

            // sync URL
            utils.setQuery('search', this.search)
            utils.setQuery('metode', this.metode)
            utils.setQuery('page', this.pagination.page)
            utils.setQuery('limit', this.pagination.limit)
            console.log(this.data);
          }
        } catch (err) {
          console.error(err)
        }
      },

      // ACTION 
      async detail(kode) {
        window.location.href = `${BASE_URL}/admin/transaksi/${kode}`
      },

      async proses(kode) {
        const ok = await Alpine.store('ui').confirm('Proses pesanan ini?')
        if (!ok) return
        try {
          await API.post('/transaksi/proses', {
            kode_transaksi: kode
          })
          Alpine.store('ui').toast('Pesanan diproses')
          this.load()
        } catch (err) {
          console.error(err)
        }
      },

      async konfirmasi(kode) {
        const ok = await Alpine.store('ui').confirm('Konfirmasi pesanan ini?')
        if (!ok) return

        try {
          await API.post('/transaksi/konfirmasi', {
            kode_transaksi: kode
          })

          Alpine.store('ui').toast('Pesanan dikonfirmasi')
          this.load()

        } catch (err) {
          console.error(err)
        }
      },

      async batal(kode) {
        const ok = await Alpine.store('ui').confirm('Batalkan pesanan ini?')
        if (!ok) return

        try {
          await API.post('/transaksi/batal', {
            kode_transaksi: kode
          })
          Alpine.store('ui').toast('Pesanan dibatalkan')
          this.load()
        } catch (err) {
          console.error(err)
        }
      }

    }
  }
</script>