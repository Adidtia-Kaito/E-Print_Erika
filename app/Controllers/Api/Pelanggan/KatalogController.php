<?php

namespace App\Controllers\Api\Pelanggan;

use Config\Database;

class KatalogController extends BaseApiController
{
    public function index()
    {
        $db  = Database::connect();
        $now = date('Y-m-d H:i:s');

        $kategoriList = $db->table('kategori_layanan')
            ->where('status_aktif', 1)
            ->orderBy('id_kategori_layanan', 'ASC')
            ->get()
            ->getResultArray();

        $layananList = $db->table('layanan')
            ->where('status_aktif', 1)
            ->orderBy('id_layanan', 'ASC')
            ->get()
            ->getResultArray();

        $opsiList = $db->table('opsi_layanan')
            ->where('status_aktif', 1)
            ->orderBy('id_opsi', 'ASC')
            ->get()
            ->getResultArray();

        $opsiByLayanan = [];
        foreach ($opsiList as $opsi) {
            $lid = (int) $opsi['id_layanan'];
            $opsiByLayanan[$lid][] = [
                'id_opsi'        => (int) $opsi['id_opsi'],
                'nama_opsi'      => $opsi['nama_opsi'],
                'grup_opsi'      => $opsi['grup_opsi'],
                'tipe_harga'     => $opsi['tipe_harga'],
                'wajib_dipilih'  => (int) $opsi['wajib_dipilih'],
                'harga_tambahan' => (float) $opsi['harga_tambahan'],
            ];
        }

        $layananByKat = [];
        foreach ($layananList as $lay) {
            $lid = (int) $lay['id_layanan'];
            $kid = (int) $lay['id_kategori_layanan'];
            $layananByKat[$kid][] = [
                'id_layanan'            => $lid,
                'id_kategori_layanan'   => $kid,
                'kode_layanan'          => $lay['kode_layanan'],
                'nama_layanan'          => $lay['nama_layanan'],
                'gambar_contoh'         => $lay['gambar_contoh'],
                'harga_dasar'           => (float) $lay['harga_dasar'],
                'satuan_hitung'         => $lay['satuan_hitung'],
                'deskripsi'             => $lay['deskripsi'],
                'butuh_file'            => (int) $lay['butuh_file'],
                'butuh_dimensi'         => (int) $lay['butuh_dimensi'],
                'estimasi_durasi_menit' => (int) $lay['estimasi_durasi_menit'],
                'opsi_layanan'          => $opsiByLayanan[$lid] ?? [],
            ];
        }

        foreach ($kategoriList as &$kat) {
            $kid            = (int) $kat['id_kategori_layanan'];
            $kat['layanan'] = $layananByKat[$kid] ?? [];
        }
        unset($kat);

        $promoRows = $db->table('promo')
            ->where('status_aktif', 1)
            ->where('berlaku_dari <=', $now)
            ->where('berlaku_sampai >=', $now)
            ->where('jumlah_terpakai < kuota', null, false)
            ->orderBy('id_promo', 'DESC')
            ->get()
            ->getResultArray();

        $promoList = array_map(static function (array $p): array {
            return [
                'id_promo'            => (int) $p['id_promo'],
                'kode_promo'          => $p['kode_promo'],
                'nama_promo'          => $p['nama_promo'],
                'jenis_diskon'        => $p['jenis_diskon'],
                'nilai_diskon'        => (float) $p['nilai_diskon'],
                'maks_diskon'         => $p['maks_diskon'] !== null ? (float) $p['maks_diskon'] : null,
                'minimal_belanja'     => (float) $p['minimal_belanja'],
                'berlaku_dari'        => $p['berlaku_dari'],
                'berlaku_sampai'      => $p['berlaku_sampai'],
                'id_kategori_layanan' => $p['id_kategori_layanan'] !== null ? (int) $p['id_kategori_layanan'] : null,
                'kuota'               => (int) $p['kuota'],
                'jumlah_terpakai'     => (int) $p['jumlah_terpakai'],
                'sisa_kuota'          => max(0, (int) $p['kuota'] - (int) $p['jumlah_terpakai']),
            ];
        }, $promoRows);

        $produkRows = $db->table('produk p')
            ->select('p.*, kp.nama_kategori')
            ->join('kategori_produk kp', 'kp.id_kategori_produk = p.id_kategori_produk', 'left')
            ->where('p.status_aktif', 1)
            ->orderBy('p.nama_produk', 'ASC')
            ->get()
            ->getResultArray();

        $produkList = array_map(static function (array $pr): array {
            $stok = (int) $pr['stok'];

            return [
                'id_produk'          => (int) $pr['id_produk'],
                'sku'                => $pr['sku'],
                'nama_produk'        => $pr['nama_produk'],
                'nama_kategori'      => $pr['nama_kategori'] ?? null,
                'merek'              => $pr['merek'],
                'satuan'             => $pr['satuan'],
                'deskripsi'          => $pr['deskripsi'],
                'gambar'             => $pr['gambar'],
                'harga_jual'         => (float) $pr['harga_jual'],
                'stok'               => $stok,
                'stok_habis'         => $stok <= 0,
            ];
        }, $produkRows);

        return $this->respondJson(true, 'Data katalog berhasil diambil.', [
            'kategori_layanan' => $kategoriList,
            'promo'            => $promoList,
            'produk_atk'       => $produkList,
        ]);
    }

    public function hitungEstimasi()
    {
        $input = $this->getInputData();

        $idLayanan     = (int) ($input['id_layanan'] ?? 0);
        $jumlahHalaman = (int) ($input['jumlah_halaman'] ?? 1);
        $jumlahSalinan = (int) ($input['jumlah_salinan'] ?? 1);
        $panjangCm     = isset($input['panjang_cm']) && $input['panjang_cm'] !== '' ? (float) $input['panjang_cm'] : null;
        $lebarCm       = isset($input['lebar_cm']) && $input['lebar_cm'] !== '' ? (float) $input['lebar_cm'] : null;
        $opsiIds       = is_array($input['opsi_ids'] ?? null) ? $input['opsi_ids'] : [];

        $db      = Database::connect();
        $layanan = $db->table('layanan')
            ->where('id_layanan', $idLayanan)
            ->where('status_aktif', 1)
            ->get()
            ->getRowArray();

        if (! $layanan) {
            return $this->respondJson(false, 'Layanan tidak ditemukan atau tidak aktif.', null, 404);
        }

        $hasil = $this->calculateLayananEstimate(
            $db,
            $layanan,
            $jumlahHalaman,
            $jumlahSalinan,
            $panjangCm,
            $lebarCm,
            $opsiIds
        );

        return $this->respondJson(true, 'Estimasi biaya berhasil dihitung.', $hasil);
    }
}
