<div x-data="stokFormPage()"
  class="bg-gray-50 min-h-dvh p-4 lg:p-6">

  <div class="max-w-3xl mx-auto bg-white rounded-xl shadow border p-6 space-y-6">

    <!-- HEADER -->
    <div class="flex-between border-b pb-4">
      <h1 class="text-xl font-bold">Tambah Perubahan Stok</h1>
      <a href="#" @click.prevent="history.back()" class="text-sm text-gray-500 hover:text-primary">← Kembali</a>
    </div>

    <!-- STEP -->
    <div class="flex gap-3 text-sm">
      <div :class="step === 1 ? 'text-primary font-semibold' : 'text-gray-400'">1. Produk</div>
      <div>→</div>
      <div :class="step === 2 ? 'text-primary font-semibold' : 'text-gray-400'">2. Detail</div>
    </div>

    <form @submit.prevent="submit" class="space-y-5">

      <!-- ================= STEP 1 ================= -->
      <template x-if="step === 1">
        <div class="space-y-4">

          <!-- TYPE -->
          <div>
            <label class="label">Jenis Perubahan</label>
            <div class="flex gap-2">
              <button type="button"
                @click="form.type='masuk'"
                :class="form.type==='masuk' ? 'bg-green-500 text-white' : 'border'"
                class="flex-1 p-3 rounded-lg border">
                Stok Masuk
              </button>

              <button type="button"
                @click="form.type='keluar'"
                :class="form.type==='keluar' ? 'bg-red-500 text-white' : 'border'"
                class="flex-1 p-3 rounded-lg border">
                Stok Keluar
              </button>
            </div>
          </div>

          <!-- PRODUK -->
          <div class="relative">
            <label class="label">Pilih Produk</label>

            <input type="text"
              x-model="produkQuery"
              @click="produkOpen = true"
              @focus="produkOpen = true"
              @input.debounce.500="loadProduk()"
              class="input"
              placeholder="Ketik nama atau kode produk...">

            <div x-show="produkOpen"
              @mousedown.outside="produkOpen=false"
              class="absolute z-20 bg-white border w-full mt-1 rounded-lg shadow max-h-72! overflow-y-auto">
              <template x-if="loadingProduk">
                <div class="p-3 text-gray-400">Memuat...</div>
              </template>
              <template x-for="p in produkList" :key="p.kode_produk">
                <div @click="selectProduk(p)"
                  class="p-3 hover:bg-gray-100 cursor-pointer border-b flex-between">
                  <div class="font-medium" x-text="p.nama_produk"></div>
                  <div class="text-xs text-gray-400" x-text="p.kode_produk"></div>
                </div>
              </template>
            </div>
          </div>

          <!-- NEXT -->
          <div class="flex justify-end">
            <button type="button" @click="next()" class="btn-primary w-auto px-5">
              Selanjutnya →
            </button>
          </div>

        </div>
      </template>

      <!-- ================= STEP 2 ================= -->
      <template x-if="step === 2">
        <div class="space-y-4">

          <div class="text-sm text-gray-600">
            Produk: <b x-text="form.nama_produk"></b>
          </div>

          <!-- JUMLAH -->
          <div>
            <label class="label">Jumlah</label>
            <input type="number" x-model.number="form.jumlah"
              @input="sync('jumlah')" class="input">
          </div>

          <!-- MASUK -->
          <template x-if="form.type==='masuk'">
            <div class="grid grid-cols-2 gap-3">

              <div>
                <label class="label">Harga Pokok</label>
                <input type="number" x-model.number="form.harga_pokok"
                  @input="sync('harga')" class="input">
              </div>

              <div>
                <label class="label">Total</label>
                <input type="number" x-model.number="form.total_pokok"
                  @input="sync('total')" class="input">
              </div>

            </div>
          </template>

          <!-- KELUAR -->
          <template x-if="form.type==='keluar'">
            <div class="relative">

              <label class="label">Pilih Batch</label>

              <input type="text"
                x-model="mutasiQuery"
                @focus="mutasiOpen=true"
                @input="filterMutasi()"
                class="input">

              <div x-show="mutasiOpen"
                @mousedown.outside="mutasiOpen=false"
                class="absolute z-20 bg-white border w-full mt-1 rounded-lg shadow max-h-60 overflow-auto">

                <template x-for="m in mutasiFiltered" :key="m.id_mutasi">
                  <div @click="selectMutasi(m)"
                    class="p-3 hover:bg-gray-100 cursor-pointer border-b text-sm flex-between">
                    <div x-text="utils.formatDateTime(m.tanggal)"></div>
                    <div class="text-xs text-gray-400">
                      Sisa Stok: <span x-text="m.sisa_stok"></span>
                    </div>
                  </div>
                </template>

              </div>
            </div>
          </template>

          <!-- KETERANGAN -->
          <div>
            <label class="label">Keterangan</label>
            <textarea x-model="form.keterangan" class="input"></textarea>
          </div>

          <!-- BUTTON -->
          <div class="flex-between pt-4 border-t">
            <button type="button" @click="step=1" class="btn-secondary px-4">
              ← Kembali
            </button>
            <button type="submit" class="btn-primary px-5">
              Simpan
            </button>
          </div>
        </div>
      </template>
    </form>
  </div>
