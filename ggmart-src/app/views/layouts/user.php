<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title><?= $title ?? $_ENV['APP_NAME'] ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<body>

  <?php include __DIR__ . '/../partials/navbar.php'; ?>

  <main x-data :class="$store.ui.openSearch || $store.ui.openUserMenu ? 'blur-sm' : ''">
    <?= $content ?>
  </main>

  <!-- TOAST -->
  <?php include __DIR__ . '/../partials/toast.php'; ?>


  <!-- FOOTER -->
  <footer class="mt-20 border-t border-gray-200">

    <div class="px-d py-10">

      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        <!-- BRAND -->
        <div>
          <h3 class="font-poppins text-lg font-semibold mb-3">
            GG MART
          </h3>
          <p class="text-sm text-gray-600 leading-relaxed">
            Marketplace resmi Sinode GMIT untuk mendukung produk lokal dan UMKM
            di Nusa Tenggara Timur.
          </p>
        </div>

        <!-- NAVIGASI -->
        <div>
          <h4 class="font-semibold mb-3">Navigasi</h4>
          <ul class="space-y-2 text-sm text-gray-600">
            <li><a :href="BASE_URL" class="link">Beranda</a></li>
            <li><a :href="BASE_URL + '/produk'" class="link">Produk</a></li>
            <li><a :href="BASE_URL + '/tentang'" class="link">Tentang</a></li>
            <li><a :href="BASE_URL + '/faq'" class="link">FAQ</a></li>
          </ul>
        </div>

        <!-- MITRA -->
        <div>
          <h4 class="font-semibold mb-3">Mitra</h4>
          <ul class="space-y-2 text-sm text-gray-600">
            <li><a href="#" class="link">Gabung Mitra</a></li>
            <li><a href="#" class="link">Syarat & Ketentuan</a></li>
            <li><a href="#" class="link">Kebijakan</a></li>
          </ul>
        </div>

        <!-- KONTAK -->
        <div>
          <h4 class="font-semibold mb-3">Kontak</h4>
          <ul class="space-y-2 text-sm text-gray-600">
            <li>Email: ggmart@gmit.or.id</li>
            <li>
              <a
                :href="`https://wa.me/${$store.utils.NOMOR_WA}?text=Halo%20Admin%20GGMart%2C%20saya%20ingin%20bertanya...`"
                class="link">
                WhatsApp Admin
              </a>
            </li>
            <li>Kupang, NTT</li>
          </ul>
        </div>

      </div>

      <!-- BOTTOM -->
      <div class="mt-10 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
        © <span x-text="new Date().getFullYear()"></span> GG MART - Sinode GMIT. All rights reserved.
      </div>

    </div>
  </footer>

</body>

</html>