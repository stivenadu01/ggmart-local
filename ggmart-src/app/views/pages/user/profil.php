<div class="py-10 max-w-4xl mx-auto" x-data="profilPage()">

  <div class="px-d mb-6">
    <h1>Profil Saya</h1>
    <p class="text-gray-500 text-sm">Kelola informasi akun Anda</p>
  </div>

  <div class="card px-d py-6 space-y-6">
    <!-- PROFILE INFO -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
      <div class="flex flex-col items-center">
        <div class="w-28 h-28 rounded-full bg-gray-200 flex-center text-sm font-medium">
          <span x-text="$store.auth.user.email.charAt(0).toUpperCase()"></span>
        </div>
        <div class="mt-3 text-sm text-gray-500" x-text="user.role"></div>
      </div>

      <div class="md:col-span-2">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="label">Nama</label>
            <div class="text-sm font-medium" x-text="user.nama"></div>
          </div>
          <div>
            <label class="label">Email</label>
            <div class="text-sm" x-text="user.email"></div>
          </div>
          <div>
            <label class="label">Telepon</label>
            <div class="text-sm" x-text="user.no_hp || '-' "></div>
          </div>
          <div>
            <label class="label">Alamat</label>
            <div class="text-sm" x-text="user.alamat || '-' "></div>
          </div>
        </div>
      </div>
    </div>

    <!-- ACTIONS -->
    <div class="flex gap-3">
      <button @click="editing = true" class="btn-primary w-auto">Edit Profil</button>
      <button @click="logout()" class="btn-outline-danger w-auto">Logout</button>
    </div>

    <!-- EDIT FORM -->
    <template x-if="editing">
      <form @submit.prevent="save" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="label">Nama</label>
            <input type="text" x-model="form.nama" class="input" required />
          </div>
          <div>
            <label class="label">Email</label>
            <input type="email" x-model="form.email" class="input" required />
          </div>
        </div>

        <div>
          <label class="label">Telepon</label>
          <input type="text" x-model="form.no_hp" class="input" />
        </div>

        <div>
          <label class="label">Alamat</label>
          <textarea x-model="form.alamat" class="input" rows="3"></textarea>
        </div>

        <div class="flex gap-3">
          <button type="submit" class="btn-primary">Simpan</button>
          <button type="button" @click="cancel()" class="btn-secondary">Batal</button>
        </div>
      </form>
    </template>

  </div>

</div>

<script>
  function profilPage() {
    return {
      user: Alpine.store('auth').user || {},
      editing: false,
      form: {},

      init() {
        this.resetForm()
      },

      resetForm() {
        this.form = {
          ...this.user
        }
      },

      cancel() {
        this.editing = false
        this.resetForm()
      },

      async save() {
        try {
          await API.put('/user', this.form)
          // await Alpine.store('auth').refresh() // assume store has refresh
          this.user = Alpine.store('auth').user
          this.editing = false
          Alpine.store('auth').refresh()
          window.location.reload() // reload page to update session data
          Alpine.store('ui').toast('Profil berhasil disimpan')
        } catch (err) {
          console.error(err)
        }
      },

      logout() {
        API.post('/auth/logout').finally(() => {
          window.location.href = BASE_URL + '/'
        })
      }
    }
  }
</script>