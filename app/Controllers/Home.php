<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('pelanggan/beranda', [
            'title'     => 'Beranda',
            'activeNav' => 'beranda',
        ]);
    }

    public function layanan(): string
    {
        return view('pelanggan/layanan', [
            'title'     => 'Daftar Layanan & Katalog ATK',
            'activeNav' => 'layanan',
        ]);
    }

    public function pesanDokumen(): string
    {
        return view('pelanggan/pesan_dokumen', [
            'title'     => 'Print Dokumen & Jilid',
            'activeNav' => 'layanan',
            'idLayanan' => (int) ($this->request->getGet('id_layanan') ?? 0),
        ]);
    }

    public function pesanSpanduk(): string
    {
        return view('pelanggan/pesan_spanduk', [
            'title'     => 'Cetak Spanduk',
            'activeNav' => 'layanan',
            'idLayanan' => (int) ($this->request->getGet('id_layanan') ?? 0),
        ]);
    }

    public function pesanUndangan(): string
    {
        return view('pelanggan/pesan_undangan', [
            'title'     => 'Cetak Undangan',
            'activeNav' => 'template',
            'idLayanan' => (int) ($this->request->getGet('id_layanan') ?? 0),
        ]);
    }

    public function pesanBanner(): string
    {
        return view('pelanggan/pesan_banner', [
            'title'     => 'Cetak Banner',
            'activeNav' => 'layanan',
            'idLayanan' => (int) ($this->request->getGet('id_layanan') ?? 0),
        ]);
    }

    public function pesan(int $idLayanan): string
    {
        $db      = \Config\Database::connect();
        $layanan = $db->table('layanan')->where('id_layanan', $idLayanan)->get()->getRowArray();

        $kode = strtoupper((string) ($layanan['kode_layanan'] ?? ''));
        $view = 'pelanggan/pesan_dokumen';
        $nav  = 'layanan';

        if (str_starts_with($kode, 'SPD') || ((int) ($layanan['id_kategori_layanan'] ?? 0) === 2)) {
            $view = 'pelanggan/pesan_spanduk';
        } elseif (str_starts_with($kode, 'UND') || ((int) ($layanan['id_kategori_layanan'] ?? 0) === 3)) {
            $view = 'pelanggan/pesan_undangan';
            $nav  = 'template';
        } elseif (str_starts_with($kode, 'BNR') || ((int) ($layanan['id_kategori_layanan'] ?? 0) === 4)) {
            $view = 'pelanggan/pesan_banner';
        }

        return view($view, [
            'title'     => $layanan['nama_layanan'] ?? 'Konfigurasi Pesanan',
            'activeNav' => $nav,
            'idLayanan' => $idLayanan,
        ]);
    }

    public function keranjang(): string
    {
        return view('pelanggan/keranjang', [
            'title'     => 'Keranjang & Pembayaran',
            'activeNav' => 'keranjang',
        ]);
    }

    public function pesananSaya(): string
    {
        return view('pelanggan/pesanan_saya', [
            'title'     => 'Pesanan Saya',
            'activeNav' => 'pesanan',
        ]);
    }

    public function profil(): string
    {
        return view('pelanggan/profil', [
            'title'     => 'Profil Saya',
            'activeNav' => 'profil',
        ]);
    }
}
