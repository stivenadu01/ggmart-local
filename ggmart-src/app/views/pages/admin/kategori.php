<div class="space-y-6 p-6" x-data="kategoriPage()">

  <!-- HEADER -->
  <div class="flex-between flex-col md:flex-row gap-4">
    <div>
      <h1>Kelola Kategori</h1>
      <p class="text-gray-500 text-sm">Atur kategori produk</p>
    </div>
    <button @click="showModal = true" class="btn-primary w-full md:w-auto">+ Tambah Kategori</button>
  </div>

  <!-- FILTERS & SEARCH -->
  <div class="flex flex-col">
    <label class="label">Cari Kategori</label>
    <input
      type="text"
      x-model="search"
      @input.debounce.750="load()"
      placeholder="Cari kategori..."
      class="input w-72">
  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto max-h-[80dvh] overflow-y-auto">
    <table class="table">
      <thead>
        <tr class="bg-gray-50">
          <th class="w-12">No</th>
          <th>Nama Kategori</th>
          <th>Deskripsi</th>
          <th class="w-24 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <template x-if="kategori.length === 0">
          <tr>
            <td colspan="4" class="text-center py-8 text-gray-500">Tidak ada data</td>
          </tr>
        </template>

        <template x-for="(item, idx) in kategori" :key="item.id_kategori">
          <tr class="hover:bg-gray-50 transition">
            <td x-text="(pagination.page - 1) * pagination.limit + idx + 1"></td>
            <td class="font-medium" x-text="item.nama_kategori"></td>
            <td class="text-gray-600 truncate max-w-96" x-text="item.deskripsi || '-'"></td>
            <td class="text-right flex-center justify-end gap-2">
              <button @click="edit(item)" class="text-blue-600 hover:text-blue-800 transition text-sm font-medium">Edit</button>
              <button @click="hapus(item.id_kategori)" class="text-red-600 hover:text-red-800 transition text-sm font-medium">Hapus</button>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>

  <!-- PAGINATION -->
  <div class="flex-between flex-col md:flex-row gap-4 text-sm text-gray-600">
    <div>
      Menampilkan <select x-model.number="pagination.limit" @change="pagination.page = 1; load()" class="input w-auto">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select> dari <span x-text="pagination.total"></span> Kategori
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

  <!-- MODAL -->
  <template x-if="showModal">
    <div class="fixed inset-0 bg-black/50 flex-center z-50" @click.self="closeModal()">
      <div class="bg-white rounded-2xl p-6 w-full max-w-xl shadow-lg" @click.stop>
        <h2 class="mb-4" x-text="editingId ? 'Edit Kategori' : 'Tambah Kategori'"></h2>

        <form @submit.prevent="save" class="space-y-4">
          <div>
            <label class="label">Nama Kategori</label>
            <input
              type="text"
              x-model="form.nama_kategori"
              class="input"
              required>
          </div>

          <div>
            <label class="label">Deskripsi</label>
            <textarea
              x-model="form.deskripsi"
              class="input"
              rows="3"></textarea>
          </div>

          <div class="flex gap-3">
            <button type="submit" class="btn-primary flex-1">
              <span x-text="editingId ? 'Simpan Perubahan' : 'Tambah'"></span>
            </button>
            <button type="button" @click="closeModal()" class="btn-secondary flex-1">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </template>

</div>

<script>
  function kategoriPage() {
    return {
      kategori: [],
      search: '',
      pagination: {
        page: 1,
        total: 0,
        total_pages: 1,
        limit: 10
      },
      showModal: false,
      editingId: null,
      form: {
        nama_kategori: '',
        deskripsi: ''
      },

      async init() {
        await this.load()
      },

      async load() {
        try {
          const res = await API.get('/kategori/list?search=' + encodeURIComponent(this.search) + '&limit=' + this.pagination.limit + '&page=' + this.pagination.page)
          if (res.success) {
            this.kategori = res.data || []
            this.pagination.total = res.pagination.total || 0
            this.pagination.total_pages = Math.ceil(this.pagination.total / this.pagination.limit)
          } else {
            this.kategori = []
          }
        } catch (err) {
          console.error(err)
        }
      },

      edit(item) {
        this.editingId = item.id_kategori
        this.form = {
          nama_kategori: item.nama_kategori,
          deskripsi: item.deskripsi
        }
        this.showModal = true
      },

      closeModal() {
        this.showModal = false
        this.editingId = null
        this.form = {
          nama_kategori: '',
          deskripsi: ''
        }
      },

      async save() {
        try {
          if (this.editingId) {
            await API.put('/kategori?id=' + this.editingId, {
              ...this.form
            })
            Alpine.store('ui').toast('Kategori berhasil diperbarui')
          } else {
            await API.post('/kategori', this.form)
            Alpine.store('ui').toast('Kategori berhasil ditambahkan')
          }
          this.closeModal()
          await this.load()
        } catch (err) {
          console.error(err)
        }
      },

      async hapus(id) {
        const ok = await Alpine.store('ui').confirm('Yakin ingin menghapus kategori ini?')
        if (ok) {
          try {
            await API.delete('/kategori?id=' + id)
            Alpine.store('ui').toast('Kategori berhasil dihapus')
            await this.load()
          } catch (err) {
            console.error(err)
          }
        }
      }
    }
  }
</script>