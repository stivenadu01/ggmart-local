<div class="section-center">
  <div class="w-full max-w-md p-6">

    <h2 class="text-2xl font-semibold text-center mb-2">
      Reset Kata Sandi
    </h2>

    <p class="text-sm text-center text-slate-600 mb-6">
      Masukkan kata sandi baru untuk akun Anda.
    </p>

    <form x-data="resetForm()" @submit.prevent="submit" class="space-y-4">

      <!-- PASSWORD -->
      <div class="form-group">
        <label class="label">Kata Sandi Baru</label>
        <input
          type="password"
          x-model="password"
          placeholder="Masukkan kata sandi baru"
          class="input py-3 px-4"
          required />
      </div>

      <!-- CONFIRM -->
      <div class="form-group">
        <label class="label">Ulangi Kata Sandi</label>
        <input
          type="password"
          x-model="confirmPassword"
          placeholder="Ulangi kata sandi"
          class="input py-3 px-4"
          required />
      </div>

      <!-- SUBMIT -->
      <div class="flex-end">
        <button type="submit" class="btn-primary px-4 py-2">
          Reset Kata Sandi
        </button>
      </div>

    </form>

  </div>
</div>

<script>
  function resetForm() {
    return {
      password: '',
      confirmPassword: '',
      async submit() {
        const params = new URLSearchParams(window.location.search);
        const token = params.get('token');

        if (this.password !== this.confirmPassword) {
          Alpine.store('ui').toast('Kata sandi tidak cocok', 'error');
          return;
        }

        const res = await API.post('/auth/reset-password', {
          token,
          password: this.password
        });
        if (res.success) {
          setTimeout(() => {
            window.location.href = BASE_URL + '/login';
          }, 3000);
        }
      }
    }
  }
</script>