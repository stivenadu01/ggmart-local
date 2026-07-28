<nav
  class="sticky top-0 z-50"
  x-data="{ mobileMenu: false, query: new URLSearchParams(location.search).get('search') || '', scrolled: false }"
  x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 10)">

  <!-- TOP BAR -->
  <div
    class="flex px-d py-2"
    :class="$store.ui.openSearch ? 'bg-white' : scrolled ? 'glass ' : 'bg-white'">

    <!-- LOGO -->
    <div class="flex w-1/2 md:w-1/3">
      <div class="flex-center gap-2 font-bold text-lg hover:cursor-pointer" @click="window.location = BASE_URL">
        <img :src="BASE_URL + '/assets/logo.png'" class="w-6" alt="Logo">
        GGMart
      </div>
    </div>

    <!-- DESKTOP MENU -->
    <div class="hidden md:flex-center w-1/3 gap-6 text-sm">
      <a :href="BASE_URL" class="link">Home</a>
      <a :href="BASE_URL + '/produk'" class="link">Produk</a>
      <a :href="BASE_URL + '/tentang'" class="link">Tentang Kami</a>
    </div>

    <!-- RIGHT -->
    <div class="w-1/2 md:w-1/3 flex-end">
      <div class="flex-center gap-4">

        <!-- SEARCH -->
        <button
          @click="() => {
          $store.ui.openSearch = !$store.ui.openSearch;
          $store.ui.openUserMenu = false;
          setTimeout(() => document.getElementById('search-input')?.focus(), 50)
          }"
          class="icon-btn">
          <svg viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
            <path class="clr-i-outline clr-i-outline-path-1" d="M16.33 5.05A10.95 10.95 0 1 1 5.39 16 11 11 0 0 1 16.33 5.05m0-2.05a13 13 0 1 0 13 13 13 13 0 0 0-13-13" />
            <path class="clr-i-outline clr-i-outline-path-2" d="m35 33.29-7.37-7.42-1.42 1.41 7.37 7.42A1 1 0 1 0 35 33.29" />
            <path fill="none" d="M0 0h36v36H0z" />
          </svg>
        </button>

        <!-- CART -->
        <div id="cart-icon" class="relative cursor-pointer" @click="location.href = BASE_URL + '/keranjang'">
          <button class="icon-btn">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="10" cy="19" r="1.5" stroke="#000" />
              <circle cx="17" cy="19" r="1.5" stroke="#000" />
              <path d="M3.5 4h2l3.504 11H17" stroke="#000" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M8.224 12.5 6.3 6.5h12.507a.5.5 0 0 1 .475.658l-1.667 5a.5.5 0 0 1-.474.342z" stroke="#000" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <span
            x-cloak
            x-show="$store.cart.totalQty > 0"
            x-text="$store.cart.totalQty"
            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 flex-center rounded-full">
          </span>
        </div>

        <!-- AUTH -->
        <div class="hidden md:block">
          <template x-if="!$store.auth.user">
            <div class="flex-center gap-2">
              <a :href="BASE_URL + '/login'" class="btn-primary py-1">Login</a>
              <a :href="BASE_URL + '/register'" class="btn-outline-primary py-1">Register</a>
            </div>
          </template>

          <template x-if="$store.auth.user">
            <div class="relative">
              <!-- TRIGGER -->
              <div
                class="flex items-center gap-2 cursor-pointer"
                @click="
                  $store.ui.openUserMenu = !$store.ui.openUserMenu;
                  $store.ui.openSearch = false;
                ">
                <div class="w-9 h-9 rounded-full bg-gray-200 flex-center text-sm font-medium">
                  <span x-text="$store.auth.user.email.charAt(0).toUpperCase()"></span>
                </div>
              </div>
              <!-- DROPDOWN -->
              <div
                x-show="$store.ui.openUserMenu"
                x-transition.origin.top.right
                x-cloak
                @mouseleave="$store.ui.openUserMenu = false"
                class="absolute -right-14 w-70 mt-2 card p-3 z-50">
                <!-- EMAIL -->
                <div class="px-5 py-2 text-xs text-gray-500 border-b truncate"
                  x-text="$store.auth.user.email">
                </div>
                <!-- MENU -->
                <a :href="BASE_URL + '/profil'"
                  class="block px-5 py-2 text-sm hover:bg-gray-100 rounded transition">
                  Profil Saya
                </a>
                <a :href="BASE_URL + '/transaksi'"
                  class="block px-5 py-2 text-sm hover:bg-gray-100 rounded transition">
                  Transaksi Saya
                </a>
                <template x-if="$store.auth.user.role === 'admin' || $store.auth.user.role === 'pimpinan'">
                  <a :href="BASE_URL + '/admin'"
                    class="block px-5 py-2 text-sm hover:bg-gray-100 rounded transition">
                    Dashboard Admin
                  </a>
                </template>
                <button
                  @click="$store.auth.logout()"
                  class="w-full text-left px-5 py-2 text-sm text-red-500 hover:bg-red-50 rounded transition">
                  Logout
                </button>
              </div>
            </div>
          </template>
        </div>

        <!-- HAMBURGER -->
        <button class="md:hidden icon-btn" @click="mobileMenu = true">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 15H9a1 1 0 0 1 0-2h12a1 1 0 0 1 0 2m0-6H3a1 1 0 1 1 0-2h18a1 1 0 0 1 0 2" fill="#231f20" />
          </svg>
        </button>

      </div>
    </div>
  </div>

  <!-- SEARCH PANEL -->
  <div
    x-show="$store.ui.openSearch"
    x-cloak
    x-transition
    @mouseleave="$store.ui.openSearch = false"
    @click.outside="$store.ui.openSearch = false"
    class="absolute left-0 top-full w-full bg-white shadow-sm z-50 pb-10">

    <div class="max-w-3xl mx-auto p-5 pl-10 relative">
      <button class="icon-left text-gray-700" @click="window.location = BASE_URL + '/produk?search=' + query">
        <svg viewBox=" 0 0 36 36" xmlns="http://www.w3.org/2000/svg">
          <path class="clr-i-outline clr-i-outline-path-1" d="M16.33 5.05A10.95 10.95 0 1 1 5.39 16 11 11 0 0 1 16.33 5.05m0-2.05a13 13 0 1 0 13 13 13 13 0 0 0-13-13" />
          <path class="clr-i-outline clr-i-outline-path-2" d="m35 33.29-7.37-7.42-1.42 1.41 7.37 7.42A1 1 0 1 0 35 33.29" />
          <path fill="none" d="M0 0h36v36H0z" />
        </svg>
      </button>

      <input
        id="search-input"
        x-model="query"
        @keydown.enter="window.location = BASE_URL + '/produk?search=' + query"
        type="text"
        placeholder="Cari produk di GGMart."
        class="input text-lg border-none focus:ring-0 font-bold" />

      <!-- TOMBOL X -->
      <button
        x-show="query.length > 0"
        @click="query = ''"
        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 transition">
        ✕
      </button>
    </div>
  </div>

  <!-- MOBILE MENU -->
  <div
    x-show="mobileMenu"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-60 bg-white">

    <!-- CLOSE -->
    <div class="flex-end">
      <button @click="mobileMenu = false" class="p-4 text-2xl font-light">
        ✕
      </button>
    </div>

    <!-- MENU -->
    <div class="px-6 flex flex-col gap-6">
      <a :href="BASE_URL" class="link-lg">Home</a>
      <a :href="BASE_URL+'/produk'" class="link-lg">Produk</a>
      <a :href="BASE_URL + '/tentang'" class="link-lg">Tentang Kami</a>
    </div>

    <!-- AUTH -->
    <div class="px-6 border-gray-200">
      <template x-if="!$store.auth.user">
        <div class="flex flex-col gap-4 pt-10">
          <a :href="BASE_URL + '/login'" class="link-lg underline">Login</a>
          <a :href="BASE_URL + '/register'" class="link-lg underline">Register</a>
        </div>
      </template>

      <template x-if="$store.auth.user">
        <div class="flex flex-col gap-6 mt-6">
          <a :href="BASE_URL + '/transaksi'" class="link-lg">Transaksi Saya</a>
          <a :href="BASE_URL + '/profil'" class="link-lg">Profil Saya</a>
          <template x-if="$store.auth.user.role === 'admin'">
            <a :href="BASE_URL + '/admin'" class="link-lg">Dashboard Admin</a>
          </template>
          <div class="flex-center flex-col gap-2 pt-10">
            <span x-text="$store.auth.user.email" class="text-sm font-light"></span>
            <button @click="$store.auth.logout()" class="btn-outline-danger">Logout</button>
          </div>
        </div>
      </template>
    </div>

  </div>
</nav>