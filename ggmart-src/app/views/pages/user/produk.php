<div class="py-10" x-data="produkPage()">

  <!-- HEADER -->
  <div class="px-d mb-5">
    <h1>Katalog Produk</h1>
    <p class="text-gray-500 text-sm">Temukan produk terbaik dari UMKM lokal</p>
    <p x-show="filter.search" x-cloak class="mt-1 text-xs text-gray-500">Hasil pencarian untuk: <span class="font-semibold text-gray-700" x-text="filter.search"></span></p>
  </div>

  <!-- FILTER -->
  <div id="produk-filter-bar" class="sticky top-16 z-49 glass border-b border-black/5" x-data="{ openKategori: false, openSort: false}">
    <div class="flex flex-col">
      <div class="relative w-full sm:w-auto flex justify-center">
        <!-- Menu Kategori -->
        <div x-show="openKategori" x-transition x-cloak class="dropdown-menu min-w-sm top-12 left-1/2 -translate-x-1/2 sm:left-15 sm:translate-x-0">
          <div
            @click="filter.kategori=''; openKategori=false; resetLoad()"
            class="dropdown-item"
            :class="!filter.kategori && 'dropdown-item-active'">
            Semua Kategori
          </div>
          <template x-for="k in kategoriList" :key="k.id_kategori">
            <div
              @click="filter.kategori=k.id_kategori; openKategori=false; resetLoad()"
              class="dropdown-item"
              :class="filter.kategori == k.id_kategori && 'dropdown-item-active'">
              <span x-text="k.nama_kategori"></span>
            </div>
          </template>
        </div>
        <div x-show="openSort" x-transition x-cloak class="dropdown-menu min-w-sm top-12 left-1/2 -translate-x-1/2 sm:left-80 sm:translate-x-0">
          <template x-for="s in sortList" :key="s.value">
            <div
              @click="filter.sort = s.value; openSort=false; resetLoad()"
              class="dropdown-item"
              :class="filter.sort == s.value && 'dropdown-item-active'">

              <span x-text="s.label"></span>
            </div>
          </template>
        </div>
      </div>

      <div class="px-d py-2 flex gap-2 items-center overflow-x-auto">
        <!-- KATEGORI DROPDOWN -->
        <div @click="openKategori = !openKategori" @click.outside="openKategori = false" class="btn-filter" :class="filter.kategori && 'btn-filter-active'">
          <span>
            <template x-if="!filter.kategori">
              <span>Semua Kategori</span>
            </template>
            <template x-for="k in kategoriList" :key="k.id_kategori">
              <span
                x-show="filter.kategori == k.id_kategori"
                x-text="k.nama_kategori">
              </span>
            </template>
          </span>
          <span>▾</span>
        </div>
        <!-- LOKAL -->
        <button
          @click="filter.lokal = filter.lokal ? null : 1; resetLoad()"
          class="btn-filter"
          :class="filter.lokal ? 'btn-filter-active' : ''">
          Produk Lokal
        </button>
        <!-- SORT DROPDOWN -->
        <div
          @click="openSort = !openSort"
          @click.outside="openSort = false"
          class="btn-filter" :class="getSortLabel() != 'Rekomendasi' && 'btn-filter-active'">

          <span x-text="getSortLabel()"></span>
          <span>▾</span>
        </div>
      </div>
    </div>
  </div>

  <!-- LIST PRODUK -->
  <div class="px-2 md:px-8 lg:px-16 mt-6 min-h-[75dvh]">

    <!-- LOADING AWAL -->
    <div x-show="loading && items.length === 0" x-cloak class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" aria-label="Memuat produk">
      <template x-for="i in 8" :key="i">
        <div class="rounded-2xl overflow-hidden bg-white shadow animate-pulse">
          <div class="aspect-square bg-gray-100"></div>
          <div class="p-3 space-y-2">
            <div class="h-4 w-4/5 rounded bg-gray-100"></div>
            <div class="h-4 w-2/5 rounded bg-gray-100"></div>
            <div class="h-9 rounded bg-gray-100"></div>
          </div>
        </div>
      </template>
    </div>

    <div x-show="error && items.length === 0" x-cloak class="rounded-2xl border border-red-200 bg-red-50 px-5 py-8 text-center">
      <p class="font-semibold text-red-800">Produk belum dapat dimuat.</p>
      <p class="mt-1 text-sm text-red-700">Silakan coba lagi beberapa saat.</p>
      <button @click="resetLoad()" class="btn-outline-primary btn-rounded mt-4 w-auto px-5">Coba Lagi</button>
    </div>

    <div x-show="!loading && !error && items.length === 0" x-cloak class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-5 py-10 text-center">
      <p class="font-semibold text-gray-800">Produk tidak ditemukan.</p>
      <p class="mt-1 text-sm text-gray-500">Coba ubah kata pencarian atau filter yang digunakan.</p>
      <button @click="clearFilters()" class="btn-outline-primary btn-rounded mt-4 w-auto px-5">Reset Filter</button>
    </div>

    <!-- GRID PRODUK -->
    <div x-show="items.length > 0" class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">

      <template x-for="item in items" :key="item.kode_produk">
        <article class="group user-card user-card-hover flex min-h-full flex-col overflow-hidden">
          <button
            type="button"
            @click="goDetail(item.kode_produk)"
            class="aspect-square overflow-hidden bg-slate-50"
            :aria-label="'Lihat ' + item.nama_produk">
            <img
              loading="lazy"
              decoding="async"
              :src="item.gambar ? BASE_URL + '/uploads' + item.gambar : BASE_URL + '/assets/no-image.png'"
              :alt="item.nama_produk"
              class="h-full w-full object-contain transition duration-200 group-hover:scale-105">
          </button>

          <div class="flex flex-1 flex-col p-3">
            <h3 class="line-clamp-2 text-sm font-medium text-slate-900" x-text="item.nama_produk"></h3>
            <p class="mt-1 font-poppins font-semibold text-primary" x-text="$store.utils.formatRupiah(item.harga_jual)"></p>
            <p
              x-show="item.asal_produk"
              x-text="item.asal_produk"
              class="mt-1 line-clamp-1 text-xs italic text-slate-500">
            </p>

            <div class="mt-auto grid grid-cols-2 gap-2 pt-3">
              <button
                type="button"
                @click="addToCart(item, $event)"
                :disabled="item.stok <= 0"
                class="user-action-primary min-h-9 px-2 py-2 text-xs disabled:cursor-not-allowed disabled:opacity-50">
                <span x-text="item.stok > 0 ? '+ Keranjang' : 'Habis'"></span>
              </button>
              <button
                type="button"
                @click="goDetail(item.kode_produk)"
                class="user-action-outline min-h-9 px-2 py-2 text-xs">
                Detail
              </button>
            </div>
          </div>
        </article>
      </template>

    </div>

    <!-- LOAD MORE -->
    <div class="flex-center mt-8">
      <button
        x-show="hasMore && !loading"
        @click="loadMore()"
        class="btn-outline-primary w-auto px-6">
        Muat Lebih Banyak
      </button>

      <div x-show="loading && items.length > 0" class="h-[70dvh]! flex-center">
        <div class="w-10 h-10 border-4 border-gray-300 border-t-primary rounded-full animate-spin"></div>
      </div>
    </div>

  </div>

  <script>
    function produkPage() {
      return {
        items: [],
        kategoriList: [],
        loading: false,
        error: false,
        hasMore: true,
        offset: 0,
        limit: 12,

        filter: {
          search: new URLSearchParams(window.location.search).get('search') || '',
          kategori: '',
          lokal: null,
          sort: 'rekomendasi'
        },

        sortList: [{
            value: 'rekomendasi',
            label: 'Rekomendasi'
          },
          {
            value: 'terbaru',
            label: 'Terbaru'
          },
          {
            value: 'harga_asc',
            label: 'Harga Terendah'
          },
          {
            value: 'harga_desc',
            label: 'Harga Tertinggi'
          },
          {
            value: 'terlaris',
            label: 'Terlaris'
          }
        ],

        getSortLabel() {
          const found = this.sortList.find(s => s.value === this.filter.sort)
          return found ? found.label : 'Sort'
        },

        async init() {
          await this.loadKategori()
          await this.load()
        },

        async loadKategori() {
          try {
            const res = await API.get('/kategori')
            this.kategoriList = res.data || []
          } catch {
            this.kategoriList = []
          }
        },

        async load() {
          this.loading = true
          this.error = false
          try {
            const query = new URLSearchParams()

            query.append('limit', this.limit)
            query.append('offset', this.offset)

            if (this.filter.search) query.append('search', this.filter.search)
            if (this.filter.kategori) query.append('kategori', this.filter.kategori)
            if (this.filter.lokal) query.append('lokal', this.filter.lokal)
            if (this.filter.sort) query.append('sort', this.filter.sort)

            const res = await API.get('/produk/public?' + query.toString(), false)

            const data = res.data

            if (this.offset === 0) {
              this.items = data.data
            } else {
              this.items = [...this.items, ...data.data]
            }

            this.hasMore = data.has_more
            this.offset = data.next_offset

          } catch (err) {
            this.error = true
            if (this.offset === 0) this.items = []
          } finally {
            this.loading = false
          }
        },

        async loadMore() {
          if (!this.hasMore) return
          await this.load()
        },

        async resetLoad() {
          this.scrollToFilter()
          this.offset = 0
          this.hasMore = true
          await this.load()
        },

        scrollToFilter() {
          this.$nextTick(() => {
            const filterBar = document.getElementById('produk-filter-bar')
            if (!filterBar) return

            const navbar = document.querySelector('nav.sticky')
            const navbarHeight = navbar?.getBoundingClientRect().height || 64
            const targetTop = filterBar.getBoundingClientRect().top + window.scrollY - navbarHeight - 8

            window.scrollTo({
              top: Math.max(0, targetTop),
              behavior: 'smooth'
            })
          })
        },

        clearFilters() {
          this.filter.search = ''
          this.filter.kategori = ''
          this.filter.lokal = null
          this.filter.sort = 'rekomendasi'
          const url = new URL(window.location.href)
          url.searchParams.delete('search')
          window.history.replaceState({}, '', url)
          this.resetLoad()
        },

        goDetail(kode) {
          window.location.href = `${BASE_URL}/produk/${kode}`
        },

        async addToCart(item, e = null) {
          const card = e.target.closest('.group')
          const imgEl = card.querySelector('img')
          await Alpine.store('cart').add({
            ...item,
            jumlah: 1
          }, imgEl)
        }
      }
    }
  </script>