<div class="section-center bg-gray-100 px-4">
  <div class="card max-w-md w-full text-center p-8">

    <!-- LOGO -->
    <img :src="BASE_URL + '/assets/logo.png'" class="w-16 mx-auto mb-4">

    <!-- TITLE -->
    <h2 class="text-2xl font-bold mb-2">
      <?= $status === 'success' ? 'Berhasil!' : 'Gagal' ?>
    </h2>

    <!-- MESSAGE -->
    <p class="text-gray-600 mb-6">
      <?= $message ?>
    </p>

    <!-- ACTION -->
    <?php if ($status === 'success'): ?>
      <a :href="BASE_URL + '/login'" class="btn-primary block">
        Login Sekarang
      </a>
    <?php else: ?>
      <a :href="BASE_URL+'/login'" class="btn-secondary block">
        Kembali ke Login
      </a>
    <?php endif; ?>

  </div>
</div>