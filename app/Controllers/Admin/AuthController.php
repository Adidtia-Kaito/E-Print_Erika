<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('isAdminLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login');
    }

    public function attemptLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('kata_sandi');

        $adminModel = new AdminModel();
        $admin      = $adminModel->where('username', $username)->first();

        if (! $admin || ! password_verify($password, $admin['kata_sandi'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau kata sandi salah.');
        }

        if ((int) $admin['status_aktif'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Hubungi Owner.');
        }

        session()->set([
            'isAdminLoggedIn' => true,
            'id_admin'        => $admin['id_admin'],
            'admin_nama'      => $admin['nama_lengkap'],
            'admin_username'  => $admin['username'],
            'admin_role'      => $admin['role'],
        ]);

        return redirect()->to('/admin/dashboard');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/admin/login')->with('success', 'Anda telah keluar dari panel.');
    }
}