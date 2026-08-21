<?php

class ApiUserController
{
  public function __construct()
  {
    model('User');
  }

  // === GET LIST USER ===
  public function list()
  {
    try {
      $page   = max(1, intval($_GET['page'] ?? 1));
      $limit  = max(1, intval($_GET['limit'] ?? 10));
      $search = trim($_GET['search'] ?? '');
      $role   = trim($_GET['role'] ?? '');

      [$data, $total] = getUserList($page, $limit, $search, $role);

      return response([
        'success' => true,
        'data' => $data,
        'pagination' => [
          'page' => $page,
          'limit' => $limit,
          'total' => intval($total),
          'total_pages' => ($limit > 0) ? ceil($total / $limit) : 1
        ]
      ]);
    } catch (Exception $e) {
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }

  // === GET DETAIL USER ===
  public function detail()
  {
    try {
      $currentUser = $_SESSION['user'] ?? null;
      $id_user = $_GET['id'] ?? null;
      if (!$currentUser) throw new Exception('Unauthorized', 401);

      // Admin/pimpinan boleh melihat detail user untuk kebutuhan administrasi/read-only.
      // User biasa hanya boleh melihat dirinya sendiri.
      if (!in_array($currentUser['role'], ['admin', 'pimpinan'], true)) {
        $id_user = $currentUser['id_user'];
      } elseif (!$id_user) {
        throw new Exception('ID user tidak valid', 400);
      }

      $user = findUser($id_user);
      if (!$user) throw new Exception('User tidak ditemukan', 404);

      return response(['success' => true, 'data' => $user]);
    } catch (Exception $e) {
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }



  // === TAMBAH USER (ADMIN) ===
  public function tambah()
  {
    try {
      $input = input();

      if (empty($input['nama']) || empty($input['email']) || empty($input['password']) || empty($input['rePassword'])) {
        throw new Exception('Nama, email, dan password wajib diisi.', 422);
      }

      if ($input['password'] !== $input['rePassword']) {
        throw new Exception('Konfirmasi password tidak cocok.', 422);
      }

      if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid.', 422);
      }

      if (findUserByEmail($input['email'])) {
        throw new Exception('Email sudah digunakan oleh user lain.', 409);
      }

      if (empty($input['role'])) $input['role'] = 'user';
      if (!in_array($input['role'], ['admin', 'pimpinan', 'user'], true)) {
        throw new Exception('Role user tidak valid.', 422);
      }

      if (!tambahUser($input)) {
        throw new Exception('Gagal menambahkan user.', 500);
      }

      return response(['success' => true, 'message' => 'User berhasil ditambahkan'], 201);
    } catch (Exception $e) {
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }

  // === UPDATE USER ===
  public function ubah()
  {
    try {
      $input = input();
      $currentUser = $_SESSION['user'] ?? null;
      if (!$currentUser) throw new Exception('Unauthorized', 401);

      $isAdmin = ($currentUser['role'] === 'admin');
      // User biasa tidak boleh menentukan target user maupun mengubah role.
      if (!$isAdmin) {
        $id_user = (int)$currentUser['id_user'];
        $input['id_user'] = $id_user;
        $input['role'] = $currentUser['role'];
      } else {
        $id_user = isset($input['id_user']) ? (int)$input['id_user'] : 0;
        if (!$id_user) throw new Exception('ID user wajib diisi.', 400);
      }

      if (empty($input['nama']) || empty($input['email'])) {
        throw new Exception('Nama dan email wajib diisi.', 422);
      }
      if ($isAdmin && empty($input['role'])) {
        throw new Exception('Role wajib diisi.', 422);
      }
      if (!in_array($input['role'], ['admin', 'pimpinan', 'user'], true)) {
        throw new Exception('Role user tidak valid.', 422);
      }

      if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid.', 422);
      }

      $user = findUser($id_user);
      if (!$user) throw new Exception('User tidak ditemukan.', 404);
      if ($isAdmin && (int)$id_user === (int)$currentUser['id_user'] && $input['role'] !== 'admin') {
        throw new Exception('Admin yang sedang aktif tidak boleh menurunkan role dirinya sendiri.', 400);
      }

      $exist = findUserByEmail($input['email']);
      if ($exist && $exist['id_user'] != $id_user) {
        throw new Exception('Email sudah digunakan oleh user lain.', 409);
      }

      if (!empty($input['password']) && isset($input['rePassword']) && $input['password'] !== $input['rePassword']) {
        throw new Exception('Konfirmasi password tidak cocok.', 422);
      }

      if (!editUser($id_user, $input)) {
        throw new Exception('User gagal diupdate.', 500);
      }

      return response(['success' => true, 'message' => 'User berhasil diupdate']);
    } catch (Exception $e) {
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }

  // === DELETE USER ===
  public function hapus()
  {
    try {
      $input = request();
      $id_user = $input['id_user'] ?? null;

      if (!$id_user) throw new Exception('ID user wajib diisi.', 400);

      $currentUser = $_SESSION['user'] ?? null;
      if (!$currentUser || $currentUser['role'] !== 'admin') {
        throw new Exception('Akses ditolak.', 403);
      }
      if ((int)$id_user === (int)$currentUser['id_user']) {
        throw new Exception('Akun yang sedang digunakan tidak boleh dihapus.', 400);
      }

      $user = findUser($id_user);
      if (!$user) throw new Exception('User tidak ditemukan.', 404);

      if (!hapusUser($id_user)) {
        throw new Exception('Gagal menghapus user.', 500);
      }

      return response(['success' => true, 'message' => 'User berhasil dihapus']);
    } catch (Exception $e) {
      return response(['success' => false, 'message' => $e->getMessage()], $e->getCode() ?: 500);
    }
  }

  public function verify()
  {
    $id = params('id');
    try {
      if (!verifyUser($id)) {
        throw new Exception("Gagal verifikasi akun", 1);
      };
      return response(['success' => true]);
    } catch (\Throwable $th) {
      return response(['success' => false, 'message' => $th->getMessage()], $th->getCode());
    }
  }
}
