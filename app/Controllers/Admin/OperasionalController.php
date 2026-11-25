<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class OperasionalController extends BaseController
{
    public function pesanan()
    {
        $db      = \Config\Database::connect();
        $keyword = trim((string) $this->request->getGet('q'));
        $status  = (string) $this->request->getGet('status');
        $tipe    = (string) $this->request->getGet('tipe');

        $builder = $db->table('pesanan p')
            ->select('p.*, pl.nama_lengkap as nama_pelanggan')
            ->join('pelanggan pl', 'pl.id_pelanggan = p.id_pelanggan', 'left');

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('p.kode_pesanan', $keyword)
                ->orLike('p.nama_pesanan', $keyword)
                ->orLike('pl.nama_lengkap', $keyword)
                ->groupEnd();
        }
        if ($status !== '' && $status !== 'SEMUA') {
            $builder->where('p.status_pesanan', $status);
        }
        if ($tipe !== '' && $tipe !== 'SEMUA') {
            $builder->where('p.tipe_pesanan', $tipe);
        }

        $pesananList = $builder->orderBy('p.waktu_pesan', 'DESC')->get()->getResultArray();

        return view('admin/pesanan', [
            'title'       => 'Pesanan',
            'activeMenu'  => 'pesanan',
            'pesananList' => $pesananList,
            'keyword'     => $keyword,
            'statusFilter'=> $status ?: 'SEMUA',
            'tipeFilter'  => $tipe ?: 'SEMUA',
        ]);
    }

    public function updateStatusPesanan()
    {
        $idPesanan = (int) $this->request->getPost('id_pesanan');
        $status    = (string) $this->request->getPost('status_pesanan');

        $db = \Config\Database::connect();
        $db->table('pesanan')->where('id_pesanan', $idPesanan)->update([
            'status_pesanan' => $status,
            'id_admin'       => session()->get('id_admin'),
            'waktu_siap'     => $status === 'SIAP_DIAMBIL' ? date('Y-m-d H:i:s') : null,
            'waktu_diambil'  => $status === 'SELESAI' ? date('Y-m-d H:i:s') : null,
        ]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function antrean()
    {
        $db = \Config\Database::connect();

        // Diurutkan berdasarkan FIFO (waktu_masuk terawal)
        $rows = $db->table('antrean a')
            ->select('a.*, p.kode_pesanan, p.nama_pesanan, p.tipe_pesanan, pl.nama_lengkap as nama_pelanggan')
            ->join('pesanan p', 'p.id_pesanan = a.id_pesanan', 'left')
            ->join('pelanggan pl', 'pl.id_pelanggan = p.id_pelanggan', 'left')
            ->orderBy('a.waktu_masuk', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [
            'MENUNGGU'     => [],
            'DIKERJAKAN'   => [],
            'SIAP_DIAMBIL' => [],
            'SELESAI'      => [],
        ];

        foreach ($rows as $row) {
            $st = $row['status_antrean'] ?? 'MENUNGGU';
            if (isset($grouped[$st])) {
                $grouped[$st][] = $row;
            }
        }

        return view('admin/antrean', [
            'title'      => 'Antrean Produksi',
            'activeMenu' => 'antrean',
            'grouped'    => $grouped,
        ]);
    }

    public function updateStatusAntrean()
    {
        $idAntrean = (int) $this->request->getPost('id_antrean');
        $status    = (string) $this->request->getPost('status_antrean');

        $db   = \Config\Database::connect();
        $data = ['status_antrean' => $status];

        if ($status === 'DIKERJAKAN') {
            $data['waktu_mulai'] = date('Y-m-d H:i:s');
        } elseif ($status === 'SELESAI') {
            $data['waktu_selesai'] = date('Y-m-d H:i:s');
        }

        $db->table('antrean')->where('id_antrean', $idAntrean)->update($data);

        return redirect()->back()->with('success', 'Status antrean FIFO berhasil diubah menjadi ' . $status . '.');
    }

    public function pembayaran()
    {
        $db = \Config\Database::connect();

        $pembayaranList = $db->table('pembayaran pb')
            ->select('pb.*, p.kode_pesanan, pl.nama_lengkap as nama_pelanggan')
            ->join('pesanan p', 'p.id_pesanan = pb.id_pesanan', 'left')
            ->join('pelanggan pl', 'pl.id_pelanggan = p.id_pelanggan', 'left')
            ->orderBy('pb.waktu_dibuat', 'DESC')
            ->get()
            ->getResultArray();

        $diterimaRow = $db->table('pembayaran')
            ->selectSum('jumlah_dibayar')
            ->where('status_pembayaran', 'TERKONFIRMASI')
            ->get()
            ->getRowArray();

        $menungguCount = $db->table('pembayaran')
            ->where('status_pembayaran', 'MENUNGGU_VERIFIKASI')
            ->countAllResults();

        return view('admin/pembayaran', [
            'title'          => 'Pembayaran',
            'activeMenu'     => 'pembayaran',
            'pembayaranList' => $pembayaranList,
            'totalDiterima'  => (float) ($diterimaRow['jumlah_dibayar'] ?? 0),
            'menungguCount'  => $menungguCount,
        ]);
    }

    public function konfirmasiPembayaran()
    {
        $idPembayaran = (int) $this->request->getPost('id_pembayaran');
        $aksi         = (string) $this->request->getPost('aksi'); // TERKONFIRMASI / DIBATALKAN

        $db  = \Config\Database::connect();
        $pay = $db->table('pembayaran')->where('id_pembayaran', $idPembayaran)->get()->getRowArray();

        if ($pay) {
            $db->table('pembayaran')->where('id_pembayaran', $idPembayaran)->update([
                'status_pembayaran' => $aksi,
                'id_admin'          => session()->get('id_admin'),
                'jumlah_dibayar'    => $aksi === 'TERKONFIRMASI' ? $pay['jumlah_tagihan'] : 0,
                'waktu_dibayar'     => $aksi === 'TERKONFIRMASI' ? date('Y-m-d H:i:s') : null,
            ]);

            $db->table('pesanan')->where('id_pesanan', $pay['id_pesanan'])->update([
                'status_pembayaran' => $aksi === 'TERKONFIRMASI' ? 'LUNAS' : 'DIBATALKAN',
            ]);
        }

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}