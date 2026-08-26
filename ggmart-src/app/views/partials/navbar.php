<nav
  class="sticky top-0 z-50"
  x-data="{ mobileMenu: false, query: new URLSearchParams(location.search).get('search') || '', scrolled: false }"
  x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 10)">

  <div
    class="border-b border-slate-100 transition"
    :class="$store.ui.openSearch || scrolled ? 'bg-white/95 shadow-sm backdrop-blur-md' : 'bg-white'">
    <div class="user-container flex min-h-16 items-center justify-between gap-4">

      <a :href="BASE_URL" class="flex shrink-0 items-center gap-2 font-poppins text-lg font-bold text-slate-900" aria-label="GG MART Beranda">
        <img :src="BASE_URL + '/assets/logo.png'" class="h-7 w-7 object-contain" alt="Logo GG MART">
        <span>GG MART</span>
      </a>

      <div class="hidden flex-1 items-center justify-center gap-5 md:flex">
        <a :href="BASE_URL" class="user-nav-link">Home</a>
        <a :href="BASE_URL + '/produk'" class="user-nav-link">Produk</a>
        <a :href="BASE_URL + '/tentang'" class="user-nav-link">Tentang Kami</a>
      </div>

      <div class="flex shrink-0 items-center gap-1 sm:gap-2">

        <button
          type="button"
          @click="() => {
            $store.ui.openSearch = !$store.ui.openSearch;
            $store.ui.openUserMenu = false;
            setTimeout(() => document.getElementById('search-input')?.focus(), 50)
          }"
          class="user-icon-button"
          aria-label="Cari produk">
          <svg class="h-5 w-5" viewBox="0 0 36 36" fill="none" aria-hidden="true">
            <path stroke="currentColor" stroke-width="2" d="M16.33 5.05A10.95 10.95 0 1 1 5.39 16 11 11 0 0 1 16.33 5.05m0-2.05a13 13 0 1 0 13 13 13 13 0 0 0-13-13m9.8 20.82 7.37 7.42" />
          </svg>
        </button>

        <a id="cart-icon" :href="BASE_URL + '/keranjang'" class="relative user-icon-button" aria-label="Keranjang">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="10" cy="19" r="1.5" stroke="currentColor" />
            <circle cx="17" cy="19" r="1.5" stroke="currentColor" />
            <path d="M3.5 4h2l3.504 11H17" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M8.224 12.5 6.3 6.5h12.507a.5.5 0 0 1 .475.658l-1.667 5a.5.5 0 0 1-.474.342z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <span
            x-cloak
            x-show="$store.cart.totalQty > 0"
            x-text="$store.cart.totalQty"
            class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
          </span>
        </a>

        <div class="hidden md:block">
          <template x-if="!$store.auth.user">
            <div class="flex items-center gap-2">
              <a :href="BASE_URL + '/login'" class="user-action-primary min-h-9 px-3 py-2">Login</a>
              <a :href="BASE_URL + '/register'" class="user-action-outline min-h-9 px-3 py-2">Register</a>
            </div>
          </template>

          <template x-if="$store.auth.user">
            <div class="relative">
              <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-200"
                @click="$store.ui.openUserMenu = !$store.ui.openUserMenu; $store.ui.openSearch = false"
                :aria-expanded="$store.ui.openUserMenu"
                aria-label="Buka menu akun">
                <span x-text="$store.auth.user.email.charAt(0).toUpperCase()"></span>
              </button>

              <div
                x-show="$store.ui.openUserMenu"
                x-transition.origin.top.right
                x-cloak
                @click.outside="$store.ui.openUserMenu = false"
                class="absolute right-0 mt-2 w-64 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                <div class="truncate border-b border-slate-100 px-3 py-2 text-xs text-slate-500" x-text="$store.auth.user.email"></div>
                <div class="mt-1 space-y-0.5">
                  <a :href="BASE_URL + '/profil'" class="user-menu-item">Profil Saya</a>
                  <a :href="BASE_URL + '/transaksi'" class="user-menu-item">Transaksi Saya</a>
                  <template x-if="$store.auth.user.role === 'admin' || $store.auth.user.role === 'pimpinan'">
                    <a :href="BASE_URL + '/admin'" class="user-menu-item">Dashboard Admin</a>
                  </template>
                  <button type="button" @click="$store.auth.logout()" class="user-menu-item w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700">Logout</button>
                </div>
              </div>
            </div>
          </template>
        </div>

        <button type="button" class="user-icon-button md:hidden" @click="mobileMenu = true" aria-label="Buka menu">
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <div
    x-show="$store.ui.openSearch"
    x-cloak
    x-transition
    @click.outside="$store.ui.openSearch = false"
    class="absolute left-0 top-full z-50 w-full border-b border-slate-200 bg-white shadow-lg">
    <div class="user-container py-4 sm:py-5">
      <div class="relative">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        </svg>
        <input
          id="search-input"
          x-model="query"
          @keydown.enter="window.location = BASE_URL + '/produk?search=' + encodeURIComponent(query)"
          type="search"
          autocomplete="off"
          placeholder="Contoh: Beras Merah Pantar"
          class="input h-12 pl-10 pr-10 text-base" />
        <button
          type="button"
          x-show="query.length > 0"
          @click="query = ''"
          class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
          aria-label="Hapus pencarian">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <p class="form-help">Tekan Enter untuk melihat hasil pencarian produk.</p>
    </div>
  </div>

  <div
    x-show="mobileMenu"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-[60] overflow-y-auto bg-white md:hidden">
    <div class="user-container py-5">
      <div class="flex items-center justify-between">
        <a :href="BASE_URL" @click="mobileMenu = false" class="flex items-center gap-2 font-poppins text-lg font-bold text-slate-900">
          <img :src="BASE_URL + '/assets/logo.png'" class="h-7 w-7 object-contain" alt="Logo GG MART">
          GG MART
        </a>
        <button type="button" @click="mobileMenu = false" class="user-icon-button" aria-label="Tutup menu">×</button>
      </div>

      <div class="mt-8 space-y-1 border-t border-slate-100 pt-5">
        <a :href="BASE_URL" @click="mobileMenu = false" class="user-menu-item text-base font-semibold">Home</a>
        <a :href="BASE_URL + '/produk'" @click="mobileMenu = false" class="user-menu-item text-base font-semibold">Produk</a>
        <a :href="BASE_URL + '/tentang'" @click="mobileMenu = false" class="user-menu-item text-base font-semibold">Tentang Kami</a>
        <a :href="BASE_URL + '/keranjang'" @click="mobileMenu = false" class="user-menu-item text-base font-semibold">Keranjang</a>
      </div>

      <div class="mt-6 border-t border-slate-100 pt-5">
        <template x-if="!$store.auth.user">
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <a :href="BASE_URL + '/login'" class="user-action-primary user-mobile-action">Login</a>
            <a :href="BASE_URL + '/register'" class="user-action-outline user-mobile-action">Register</a>
          </div>
        </template>

        <template x-if="$store.auth.user">
          <div class="space-y-1">
            <div class="mb-3 rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-600" x-text="$store.auth.user.email"></div>
            <a :href="BASE_URL + '/transaksi'" class="user-menu-item text-base">Transaksi Saya</a>
            <a :href="BASE_URL + '/profil'" class="user-menu-item text-base">Profil Saya</a>
            <template x-if="$store.auth.user.role === 'admin' || $store.auth.user.role === 'pimpinan'">
              <a :href="BASE_URL + '/admin'" class="user-menu-item text-base">Dashboard Admin</a>
            </template>
            <button type="button" @click="$store.auth.logout()" class="user-action-danger mt-4 w-full">Logout</button>
          </div>
        </template>
      </div>
    </div>
  </div>
</nav>