</div>


<script>
  function stokFormPage() {
    return {
      step: 1,

      form: {
        kode_produk: '',
        nama_produk: '',
        type: '',
        jumlah: 0,
        harga_pokok: 0,
        total_pokok: 0,
        id_mutasi: '',
        keterangan: ''
      },

      // ===== PRODUK =====
      produkQuery: '',
      produkList: [],
      produkOpen: false,
      loadingProduk: false,

      async loadProduk() {
        this.loadingProduk = true
        const res = await API.get('/produk/dropdown?search=' + this.produkQuery)
        this.produkList = res.data || []
        console.log(this.produkList)
        this.loadingProduk = false
      },

      selectProduk(p) {
        this.form.kode_produk = p.kode_produk
        this.form.nama_produk = p.nama_produk
        this.produkOpen = false
        this.produkQuery = p.nama_produk
      },

      // ===== MUTASI =====
      mutasiQuery: '',
      mutasiList: [],
      mutasiFiltered: [],
      mutasiOpen: false,

      async loadMutasi() {
        const res = await API.get('/mutasi/dropdown?kode=' + this.form.kode_produk)
        this.mutasiList = res.data || []
        this.mutasiFiltered = this.mutasiList
      },

      filterMutasi() {
        this.mutasiFiltered = this.mutasiList.filter(m => {
          let tanggalTeks = utils.formatDateTime(m.tanggal) || '';
          return tanggalTeks.toLowerCase().replace(/,/g, '').includes(
            this.mutasiQuery.toLowerCase()
          );
        })
      },

      selectMutasi(m) {
        this.form.id_mutasi = m.id_mutasi
        this.form.harga_pokok = m.harga_pokok
        this.mutasiQuery = utils.formatDateTime(m.tanggal)
        this.mutasiOpen = false
      },

      // ===== LOGIC =====
      sync(type) {
        const j = this.form.jumlah || 0

        if (type === 'harga') {
          this.form.total_pokok = j * this.form.harga_pokok
        } else if (type === 'total') {
          this.form.harga_pokok = this.form.total_pokok / j
        } else if (type === 'jumlah') {
          this.form.total_pokok = 0
          this.form.harga_pokok = 0
        }
      },

      async next() {
        if (!this.form.type || !this.form.kode_produk) {
          Alpine.store('ui').toast('Lengkapi dulu step 1')
          return
        }

        if (this.form.type === 'keluar') {
          await this.loadMutasi()
          if (!this.mutasiList) return
        }

        this.step = 2
      },

      // ===== SUBMIT =====
      async submit() {
        try {
          const payload = new FormData()

          Object.keys(this.form).forEach(k => {
            payload.append(k, this.form[k])
          })

          if (this.form.type === 'masuk') {
            payload.append('sisa_stok', this.form.jumlah)
          }

          const res = await API.post('/mutasi', payload)

          if (res.success) {
            Alpine.store('ui').toast('Berhasil disimpan')
            setTimeout(() => history.back(), 1000)
          }

        } catch (err) {
          console.error(err)
        }
      },

      // ===== INIT =====
      async init() {
        await this.loadProduk()
        const p = utils.getQuery('p')
        if (p) {
          const res = await API.get('/produk/detail?k=' + p)
          if (res.success) {
            this.selectProduk(res.data)
            this.form.type = 'masuk'
            this.step = 2
          }
        }
      }
    }
  }
</script>