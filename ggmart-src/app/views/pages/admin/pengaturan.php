<?php
$user = $_SESSION['user'] ?? [];
$role = $user['role'] ?? 'admin';
?>

<div class="admin-page space-y-5 p-4 sm:p-5 lg:p-7">
  <div class="admin-page-header">
    <div>
      <p class="admin-eyebrow">Sistem</p>
      <h2 class="admin-page-title">Pengaturan</h2>
      <p class="admin-page-subtitle">Kelola informasi akun dan akses administrasi GG-Mart.</p>
    </div>
  </div>

  <div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1.5fr)_minmax(300px,1fr)]">
    <section class="admin-card overflow-hidden">
      <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
        <h3 class="text-base font-semibold text-slate-900">Informasi Akun</h3>
        <p class="mt-1 text-sm text-slate-500">Informasi akun yang sedang digunakan untuk panel admin.</p>
      </div>
      <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
        <div class="admin-setting-item">
          <span class="admin-setting-label">Nama</span>
          <span class="admin-setting-value"><?= htmlspecialchars($user['nama'] ?? '-') ?></span>
        </div>
        <div class="admin-setting-item">
          <span class="admin-setting-label">Email</span>
          <span class="admin-setting-value break-all"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
        </div>
        <div class="admin-setting-item">
          <span class="admin-setting-label">No. HP</span>
          <span class="admin-setting-value"><?= htmlspecialchars($user['no_hp'] ?? '-') ?></span>
        </div>
        <div class="admin-setting-item">
          <span class="admin-setting-label">Role</span>
          <span class="inline-flex w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold capitalize text-emerald-700">
            <?= htmlspecialchars($role) ?>
          </span>
        </div>
      </div>
    </section>

    <section class="admin-card overflow-hidden">
      <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
        <h3 class="text-base font-semibold text-slate-900">Akses Anda</h3>
        <p class="mt-1 text-sm text-slate-500">Ringkasan hak akses berdasarkan role.</p>
      </div>
      <div class="space-y-2 p-4 sm:p-5">
        <?php
        $permissions = [
          'Dashboard' => true,
          'Kasir' => $role === 'admin',
          'Pesanan' => $role === 'admin',
          'Kategori' => in_array($role, ['admin', 'pimpinan'], true),
          'Produk' => in_array($role, ['admin', 'pimpinan'], true),
          'Stok' => in_array($role, ['admin', 'pimpinan'], true),
          'User' => $role === 'admin',
          'Riwayat Transaksi' => in_array($role, ['admin', 'pimpinan'], true),
          'Pengaturan' => $role === 'admin',
        ];
        foreach ($permissions as $label => $allowed):
        ?>
          <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5">
            <span class="text-sm text-slate-700"><?= htmlspecialchars($label) ?></span>
            <span class="text-xs font-semibold <?= $allowed ? 'text-emerald-600' : 'text-slate-400' ?>">
              <?= $allowed ? 'Diizinkan' : 'Tidak tersedia' ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="admin-card xl:col-span-2">
      <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between sm:p-5">
        <div>
          <h3 class="text-base font-semibold text-slate-900">Keamanan Akun</h3>
          <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
            Perubahan password dan pengelolaan akun tetap mengikuti mekanisme autentikasi yang tersedia pada aplikasi. Jangan membagikan password atau session kepada pengguna lain.
          </p>
        </div>
        <a href="<?= BASE_URL ?>/profil" class="btn-secondary !w-auto shrink-0 text-center">Buka Profil</a>
      </div>
    </section>
  </div>
</div>
