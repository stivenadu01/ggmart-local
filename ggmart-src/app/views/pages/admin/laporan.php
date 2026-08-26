<div x-data="laporanPage()" x-init="fetchProduk()" class="space-y-6">
  <!-- HEADER -->
  <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <div class="mb-2 flex items-center gap-2">
          <span class="status-success">
            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.86-9.86a.75.75 0 0 0-1.06-1.06L9 10.88 7.2 9.08a.75.75 0 0 0-1.06 1.06l2.33 2.33a.75.75 0 0 0 1.06 0l4.33-4.33Z" clip-rule="evenodd" /></svg>
            Laporan untuk Pimpinan
          </span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Pembuatan Laporan</h1>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
          Pilih jenis laporan dan periode yang diperlukan, lalu ekspor data resmi GG-Mart dalam format Excel.
        </p>
      </div>

      <div class="rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-600 lg:max-w-xs">
        <p class="font-semibold text-slate-800">Sebelum mengekspor</p>
        <p class="mt-1 leading-5">Pastikan periode dan filter sudah sesuai dengan laporan yang ingin disampaikan kepada pimpinan.</p>
      </div>
    </div>
  </section>

  <!-- JENIS LAPORAN -->
  <section class="space-y-3">
    <div>
      <h2 class="text-lg font-bold text-slate-900">1. Pilih jenis laporan</h2>
      <p class="mt-1 text-sm text-slate-500">Pilih laporan sesuai kebutuhan. Setiap jenis memiliki parameter yang berbeda.</p>
    </div>

    <div class="grid gap-3 md:grid-cols-2">
      <button type="button" @click="jenis = 'transaksi'" :aria-pressed="jenis === 'transaksi'"
        :class="jenis === 'transaksi' ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-100' : 'border-slate-200 bg-white hover:border-emerald-300 hover:bg-slate-50'"
        class="w-full rounded-2xl border p-4 text-left transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
        </span>
        <span class="mt-3 block font-semibold text-slate-900">Laporan Transaksi</span>
        <span class="mt-1 block text-xs leading-5 text-slate-500">Rekap transaksi selesai berdasarkan harian, bulanan, atau tahunan.</span>
      </button>

      <button type="button" @click="jenis = 'mutasi-stok'" :aria-pressed="jenis === 'mutasi-stok'"
        :class="jenis === 'mutasi-stok' ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-100' : 'border-slate-200 bg-white hover:border-emerald-300 hover:bg-slate-50'"
        class="w-full rounded-2xl border p-4 text-left transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3v18M5 8l7-5 7 5M5 16l7 5 7-5"/><path d="M5 8v8M19 8v8"/></svg>
        </span>
        <span class="mt-3 block font-semibold text-slate-900">Laporan Mutasi Stok</span>
        <span class="mt-1 block text-xs leading-5 text-slate-500">Rincian stok masuk dan keluar berdasarkan produk dan periode.</span>
      </button>
    </div>
  </section>

  <!-- PARAMETER -->
  <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="mb-5 border-b border-slate-100 pb-4">
      <h2 class="text-lg font-bold text-slate-900">2. Atur parameter laporan</h2>
      <p class="mt-1 text-sm text-slate-500">Gunakan nilai yang spesifik agar hasil laporan mudah diperiksa dan dipertanggungjawabkan.</p>
    </div>

    <!-- TRANSAKSI -->
    <template x-if="jenis === 'transaksi'">
      <div class="space-y-5">
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="label" for="laporan-tipe">Tipe laporan *</label>
            <select id="laporan-tipe" x-model="tipe" class="input">
              <option value="harian">Harian</option>
              <option value="bulanan">Bulanan</option>
              <option value="tahunan">Tahunan</option>
            </select>
            <p class="form-help">Tentukan tingkat ringkasan yang akan diberikan kepada pimpinan.</p>
          </div>

          <div>
            <label class="label" for="laporan-metode">Metode pembayaran</label>
            <select id="laporan-metode" x-model="metode" class="input">
              <option value="">Semua metode</option>
              <option value="tunai">Tunai</option>
              <option value="qris">QRIS</option>
            </select>
            <p class="form-help">Biarkan “Semua metode” jika laporan mencakup seluruh pembayaran.</p>
          </div>
        </div>

        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
          <p class="mb-3 text-sm font-semibold text-slate-800">Periode laporan</p>
          <div class="grid gap-5 md:grid-cols-2">
            <template x-if="tipe === 'harian'">
              <div>
                <label class="label" for="laporan-tanggal">Tanggal *</label>
                <input id="laporan-tanggal" class="input" type="date" x-model="tanggal" />
                <p class="form-help">Contoh: pilih 21 Agustus 2026 untuk laporan transaksi hari tersebut.</p>
              </div>
            </template>

            <template x-if="tipe === 'bulanan'">
              <div>
                <label class="label" for="laporan-bulan">Bulan *</label>
                <input id="laporan-bulan" class="input" type="month" x-model="bulan" />
                <p class="form-help">Contoh: Agustus 2026 untuk rekap transaksi selama satu bulan.</p>
              </div>
            </template>

            <template x-if="tipe === 'tahunan'">
              <div>
                <label class="label" for="laporan-tahun">Tahun *</label>
                <input id="laporan-tahun" class="input" type="number" x-model="tahun" min="2020" max="2100" placeholder="Contoh: 2026" />
                <p class="form-help">Masukkan tahun dengan empat digit, misalnya 2026.</p>
              </div>
            </template>
          </div>
        </div>
      </div>
    </template>

    <!-- MUTASI STOK -->
    <template x-if="jenis === 'mutasi-stok'">
      <div class="space-y-5">
        <div>
          <label class="label" for="laporan-produk">Produk</label>
          <div class="relative" @click.outside="showDropdown = false">
            <input id="laporan-produk" class="input" type="text" x-model="query" @input.debounce.300ms="fetchProduk()" @focus="showDropdown = true"
              placeholder="Contoh: Beras Merah Pantar" autocomplete="off" />
            <p class="form-help">Kosongkan untuk membuat laporan seluruh produk. Ketik nama produk untuk mempersempit pilihan.</p>

            <template x-if="showDropdown">
              <ul class="absolute z-30 mt-2 max-h-60 w-full overflow-auto rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                <li @click="pilihProduk('')" class="cursor-pointer rounded-lg px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50">Semua Produk</li>
                <template x-for="p in produkList" :key="p.kode_produk">
                  <li @click="pilihProduk(p)" class="cursor-pointer rounded-lg px-3 py-2.5 text-sm text-slate-700 hover:bg-emerald-50" x-text="p.nama_produk"></li>
                </template>
                <template x-if="!produkList.length && query">
                  <li class="px-3 py-3 text-sm text-slate-500">Produk tidak ditemukan.</li>
                </template>
              </ul>
            </template>
          </div>
        </div>

        <div class="grid gap-5 rounded-xl border border-slate-100 bg-slate-50 p-4 md:grid-cols-2">
          <div>
            <label class="label" for="mutasi-mulai">Tanggal mulai</label>
            <input id="mutasi-mulai" class="input" type="date" x-model="tanggalMulai" />
            <p class="form-help">Kosongkan jika laporan dimulai dari data paling awal.</p>
          </div>
          <div>
            <label class="label" for="mutasi-selesai">Tanggal selesai</label>
            <input id="mutasi-selesai" class="input" type="date" x-model="tanggalSelesai" />
            <p class="form-help">Contoh: 21 Agustus 2026 sebagai batas akhir laporan.</p>
          </div>
        </div>
      </div>
    </template>

    <!-- RINGKASAN PILIHAN -->
    <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50/60 p-4">
      <div class="flex items-start gap-3">
        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/></svg>
        </span>
        <div class="min-w-0">
          <p class="text-sm font-semibold text-slate-900">Ringkasan laporan</p>
          <p class="mt-1 text-sm leading-6 text-slate-600" x-text="summaryText()"></p>
        </div>
      </div>
    </div>

    <!-- EXPORT -->
    <div class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <p class="text-sm font-semibold text-slate-800">Siap membuat laporan?</p>
        <p class="form-help">File akan diunduh dalam format Excel (.xlsx).</p>
      </div>
      <button type="button" @click="cetakLaporan()" :disabled="loading" class="admin-action-primary w-full sm:w-auto">
        <template x-if="loading">
          <span class="flex items-center gap-2">
            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".3" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3"/></svg>
            Menyiapkan laporan...
          </span>
        </template>
        <template x-if="!loading">
          <span class="flex items-center gap-2">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M5 20h14"/></svg>
            Export Excel
          </span>
        </template>
      </button>
    </div>
  </section>

  <!-- CATATAN -->
  <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
    <div class="flex items-start gap-3">
      <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 9v4m0 4h.01"/><path d="m10.3 4.6-7.5 13A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-3l-7.5-13a2 2 0 0 0-3.4 0Z"/></svg>
      <div>
        <p class="text-sm font-semibold text-amber-900">Catatan laporan</p>
        <p class="mt-1 text-sm leading-6 text-amber-800">Laporan menggunakan data yang tersimpan di sistem GG-Mart. Pastikan transaksi yang ingin dilaporkan sudah memiliki status selesai.</p>
      </div>
    </div>
  </section>
