<div class="space-y-6 p-6" x-data="userPage()">

  <!-- HEADER -->
  <div class="flex-between flex-col md:flex-row gap-4">
    <div>
      <h1>Kelola User</h1>
      <p class="text-gray-500 text-sm">Atur semua pengguna ggmart di sini</p>
    </div>
    <button @click="showModal = true" class="btn-primary w-full md:w-auto">+ Tambah User</button>
  </div>

  <!-- FILTERS & SEARCH -->
  <div class="flex gap-2">
    <div class="flex flex-col">
      <label class="label">Cari User</label>
      <input
        type="text"
        x-model="search"
        @input.debounce.750="load()"
        placeholder="Cari user..."
        class="input w-72">
    </div>
    <div>
      <label class="label" @change="load()">Role</label>
      <select x-model="role" @change="pagination.page = 1; load()" class="input w-full">
        <option value="">Semua</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="pimpinan">Pimpinan</option>
      </select>
    </div>
  </div>

  <!-- TABLE -->
  <div class="overflow-x-auto max-h-[80dvh] overflow-y-auto">
    <table class="table">
      <thead>
        <tr class="bg-gray-50">
          <th class="w-12">No</th>
          <th>Nama</th>
          <th>Kontak</th>
          <th>Alamat</th>
          <th>Role</th>
          <th>Verifikasi</th>
          <th class="w-24 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <template x-if="user.length === 0">
          <tr>
            <td colspan="4" class="text-center py-8 text-gray-500">Tidak ada data</td>
          </tr>
        </template>

        <template x-for="(user, idx) in user" :key="user.id_user">
          <tr class="hover:bg-gray-50 transition">
            <td x-text="(pagination.page - 1) * pagination.limit + idx + 1"></td>
            <td class="font-medium" x-text="user.nama"></td>
            <td class="max-w-96">
              <div class="text-gray-600 truncate" x-text="`Email: ${user.email}`"></div>
              <div class="text-gray-600 truncate" x-text="`Nomor Hp: ${user.no_hp}`"></div>
            </td>
            <td class="truncate line-clamp-3" x-text="user.alamat || '-'"></td>
            <td>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-medium" :class="user.role == 'user' ? 'px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200' : 'bg-red-100 text-red-800 border border-red-200'" x-text="user.role"></span>
            </td>
            <td>
              <span class="text-sm rounded-full px-2 py-1" :class="user.is_verified ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'" x-text="user.is_verified ? 'Sudah Verifikasi' : 'Belum Verifikasi'"></span>
            </td>
            <td class="flex-center gap-2">
              <button @click="edit(user)" class="text-blue-600 hover:text-blue-800 transition text-sm font-medium">Edit</button>
              <button @click="hapus(user.id_user)" class="text-red-600 hover:text-red-800 transition text-sm font-medium">Hapus</button>
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
      </select> dari <span x-text="pagination.total"></span> user
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
        <div class="flex-between">
          <h2 class="mb-4" x-text="editingId ? 'Edit user' : 'Tambah user'"></h2>
          <div class="inline-flex items-center">
            <button
              x-show="!form.is_verified"
              @click="verifikasiAkun(editingId)"
              class="inline-flex items-center gap-1.5 text-gray-600 hover:text-primary hover:underline text-sm font-medium transition">
              <svg class="h-6 w-6" viewBox="0 0 48 48" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 2S6 7.1 6 8v18.2c0 9.2 13.3 17.3 17 19.5a1.8 1.8 0 0 0 2 0c3.8-2.1 17-10.3 17-19.5V8c0-.9-18-6-18-6m9.4 16.4-11 11a1.9 1.9 0 0 1-2.8 0l-4.9-4.9a2.2 2.2 0 0 1-.4-2.7 2 2 0 0 1 3.1-.2l3.6 3.6 9.6-9.6a2 2 0 0 1 2.8 2.8" />
              </svg>
              Verifikasi akun ini
            </button>
            <span
              x-show="form.is_verified"
              class="inline-flex items-center gap-1.5 text-emerald-600 text-sm font-semibold ">
              <!-- SVG Tameng Centang Berwarna Hijau -->
              <svg class="h-6 w-6" viewBox="0 0 48 48" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 2S6 7.1 6 8v18.2c0 9.2 13.3 17.3 17 19.5a1.8 1.8 0 0 0 2 0c3.8-2.1 17-10.3 17-19.5V8c0-.9-18-6-18-6m9.4 16.4-11 11a1.9 1.9 0 0 1-2.8 0l-4.9-4.9a2.2 2.2 0 0 1-.4-2.7 2 2 0 0 1 3.1-.2l3.6 3.6 9.6-9.6a2 2 0 0 1 2.8 2.8" />
              </svg>
              <span>Akun Terverifikasi</span>
          </div>
        </div>

        <form @submit.prevent="save" class="space-y-4">
          <div>
            <label class="label">Nama user</label>
            <input
              type="text"
              x-model="form.nama"
              class="input"
              required>
          </div>
          <div>
            <label class="label">email</label>
            <input
              x-model="form.email"
              class="input"
              type="text"></input>
          </div>
          <div>
            <label class="label">Nomor Hp</label>
            <input
              x-model="form.no_hp"
              class="input"
              type="text"></input>
          </div>
          <div>
            <label class="label">Role</label>
            <select x-model="form.role" class="input w-full">
              <option value="user">user</option>
              <option value="admin">admin</option>
            </select>
          </div>
          <div>
            <label class="label">Alamat</label>
            <textarea
              x-model="form.alamat"
              class="input"
              rows="3"></textarea>
          </div>
          <div>
            <label class="label">Password</label>
            <input
              x-model="form.password"
              class="input"
              type="text" placeholder="Biarkan Jika tidak ingin mengubah password"></input>
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
  function userPage() {
    return {
      user: [],
      search: utils.getQuery('search') ?? '',
      role: utils.getQuery('role') ?? '',
      pagination: {
        page: utils.getQuery('page') || 1,
        total: 0,
        total_pages: 1,
        limit: utils.getQuery('limit') || 10
      },
      showModal: false,
      editingId: null,
      form: {
        nama: '',
        email: '',
        no_hp: '',
        alamat: '',
        password: '',
        role: '',
        is_verified: false
      },

      async init() {
        await this.load()
      },

      async load() {
        try {
          const res = await API.get('/user/list?search=' + encodeURIComponent(this.search) + '&role=' + this.role + '&limit=' + this.pagination.limit + '&page=' + this.pagination.page)
          if (res.success) {
            this.user = res.data || []
            this.pagination.total = res.pagination.total || 0
            this.pagination.total_pages = Math.ceil(this.pagination.total / this.pagination.limit)
            utils.setQuery('search', this.search)
            utils.setQuery('role', this.role)
            utils.setQuery('page', this.pagination.page)
            utils.setQuery('limit', this.pagination.limit)
          } else {
            this.user = []
          }
        } catch (err) {
          console.error(err)
        }
      },

      edit(user) {
        this.editingId = user.id_user
        this.form = {
          nama: user.nama,
          email: user.email,
          no_hp: user.no_hp,
          alamat: user.alamat,
          role: user.role,
          is_verified: user.is_verified
        }
        this.showModal = true
      },

      closeModal() {
        this.showModal = false
        this.editingId = null
        this.form = {
          nama: '',
          email: '',
          no_hp: '',
          alamat: '',
          role: '',
          is_verified: ''
        }
      },

      async save() {
        try {
          if (this.editingId) {
            await API.put('/user', {
              ...this.form,
              id_user: this.editingId
            })
            Alpine.store('ui').toast('user berhasil diperbarui')
          } else {
            await API.post('/user', this.form)
            Alpine.store('ui').toast('user berhasil ditambahkan')
          }
          this.closeModal()
          await this.load()
        } catch (err) {
          console.error(err)
        }
      },

      async hapus(id_user) {
        const ok = await Alpine.store('ui').confirm('Yakin ingin menghapus user ini?')
        if (ok) {
          try {
            await API.delete('/user', {
              id_user
            })
            Alpine.store('ui').toast('user berhasil dihapus')
            await this.load()
          } catch (err) {
            console.error(err)
          }
        }
      },

      async verifikasiAkun(id) {
        const ok = await Alpine.store('ui').confirm('Yakin ingin mengubah akun ini menjadi terverifikasi?')
        if (ok) {
          const res = await API.put(`/user/${id}/verify`)
          this.form.is_verified = true
          Alpine.store('ui').toast('Akun berhasil diverifikasi!')
          await this.load();
          this.closeModal();
        }
      }
    }
  }
</script>