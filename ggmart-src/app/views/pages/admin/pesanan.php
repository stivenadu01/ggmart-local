<div class="space-y-6 p-4 sm:p-5 lg:p-6" x-data="pesananPage()">

  <!-- PAGE HEADER -->
  <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div class="min-w-0">
      <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-600">Order Management</p>
      <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Pesanan</h1>
      <p class="mt-1 max-w-2xl text-sm text-slate-500">
        Kelola pesanan pelanggan yang menunggu diproses hingga dikonfirmasi.
      </p>
    </div>

    <button @click="load()" type="button" class="admin-action-secondary w-full sm:w-auto">
      <span>↻</span>
      <span>Refresh</span>
    </button>
  </div>

  <!-- FILTER BAR -->
  <section class="card overflow-visible">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
      <div class="min-w-0 flex-1">
        <label class="label">Cari pesanan</label>
        <div class="relative">
          <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">⌕</span>
          <input
            type="text"
            x-model="search"
            @input.debounce.500="pagination.page=1; load()"
            class="input pl-9"
            placeholder="Kode transaksi atau produk...">
        </div>
      </div>

      <div class="w-full lg:w-56">
        <label class="label">Metode pembayaran</label>
        <select x-model="metode" @change="pagination.page=1; load()" class="input">
          <option value="">Semua metode</option>
          <option value="tunai">Tunai</option>
          <option value="qris">QRIS</option>
        </select>
      </div>

      <button
        x-show="search || metode"
        x-cloak
        @click="search=''; metode=''; pagination.page=1; load()"
        type="button"
        class="admin-action-secondary w-full lg:w-auto">
        Reset
      </button>
    </div>
  </section>

  <!-- DESKTOP TABLE -->
  <section class="card hidden overflow-hidden md:block">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
      <div>
        <h2 class="font-semibold text-slate-900">Daftar Pesanan</h2>
        <p class="mt-0.5 text-xs text-slate-500">
          <span x-text="pagination.total"></span> pesanan ditemukan
        </p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>No</th>
            <th>Pesanan</th>
            <th>Pelanggan</th>
            <th>Tanggal</th>
            <th>Pembayaran</th>
            <th>Total</th>
            <th>Status</th>
            <th class="text-right">Aksi</th>
          </tr>
        </thead>

        <tbody>
          <template x-if="data.length === 0">
            <tr>
              <td colspan="8" class="py-16 text-center">
                <div class="mx-auto flex max-w-sm flex-col items-center">
                  <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400">⌕</div>
                  <p class="font-semibold text-slate-700">Tidak ada pesanan</p>
                  <p class="mt-1 text-sm text-slate-500">Belum ada pesanan yang sesuai dengan filter Anda.</p>
                </div>
              </td>
            </tr>
          </template>

          <template x-for="(item, idx) in data" :key="item.kode_transaksi">
            <tr>
              <td class="text-slate-400" x-text="(pagination.page-1)*pagination.limit + idx + 1"></td>

              <td>
                <button @click="detail(item.kode_transaksi)" type="button" class="text-left">
                  <span class="font-semibold text-slate-900 hover:text-emerald-600" x-text="item.kode_transaksi"></span>
                  <span class="mt-0.5 block text-xs text-slate-400">Lihat detail pesanan</span>
                </button>
              </td>

              <td>
                <span class="font-medium text-slate-700" x-text="item.user || '-'"></span>
              </td>

              <td class="whitespace-nowrap text-sm text-slate-500" x-text="utils.formatDateTime(item.tanggal_transaksi)"></td>

              <td>
                <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold uppercase text-slate-600" x-text="item.metode_bayar"></span>
              </td>

              <td class="whitespace-nowrap font-bold text-slate-900" x-text="utils.formatRupiah(item.total_harga)"></td>

              <td>
                <span
                  class="badge"
                  :class="{
                    'bg-amber-100 text-amber-700': item.status === 'diproses',
                    'bg-blue-100 text-blue-700': item.status === 'pending'
                  }"
                  x-text="item.status === 'pending' ? 'Menunggu' : 'Diproses'">
                </span>
              </td>

              <td>
                <div class="flex justify-end gap-2">
                  <button @click="detail(item.kode_transaksi)" type="button" class="admin-action-secondary admin-action-sm">Detail</button>
                  <button
                    x-show="item.status === 'pending' || item.status === 'diproses'"
                    @click="item.status === 'pending' ? proses(item.kode_transaksi) : konfirmasi(item.kode_transaksi)"
                    type="button"
                    class="btn-primary px-3 py-2 text-xs"
                    x-text="item.status === 'pending' ? 'Proses' : 'Konfirmasi'">
                  </button>
                  <button @click="batal(item.kode_transaksi)" type="button" class="admin-action-danger admin-action-sm">Batal</button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </section>

  <!-- MOBILE CARDS -->
  <section class="space-y-3 md:hidden">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-slate-900">Daftar Pesanan</h2>
        <p class="text-xs text-slate-500"><span x-text="pagination.total"></span> pesanan</p>
      </div>
    </div>

    <template x-if="data.length === 0">
      <div class="card px-5 py-12 text-center">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400">⌕</div>
        <p class="font-semibold text-slate-700">Tidak ada pesanan</p>
        <p class="mt-1 text-sm text-slate-500">Belum ada pesanan yang sesuai.</p>
      </div>
    </template>

    <template x-for="(item, idx) in data" :key="'mobile-'+item.kode_transaksi">
      <article class="card p-4">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <button @click="detail(item.kode_transaksi)" type="button" class="text-left font-bold text-slate-900">
              <span x-text="item.kode_transaksi"></span>
            </button>
            <p class="mt-1 truncate text-xs text-slate-500" x-text="item.user || 'Pelanggan umum'"></p>
          </div>
          <span
            class="badge shrink-0"
            :class="{
              'bg-amber-100 text-amber-700': item.status === 'diproses',
              'bg-blue-100 text-blue-700': item.status === 'pending'
            }"
            x-text="item.status === 'pending' ? 'Menunggu' : 'Diproses'">
          </span>
        </div>

        <div class="my-4 grid grid-cols-2 gap-3 border-y border-slate-100 py-3">
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Tanggal</p>
            <p class="mt-1 text-xs font-medium text-slate-700" x-text="utils.formatDateTime(item.tanggal_transaksi)"></p>
          </div>
          <div>
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Pembayaran</p>
            <p class="mt-1 text-xs font-semibold uppercase text-slate-700" x-text="item.metode_bayar"></p>
          </div>
          <div class="col-span-2">
            <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">Total</p>
            <p class="mt-1 text-lg font-bold text-slate-900" x-text="utils.formatRupiah(item.total_harga)"></p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <button @click="detail(item.kode_transaksi)" type="button" class="admin-action-secondary admin-action-sm">Detail</button>
          <button
            @click="item.status === 'pending' ? proses(item.kode_transaksi) : konfirmasi(item.kode_transaksi)"
            type="button"
            class="btn-primary"
            x-text="item.status === 'pending' ? 'Proses' : 'Konfirmasi'">
          </button>
          <button @click="batal(item.kode_transaksi)" type="button" class="admin-action-danger col-span-2">Batalkan Pesanan</button>
        </div>
      </article>
    </template>
  </section>

  <!-- PAGINATION -->
  <div class="card flex flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="text-sm text-slate-500">
      Menampilkan
      <select x-model.number="pagination.limit" @change="pagination.page=1; load()" class="input inline-block w-auto py-2">
        <option value="10">10</option>
        <option value="25">25</option>
      </select>
      dari <span class="font-semibold text-slate-700" x-text="pagination.total"></span> pesanan
    </div>

    <div class="flex items-center gap-2">
      <button
        @click="pagination.page--; load()"
        :disabled="pagination.page==1"
        type="button"
        class="admin-page-button"
        aria-label="Halaman sebelumnya">←</button>

      <span class="min-w-24 text-center text-sm text-slate-500">
        Halaman <span class="font-semibold text-slate-700" x-text="pagination.page"></span>
        / <span x-text="pagination.total_pages"></span>
      </span>

      <button
        @click="pagination.page++; load()"
        :disabled="pagination.page==pagination.total_pages"
        type="button"
        class="admin-page-button"
        aria-label="Halaman berikutnya">→</button>
    </div>
  </div>

