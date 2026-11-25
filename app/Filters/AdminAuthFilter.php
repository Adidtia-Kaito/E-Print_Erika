<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isAdminLoggedIn')) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (! empty($arguments) && in_array('OWNER', $arguments)) {
            if ($session->get('admin_role') !== 'OWNER') {
                return redirect()->to('/admin/dashboard')->with('error', 'Akses ditolak. Menu ini khusus Owner.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}