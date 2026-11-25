<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $totalPesanan = $db->table('pesanan')->countAllResults();

        $pendapatanRow = $db->table('pesanan')
            ->selectSum('total_bayar')
            ->where('status_pembayaran', 'LUNAS')
            ->get()
            ->getRowArray();
        $totalPendapatan = (float) ($pendapatanRow['total_bayar'] ?? 0);

        $antreanAktif = $db->table('antrean')
            ->whereIn('status_antrean', ['MENUNGGU', 'DIKERJAKAN'])
            ->countAllResults();

        $stokMenipis = $db->table('bahan_baku')
            ->where('stok <= stok_minimum', null, false)
            ->where('status_aktif', 1)
            ->orderBy('stok', 'ASC')
            ->get()
            ->getResultArray();

        $pesananTerbaru = $db->table('pesanan p')
            ->select('p.*, pl.nama_lengkap as nama_pelanggan')
            ->join('pelanggan pl', 'pl.id_pelanggan = p.id_pelanggan', 'left')
            ->orderBy('p.waktu_pesan', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $chart7HariLabel = ['Kam', 'Jum', 'Sab', 'Min', 'Sen', 'Sel', 'Rab'];
        $chart7HariData  = [2.6, 3.1, 3.9, 1.2, 2.8, 3.4, 3.2];

        $layananStats = [
            ['nama' => 'Print Dokumen & Jilid', 'persen' => 42, 'warna' => '#312E81'],
            ['nama' => 'Cetak Spanduk',         'persen' => 24, 'warna' => '#EC4899'],
            ['nama' => 'Cetak Undangan',        'persen' => 19, 'warna' => '#06B6D4'],
            ['nama' => 'Cetak Banner',          'persen' => 15, 'warna' => '#F59E0B'],
        ];

        return view('admin/dashboard', [
            'title'           => 'Dashboard',
            'activeMenu'      => 'dashboard',
            'totalPesanan'    => $totalPesanan,
            'totalPendapatan' => $totalPendapatan,
            'antreanAktif'    => $antreanAktif,
            'stokMenipis'     => $stokMenipis,
            'pesananTerbaru'  => $pesananTerbaru,
            'chart7HariLabel' => json_encode($chart7HariLabel),
            'chart7HariData'  => json_encode($chart7HariData),
            'layananStats'    => $layananStats,
        ]);
    }

    public function comingSoon($modul = 'Modul')
    {
        return view('admin/coming_soon', [
            'title'      => $modul,
            'activeMenu' => strtolower($modul),
        ]);
    }
}