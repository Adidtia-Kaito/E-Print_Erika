<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class OwnerController extends BaseController
{
    public function laporan()
    {
        $db = \Config\Database::connect();

        $pendapatanRow = $db->table('pesanan')
            ->selectSum('total_bayar')
            ->where('status_pembayaran', 'LUNAS')
            ->get()
            ->getRowArray();
        $totalPendapatan = (float) ($pendapatanRow['total_bayar'] ?? 0);

        $totalPesanan = $db->table('pesanan')->countAllResults();

        $pembelianRow = $db->table('pembelian')
            ->selectSum('total_pembelian')
            ->get()
            ->getRowArray();
        $pengeluaran = (float) ($pembelianRow['total_pembelian'] ?? 0);

        $labaKotor = $totalPendapatan - $pengeluaran;

        $rekapLayanan = [
            ['nama' => 'Print Dokumen & Jilid', 'pesanan' => 172, 'pendapatan' => 19850000, 'porsi' => '41%'],
            ['nama' => 'Cetak Spanduk',         'pesanan' => 96,  'pendapatan' => 11700000, 'porsi' => '24%'],
            ['nama' => 'Cetak Undangan',        'pesanan' => 78,  'pendapatan' => 9240000,  'porsi' => '19%'],
            ['nama' => 'Cetak Banner',          'pesanan' => 66,  'pendapatan' => 7830000,  'porsi' => '16%'],
        ];

        return view('admin/laporan', [
            'title'           => 'Laporan Penjualan',
            'activeMenu'      => 'laporan',
            'totalPendapatan' => $totalPendapatan,
            'totalPesanan'    => $totalPesanan,
            'pengeluaran'     => $pengeluaran,
            'labaKotor'       => $labaKotor,
            'rekapLayanan'    => $rekapLayanan,
        ]);
    }

    public function exportLaporan($format = 'excel')
    {
        $db    = \Config\Database::connect();
        $rows  = $db->table('pesanan')->orderBy('waktu_pesan', 'DESC')->get()->getResultArray();
        $filename = 'laporan-eprint-erika-' . date('Ymd-His') . ($format === 'pdf' ? '.html' : '.xls');

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo "<table border='1'>";
        echo "<tr><th>Kode Pesanan</th><th>Nama Pesanan</th><th>Tipe</th><th>Status</th><th>Total Bayar</th><th>Waktu</th></tr>";
        foreach ($rows as $r) {
            echo "<tr>";
            echo "<td>" . esc($r['kode_pesanan']) . "</td>";
            echo "<td>" . esc($r['nama_pesanan']) . "</td>";
            echo "<td>" . esc($r['tipe_pesanan']) . "</td>";
            echo "<td>" . esc($r['status_pesanan']) . "</td>";
            echo "<td>" . esc($r['total_bayar']) . "</td>";
            echo "<td>" . esc($r['waktu_pesan']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }

    public function pengguna()
    {
        $db = \Config\Database::connect();

        $adminList = $db->table('admin')->orderBy('id_admin', 'ASC')->get()->getResultArray();

        $pelangganList = $db->table('pelanggan pl')
            ->select('pl.*, COUNT(p.id_pesanan) as total_pesanan')
            ->join('pesanan p', 'p.id_pelanggan = pl.id_pelanggan', 'left')
            ->groupBy('pl.id_pelanggan')
            ->orderBy('pl.id_pelanggan', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/pengguna', [
            'title'         => 'Pengguna',
            'activeMenu'    => 'pengguna',
            'adminList'     => $adminList,
            'pelangganList' => $pelangganList,
        ]);
    }

    public function storeAdmin()
    {
        $model = new AdminModel();
        $model->insert([
            'username'     => trim((string) $this->request->getPost('username')),
            'nama_lengkap' => trim((string) $this->request->getPost('nama_lengkap')),
            'email'        => trim((string) $this->request->getPost('email')),
            'no_wa'        => trim((string) $this->request->getPost('no_wa')),
            'role'         => $this->request->getPost('role') === 'OWNER' ? 'OWNER' : 'ADMIN',
            'kata_sandi'   => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT),
            'status_aktif' => 1,
        ]);

        return redirect()->back()->with('success', 'Akun admin/owner baru berhasil ditambahkan.');
    }

    public function toggleStatusAdmin($id)
    {
        $model = new AdminModel();
        $admin = $model->find((int) $id);

        if ($admin) {
            $statusBaru = (int) $admin['status_aktif'] === 1 ? 0 : 1;
            $model->update((int) $id, ['status_aktif' => $statusBaru]);
        }

        return redirect()->back()->with('success', 'Status akun berhasil diubah.');
    }
}