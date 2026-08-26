<?php
$user = $_SESSION['user'] ?? [];
$role = $user['role'] ?? 'admin';
$roleLabel = ucfirst($role);
$roleDescriptions = [
  'admin' => 'Memiliki akses penuh untuk mengelola operasional dan administrasi GG-Mart.',
  'pimpinan' => 'Memiliki akses untuk memantau produk, stok, transaksi, dan laporan.',
  'user' => 'Akun pengguna yang digunakan untuk aktivitas pelanggan.',
];
$roleDescription = $roleDescriptions[$role] ?? 'Hak akses mengikuti role akun yang sedang digunakan.';

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

$allowedCount = count(array_filter($permissions));
?>

<div class="admin-page space-y-5 p-4 sm:p-5 lg:p-7">
  <!-- HEADER -->
  <div class="admin-page-header">
    <div>
      <p class="admin-eyebrow">Sistem</p>
      <h2 class="admin-page-title">Pengaturan</h2>
      <p class="admin-page-subtitle">
        Lihat informasi akun, pahami hak akses, dan lanjutkan perubahan profil dari halaman yang sesuai.
      </p>
    </div>
  </div>

  <!-- ACCOUNT OVERVIEW -->
  <section class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1.4fr)_minmax(300px,0.6fr)]">
    <div class="admin-card overflow-hidden">
      <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h3 class="text-base font-semibold text-slate-900">Akun yang Sedang Digunakan</h3>
            <p class="mt-1 text-sm leading-5 text-slate-500">Informasi ini mengikuti sesi akun admin yang sedang aktif.</p>
          </div>
          <span class="status-success shrink-0">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            Aktif
          </span>
        </div>
      </div>

      <div class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5">
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
          <span class="status-neutral w-fit capitalize"><?= htmlspecialchars($roleLabel) ?></span>
        </div>
      </div>

      <div class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50/70 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="min-w-0">
          <p class="text-sm font-semibold text-slate-800">Perlu mengubah data akun?</p>
          <p class="form-help mt-0">Gunakan halaman Profil agar perubahan informasi akun tetap melalui alur yang tersedia.</p>
        </div>
        <a href="<?= BASE_URL ?>/profil" class="admin-action-primary w-full shrink-0 sm:w-auto">
          <span>Kelola Profil</span>
          <span aria-hidden="true">→</span>
        </a>
      </div>
    </div>

    <div class="admin-card overflow-hidden">
      <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Role Saat Ini</p>
        <h3 class="mt-1 text-xl font-bold capitalize text-slate-900"><?= htmlspecialchars($roleLabel) ?></h3>
      </div>
      <div class="space-y-4 p-4 sm:p-5">
        <p class="text-sm leading-6 text-slate-600"><?= htmlspecialchars($roleDescription) ?></p>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-3">
          <p class="text-xs font-semibold text-emerald-800">Ringkasan akses</p>
          <p class="mt-1 text-sm text-emerald-700">
            <strong><?= $allowedCount ?></strong> dari <?= count($permissions) ?> menu tersedia untuk akun ini.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- PERMISSIONS -->
  <section class="admin-card overflow-hidden">
    <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
      <h3 class="text-base font-semibold text-slate-900">Hak Akses Menu</h3>
      <p class="mt-1 text-sm leading-5 text-slate-500">Ringkasan menu yang dapat digunakan berdasarkan role akun saat ini.</p>
    </div>

    <div class="grid gap-2 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">
      <?php foreach ($permissions as $label => $allowed): ?>
        <div class="flex min-h-12 items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3.5 py-3">
          <span class="min-w-0 text-sm font-medium text-slate-700"><?= htmlspecialchars($label) ?></span>
          <?php if ($allowed): ?>
            <span class="status-success shrink-0">Diizinkan</span>
          <?php else: ?>
            <span class="status-neutral shrink-0">Tidak tersedia</span>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- SECURITY -->
  <section class="admin-card overflow-hidden">
    <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between sm:p-5">
      <div class="min-w-0">
        <p class="admin-eyebrow">Keamanan</p>
        <h3 class="text-base font-semibold text-slate-900">Keamanan Akun</h3>
        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
          Pengaturan ini tidak membuat mekanisme autentikasi baru. Untuk mengubah informasi profil, gunakan halaman Profil yang sudah tersedia.
        </p>
      </div>
      <a href="<?= BASE_URL ?>/profil" class="admin-action-secondary w-full shrink-0 sm:w-auto">
        Buka Profil
      </a>
    </div>

    <div class="grid gap-3 border-t border-slate-100 p-4 sm:grid-cols-3 sm:p-5">
      <div class="rounded-xl bg-slate-50 p-3.5">
        <p class="text-xs font-semibold text-slate-400">Password</p>
        <p class="mt-1 text-sm font-semibold text-slate-800">Kelola melalui alur akun yang tersedia</p>
      </div>
      <div class="rounded-xl bg-slate-50 p-3.5">
        <p class="text-xs font-semibold text-slate-400">Session</p>
        <p class="mt-1 text-sm font-semibold text-slate-800">Jangan bagikan session kepada pengguna lain</p>
      </div>
      <div class="rounded-xl bg-slate-50 p-3.5">
        <p class="text-xs font-semibold text-slate-400">Akses</p>
        <p class="mt-1 text-sm font-semibold text-slate-800">Role menentukan menu yang tersedia</p>
      </div>
    </div>
  </section>

  <!-- GUIDANCE -->
  <section class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 sm:p-5">
    <div class="flex gap-3">
      <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm" aria-hidden="true">i</span>
      <div>
        <h3 class="text-sm font-semibold text-emerald-900">Catatan</h3>
        <p class="mt-1 text-sm leading-6 text-emerald-800">
          Halaman Pengaturan saat ini berfungsi sebagai pusat informasi akun dan hak akses. Jangan mengubah role atau aturan akses dari sisi tampilan karena pembatasan akses tetap ditentukan oleh sistem.
        </p>
      </div>
    </div>
  </section>
</div>
