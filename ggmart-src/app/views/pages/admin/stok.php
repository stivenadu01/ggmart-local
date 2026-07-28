<div class="space-y-4 p-4" x-data="stokPage()">

  <!-- HEADER -->
  <div class="flex-between flex-col md:flex-row gap-4">
    <div>
      <h1>Kelola Stok</h1>
      <p class="text-gray-500 text-sm">Riwayat perubahan stok produk</p>
    </div>

    <a href="<?= BASE_URL ?>/admin/stok/form" class="btn-primary w-full md:w-auto">
      + Tambah Stok
    </a>
  </div>

  <!-- FILTER -->
  <div class="flex flex-col md:flex-row gap-2">

    <div class="flex flex-col">
      <label class="label">Cari Produk</label>
      <input
        type="text"
        x-model="search"
        @input.debounce.500="pagination.page = 1; load()"
        placeholder="Cari produk..."
        class="input min-w-64">
    </div>

    <div>
      <label class="label">Tipe</label>
      <select x-model="type" @change="pagination.page = 1; load()" class="input">
        <option value="">Semua</option>
        <option value="masuk">Stok Masuk</option>
        <option value="keluar">Stok Keluar</option>
      </select>
    </div>

  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto max-h-[80dvh] overflow-y-auto">
    <table class="table">
      <thead>
        <tr class="bg-gray-50">
          <th>No</th>
          <th>Tanggal</th>
          <th>Produk</th>
          <th>Jenis</th>
          <th>Jumlah</th>
          <th>Keterangan</th>
          <th class="text-right">Aksi</th>
        </tr>
      </thead>

      <tbody>

        <!-- EMPTY -->
        <template x-if="mutasi.length === 0">
          <tr>
            <td colspan="7" class="text-center py-8 text-gray-500">
              Tidak ada data
            </td>
          </tr>
        </template>

        <!-- DATA -->
        <template x-for="(item, idx) in mutasi" :key="item.id_mutasi">
          <tr class="hover:bg-gray-50 transition">
            <td x-text="(pagination.page - 1) * pagination.limit + idx + 1"></td>
            <td class="text-sm text-gray-500" x-text="utils.formatDateTime(item.tanggal)"></td>
            <td>
              <div class="font-medium" x-text="item.nama_produk"></div>
              <div class="text-xs text-gray-500" x-text="item.kode_produk"></div>
            </td>
            <td>
              <span
                :class="item.type === 'masuk'
                  ? 'text-green-600'
                  : 'text-red-600'"
                class="font-medium"
                x-text="item.type === 'masuk' ? 'Masuk' : 'Keluar'">
              </span>
            </td>
            <td class="font-semibold" x-text="`${item.jumlah} ${item.satuan_dasar}`"></td>
            <td class="text-sm text-gray-600" x-text="item.keterangan || '-'"></td>
            <td class="text-right">
              <button
                @click="hapus(item.id_mutasi)"
                class="text-red-600 hover:text-red-800 text-sm">
                Hapus
              </button>
            </td>

          </tr>
        </template>

      </tbody>
    </table>
  </div>

  <!-- PAGINATION -->
  <div class="flex-between flex-col md:flex-row gap-4 text-sm text-gray-600 mb-5">

    <div>
      Menampilkan
      <select x-model.number="pagination.limit" @change="pagination.page = 1; load()" class="input w-auto">
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
        class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50">
        ← Prev
      </button>

      <template x-for="page in pagination.total_pages" :key="page">
        <button
          @click="pagination.page = page; load()"
          :class="pagination.page == page ? 'bg-primary text-white' : 'border hover:bg-gray-100'"
          class="w-10 h-10 rounded-lg">
          <span x-text="page"></span>
        </button>
      </template>

      <button
        @click="pagination.page++; load()"
        :disabled="pagination.page == pagination.total_pages"
        class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50">
        Next →
      </button>

    </div>

  </div>

</div>

<script>
  function stokPage() {
    return {
      mutasi: [],
      search: utils.getQuery('search') || '',
      type: utils.getQuery('type') || '',

      pagination: {
        page: utils.getQuery('page') || 1,
        total: 0,
        total_pages: 1,
        limit: utils.getQuery('limit') || 10
      },

      async init() {
        await this.load()
      },

      async load() {
        try {
          const res = await API.get(
            '/mutasi/list?search=' + encodeURIComponent(this.search) +
            '&type=' + this.type +
            '&limit=' + this.pagination.limit +
            '&page=' + this.pagination.page
          )

          if (res.success) {
            this.mutasi = res.data || []
            this.pagination.total = res.pagination.total || 0
            this.pagination.total_pages = Math.max(
              1,
              Math.ceil(this.pagination.total / this.pagination.limit)
            )

            // sync URL
            utils.setQuery('search', this.search)
            utils.setQuery('type', this.type)
            utils.setQuery('page', this.pagination.page)
            utils.setQuery('limit', this.pagination.limit)

          } else {
            this.mutasi = []
            this.pagination.total = 0
            this.pagination.total_pages = 1
          }

        } catch (err) {
          console.error(err)
        }
      },

      async hapus(id) {
        const ok = await Alpine.store('ui').confirm('Yakin ingin menghapus data ini?')
        if (!ok) return

        try {
          await API.delete('/mutasi', {
            id
          })
          Alpine.store('ui').toast('Data berhasil dihapus')
          await this.load()
        } catch (err) {
          console.error(err)
        }
      }
    }
  }
</script>