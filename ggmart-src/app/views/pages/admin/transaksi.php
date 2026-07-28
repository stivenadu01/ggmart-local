<div class="space-y-4 p-4" x-data="riwayatPage()">

  <!-- HEADER -->
  <div class="flex-between flex-col md:flex-row gap-4">
    <div>
      <h1>Riwayat Transaksi</h1>
      <p class="text-gray-500 text-sm">Data transaksi yang telah selesai</p>
    </div>
  </div>

  <!-- FILTER -->
  <div class="flex flex-col md:flex-row gap-2 flex-wrap">

    <!-- SEARCH -->
    <div>
      <label class="label">Cari</label>
      <input
        type="text"
        x-model="search"
        @input.debounce.500="reload()"
        placeholder="Kode / produk..."
        class="input min-w-64">
    </div>

    <!-- STATUS -->
    <div>
      <label class="label">Status</label>
      <select x-model="status" @change="reload()" class="input">
        <option value="selesai,dibatalkan">Riwayat</option>
        <option value="selesai">Selesai</option>
        <option value="dibatalkan">Dibatalkan</option>
      </select>
    </div>

    <!-- METODE -->
    <div>
      <label class="label">Metode</label>
      <select x-model="metode" @change="reload()" class="input">
        <option value="">Semua</option>
        <option value="tunai">Tunai</option>
        <option value="qris">QRIS</option>
      </select>
    </div>

    <!-- TANGGAL -->
    <div>
      <label class="label">Dari</label>
      <input type="date" x-model="start" @change="reload()" class="input">
    </div>

    <div>
      <label class="label">Sampai</label>
      <input type="date" x-model="end" @change="reload()" class="input">
    </div>

  </div>

  <!-- SUMMARY -->
  <div class="grid md:grid-cols-3 gap-3" x-show="status!='dibatalkan'">

    <div class="bg-white p-4 rounded-xl shadow border">
      <div class="text-sm text-gray-500">Omzet</div>
      <div class="text-xl font-bold text-primary"
        x-text="utils.formatRupiah(summary.jual || 0)">
      </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow border">
      <div class="text-sm text-gray-500">Modal</div>
      <div class="text-xl font-bold"
        x-text="utils.formatRupiah(summary.pokok || 0)">
      </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow border">
      <div class="text-sm text-gray-500">Laba</div>
      <div class="text-xl font-bold text-green-600"
        x-text="utils.formatRupiah(summary.laba || 0)">
      </div>
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
          <th>Pelanggan</th>
          <th>Metode</th>
          <th>Status</th>
          <th>Total</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>

      <tbody>

        <!-- EMPTY -->
        <template x-if="data.length === 0">
          <tr>
            <td colspan="8" class="text-center py-8 text-gray-500">
              Tidak ada data
            </td>
          </tr>
        </template>

        <!-- DATA -->
        <template x-for="(item, idx) in data" :key="item.kode_transaksi">
          <tr class="hover:bg-gray-50 transition">

            <td x-text="(pagination.page - 1) * pagination.limit + idx + 1"></td>

            <td class="font-medium" x-text="item.kode_transaksi"></td>

            <td class="text-sm text-gray-500"
              x-text="utils.formatDateTime(item.tanggal_transaksi)">
            </td>

            <td x-text="item.user || 'Offline'"></td>
            <td class="uppercase text-sm" x-text="item.metode_bayar"></td>

            <td>
              <span
                :class="item.status === 'selesai'
                  ? 'text-green-600'
                  : item.status === 'dibatalkan'
                  ? 'text-red-600'
                  : 'text-yellow-600'"
                class="font-medium"
                x-text="item.status">
              </span>
            </td>

            <td class="font-semibold"
              x-text="utils.formatRupiah(item.total_harga)">
            </td>

            <td class="text-right">
              <button
                @click="detail(item.kode_transaksi)"
                class="text-primary hover:underline text-sm">
                Detail
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
      <select x-model.number="pagination.limit" @change="reload()" class="input w-auto">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
      dari <span x-text="pagination.total"></span> data
    </div>

    <div class="flex gap-2 flex-wrap">

      <button
        @click="pagination.page--; load()"
        :disabled="pagination.page == 1"
        class="px-3 py-1 border rounded-lg">
        ←
      </button>

      <template x-for="page in pagination.total_pages" :key="page">
        <button
          @click="pagination.page = page; load()"
          :class="pagination.page == page ? 'bg-primary text-white' : 'border'"
          class="w-10 h-10 rounded-lg">
          <span x-text="page"></span>
        </button>
      </template>

      <button
        @click="pagination.page++; load()"
        :disabled="pagination.page == pagination.total_pages"
        class="px-3 py-1 border rounded-lg">
        →
      </button>

    </div>

  </div>

</div>

<script>
  function riwayatPage() {
    return {
      data: [],
      summary: {},

      search: utils.getQuery('search') ?? '',
      status: utils.getQuery('status') ?? 'selesai,dibatalkan',
      metode: utils.getQuery('metode') ?? '',
      start: utils.getQuery('start') ?? '',
      end: utils.getQuery('end') ?? '',

      pagination: {
        page: utils.getQuery('page') ?? 1,
        limit: utils.getQuery('limit') ?? 10,
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
            'search=' + encodeURIComponent(this.search) +
            '&status=' + this.status +
            '&metode=' + this.metode +
            '&start=' + this.start +
            '&end=' + this.end +
            '&limit=' + this.pagination.limit +
            '&page=' + this.pagination.page
          )

          if (res.success) {
            this.data = res.data || []
            this.summary = res.totalSummary || {}

            this.pagination.total = res.pagination.total || 0
            this.pagination.total_pages = Math.max(
              1,
              Math.ceil(this.pagination.total / this.pagination.limit)
            )

            utils.setQuery('search', this.search)
            utils.setQuery('status', this.status)
            utils.setQuery('metode', this.metode)
            utils.setQuery('start', this.start)
            utils.setQuery('end', this.end)
            utils.setQuery('page', this.pagination.page)
            utils.setQuery('limit', this.pagination.limit)
          } else {
            this.data = []
          }

        } catch (err) {
          console.error(err)
        }
      },

      reload() {
        this.pagination.page = 1
        this.load()
      },

      detail(kode) {
        window.location.href = BASE_URL + '/admin/transaksi/' + kode
      }
    }
  }
</script>