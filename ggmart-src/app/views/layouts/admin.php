<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'GGMart' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

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
    window.NOMOR_WA = '<?= $_ENV['NOMOR_WA'] ?>';
  </script>
</head>

<body class="bg-white">

  <div class="flex" style="height: 100dvh;">

    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- CONTENT -->
    <div class="flex-1 overflow-y-auto">
      <!-- MAIN -->
      <main>
        <?= $content ?>
      </main>
    </div>

  </div>

  <!-- TOAST -->
  <?php include __DIR__ . '/../partials/toast.php'; ?>

</body>

</html>