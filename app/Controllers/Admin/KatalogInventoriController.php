<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class KatalogInventoriController extends BaseController
{
    public function layanan()
    {
        $db = \Config\Database::connect();

        $kategoriList = $db->table('kategori_layanan')->get()->getResultArray();
        $layananList  = $db->table('layanan l')
            ->select('l.*, kl.nama_kategori')
            ->join('kategori_layanan kl', 'kl.id_kategori_layanan = l.id_kategori_layanan', 'left')
            ->orderBy('l.id_layanan', 'ASC')
            ->get()
            ->getResultArray();

        $opsiList = $db->table('opsi_layanan o')
            ->select('o.*, l.nama_layanan')
            ->join('layanan l', 'l.id_layanan = o.id_layanan', 'left')
            ->get()
            ->getResultArray();

        $bahanResep = $db->table('layanan_bahan lb')
            ->select('lb.*, l.nama_layanan, b.nama_bahan, b.satuan')
            ->join('layanan l', 'l.id_layanan = lb.id_layanan', 'left')
            ->join('bahan_baku b', 'b.id_bahan = lb.id_bahan', 'left')
            ->get()
            ->getResultArray();

        return view('admin/layanan', [
            'title'        => 'Layanan & Opsi',
            'activeMenu'   => 'layanan',
            'kategoriList' => $kategoriList,
            'layananList'  => $layananList,
            'opsiList'     => $opsiList,
            'bahanResep'   => $bahanResep,
        ]);
    }

    public function storeLayanan()
    {
        $db = \Config\Database::connect();
        $db->table('layanan')->insert([
            'kode_layanan'          => strtoupper(trim((string) $this->request->getPost('kode_layanan'))),
            'nama_layanan'          => trim((string) $this->request->getPost('nama_layanan')),
            'id_kategori_layanan'  => (int) $this->request->getPost('id_kategori_layanan'),
            'harga_dasar'           => (float) $this->request->getPost('harga_dasar'),
            'satuan_hitung'         => trim((string) $this->request->getPost('satuan_hitung')),
            'estimasi_durasi_menit' => (int) $this->request->getPost('estimasi_durasi_menit'),
            'butuh_file'            => $this->request->getPost('butuh_file') ? 1 : 0,
            'butuh_dimensi'         => $this->request->getPost('butuh_dimensi') ? 1 : 0,
            'status_aktif'          => 1,
        ]);

        return redirect()->back()->with('success', 'Layanan baru berhasil ditambahkan.');
    }

    public function promo()
    {
        $db = \Config\Database::connect();

        $promoList = $db->table('promo pr')
            ->select('pr.*, kl.nama_kategori')
            ->join('kategori_layanan kl', 'kl.id_kategori_layanan = pr.id_kategori_layanan', 'left')
            ->orderBy('pr.id_promo', 'DESC')
            ->get()
            ->getResultArray();

        $kategoriList = $db->table('kategori_layanan')
            ->where('status_aktif', 1)
            ->orderBy('nama_kategori', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/promo', [
            'title'        => 'Promo',
            'activeMenu'   => 'promo',
            'promoList'    => $promoList,
            'kategoriList' => $kategoriList,
        ]);
    }

    public function storePromo()
    {
        $db = \Config\Database::connect();

        $kodePromo = strtoupper(trim((string) $this->request->getPost('kode_promo')));
        $namaPromo = trim((string) $this->request->getPost('nama_promo'));
        $idKat     = $this->request->getPost('id_kategori_layanan');
        $jenis     = $this->request->getPost('jenis_diskon') === 'NOMINAL' ? 'NOMINAL' : 'PERSEN';
        $nilai     = (float) $this->request->getPost('nilai_diskon');

        $maksDiskon     = preg_replace('/[^\d.]/', '', (string) $this->request->getPost('maks_diskon'));
        $minimalBelanja = preg_replace('/[^\d.]/', '', (string) $this->request->getPost('minimal_belanja'));

        $tglMulai   = $this->parseTanggalPromo((string) $this->request->getPost('berlaku_dari'), '00:00:00');
        $tglSelesai = $this->parseTanggalPromo((string) $this->request->getPost('berlaku_sampai'), '23:59:59');
        $kuota      = max(1, (int) $this->request->getPost('kuota'));

        $db->table('promo')->insert([
            'kode_promo'          => $kodePromo,
            'nama_promo'          => $namaPromo,
            'id_kategori_layanan' => ! empty($idKat) ? (int) $idKat : null,
            'jenis_diskon'        => $jenis,
            'nilai_diskon'        => $nilai,
            'maks_diskon'         => $maksDiskon !== '' ? (float) $maksDiskon : null,
            'minimal_belanja'     => $minimalBelanja !== '' ? (float) $minimalBelanja : 0.00,
            'berlaku_dari'        => $tglMulai,
            'berlaku_sampai'      => $tglSelesai,
            'kuota'               => $kuota,
            'jumlah_terpakai'     => 0,
            'status_aktif'        => 1,
        ]);

        return redirect()->to('/admin/promo')->with('success', 'Promo baru berhasil disimpan.');
    }

    public function togglePromo($id)
    {
        $db    = \Config\Database::connect();
        $promo = $db->table('promo')->where('id_promo', (int) $id)->get()->getRowArray();

        if ($promo) {
            $statusBaru = (int) $promo['status_aktif'] === 1 ? 0 : 1;
            $db->table('promo')->where('id_promo', (int) $id)->update(['status_aktif' => $statusBaru]);
        }

        return redirect()->to('/admin/promo')->with('success', 'Status promo berhasil diperbarui.');
    }

    private function parseTanggalPromo(string $input, string $jamDefault): string
    {
        $input = trim($input);
        if ($input === '') {
            return date('Y-m-d') . ' ' . $jamDefault;
        }

        $ts = strtotime($input);
        if ($ts !== false) {
            return date('Y-m-d', $ts) . ' ' . $jamDefault;
        }

        return date('Y-m-d') . ' ' . $jamDefault;
    }

    public function inventori()
    {
        $db = \Config\Database::connect();

        $bahanList = $db->table('bahan_baku')->orderBy('kode_bahan', 'ASC')->get()->getResultArray();
        $produkList = $db->table('produk p')
            ->select('p.*, kp.nama_kategori')
            ->join('kategori_produk kp', 'kp.id_kategori_produk = p.id_kategori_produk', 'left')
            ->get()
            ->getResultArray();

        $mutasiList = $db->table('mutasi_stok m')
            ->select('m.*, b.nama_bahan, b.satuan as satuan_bahan, a.nama_lengkap as nama_admin')
            ->join('bahan_baku b', 'b.id_bahan = m.id_bahan', 'left')
            ->join('admin a', 'a.id_admin = m.id_admin', 'left')
            ->orderBy('m.waktu_mutasi', 'DESC')
            ->limit(15)
            ->get()
            ->getResultArray();

        return view('admin/inventori', [
            'title'      => 'Bahan & Produk',
            'activeMenu' => 'inventori',
            'bahanList'  => $bahanList,
            'produkList' => $produkList,
            'mutasiList' => $mutasiList,
        ]);
    }

    public function storeBahan()
    {
        $db = \Config\Database::connect();
        $db->table('bahan_baku')->insert([
            'kode_bahan'          => strtoupper(trim((string) $this->request->getPost('kode_bahan'))),
            'nama_bahan'          => trim((string) $this->request->getPost('nama_bahan')),
            'jenis_bahan'         => trim((string) $this->request->getPost('jenis_bahan')),
            'stok'                => (int) $this->request->getPost('stok'),
            'stok_minimum'        => (int) $this->request->getPost('stok_minimum'),
            'satuan'              => trim((string) $this->request->getPost('satuan')),
            'harga_beli_terakhir' => (float) $this->request->getPost('harga_beli_terakhir'),
            'status_aktif'        => 1,
        ]);

        return redirect()->back()->with('success', 'Bahan baku baru berhasil ditambahkan.');
    }

    public function penyesuaianStok()
    {
        $idBahan    = (int) $this->request->getPost('id_bahan');
        $stokBaru   = (int) $this->request->getPost('stok_baru');
        $keterangan = trim((string) $this->request->getPost('keterangan')) ?: 'Stok opname';

        $db    = \Config\Database::connect();
        $bahan = $db->table('bahan_baku')->where('id_bahan', $idBahan)->get()->getRowArray();

        if ($bahan) {
            $sebelum = (int) $bahan['stok'];
            $selisih = abs($stokBaru - $sebelum);

            $db->table('bahan_baku')->where('id_bahan', $idBahan)->update(['stok' => $stokBaru]);
            $db->table('mutasi_stok')->insert([
                'id_admin'     => session()->get('id_admin'),
                'id_bahan'     => $idBahan,
                'jumlah'       => $selisih,
                'jenis_mutasi' => 'KOREKSI',
                'stok_sebelum' => $sebelum,
                'stok_sesudah' => $stokBaru,
                'waktu_mutasi' => date('Y-m-d H:i:s'),
                'keterangan'   => $keterangan,
            ]);
        }

        return redirect()->back()->with('success', 'Penyesuaian stok (mutasi_stok) berhasil dicatat.');
    }

    public function pembelian()
    {
        $db = \Config\Database::connect();

        $tab      = $this->request->getGet('tab') === 'pemasok' ? 'pemasok' : 'pembelian';
        $idFaktur = (int) $this->request->getGet('faktur');

        $pembelianRows = $db->table('pembelian pb')
            ->select('pb.*, pm.nama_pemasok, ad.nama_lengkap as nama_admin, COUNT(dp.id_detail_pembelian) as jumlah_item')
            ->join('pemasok pm', 'pm.id_pemasok = pb.id_pemasok', 'left')
            ->join('admin ad', 'ad.id_admin = pb.id_admin', 'left')
            ->join('detail_pembelian dp', 'dp.id_pembelian = pb.id_pembelian', 'left')
            ->groupBy('pb.id_pembelian')
            ->orderBy('pb.tanggal_pembelian', 'DESC')
            ->orderBy('pb.id_pembelian', 'DESC')
            ->get()
            ->getResultArray();

        $fakturAktif = null;
        if (! empty($pembelianRows)) {
            if ($idFaktur > 0) {
                foreach ($pembelianRows as $row) {
                    if ((int) $row['id_pembelian'] === $idFaktur) {
                        $fakturAktif = $row;
                        break;
                    }
                }
            }
            if (! $fakturAktif) {
                $fakturAktif = $pembelianRows[0];
            }
        }

        $detailFaktur = [];
        if ($fakturAktif) {
            $detailFaktur = $db->table('detail_pembelian dp')
                ->select('dp.*, bb.nama_bahan, bb.satuan as satuan_bahan, pr.nama_produk, pr.satuan as satuan_produk')
                ->join('bahan_baku bb', 'bb.id_bahan = dp.id_bahan', 'left')
                ->join('produk pr', 'pr.id_produk = dp.id_produk', 'left')
                ->where('dp.id_pembelian', (int) $fakturAktif['id_pembelian'])
                ->orderBy('dp.id_detail_pembelian', 'ASC')
                ->get()
                ->getResultArray();
        }

        $pemasokList = $db->table('pemasok')
            ->orderBy('id_pemasok', 'ASC')
            ->get()
            ->getResultArray();

        $bahanList = $db->table('bahan_baku')
            ->where('status_aktif', 1)
            ->orderBy('nama_bahan', 'ASC')
            ->get()
            ->getResultArray();

        $produkList = $db->table('produk')
            ->where('status_aktif', 1)
            ->orderBy('nama_produk', 'ASC')
            ->get()
            ->getResultArray();

        return view('admin/pembelian', [
            'title'         => 'Pembelian & Pemasok',
            'activeMenu'    => 'pembelian',
            'tab'           => $tab,
            'pembelianRows' => $pembelianRows,
            'fakturAktif'   => $fakturAktif,
            'detailFaktur'  => $detailFaktur,
            'pemasokList'   => $pemasokList,
            'bahanList'     => $bahanList,
            'produkList'    => $produkList,
        ]);
    }

    public function storePemasok()
    {
        $db = \Config\Database::connect();

        $db->table('pemasok')->insert([
            'nama_pemasok'   => trim((string) $this->request->getPost('nama_pemasok')),
            'jenis_pasokan'  => trim((string) $this->request->getPost('jenis_pasokan')),
            'kontak_pemasok' => trim((string) $this->request->getPost('nama_kontak')),
            'no_telp'        => trim((string) $this->request->getPost('no_telp')),
            'email'          => trim((string) $this->request->getPost('email')),
            'alamat'         => trim((string) $this->request->getPost('alamat')),
            'status_aktif'   => 1,
        ]);

        return redirect()->to('/admin/pembelian')->with('success', 'Pemasok baru berhasil ditambahkan.');
    }

    public function storePembelian()
    {
        $db = \Config\Database::connect();

        $idPemasok   = (int) $this->request->getPost('id_pemasok');
        $noFaktur    = trim((string) $this->request->getPost('no_faktur'));
        $tanggal     = (string) $this->request->getPost('tanggal_pembelian') ?: date('Y-m-d');
        $statusBayar = $this->request->getPost('status_pembayaran') === 'BELUM_LUNAS' ? 'BELUM_LUNAS' : 'LUNAS';
        $tipeItem    = $this->request->getPost('tipe_item') === 'PRODUK' ? 'PRODUK' : 'BAHAN';
        $jumlah      = max(1, (int) $this->request->getPost('jumlah'));
        $hargaSatuan = max(0, (float) $this->request->getPost('harga_beli_satuan'));
        $subTotal    = $jumlah * $hargaSatuan;

        $db->transStart();

        $db->table('pembelian')->insert([
            'id_pemasok'        => $idPemasok,
            'id_admin'          => session()->get('id_admin'),
            'nomor_faktur'      => $noFaktur,
            'tanggal_pembelian' => $tanggal,
            'total_pembelian'   => $subTotal,
            'status_pembayaran' => $statusBayar,
            'catatan'           => trim((string) $this->request->getPost('catatan')),
        ]);
        $idPembelian = $db->insertID();

        if ($tipeItem === 'PRODUK') {
            $idProduk = (int) $this->request->getPost('id_produk');
            $produk   = $db->table('produk')->where('id_produk', $idProduk)->get()->getRowArray();

            $db->table('detail_pembelian')->insert([
                'id_pembelian'      => $idPembelian,
                'id_produk'         => $idProduk,
                'jumlah'            => $jumlah,
                'harga_beli_satuan' => $hargaSatuan,
                'sub_total'         => $subTotal,
            ]);

            if ($produk) {
                $db->table('produk')->where('id_produk', $idProduk)->update([
                    'stok'       => (int) $produk['stok'] + $jumlah,
                    'harga_beli' => $hargaSatuan,
                ]);
            }
        } else {
            $idBahan = (int) $this->request->getPost('id_bahan');
            $bahan   = $db->table('bahan_baku')->where('id_bahan', $idBahan)->get()->getRowArray();

            $db->table('detail_pembelian')->insert([
                'id_pembelian'      => $idPembelian,
                'id_bahan'          => $idBahan,
                'jumlah'            => $jumlah,
                'harga_beli_satuan' => $hargaSatuan,
                'sub_total'         => $subTotal,
            ]);

            if ($bahan) {
                $sebelum = (int) $bahan['stok'];
                $sesudah = $sebelum + $jumlah;

                $db->table('bahan_baku')->where('id_bahan', $idBahan)->update([
                    'stok'                => $sesudah,
                    'harga_beli_terakhir' => $hargaSatuan,
                ]);

                $db->table('mutasi_stok')->insert([
                    'id_admin'     => session()->get('id_admin'),
                    'id_bahan'     => $idBahan,
                    'id_pembelian' => $idPembelian,
                    'jumlah'       => $jumlah,
                    'jenis_mutasi' => 'TAMBAH',
                    'stok_sebelum' => $sebelum,
                    'stok_sesudah' => $sesudah,
                    'waktu_mutasi' => date('Y-m-d H:i:s'),
                    'keterangan'   => 'Pembelian faktur ' . $noFaktur,
                ]);
            }
        }

        $db->transComplete();

        return redirect()->to('/admin/pembelian?faktur=' . $idPembelian)->with('success', 'Pembelian baru disimpan dan stok telah diperbarui.');
    }
}