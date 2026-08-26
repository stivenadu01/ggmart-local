<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title><?= $title ?? $_ENV['APP_NAME'] ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#22c55e">
  <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">

  <script defer src="https://unpkg.com/alpinejs"></script>
  <script src="<?= BASE_URL ?>/assets/js/api.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/store.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/utils.js"></script>
  <script>
    window.__USER__ = <?= json_encode($_SESSION['user'] ?? null) ?>;
    window.BASE_URL = '<?= BASE_URL ?>';
    window.NOMOR_WA = '<?= $_ENV['NOMOR_WA'] ?>';
  </script>
</head>

<body class="min-w-0 bg-white text-slate-800">

  <?php include __DIR__ . '/../partials/navbar.php'; ?>

  <main x-data :class="$store.ui.openSearch || $store.ui.openUserMenu ? 'blur-sm' : ''" class="min-w-0">
    <?= $content ?>
  </main>

  <?php include __DIR__ . '/../partials/toast.php'; ?>

  <footer class="mt-16 border-t border-slate-200 bg-white sm:mt-20">
    <div class="user-container py-10 sm:py-12">
      <div class="grid grid-cols-1 gap-8 md:grid-cols-4 md:gap-10">

        <div class="md:col-span-1">
          <div class="mb-3 flex items-center gap-2 font-poppins text-lg font-bold text-slate-900">
            <img :src="BASE_URL + '/assets/logo.png'" class="h-7 w-7 object-contain" alt="Logo GG MART">
            <span>GG MART</span>
          </div>
          <p class="max-w-sm text-sm leading-6 text-slate-500">
            Marketplace resmi Sinode GMIT untuk mendukung produk lokal dan UMKM di Nusa Tenggara Timur.
          </p>
        </div>

        <div>
          <h3 class="mb-3 text-sm font-bold text-slate-900">Navigasi</h3>
          <ul class="space-y-1">
            <li><a :href="BASE_URL" class="user-menu-item">Beranda</a></li>
            <li><a :href="BASE_URL + '/produk'" class="user-menu-item">Produk</a></li>
            <li><a :href="BASE_URL + '/tentang'" class="user-menu-item">Tentang</a></li>
            <li><a :href="BASE_URL + '/faq'" class="user-menu-item">FAQ</a></li>
          </ul>
        </div>

        <div>
          <h3 class="mb-3 text-sm font-bold text-slate-900">Mitra</h3>
          <ul class="space-y-1">
            <li><a href="#" class="user-menu-item">Gabung Mitra</a></li>
            <li><a href="#" class="user-menu-item">Syarat &amp; Ketentuan</a></li>
            <li><a href="#" class="user-menu-item">Kebijakan</a></li>
          </ul>
        </div>

        <div>
          <h3 class="mb-3 text-sm font-bold text-slate-900">Kontak</h3>
          <ul class="space-y-1 text-sm text-slate-500">
            <li class="px-3 py-2.5">Email: ggmart@gmit.or.id</li>
            <li>
              <a :href="`https://wa.me/${$store.utils.NOMOR_WA}?text=Halo%20Admin%20GGMart%2C%20saya%20ingin%20bertanya...`" class="user-menu-item">
                WhatsApp Admin
              </a>
            </li>
            <li class="px-3 py-2.5">Kupang, NTT</li>
          </ul>
        </div>

      </div>

      <div class="mt-8 border-t border-slate-100 pt-6 text-center text-xs text-slate-500 sm:mt-10 sm:text-sm">
        © <span x-text="new Date().getFullYear()"></span> GG MART - Sinode GMIT. All rights reserved.
      </div>
    </div>
  </footer>

</body>

</html>
