<div class="section-center">
  <div class="w-full max-w-6xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">

    <!-- LEFT -->
    <div class="hidden md:flex flex-center flex-col gap-6 p-10">
      <img :src="BASE_URL + '/assets/logo.png'" alt="Logo" class="w-20 md:w-34">
      <p class="text-2xl font-semibold leading-relaxed max-w-md">
        Gerakan Beli Produk Lokal
      </p>
    </div>

    <!-- RIGHT -->
    <div class="p-10 lg:p-14">
      <div class="max-w-md mx-auto">

        <h2 class="text-2xl font-semibold text-slate-900 text-center">
          Buat Akun GGmart Anda
        </h2>

        <p class="mt-2 text-sm text-center text-slate-600">
          Daftarkan akun untuk mulai berbelanja produk lokal terbaik.
        </p>

        <form x-data="registerForm()" @submit.prevent="submit" class="mt-8 space-y-5">

          <!-- NAMA -->
          <div class="form-group">
            <label class="label">Nama Lengkap</label>
            <input type="text" x-model="nama"
              placeholder="Masukkan nama lengkap"
              class="input py-3 px-4" />
          </div>

          <!-- EMAIL -->
          <div class="form-group">
            <label class="label">Email</label>
            <input type="email" x-model="email"
              placeholder="email@contoh.com"
              class="input py-3 px-4" />
          </div>

          <!-- PASSWORD -->
          <div class="form-group">
            <label class="label">Kata Sandi</label>
            <input type="password" x-model="password"
              placeholder="Masukkan kata sandi"
              class="input py-3 px-4" />
          </div>

          <!-- NO HP -->
          <div class="form-group">
            <label class="label">Nomor HP</label>
            <input type="text" x-model="no_hp"
              placeholder="0812xxxx"
              class="input py-3 px-4" />
          </div>

          <!-- CONFIRM PASSWORD -->
          <div class="form-group">
            <label class="label">Konfirmasi Kata Sandi</label>
            <input type="password" x-model="password_confirm"
              placeholder="Konfirmasi kata sandi"
              class="input py-3 px-4" />
          </div>

          <!-- SUBMIT -->
          <button type="submit" class="btn-primary py-3">
            Daftar
          </button>

        </form>

        <!-- LOGIN LINK -->
        <div class="mt-6 text-center text-sm text-slate-600">
          Sudah punya akun?
          <a :href="BASE_URL + '/login'" class="text-primary-dark font-semibold hover:underline">
            Masuk di sini
          </a>
        </div>

      </div>
    </div>

  </div>
</div>


<script>
  function registerForm() {
    return {
      nama: '',
      email: '',
      password: '',
      password_confirm: '',
      no_hp: '',
      async submit() {
        if (!this.nama || !this.email || !this.password || !this.password_confirm || !this.no_hp) {
          Alpine.store('ui').toast('Semua field harus diisi', 'error');
          return;
        }

        if (this.password !== this.password_confirm) {
          Alpine.store('ui').toast('Kata sandi tidak cocok', 'error');
          return;
        }

        try {
          const res = await API.post('/auth/register', {
            nama: this.nama,
            email: this.email,
            password: this.password,
            no_hp: this.no_hp,
          });
          if (res.success) {
            setTimeout(() => {
              window.location.href = BASE_URL + '/login';
            }, 5000);
          }
        } catch (error) {
          console.log(error);
        }
      }
    }
  }
</script>