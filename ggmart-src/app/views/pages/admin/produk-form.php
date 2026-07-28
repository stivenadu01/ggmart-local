<div x-data="produkFormPage()"
  @paste.window="handlePaste($event)"
  @dragover.window.prevent="dragging = true"
  @dragleave.window="dragging = false"
  @drop.window.prevent="handleDrop($event)"
  class="bg-gray-50 min-h-dvh p-4 lg:p-6 relative">
  <!-- GLOBAL DRAG OVERLAY -->
  <template x-if="dragging">
    <div class="fixed inset-0 z-[999] bg-green-500/10 backdrop-blur-sm flex items-center justify-center pointer-events-none">
      <div class="text-center border-2 border-dashed border-green-400 bg-white/80 px-10 py-8 rounded-2xl shadow-xl">

        <div class="text-2xl font-semibold text-green-600 mb-2">
          Lepaskan gambar di sini
        </div>

        <p class="text-gray-600">
          Drag & drop, klik pilih file, atau <b>Ctrl + V</b>
        </p>

        <p class="text-xs text-gray-400 mt-2">
          Format: JPG, PNG • Maks 2MB
        </p>

      </div>
    </div>
  </template>

  <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between border-b pb-4 border-gray-200">
      <h1 class="text-2xl font-bold text-gray-800" x-text="formTitle"></h1>
      <a href="#" @click="history.back();" class="text-sm text-gray-500 hover:text-primary">← Kembali</a>
    </div>

    <!-- STEP INDICATOR -->
    <div class="flex items-center gap-4 text-sm">
      <div :class="step === 1 ? 'text-primary font-semibold' : 'text-gray-400'">1. Data Produk</div>
      <div>→</div>
      <div :class="step === 2 ? 'text-primary font-semibold' : 'text-gray-400'">2. Gambar & Tagline</div>
    </div>

    <form @submit.prevent="submitForm" class="space-y-5">

      <!-- ================= STEP 1 ================= -->
      <template x-if="step === 1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

          <div class="space-y-2">
            <label class="label">Nama Produk</label>
            <input type="text" x-model="form.nama_produk" required class="input">
          </div>

          <div class="space-y-2">
            <label class="label">Kategori</label>
            <select x-model="form.id_kategori" required class="input">
              <option value="">Pilih kategori</option>
              <template x-for="kategori in kategoriOptions">
                <option :value="kategori.id_kategori" x-text="kategori.nama_kategori"></option>
              </template>
            </select>
          </div>

          <div class="space-y-2">
            <label class="label">Harga Jual</label>
            <input type="number" x-model="form.harga_jual" required class="input">
          </div>

          <div class="space-y-2">
            <label class="label">Satuan</label>
            <input type="text" x-model="form.satuan_dasar" required class="input">
          </div>

          <div class="md:col-span-2 space-y-2">
            <label class="label">Deskripsi</label>
            <textarea x-model="form.deskripsi" class="input"></textarea>
          </div>

          <div class="md:col-span-2 space-y-2">
            <label class="label">Asal Produk</label>
            <input type="text" x-model="form.asal_produk" class="input">
          </div>

          <!-- NEXT BUTTON -->
          <div class="md:col-span-2 flex justify-end pt-4">
            <button type="button"
              @click="step = 2"
              class="btn-primary w-auto px-5">
              Selanjutnya →
            </button>
          </div>

        </div>
      </template>

      <!-- ================= STEP 2 ================= -->
      <template x-if="step === 2">
        <div class="space-y-5">

          <div x-cloak x-show="form.asal_produk != ''" class="space-y-2">
            <div class="flex-between">
              <label class="label">Tagline <span class="text-gray-400 text-sm">(opsional untuk deskripsi singkat di halaman homepage)</span></label>
              <button type="button" @click="generateTagline" class="text-sm text-gray-800 hover:text-primary underline">🎲 Generate Otomatis</button>
            </div>
            <input type="text" x-model="form.tagline" class="input" placeholder="Contoh: 'Manis alami gula semut Imanuel, rasa otentik Rote.'" maxlength="150">
          </div>

          <!-- DROP AREA -->
          <div
            class="border-2 border-dashed rounded-xl p-6 text-center transition border-gray-300">
            <p class="text-gray-600">
              Drag & drop, tempel gambar(<b>Ctrl + V</b>) atau klik tombol di bawah untuk memilih gambar produk
            </p>

            <label class="btn-outline-primary w-auto mt-3 inline-block cursor-pointer">
              Pilih File
              <input type="file" class="hidden" @change="onFileChange($event)" accept="image/*">
            </label>

            <p class="text-xs text-gray-400 mt-2">
              Format: JPG, PNG • Max 2MB
            </p>
          </div>

          <!-- PREVIEW -->
          <template x-if="previewUrl">
            <div class="text-center space-y-2">
              <p class="text-sm text-gray-500">Preview:</p>
              <img :src="previewUrl" class="w-40 h-40 object-cover rounded-lg border mx-auto">
            </div>
          </template>

          <!-- CURRENT IMAGE (EDIT MODE) -->
          <template x-if="currentImageUrl && !previewUrl">
            <div class="text-center">
              <p class="text-sm text-gray-500">Gambar saat ini:</p>
              <img :src="currentImageUrl" class="w-40 h-40 object-cover rounded-lg border mx-auto">
            </div>
          </template>

          <!-- PANDUAN -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
            <b>Tips:</b>
            <ul class="list-disc ml-5 mt-1 space-y-1">
              <li>Gunakan gambar jelas dan tidak blur</li>
              <li>Background transparan(.png) lebih disarankan, gunakan <a href="https://www.remove.bg/id/upload" target="_blank" class="text-gray-800 underline">remove.bg</a> untuk menghapus latar belakang</li>
              <li>Ukuran optimal 1:1 (persegi)</li>
            </ul>
          </div>

          <!-- NAVIGATION -->
          <div class="flex justify-between pt-4">
            <button type="button"
              @click="step = 1"
              class="btn-secondary w-auto px-5">
              ← Kembali
            </button>

            <button type="submit"
              class="btn-primary w-auto px-5">
              Simpan
            </button>
          </div>

        </div>
      </template>

    </form>
  </div>
