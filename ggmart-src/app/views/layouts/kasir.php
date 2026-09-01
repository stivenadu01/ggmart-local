<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title ?? 'GGMart') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#22c55e">
  <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">

  <script defer src="https://unpkg.com/alpinejs"></script>
  <script src="<?= BASE_URL ?>/assets/js/api.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/store.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/storeAdmin.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
  <script src="<?= BASE_URL ?>/assets/js/utils.js"></script>
  <script>
    window.__USER__ = <?= json_encode($_SESSION['user'] ?? null) ?>;
    window.BASE_URL = '<?= BASE_URL ?>';
    window.NOMOR_WA = '<?= $_ENV['NOMOR_WA'] ?? '' ?>';
  </script>
</head>

<body class="admin-body bg-slate-50 text-slate-800">

  <?php $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin'; ?>
  <div class="min-h-screen" x-data>
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
          <img src="<?= BASE_URL ?>/assets/logo.png" alt="GG-Mart" class="h-9 w-9 rounded-xl object-contain ring-1 ring-slate-200">
          <div class="min-w-0">
            <div class="truncate font-poppins text-base font-bold text-slate-900">GG-Mart</div>
            <div class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">Kasir</div>
          </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
          <div class="hidden items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 sm:flex">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <span class="text-xs font-medium text-slate-600">Online</span>
          </div>

          <?php if ($isAdmin): ?>
            <a href="<?= BASE_URL ?>/admin" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
              Kembali ke Dashboard
            </a>
          <?php endif; ?>

          <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-2 py-1.5">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">
              <?= htmlspecialchars(strtoupper(substr($_SESSION['user']['nama'] ?? $_SESSION['user']['email'] ?? 'A', 0, 1))) ?>
            </span>
            <span class="hidden text-sm font-semibold text-slate-700 sm:inline">
              <?= htmlspecialchars($_SESSION['user']['nama'] ?? 'Kasir') ?>
            </span>
          </div>

          <button type="button" @click="$store.auth.logout()" class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100">
            Logout
          </button>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-3 py-4 sm:px-5 lg:px-8">
      <?= $content ?>
    </main>
  </div>

  <?php include __DIR__ . '/../partials/toast.php'; ?>

</body>

</html>