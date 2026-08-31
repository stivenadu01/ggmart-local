<div class="py-10">

  <!-- HERO -->
  <div class="px-d text-center max-w-5xl mx-auto">

    <h1 class="mb-6">
      GGMart Gerakan Beli Produk Lokal
    </h1>

    <!-- BANNER -->
    <div class="max-w-4xl max-h-96 overflow-hidden mx-auto mb-6 flex-center">
      <img :src="BASE_URL + '/assets/banner-ggmart.png'" alt="GGMart | Sinode GMIT" class="w-full h-auto object-center" />
    </div>

    <p class="text-gray-600 mb-6">
      GGMart Mendukung pemasaran produk jemaat dan UMKM lokal di Nusa Tenggara Timur.
    </p>

    <a :href="BASE_URL + '/produk'" class="btn-primary w-auto px-6 py-2 my-2">
      Mulai Belanja
    </a>

  </div>

  <!-- PRODUK UNGGULAN -->
  <div class="mt-24" x-data="homePage()">

    <h2 class="text-center text-3xl!">
      Produk Lokal Unggulan
    </h2>

    <!-- STATE -->
    <div x-show="loading" x-cloak class="px-d pt-8">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6" aria-label="Memuat produk unggulan">
        <template x-for="i in 3" :key="i">
          <div class="animate-pulse space-y-4">
            <div class="aspect-4/3 rounded-xl bg-gray-100"></div>
            <div class="mx-auto h-6 w-2/3 rounded bg-gray-100"></div>
            <div class="mx-auto h-4 w-5/6 rounded bg-gray-100"></div>
          </div>
        </template>
      </div>
    </div>

    <div x-show="error" x-cloak class="px-d pt-8">
      <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-6 text-center">
        <p class="font-semibold text-red-800">Produk unggulan belum dapat dimuat.</p>
        <p class="mt-1 text-sm text-red-700">Periksa koneksi lalu coba lagi.</p>
        <button @click="load(); startAutoSlide()" class="btn-outline-primary btn-rounded mt-4 w-auto px-5">Coba Lagi</button>
      </div>
    </div>

    <div x-show="!loading && !error && items.length === 0" x-cloak class="px-d pt-8">
      <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-5 py-8 text-center">
        <p class="font-semibold text-gray-800">Belum ada produk unggulan.</p>
        <p class="mt-1 text-sm text-gray-500">Silakan lihat katalog untuk menemukan produk GG MART lainnya.</p>
        <a :href="BASE_URL + '/produk'" class="btn-primary btn-rounded mt-4 w-auto px-5">Lihat Produk</a>
      </div>
    </div>

    <!-- WRAPPER -->
    <div x-show="!loading && !error && items.length > 0" class="relative overflow-hidden">

      <!-- LEFT BUTTON -->
      <button
        @mouseenter="clearInterval(interval)"
        @click="prev()"
        class="absolute left-1 sm:left-2 top-1/2 -translate-y-1/2 z-10
             bg-white/60 backdrop-blur-md hover:bg-white/80
             w-11 h-11 sm:w-14 sm:h-14 rounded-full shadow">
        ‹
      </button>

      <!-- RIGHT BUTTON -->
      <button
        @mouseenter="clearInterval(interval)"
        @click="next()"
        class="absolute right-1 sm:right-2 top-1/2 -translate-y-1/2 z-10
             bg-white/60 backdrop-blur-md hover:bg-white/80
             w-11 h-11 sm:w-14 sm:h-14 rounded-full shadow">
        ›
      </button>

      <!-- SLIDER -->
      <div
        class="flex ease-out"
        :class="!disableTransition && 'transition-transform duration-500'"
        :style="`transform: translateX(-${index * slideWidth}%)`">

        <template x-for="(item, i) in items" :key="i">
          <div
            :style="`width: ${slideWidth}%`"
            class="shrink-0 pt-10 pb-4 md:px-8 xl:px-12">
            <!-- CARD -->
            <div class="px-4 text-center">
              <!-- CONTENT -->
              <h3 class="text-2xl! mb-1" x-text="item.nama_produk"></h3>
              <p class="line-clamp-3 whitespace-pre-wrap" x-text="item.tagline"></p>
              <div class="mt-4 flex-center gap-4">
                <span class="text-primary-dark text-lg font-light font-poppins"
                  x-text="$store.utils.formatRupiah(item.harga_jual)"></span>

                <a :href="`${BASE_URL}/produk/${item.kode_produk}`" class="btn-rounded btn-primary">Selengkapnya</a>
              </div>

              <!-- IMAGE -->
              <div
                @mouseenter="clearInterval(interval)"
                @mouseleave="startAutoSlide()"
                class="hover:scale-105 transition duration-300 h-full rounded-xl mb-4 overflow-hidden cursor-pointer" @click="location.href = `${BASE_URL}/produk/${item.kode_produk}`">
                <img
                  :src=" BASE_URL+ '/uploads'+ item.gambar"
                  class="w-full h-full object-cover">
              </div>
            </div>
          </div>
        </template>

      </div>
    </div>
  </div>

  <!-- LOKASI -->
  <section id="lokasi" class="mt-14 py-12 px-d">
    <h2 class="text-center mb-10 text-3xl!">Lokasi GG MART</h2>
    <div class="grid lg:grid-cols-2 gap-8 items-start max-w-6xl mx-auto">
      <!-- MAP -->
      <div class="w-full overflow-hidden rounded-2xl border border-gray-200">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3927.2451329903033!2d123.61511911068168!3d-10.160719039911163!2m3!1f0!2f0!3f0!2m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2c568366993c75cb%3A0x95756d2b31e5b80e!2sGG%20Mart%20Sinode!5e0!3m2!1sid!2sid"
          class="w-full h-87.5 lg:h-105"
          style="border:0"
          loading="lazy"></iframe>
      </div>

      <!-- INFO -->
      <div class="space-y-6 text-sm text-gray-700">

        <div>
          <p class="font-semibold text-base text-gray-900">Alamat</p>
          <p class="mt-1">
            GG MART, Jl. S. K. Lerik<br>
            Kota Baru, Kec. Klp. Lima, Kota Kupang, NTT 85228
          </p>
        </div>

        <div>
          <p class="font-semibold text-base text-gray-900">Jam Operasional</p>
          <p class="mt-1">Senin – Jumat · 09.00 – 17.00 WITA</p>
        </div>

        <div>
          <p class="font-semibold text-base text-gray-900">Kontak</p>
          <p class="mt-1">
            <a :href="`https://wa.me/${NOMOR_WA}`" class="link" x-text="NOMOR_WA"></a>
          </p>
        </div>

        <a
          href="https://maps.app.goo.gl/4su1dovCZ515VsWZ8"
          target="_blank"
          class="btn-primary w-auto btn-rounded py-3">
          Buka di Google Maps
        </a>

      </div>
    </div>
  </section>