</div>

<script>
  function laporanPage() {
    return {
      jenis: 'transaksi',
      tipe: 'harian',
      metode: '',
      tanggal: new Date().toISOString().slice(0, 10),
      bulan: new Date().toISOString().slice(0, 7),
      tahun: new Date().getFullYear(),
      produk: '',
      query: '',
      produkList: [],
      showDropdown: false,
      tanggalMulai: '',
      tanggalSelesai: new Date().toISOString().slice(0, 10),
      loading: false,

      async fetchProduk() {
        try {
          const res = await fetch(`${BASE_URL}/api/produk/dropdown?search=${encodeURIComponent(this.query)}`);
          if (!res.ok) throw new Error('Gagal memuat daftar produk.');
          const data = await res.json();
          this.produkList = data.data || [];
        } catch (err) {
          this.produkList = [];
          console.error('Gagal ambil produk:', err);
        }
      },

      pilihProduk(p) {
        if (!p) {
          this.produk = '';
          this.query = '';
        } else {
          this.produk = p.kode_produk;
          this.query = p.nama_produk;
        }
        this.showDropdown = false;
      },

      summaryText() {
        if (this.jenis === 'transaksi') {
          const metode = this.metode ? this.metode.toUpperCase() : 'semua metode pembayaran';
          if (this.tipe === 'harian') return `Laporan transaksi harian untuk ${this.tanggal || 'tanggal yang dipilih'}, mencakup ${metode}.`;
          if (this.tipe === 'bulanan') return `Laporan transaksi bulanan untuk ${this.bulan || 'bulan yang dipilih'}, mencakup ${metode}.`;
          return `Laporan transaksi tahunan untuk ${this.tahun || 'tahun yang dipilih'}, mencakup ${metode}.`;
        }

        const produk = this.produk ? this.query : 'semua produk';
        const mulai = this.tanggalMulai || 'data awal';
        const selesai = this.tanggalSelesai || 'tanggal akhir';
        return `Laporan mutasi stok ${produk} untuk periode ${mulai} sampai ${selesai}.`;
      },

      validate() {
        if (this.jenis === 'transaksi') {
          if (this.tipe === 'harian' && !this.tanggal) return 'Tanggal laporan wajib dipilih.';
          if (this.tipe === 'bulanan' && !this.bulan) return 'Bulan laporan wajib dipilih.';
          if (this.tipe === 'tahunan' && !this.tahun) return 'Tahun laporan wajib diisi.';
        }

        if (this.jenis === 'mutasi-stok' && this.tanggalMulai && this.tanggalSelesai && this.tanggalMulai > this.tanggalSelesai) {
          return 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.';
        }

        return '';
      },

      async cetakLaporan() {
        const validationError = this.validate();
        if (validationError) {
          Alpine.store('ui').toast(validationError, 'error');
          return;
        }

        this.loading = true;
        let url = `${BASE_URL}/api/laporan/${this.jenis}?`;

        if (this.jenis === 'transaksi') {
          url += `tipe=${encodeURIComponent(this.tipe)}&metode=${encodeURIComponent(this.metode)}`;
          if (this.tipe === 'harian') url += `&tanggal=${encodeURIComponent(this.tanggal)}`;
          if (this.tipe === 'bulanan') url += `&bulan=${encodeURIComponent(this.bulan)}`;
          if (this.tipe === 'tahunan') url += `&tahun=${encodeURIComponent(this.tahun)}`;
        }

        if (this.jenis === 'mutasi-stok') {
          url += `mulai=${encodeURIComponent(this.tanggalMulai)}&selesai=${encodeURIComponent(this.tanggalSelesai)}`;
          if (this.produk) url += `&produk=${encodeURIComponent(this.produk)}`;
        }

        try {
          const a = document.createElement('a');
          a.href = url;
          a.download = '';
          document.body.appendChild(a);
          a.click();
          a.remove();
          Alpine.store('ui').toast('Laporan sedang diunduh.', 'success');
        } catch (err) {
          Alpine.store('ui').toast(err.message || 'Gagal membuat laporan.', 'error');
          console.error(err);
        } finally {
          window.setTimeout(() => { this.loading = false; }, 800);
        }
      }
    }
  }
</script>
