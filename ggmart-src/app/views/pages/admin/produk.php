<div class="space-y-4 p-4" x-data="produkPage()">

  <!-- HEADER -->
  <div class="flex-between flex-col md:flex-row gap-4">
    <div>
      <h1>Kelola Produk</h1>
      <p class="text-gray-500 text-sm">Atur produk yang tersedia</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/produk/form" class="btn-primary w-full md:w-auto">+ Tambah Produk</a>
  </div>

  <!-- FILTERS & SEARCH -->
  <div class="flex flex-col md:flex-row gap-2">
    <div class="flex flex-col">
      <label class="label">Cari Produk</label>
      <input
        type="text"
        x-model="search"
        @input.debounce.750="pagination.page = 1; load()"
        placeholder="Cari produk..."
        class="input w-auto min-w-72">
    </div>

    <div>
      <label class="label">Urutan</label>
      <select x-model="order_by" @change="pagination.page = 1; load()" class="input w-full">
        <template x-for="opt in orderOptions" :key="opt.value">
          <option :value="opt.value" x-text="opt.label"></option>
        </template>
      </select>
    </div>

    <div>
      <label class="label">Arah</label>
      <select x-model="order_dir" @change="pagination.page = 1; load()" class="input w-full">
        <option value="DESC">Menurun</option>
        <option value="ASC">Menaik</option>
      </select>
    </div>
  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto max-h-[80dvh] overflow-y-auto">
    <table class="table">
      <thead>
        <tr class="bg-gray-50">
          <th class="w-8">No</th>
          <th class="min-w-64">Produk</th>
          <th class="min-w-40">Detail</th>
          <th>Deskripsi</th>
          <th class="w-28 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <template x-if="produk.length === 0">
          <tr>
            <td colspan="5" class="text-center py-8 text-gray-500">Tidak ada data</td>
          </tr>
        </template>

        <template x-for="(item, idx) in produk" :key="item.kode_produk">
          <tr class="hover:bg-gray-50 transition">
            <td x-text="(pagination.page - 1) * pagination.limit + idx + 1"></td>
            <td>
              <div class="flex items-center gap-3">
                <div class="w-16 h-16 rounded-lg overflow-hidden flex-center">
                  <template x-if="item.gambar">
                    <img :src="`${BASE_URL}/uploads/${item.gambar}`" alt="" class="w-full h-full object-cover">
                  </template>
                  <template x-if="!item.gambar">
                    <span class="text-xs text-gray-500">No image</span>
                  </template>
                </div>
                <div class="min-w-0">
                  <div class="font-medium line-clamp-2" x-text="item.nama_produk"></div>
                  <div class="text-xs text-gray-500 line-clamp-2" x-text="item.kode_produk"></div>
                  <div class="text-xs text-gray-500" x-text="item.nama_kategori || '-'"></div>
                </div>
              </div>
            </td>
            <td>
              <div class="text-xs text-gray-700">
                <div class="text-sm"><span class="font-medium">Harga:</span><span x-text="' '+Alpine.store('utils').formatRupiah(item.harga_jual)"></span></div>
                <div class="mt-1"><span class="font-medium">Stok:</span> <span x-text="item.stok"></span> · <span class="font-medium">Terjual:</span> <span x-text="item.terjual"></span></div>
                <div class="mt-1"><span class="font-medium">Asal Produk:</span> <span x-text="item.asal_produk || '-'"></span></div>
              </div>
            </td>
            <td class="text-sm text-gray-700">
              <span x-text="item.deskripsi" class="line-clamp-4"></span>
            </td>
            <td class="flex items-end gap-2">
              <a :href="BASE_URL + '/admin/produk/form?k=' + item.kode_produk" class="text-blue-600 hover:text-blue-800 transition text-sm font-medium">Edit</a>
              <button @click="hapus(item.kode_produk)" class="text-red-600 hover:text-red-800 transition text-sm font-medium">Hapus</button>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>

  <!-- PAGINATION -->
  <div class="flex-between flex-col md:flex-row gap-4 text-sm text-gray-600 mb-5">
    <div>
      Menampilkan <select x-model.number="pagination.limit" @change="pagination.page = 1; load()" class="input w-auto">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select> dari <span x-text="pagination.total"></span> produk
    </div>
    <div class="flex gap-2 flex-wrap">
      <button
        @click="pagination.page-- ; load()"
        :disabled="pagination.page == 1"
        class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition">
        ← Prev
      </button>

      <template x-for="page in pagination.total_pages" :key="page">
        <button
          @click="pagination.page = page; load()"
          :class="pagination.page == page
            ? 'bg-primary text-white'
            : 'border hover:bg-gray-100'"
          class="w-10 h-10 rounded-lg transition">
          <span x-text="page"></span>
        </button>
      </template>

      <button
        @click="pagination.page++ ; load()"
        :disabled="pagination.page == pagination.total_pages"
        class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition">
        Next →
      </button>
    </div>
  </div>

</div>

<script>
  function produkPage() {
    return {
      produk: [],
      search: '',
      order_by: utils.getQuery('order_by'),
      order_dir: utils.getQuery('order_dir'),
      orderOptions: [{
          label: 'Terbaru',
          value: ''
        },
        {
          label: 'Nama Produk',
          value: 'nama_produk'
        },
        {
          label: 'Harga',
          value: 'harga_jual'
        },
        {
          label: 'Stok',
          value: 'stok'
        },
        {
          label: 'Terjual',
          value: 'terjual'
        }
      ],
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
          const res = await API.get('/produk/list?search=' + encodeURIComponent(this.search) + '&order_by=' + this.order_by + '&order_dir=' + this.order_dir + '&limit=' + this.pagination.limit + '&page=' + this.pagination.page)
          if (res.success) {
            this.produk = res.data || []
            this.pagination.total = res.pagination.total || 0
            this.pagination.total_pages = Math.max(1, Math.ceil(this.pagination.total / this.pagination.limit))
            utils.setQuery('search', this.search)
            utils.setQuery('order_by', this.order_by)
            utils.setQuery('order_dir', this.order_dir)
            utils.setQuery('page', this.pagination.page)
            utils.setQuery('limit', this.pagination.limit)
          } else {
            this.produk = []
            this.pagination.total = 0
            this.pagination.total_pages = 1
          }
        } catch (err) {
          console.error(err)
        }
      },

      imageUrl(path) {
        if (!path) return ''
        if (path.startsWith('http')) return path
        return BASE_URL + path
      },

      async hapus(kode) {
        const ok = await Alpine.store('ui').confirm('Yakin ingin menghapus produk ini?')
        if (!ok) return

        try {
          await API.delete('/produk', {
            kode_produk: kode
          })
          Alpine.store('ui').toast('Produk berhasil dihapus')
          await this.load()
        } catch (err) {
          console.error(err)
        }
      }
    }
  }
</script>