</div>



<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('homePage', () => ({
      items: [],
      loading: false,
      error: false,
      index: 0,

      disableTransition: false,
      interval: null,

      get perView() {
        return window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1
      },

      get slideWidth() {
        return 100 / this.perView;
      },

      async init() {
        await this.load()
        this.startAutoSlide()
      },

      async load() {
        this.loading = true
        this.error = false
        try {
          const res = await API.get('/produk/landing');
          const originalItems = res.data ?? [];
          if (originalItems.length > 0) {
            // Ambil beberapa item dari belakang untuk ditaruh di paling depan (untuk prev)
            const headClones = originalItems.slice(-this.perView).map(item => ({
              ...item,
              _clone: true
            }));
            // Ambil beberapa item dari depan untuk ditaruh di paling belakang (untuk next)
            const tailClones = originalItems.slice(0, this.perView).map(item => ({
              ...item,
              _clone: true
            }));
            // Gabungkan: [Klon Belakang] + [Item Asli] + [Klon Depan]
            this.items = [...headClones, ...originalItems, ...tailClones];
            // Karena ada klon di depan, index awal harus dimulai dari posisi setelah klon tersebut
            this.disableTransition = true
            this.index = this.perView
            setTimeout(() => {
              this.disableTransition = false
            }, 50);
          }
        } catch (err) {
          this.items = [];
          this.error = true;
        } finally {
          this.loading = false
        }
      },


      next() {
        this.index++
        // Jika sampai pada batas klon kanan
        if (this.index >= this.items.length - this.perView) {
          setTimeout(() => {
            this.disableTransition = true
            this.index = this.perView // Reset ke posisi awal item asli
            setTimeout(() => {
              this.disableTransition = false
            }, 50)
          }, 500)
        }
      },

      prev() {
        this.index--
        // Jika masuk ke area klon kiri (melewati batas item asli terkiri)
        if (this.index < (this.perView - this.perView)) {
          setTimeout(() => {
            this.disableTransition = true
            // Lompat instan ke posisi item asli paling kanan
            this.index = this.items.length - (this.perView * 2)
            setTimeout(() => {
              this.disableTransition = false
            }, 50)
          }, 500) // Sesuaikan dengan durasi transition slider (500ms)
        }
      },

      startAutoSlide() {
        this.interval = setInterval(() => {
          this.next()
        }, 3000)
      },
    }));

    Alpine.data("faqSection", () => ({
      activeIndex: null,

      faqItems: [{
          q: "Apa itu GG MART?",
          a: `GG MART adalah marketplace dan outlet resmi milik Sinode GMIT yang berfokus pada pemasaran dan distribusi produk lokal jemaat serta UMKM di NTT.
        <br><br>
        GG MART hadir sebagai sarana pemberdayaan ekonomi jemaat.
        <br><br>
        <a href='${BASE_URL}/tentang' class='text-green-600 hover:underline'>Tentang Kami</a>.`
        },
        {
          q: "Siapa yang bisa menjadi mitra GG MART?",
          a: `Mitra GG MART terbuka bagi jemaat GMIT, pelaku UMKM lokal, komunitas usaha, maupun kelompok binaan gereja.`
        },
        {
          q: "Produk apa saja yang dijual di GG MART?",
          a: `Produk unggulan lokal seperti hasil pertanian, pangan olahan, UMKM, dan kebutuhan rumah tangga.`
        },
        {
          q: "Bagaimana cara membeli produk?",
          a: `Pembelian dapat dilakukan online melalui website atau langsung di outlet GG MART.`
        },
        {
          q: "Bagaimana cara menjadi mitra pemasok?",
          a: `Silakan daftar melalui menu Gabung Mitra dan hubungi admin untuk verifikasi.`
        },
        {
          q: "Apakah GG MART hanya menjual produk lokal?",
          a: `Fokus utama tetap produk lokal NTT, namun produk luar tetap bisa masuk jika memenuhi standar.`
        },
        {
          q: "Apakah GG MART punya toko fisik?",
          a: `Ya, GG MART memiliki outlet fisik dan terus dikembangkan.`
        },
        {
          q: "Bagaimana cara menghubungi admin?",
          a: `Hubungi via WhatsApp atau datang langsung ke kantor Sinode GMIT.`
        }
      ],

      toggle(i) {
        this.activeIndex = this.activeIndex === i ? null : i;
      }
    }));
  });
</script>