</div>

<script>
  function pesananPage() {
    return {
      data: [],
      search: utils.getQuery('search') || '',
      metode: utils.getQuery('metode') || '',

      pagination: {
        page: Number(utils.getQuery('page') || 1),
        limit: Number(utils.getQuery('limit') || 10),
        total: 0,
        total_pages: 1
      },

      async init() {
        await this.load()
      },

      async load() {
        try {
          const params = new URLSearchParams({
            status: 'diproses,pending',
            search: this.search,
            metode: this.metode,
            page: this.pagination.page,
            limit: this.pagination.limit
          })

          const res = await API.get('/transaksi/list?' + params.toString())

          if (res.success) {
            this.data = res.data
            this.pagination.total = res.pagination.total
            this.pagination.total_pages = res.pagination.total_pages || 1

            if (this.pagination.page > this.pagination.total_pages) {
              this.pagination.page = this.pagination.total_pages
            }

            utils.setQuery('search', this.search)
            utils.setQuery('metode', this.metode)
            utils.setQuery('page', this.pagination.page)
            utils.setQuery('limit', this.pagination.limit)
          }
        } catch (err) {
          console.error(err)
        }
      },

      async detail(kode) {
        window.location.href = `${BASE_URL}/admin/transaksi/${kode}`
      },

      async proses(kode) {
        const ok = await Alpine.store('ui').confirm('Proses pesanan ini?')
        if (!ok) return
        try {
          await API.post('/transaksi/proses', { kode_transaksi: kode })
          Alpine.store('ui').toast('Pesanan diproses')
          await this.load()
        } catch (err) {
          console.error(err)
        }
      },

      async konfirmasi(kode) {
        const ok = await Alpine.store('ui').confirm('Konfirmasi pesanan ini?')
        if (!ok) return
        try {
          await API.post('/transaksi/konfirmasi', { kode_transaksi: kode })
          Alpine.store('ui').toast('Pesanan dikonfirmasi')
          await this.load()
        } catch (err) {
          console.error(err)
        }
      },

      async batal(kode) {
        const ok = await Alpine.store('ui').confirm('Batalkan pesanan ini?')
        if (!ok) return
        try {
          await API.post('/transaksi/batal', { kode_transaksi: kode })
          Alpine.store('ui').toast('Pesanan dibatalkan')
          await this.load()
        } catch (err) {
          console.error(err)
        }
      }
    }
  }
</script>
