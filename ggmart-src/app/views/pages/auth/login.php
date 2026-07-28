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
          Masuk ke akun GGmart Anda
        </h2>

        <p class="mt-2 text-sm text-center text-slate-600">
          Masukkan email dan kata sandi untuk melanjutkan berbelanja.
        </p>

        <form x-data="loginForm()" @submit.prevent="submit" class="mt-8 space-y-5">

          <!-- EMAIL -->
          <div class="form-group">
            <label class="label">Email</label>
            <input type="email" x-model="email" placeholder="email@contoh.com"
              class="input py-3 px-4" />
          </div>

          <!-- PASSWORD -->
          <div class="form-group">
            <label class="label">Kata Sandi</label>
            <input type="password" x-model="password" placeholder="Masukkan kata sandi"
              class="input py-3 px-4" />
          </div>

          <!-- FORGOT -->
          <div class="flex-between text-sm">
            <button type="button" @click="forgotModal = true"
              class="text-primary-dark hover:underline">
              Lupa kata sandi??
            </button>
          </div>

          <!-- MODAL -->
          <div x-show="forgotModal" x-cloak class="modal-backdrop">

            <div @click.away="forgotModal = false" class="modal-box">

              <h3 class="text-lg font-medium">Reset Kata Sandi</h3>

              <p class="text-sm text-slate-600 mt-1">
                Masukkan email Anda untuk menerima instruksi reset.
              </p>

              <div class="mt-4 form-group">
                <label class="label">Email</label>
                <input type="email" x-model="forgotEmail"
                  placeholder="email@contoh.com"
                  class="input py-3 px-4" />
              </div>

              <div class="mt-5 flex-end gap-2">
                <button type="button" @click="forgotModal = false"
                  class="btn-secondary px-4 py-2">
                  Batal
                </button>

                <button type="button" @click="submitForgot"
                  class="btn-primary px-4 py-2">
                  <span>Kirim</span>
                </button>
              </div>

            </div>
          </div>

          <!-- SUBMIT -->
          <button type="submit" class="btn-primary py-3" :disabled="(!email || !password)">
            Masuk
          </button>

        </form>

        <!-- REGISTER -->
        <div class="mt-6 text-center text-sm text-slate-600">
          Belum punya akun?
          <a :href="BASE_URL + '/register'" class="text-primary-dark font-semibold hover:underline">
            Daftar sekarang
          </a>
        </div>

      </div>
    </div>

  </div>
</div>


<script>
  function loginForm() {
    return {
      email: '',
      password: '',
      forgotModal: false,
      forgotEmail: '',

      async submitForgot() {
        try {
          const res = await API.post('/auth/request-reset', {
            email: this.forgotEmail,
          });
          if (res.success) {
            this.forgotModal = false;
          }
        } catch (error) {
          console.log(error);
        }
      },

      async submit() {
        try {
          if (!this.email || !this.password) return;
          const res = await API.post('/auth/login', {
            email: this.email,
            password: this.password,
          });
          if (res.success) {
            setTimeout(() => {
              window.location.href = BASE_URL;
            }, 1500);
          }
        } catch (error) {
          console.log(error);
        }
      }
    }
  }
</script>