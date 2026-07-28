<div x-data="laporanPage()" x-init="fetchProduk()" class="bg-gray-50 p-4 lg:p-6 space-y-4">
  <!-- HEADER -->
  <div>
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Laporan</h1>
    <p class="text-sm text-gray-500">Cetak laporan transaksi, penjualan produk, atau stok produk dengan periode yang fleksibel.</p>
  </div>

  <!-- CARD PARAMETER -->
  <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 space-y-5 max-w-lg">
    <h2 class="text-xl font-bold text-gray-800 border-b pb-3 border-gray-200">Pilih Parameter Laporan</h2>

    <!-- Jenis Laporan -->
    <div>
      <label class="text-sm font-medium text-gray-700 block mb-1">Jenis Laporan</label>
      <select x-model="jenis" class="input">
        <option value="transaksi">Laporan Transaksi</option>
        <option value="mutasi-stok">Laporan Mutasi Stok</option>
        <option value="penjualan_produk">Laporan Penjualan Produk</option>
      </select>
    </div>

    <!-- === LAPORAN TRANSAKSI === -->
    <template x-if="jenis === 'transaksi'">
      <div class="space-y-4">
        <!-- Filter metode -->
        <div>
          <label class="text-sm font-medium text-gray-700 block mb-1">Metode Bayar</label>
          <select x-model="metode" class="input">
            <option value="">Semua</option>
            <option value="tunai">Tunai</option>
            <option value="qris">Qris</option>
          </select>
        </div>

        <!-- Pilih tipe laporan -->
        <div>
          <label class="text-sm font-medium text-gray-700 block mb-1">Tipe Laporan</label>
          <select x-model="tipe" class="input">
            <option value="harian">Harian</option>
            <option value="bulanan">Bulanan</option>
            <option value="tahunan">Tahunan</option>
          </select>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
          <template x-if="tipe === 'harian'">
            <div class="flex-1">
              <label class="text-sm font-medium text-gray-700 block mb-1">Tanggal</label>
              <input class="input" type="date" x-model="tanggal" />
            </div>
          </template>

          <template x-if="tipe === 'bulanan'">
            <div class="flex-1">
              <label class="text-sm font-medium text-gray-700 block mb-1">Bulan</label>
              <input class="input" type="month" x-model="bulan" />
            </div>
          </template>

          <template x-if="tipe === 'tahunan'">
            <div class="flex-1">
              <label class="text-sm font-medium text-gray-700 block mb-1">Tahun</label>
              <input class="input" type="number" x-model="tahun" min="2020" max="2100" placeholder="Tahun" />
            </div>
          </template>
        </div>
      </div>
    </template>

    <!-- === LAPORAN PENJUALAN PRODUK === -->
    <template x-if="jenis === 'penjualan_produk'">
      <div class="space-y-4">
        <!-- Filter produk -->
        <div class="relative" @click.outside="showDropdown = false">
          <label class="text-sm font-medium text-gray-700 block mb-1">Pilih Produk</label>
          <input class="input" type="text" x-model="query" @input.debounce.300ms="fetchProduk()" @focus="showDropdown = true"
            placeholder="Default (Semua Produk)" />

          <template x-if="showDropdown">
            <ul class="absolute z-20 mt-1 bg-white w-full border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-auto">
              <li @click="pilihProduk('')" class="px-3 py-2 hover:bg-red-50 cursor-pointer text-gray-700">
                Semua Produk
              </li>
              <template x-for="p in produkList" :key="p.kode_produk">
                <li @click="pilihProduk(p)" class="px-3 py-2 hover:bg-red-50 cursor-pointer text-gray-700"
                  x-text="p.nama_produk"></li>
              </template>
            </ul>
          </template>
        </div>

        <!-- Periode -->
        <div class="flex gap-4 flex-col md:flex-row">
          <div class="flex-1">
            <label class="text-sm font-medium text-gray-700 block mb-1">Tanggal Mulai</label>
            <input class="input" type="date" x-model="tanggalMulai" />
          </div>

          <div class="flex-1">
            <label class="text-sm font-medium text-gray-700 block mb-1">Tanggal Selesai</label>
            <input class="input" type="date" x-model="tanggalSelesai" />
          </div>
        </div>
      </div>
    </template>

    <!-- === LAPORAN MUTASI STOK === -->
    <template x-if="jenis === 'mutasi-stok'">
      <div class="space-y-4">
        <div class="relative" @click.outside="showDropdown = false">
          <label class="text-sm font-medium text-gray-700 block mb-1">Pilih Produk</label>
          <input class="input" type="text" x-model="query" @input.debounce.300ms="fetchProduk()" @focus="showDropdown = true"
            placeholder="Default (Semua Produk)" />

          <template x-if="showDropdown">
            <ul class="absolute z-20 mt-1 bg-white w-full border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-auto">
              <li @click="pilihProduk('')" class="px-3 py-2 hover:bg-red-50 cursor-pointer text-gray-700">
                Semua Produk
              </li>
              <template x-for="p in produkList" :key="p.kode_produk">
                <li @click="pilihProduk(p)" class="px-3 py-2 hover:bg-red-50 cursor-pointer text-gray-700"
                  x-text="p.nama_produk"></li>
              </template>
            </ul>
          </template>
        </div>

        <div class="flex justify-between gap-4 flex-col md:flex-row">
          <div class="flex-1">
            <label class="text-sm font-medium text-gray-700 block mb-1">Tanggal Mulai</label>
            <input class="input" type="date" x-model="tanggalMulai" />
          </div>

          <div class="flex-1">
            <label class="text-sm font-medium text-gray-700 block mb-1">Tanggal Selesai</label>
            <input class="input" type="date" x-model="tanggalSelesai" />
          </div>
        </div>
      </div>
    </template>

    <!-- TOMBOL EXPORT -->
    <div class="flex gap-3 pt-3">
      <!-- EXCEL -->
      <button
        @click="cetakLaporan()"
        :disabled="loading"
        class="flex items-center px-4 py-2 rounded-lg shadow-md transition text-white"
        :class="loading && formatProses === 'excel' ? 'bg-green-400 cursor-wait' : 'bg-green-600 hover:bg-green-600/80'">

        <template x-if="loading">
          <span class="flex items-center justify-center gap-x-2">
            <span>Memporses...</span>
          </span>
        </template>

        <template x-if="!loading">
          <span class="flex items-center justify-center gap-x-2">
            <span>Export Excel</span>
          </span>
        </template>
      </button>
    </div>

  </div>

  <div class="text-red-500 text-sm font-semibold pt-2">
    <p>Laporan akan mencakup data yang tersimpan dari sistem GG-MART.</p>
  </div>