</div>

<script>
  function produkFormPage() {
    return {
      baseUrl: BASE_URL,
      formTitle: <?= json_encode($title) ?>,
      kodeProduk: <?= json_encode($kodeProduk) ?>,
      kategoriOptions: [],
      currentImageUrl: '',
      form: {
        nama_produk: '',
        id_kategori: '',
        deskripsi: '',
        harga_jual: '',
        satuan_dasar: '',
        asal_produk: '',
        tagline: '',
        gambar: null
      },
      step: 1,
      dragging: false,
      previewUrl: '',

      init() {
        this.loadKategori();
        if (this.kodeProduk) {
          this.loadProduk();
        }
      },

      async loadKategori() {
        try {
          const res = await API.get('/kategori');
          if (res.success) {
            this.kategoriOptions = res.data || [];
          }
        } catch (err) {
          console.error(err);
        }
      },

      async loadProduk() {
        try {
          const res = await API.get('/produk/detail?k=' + encodeURIComponent(this.kodeProduk));
          if (res.success && res.data) {
            const data = res.data;
            this.form.nama_produk = data.nama_produk || '';
            this.form.id_kategori = data.id_kategori || '';
            this.form.deskripsi = data.deskripsi || '';
            this.form.harga_jual = data.harga_jual || '';
            this.form.satuan_dasar = data.satuan_dasar || '';
            this.form.asal_produk = data.asal_produk || '';
            this.form.tagline = data.tagline || '';
            this.currentImageUrl = data.gambar ? BASE_URL + '/uploads/' + data.gambar : '';
          }
        } catch (err) {
          console.error(err);
        }
      },

      onFileChange(event) {
        const file = event.target.files[0];
        if (file) {
          this.form.gambar = file;
          this.previewUrl = URL.createObjectURL(file);
        }
      },

      async submitForm() {
        try {
          const payload = new FormData();
          payload.append('nama_produk', this.form.nama_produk);
          payload.append('id_kategori', this.form.id_kategori);
          payload.append('deskripsi', this.form.deskripsi || '');
          payload.append('harga_jual', this.form.harga_jual);
          payload.append('satuan_dasar', this.form.satuan_dasar || '');
          payload.append('asal_produk', this.form.asal_produk || '');
          payload.append('tagline', this.form.tagline || '');
          if (this.form.gambar) {
            payload.append('gambar', this.form.gambar);
          }

          let res;
          if (this.kodeProduk) {
            payload.append('kode_produk', this.kodeProduk);
            res = await API.put('/produk', payload);
          } else {
            res = await API.post('/produk', payload);
          }

          if (res.success) {
            Alpine.store('ui')?.toast(this.kodeProduk ? 'Produk berhasil diperbarui' : 'Produk berhasil ditambahkan');
            setTimeout(() => {
              window.history.back();
            }, 1000);
          }
        } catch (err) {
          console.error(err);
        }
      },

      handleDrop(e) {
        this.dragging = false;
        const file = e.dataTransfer.files[0];

        if (file && this.validateImage(file)) {
          this.form.gambar = file;
          this.previewUrl = URL.createObjectURL(file);
        }
      },

      handlePaste(e) {
        const items = e.clipboardData?.items;
        if (!items) return;

        for (let i = 0; i < items.length; i++) {
          const item = items[i];

          if (item.type.indexOf('image') !== -1) {
            const file = item.getAsFile();

            if (file && this.validateImage(file)) {
              this.form.gambar = file;
              this.previewUrl = URL.createObjectURL(file);

              Alpine.store('ui')?.toast('Gambar berhasil ditempel (Ctrl+V)');
            }

            break;
          }
        }
      },

      validateImage(file) {
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!file.type.startsWith('image/')) {
          Alpine.store('ui')?.toast('File harus berupa gambar');
          return false;
        }

        if (file.size > maxSize) {
          Alpine.store('ui')?.toast('Ukuran gambar maksimal 2MB');
          return false;
        }

        return true;
      },

      async generateTagline() {
        if (!this.form.nama_produk || !this.form.asal_produk) {
          Alpine.store('ui')?.toast('Nama produk dan asal produk harus diisi untuk generate tagline');
          return;
        }

        const res = await API.get('/produk/tagline?nama_produk=' + encodeURIComponent(this.form.nama_produk) + '&asal_produk=' + encodeURIComponent(this.form.asal_produk) + '&deskripsi=' + encodeURIComponent(this.form.deskripsi));
        if (res.success) {
          this.form.tagline = res.data.tagline || '';
          Alpine.store('ui')?.toast('Tagline berhasil dihasilkan');
        } else {
          Alpine.store('ui')?.toast('Gagal menghasilkan tagline');
        }
      }

    }
  }
</script>