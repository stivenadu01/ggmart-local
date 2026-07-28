<div class="py-10" x-data="produkPage()">

  <!-- HEADER -->
  <div class="px-d mb-5">
    <h1>Katalog Produk</h1>
    <p class="text-gray-500 text-sm">Temukan produk terbaik dari UMKM lokal</p>
  </div>

  <!-- FILTER -->
  <div class="sticky top-11 md:top-[52.399px] z-49 glass border-b border-black/5" x-data="{ openKategori: false, openSort: false}">
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

    <!-- GRID PRODUK -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">

      <template x-for="item in items" :key="item.kode_produk">
        <div class="group min-h-48 md:min-h-64">

          <div class="rounded-2xl overflow-hidden shadow hover:shadow-lg transition bg-white flex flex-col">

            <!-- IMAGE -->
            <div class="flex items-center justify-center cursor-pointer"
              @click="goDetail(item.kode_produk)">
              <img
                loading="lazy"
                decoding="async"
                x-ref="img_produk"
                :src="item.gambar ? BASE_URL + '/uploads' + item.gambar : '/assets/no-image.png'"
                class="w-full h-auto object-contain group-hover:scale-105 transition">
            </div>

            <!-- CONTENT -->
            <div class="p-3 space-y-1.5 flex-1">

              <div class="text-sm font-medium line-clamp-2"
                x-text="item.nama_produk"></div>

              <div class="text-primary font-semibold font-poppins"
                x-text="$store.utils.formatRupiah(item.harga_jual)">
              </div>

              <div
                x-show="item.asal_produk"
                x-text="item.asal_produk"
                class="text-xs text-gray-500 italic line-clamp-2">
              </div>

            </div>

            <!-- ACTION -->
            <div class="flex gap-2 p-3">
              <button
                @click="addToCart(item,  $event)"
                class="btn-primary btn-rounded whitespace-nowrap text-xs px-3 py-1">
                + Keranjang
              </button>

              <button
                @click="goDetail(item.kode_produk)"
                class="btn-outline btn-rounded text-xs px-3 py-1">
                Detail
              </button>
            </div>

          </div>

        </div>
      </template>

    </div>

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

</div>

<script>
  function produkPage() {
    return {
      items: [],
      kategoriList: [],
      loading: false,
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
            console.log('data : ', data.data);
            this.items = [...this.items, ...data.data]
          }

          this.hasMore = data.has_more
          this.offset = data.next_offset
          console.log('items :', this.items);

        } finally {
          this.loading = false
        }
      },

      async loadMore() {
        if (!this.hasMore) return
        await this.load()
      },

      async resetLoad() {
        scrollTo({
          top: 140,
          behavior: 'smooth'
        })
        this.offset = 0
        this.hasMore = true
        await this.load()
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