</div>

<script>
  function laporanPage() {
    return {
      // === Data utama ===
      jenis: 'transaksi',
      tipe: 'harian',
      metode: '',
      tanggal: new Date().toISOString().slice(0, 10),
      bulan: new Date().toISOString().slice(0, 7),
      tahun: new Date().getFullYear(),

      // === Penjualan / stok ===
      produk: '',
      query: '',
      produkList: [],
      showDropdown: false,
      tanggalMulai: '',
      tanggalSelesai: new Date().toISOString().slice(0, 10),

      // === Ambil produk untuk dropdown ===
      async fetchProduk() {
        try {
          const res = await fetch(`${BASE_URL}/api/produk/dropdown?search=${encodeURIComponent(this.query)}`);
          const data = await res.json();
          this.produkList = data.data || [];
        } catch (err) {
          console.error("Gagal ambil produk:", err);
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

      // === Status loading ===
      loading: false,

      // === Cetak Laporan ===
      async cetakLaporan() {
        try {
          this.loading = true;
          let url = `${BASE_URL}/api/laporan/${this.jenis}?`;
          if (this.jenis === 'transaksi') {
            url += `tipe=${this.tipe}&metode=${this.metode}`;
            if (this.tipe === 'harian') url += `&tanggal=${this.tanggal}`;
            if (this.tipe === 'bulanan') url += `&bulan=${this.bulan}`;
            if (this.tipe === 'tahunan') url += `&tahun=${this.tahun}`;
          }
          if (this.jenis === 'penjualan_produk' || this.jenis === 'mutasi-stok') {
            url += `mulai=${this.tanggalMulai}&selesai=${this.tanggalSelesai}`;
            if (this.produk) url += `&produk=${this.produk}`;
          }

          // Link download langsung
          const a = document.createElement('a');
          a.href = url;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
        } catch (err) {
          Alpine.store('ui').toast(err.message || 'Gagal Cetak Laporan', 'error');
          console.error(err);
        } finally {
          this.loading = false;
        }
      }
    }
  }
</script>