<?php

namespace App\Controllers\Api\Pelanggan;

use App\Filters\JwtPelangganFilter;
use App\Libraries\FileUploadService;
use App\Models\PelangganModel;
use Config\Database;
use RuntimeException;
use Throwable;

class PesananController extends BaseApiController
{
    public function cekPromo()
    {
        $input       = $this->getInputData();
        $kodePromo   = (string) ($input['kode_promo'] ?? '');
        $subtotal    = (float) ($input['subtotal'] ?? 0);
        $kategoriIds = is_array($input['kategori_ids'] ?? null) ? $input['kategori_ids'] : [];

        try {
            $db     = Database::connect();
            $result = $this->validateAndComputePromo($db, $kodePromo, $subtotal, $kategoriIds);

            return $this->respondJson(true, 'Promo valid.', $result);
        } catch (RuntimeException $e) {
            return $this->respondJson(false, $e->getMessage(), null, 422);
        }
    }

    public function checkout()
    {
        $idPelanggan = JwtPelangganFilter::getIdPelanggan();
        $pelanggan   = (new PelangganModel())->find($idPelanggan);
        if (! $pelanggan) {
            return $this->respondJson(false, 'Akun pelanggan tidak ditemukan.', null, 404);
        }

        $namaPesanan      = trim((string) $this->request->getPost('nama_pesanan'));
        $catatanPelanggan = trim((string) $this->request->getPost('catatan_pelanggan'));
        $kodePromo        = trim((string) $this->request->getPost('kode_promo'));
        $metodePembayaran = strtoupper(trim((string) $this->request->getPost('metode_pembayaran')));

        if ($namaPesanan === '') {
            return $this->respondJson(false, 'Nama pesanan wajib diisi.', null, 422);
        }
        if (! in_array($metodePembayaran, ['QRIS', 'TRANSFER_BANK', 'TUNAI'], true)) {
            return $this->respondJson(false, 'Metode pembayaran tidak valid (pilih QRIS, TRANSFER_BANK, atau TUNAI).', null, 422);
        }

        $itemsLayananRaw = $this->request->getPost('items_layanan');
        $itemsProdukRaw  = $this->request->getPost('items_produk');
        $itemsLayanan    = is_string($itemsLayananRaw) ? (json_decode($itemsLayananRaw, true) ?: []) : (is_array($itemsLayananRaw) ? $itemsLayananRaw : []);
        $itemsProduk     = is_string($itemsProdukRaw) ? (json_decode($itemsProdukRaw, true) ?: []) : (is_array($itemsProdukRaw) ? $itemsProdukRaw : []);

        if (empty($itemsLayanan) && empty($itemsProduk)) {
            return $this->respondJson(false, 'Minimal harus ada 1 item layanan atau produk ATK.', null, 422);
        }

        $buktiFile = $this->request->getFile('bukti_pembayaran');
        $hasBukti  = $buktiFile && $buktiFile->getError() !== UPLOAD_ERR_NO_FILE;
        if ($metodePembayaran !== 'TUNAI' && ! $hasBukti) {
            return $this->respondJson(false, 'Bukti pembayaran wajib diunggah untuk metode QRIS atau TRANSFER_BANK.', null, 422);
        }

        $uploadedFiles = $this->request->getFileMultiple('files_layanan') ?: [];
        $uploadService = new FileUploadService();
        $db            = Database::connect();

        $db->transBegin();

        try {
            $kodePesanan = $this->generateKodePesanan($db);
            $now         = date('Y-m-d H:i:s');

            $db->table('pesanan')->insert([
                'kode_pesanan'      => $kodePesanan,
                'diskon'            => 0.00,
                'id_pelanggan'      => $idPelanggan,
                'nama_pesanan'      => $namaPesanan,
                'tipe_pesanan'      => 'ONLINE',
                'no_kontak'         => $pelanggan['no_wa'] ?? null,
                'status_pesanan'    => 'MENUNGGU_KONFIRMASI',
                'status_pembayaran' => $hasBukti ? 'MENUNGGU_VERIFIKASI' : 'BELUM_BAYAR',
                'catatan_pelanggan' => $catatanPelanggan !== '' ? $catatanPelanggan : null,
                'waktu_pesan'       => $now,
                'nama_pengambil'    => $pelanggan['nama_lengkap'] ?? null,
            ]);
            $idPesanan = (int) $db->insertID();

            $subtotalLayanan = 0.0;
            $subtotalProduk  = 0.0;
            $kategoriIds     = [];
            $maxEstimasiMenit = 0;

            foreach ($itemsLayanan as $item) {
                $idLayanan = (int) ($item['id_layanan'] ?? 0);
                $layanan   = $db->table('layanan')
                    ->where('id_layanan', $idLayanan)
                    ->where('status_aktif', 1)
                    ->get()
                    ->getRowArray();

                if (! $layanan) {
                    throw new RuntimeException('Layanan dengan ID ' . $idLayanan . ' tidak tersedia.');
                }

                $kategoriIds[]     = (int) $layanan['id_kategori_layanan'];
                $maxEstimasiMenit  = max($maxEstimasiMenit, (int) $layanan['estimasi_durasi_menit']);

                $jumlahHalaman = max(1, (int) ($item['jumlah_halaman'] ?? 1));
                $jumlahSalinan = max(1, (int) ($item['jumlah_salinan'] ?? 1));
                $panjangCm     = isset($item['panjang_cm']) && $item['panjang_cm'] !== '' ? (float) $item['panjang_cm'] : null;
                $lebarCm       = isset($item['lebar_cm']) && $item['lebar_cm'] !== '' ? (float) $item['lebar_cm'] : null;
                $opsiIds       = is_array($item['opsi_ids'] ?? null) ? $item['opsi_ids'] : [];
                $fileIndexes   = is_array($item['file_index'] ?? null) ? $item['file_index'] : [];

                // Jika file diunggah dan jumlah_halaman masih default, hitung estimasi awal lalu perbarui jika perlu
                $est = $this->calculateLayananEstimate(
                    $db,
                    $layanan,
                    $jumlahHalaman,
                    $jumlahSalinan,
                    $panjangCm,
                    $lebarCm,
                    $opsiIds
                );

                $db->table('detail_pesanan_layanan')->insert([
                    'id_pesanan'           => $idPesanan,
                    'id_layanan'           => $idLayanan,
                    'panjang_cm'           => $panjangCm,
                    'lebar_cm'             => $lebarCm,
                    'jumlah_salinan'       => $jumlahSalinan,
                    'harga_satuan'         => (float) $layanan['harga_dasar'],
                    'status_item'          => 'MENUNGGU',
                    'judul_pekerjaan'      => trim((string) ($item['judul_pekerjaan'] ?? $layanan['nama_layanan'])),
                    'jumlah_halaman'       => $jumlahHalaman,
                    'sub_total'            => $est['sub_total'],
                    'spesifikasi_tambahan' => $item['spesifikasi_tambahan'] ?? null,
                    'catatan_pekerjaan'    => $item['catatan_pekerjaan'] ?? null,
                    'kuantitas_hitung'     => (int) max(1, round((float) $est['kuantitas_hitung'])),
                ]);
                $idDetailLayanan = (int) $db->insertID();

                foreach ($est['detail_opsi'] as $op) {
                    $db->table('opsi_detail_pesanan')->insert([
                        'id_detail_pesanan' => $idDetailLayanan,
                        'id_opsi'           => (int) $op['id_opsi'],
                        'subtotal_opsi'     => (float) $op['biaya'],
                    ]);
                }

                if ((int) $layanan['butuh_file'] === 1 && empty($fileIndexes)) {
                    throw new RuntimeException('Layanan "' . $layanan['nama_layanan'] . '" wajib melampirkan berkas dokumen.');
                }

                foreach ($fileIndexes as $fIdx) {
                    $fileObj = $uploadedFiles[(int) $fIdx] ?? null;
                    if (! $fileObj || $fileObj->getError() === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    $uploadedMeta = $uploadService->uploadDokumen($fileObj);
                    $db->table('file_pesanan')->insert([
                        'id_detail_layanan'         => $idDetailLayanan,
                        'jenis_file'                => $uploadedMeta['jenis_file'],
                        'nama_file_asli'            => $uploadedMeta['nama_file_asli'],
                        'path_penyimpanan'          => $uploadedMeta['path_penyimpanan'],
                        'nama_file_tersimpan'       => $uploadedMeta['nama_file_tersimpan'],
                        'jumlah_halaman_terdeteksi' => $uploadedMeta['jumlah_halaman'],
                        'checksum_sha256'           => $uploadedMeta['checksum_sha256'],
                    ]);
                }

                $subtotalLayanan += (float) $est['sub_total'];
            }

            foreach ($itemsProduk as $prodItem) {
                $idProduk = (int) ($prodItem['id_produk'] ?? 0);
                $jumlah   = max(1, (int) ($prodItem['jumlah'] ?? 1));
                $produk   = $db->table('produk')
                    ->where('id_produk', $idProduk)
                    ->where('status_aktif', 1)
                    ->get()
                    ->getRowArray();

                if (! $produk) {
                    throw new RuntimeException('Produk ATK dengan ID ' . $idProduk . ' tidak ditemukan.');
                }
                if ((int) $produk['stok'] < $jumlah) {
                    throw new RuntimeException('Stok produk "' . $produk['nama_produk'] . '" tidak mencukupi.');
                }

                $hargaSatuan = (float) $produk['harga_jual'];
                $sub         = round($hargaSatuan * $jumlah, 2);

                $db->table('detail_pesanan_produk')->insert([
                    'id_pesanan'   => $idPesanan,
                    'id_produk'    => $idProduk,
                    'jumlah'       => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'sub_total'    => $sub,
                ]);

                $subtotalProduk += $sub;
            }

            $totalSebelumDiskon = round($subtotalLayanan + $subtotalProduk, 2);
            $diskon             = 0.0;
            $idPromo            = null;

            if ($kodePromo !== '') {
                $promoResult = $this->validateAndComputePromo($db, $kodePromo, $totalSebelumDiskon, $kategoriIds);
                $diskon      = (float) $promoResult['nominal_diskon'];
                $idPromo     = (int) $promoResult['id_promo'];

                $db->table('promo')
                    ->where('id_promo', $idPromo)
                    ->set('jumlah_terpakai', 'jumlah_terpakai + 1', false)
                    ->update();
            }

            $totalBayar      = round(max(0.0, $totalSebelumDiskon - $diskon), 2);
            $estimasiSelesai = $maxEstimasiMenit > 0
                ? date('Y-m-d H:i:s', strtotime('+' . $maxEstimasiMenit . ' minutes'))
                : null;

            $db->table('pesanan')->where('id_pesanan', $idPesanan)->update([
                'id_promo'         => $idPromo,
                'diskon'           => $diskon,
                'subtotal_layanan' => round($subtotalLayanan, 2),
                'subtotal_produk'  => round($subtotalProduk, 2),
                'total_bayar'      => $totalBayar,
                'estimasi_selesai' => $estimasiSelesai,
            ]);

            $pathBukti = null;
            if ($hasBukti && $buktiFile) {
                $pathBukti = $uploadService->uploadBukti($buktiFile);
            }

            $orderIdGateway = $this->generateOrderIdGateway($db);
            $db->table('pembayaran')->insert([
                'id_pesanan'        => $idPesanan,
                'order_id_gateway'  => $orderIdGateway,
                'metode_pembayaran' => $metodePembayaran,
                'nama_channel'      => $metodePembayaran,
                'jumlah_tagihan'    => $totalBayar,
                'jumlah_dibayar'    => 0.00,
                'status_pembayaran' => 'MENUNGGU_VERIFIKASI',
                'bukti_pembayaran'  => $pathBukti,
            ]);

            $nomorAntrean = $this->generateNomorAntrean($db);
            $db->table('antrean')->insert([
                'id_pesanan'      => $idPesanan,
                'tanggal_antrean' => date('Y-m-d'),
                'nomor_antrean'   => $nomorAntrean,
                'status_antrean'  => 'MENUNGGU',
                'prioritas'       => 'NORMAL',
                'waktu_masuk'     => $now,
            ]);

            if ($db->transStatus() === false) {
                throw new RuntimeException('Transaksi database gagal disimpan.');
            }

            $db->transCommit();

            return $this->respondJson(true, 'Checkout pesanan berhasil dibuat.', [
                'id_pesanan'    => $idPesanan,
                'kode_pesanan'  => $kodePesanan,
                'nomor_antrean' => $nomorAntrean,
                'total_bayar'   => $totalBayar,
            ], 201);
        } catch (Throwable $e) {
            $db->transRollback();

            return $this->respondJson(false, $e->getMessage(), null, 422);
        }
    }

    public function index()
    {
        $idPelanggan = JwtPelangganFilter::getIdPelanggan();
        $db          = Database::connect();

        $rows = $db->table('pesanan p')
            ->select('p.*, a.nomor_antrean, a.status_antrean, a.prioritas')
            ->join('antrean a', 'a.id_pesanan = p.id_pesanan', 'left')
            ->where('p.id_pelanggan', $idPelanggan)
            ->orderBy('p.waktu_pesan', 'DESC')
            ->get()
            ->getResultArray();

        return $this->respondJson(true, 'Daftar pesanan berhasil diambil.', $rows);
    }

    public function detail(int $idPesanan)
    {
        $idPelanggan = JwtPelangganFilter::getIdPelanggan();
        $db          = Database::connect();

        $pesanan = $db->table('pesanan')
            ->where('id_pesanan', $idPesanan)
            ->where('id_pelanggan', $idPelanggan)
            ->get()
            ->getRowArray();

        if (! $pesanan) {
            return $this->respondJson(false, 'Pesanan tidak ditemukan atau bukan milik Anda.', null, 404);
        }

        $detailLayanan = $db->table('detail_pesanan_layanan dpl')
            ->select('dpl.*, l.nama_layanan, l.kode_layanan, l.satuan_hitung')
            ->join('layanan l', 'l.id_layanan = dpl.id_layanan', 'left')
            ->where('dpl.id_pesanan', $idPesanan)
            ->get()
            ->getResultArray();

        $detailIds = array_map(static fn ($r) => (int) $r['id_detail_layanan'], $detailLayanan);

        $files = [];
        if (! empty($detailIds)) {
            $files = $db->table('file_pesanan')
                ->select('id_file, id_detail_layanan, jenis_file, nama_file_asli, jumlah_halaman_terdeteksi, checksum_sha256, waktu_unggah')
                ->whereIn('id_detail_layanan', $detailIds)
                ->where('dihapus_pada IS NULL', null, false)
                ->orderBy('waktu_unggah', 'DESC')
                ->get()
                ->getResultArray();
        }

        $detailProduk = $db->table('detail_pesanan_produk dpp')
            ->select('dpp.*, pr.nama_produk, pr.sku, pr.satuan')
            ->join('produk pr', 'pr.id_produk = dpp.id_produk', 'left')
            ->where('dpp.id_pesanan', $idPesanan)
            ->get()
            ->getResultArray();

        $antrean = $db->table('antrean')
            ->where('id_pesanan', $idPesanan)
            ->get()
            ->getRowArray();

        $pembayaran = $db->table('pembayaran')
            ->where('id_pesanan', $idPesanan)
            ->orderBy('id_pembayaran', 'DESC')
            ->get()
            ->getRowArray();

        $revisiKomplain = [];
        if ($db->tableExists('revisi_komplain')) {
            $revisiKomplain = $db->table('revisi_komplain')
                ->where('id_pesanan', $idPesanan)
                ->orderBy('dibuat_pada', 'DESC')
                ->get()
                ->getResultArray();
        }

        return $this->respondJson(true, 'Detail pesanan berhasil diambil.', [
            'pesanan'         => $pesanan,
            'antrean'         => $antrean,
            'detail_layanan'  => $detailLayanan,
            'detail_produk'   => $detailProduk,
            'files'           => $files,
            'pembayaran'      => $pembayaran,
            'revisi_komplain' => $revisiKomplain,
        ]);
    }

    public function revisiFile(int $idPesanan)
    {
        $idPelanggan = JwtPelangganFilter::getIdPelanggan();
        $db          = Database::connect();

        $pesanan = $db->table('pesanan')
            ->where('id_pesanan', $idPesanan)
            ->where('id_pelanggan', $idPelanggan)
            ->get()
            ->getRowArray();

        if (! $pesanan) {
            return $this->respondJson(false, 'Pesanan tidak ditemukan atau bukan milik Anda.', null, 404);
        }

        if ($pesanan['status_pesanan'] !== 'PERLU_REVISI') {
            return $this->respondJson(false, 'Revisi file hanya dapat diunggah jika status pesanan PERLU_REVISI.', null, 422);
        }

        $idDetailLayanan = (int) $this->request->getPost('id_detail_layanan');
        if ($idDetailLayanan <= 0) {
            $firstDetail = $db->table('detail_pesanan_layanan')
                ->where('id_pesanan', $idPesanan)
                ->orderBy('id_detail_layanan', 'ASC')
                ->get()
                ->getRowArray();
            $idDetailLayanan = (int) ($firstDetail['id_detail_layanan'] ?? 0);
        } else {
            $detail = $db->table('detail_pesanan_layanan')
                ->where('id_detail_layanan', $idDetailLayanan)
                ->where('id_pesanan', $idPesanan)
                ->get()
                ->getRowArray();
            if (! $detail) {
                return $this->respondJson(false, 'Detail item layanan tidak valid untuk pesanan ini.', null, 422);
            }
        }

        if ($idDetailLayanan <= 0) {
            return $this->respondJson(false, 'Detail layanan tidak ditemukan.', null, 422);
        }

        $file = $this->request->getFile('file_revisi');
        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return $this->respondJson(false, 'Berkas revisi wajib diunggah.', null, 422);
        }

        $catatanRevisi = trim((string) $this->request->getPost('catatan_revisi'));

        $db->transBegin();
        try {
            $uploadService = new FileUploadService();
            $uploadedMeta  = $uploadService->uploadDokumen($file);
            $now           = date('Y-m-d H:i:s');

            // Soft-delete file lama pada detail layanan ini
            $db->table('file_pesanan')
                ->where('id_detail_layanan', $idDetailLayanan)
                ->where('dihapus_pada IS NULL', null, false)
                ->update(['dihapus_pada' => $now]);

            $db->table('file_pesanan')->insert([
                'id_detail_layanan'         => $idDetailLayanan,
                'jenis_file'                => $uploadedMeta['jenis_file'],
                'nama_file_asli'            => $uploadedMeta['nama_file_asli'],
                'path_penyimpanan'          => $uploadedMeta['path_penyimpanan'],
                'nama_file_tersimpan'       => $uploadedMeta['nama_file_tersimpan'],
                'jumlah_halaman_terdeteksi' => $uploadedMeta['jumlah_halaman'],
                'checksum_sha256'           => $uploadedMeta['checksum_sha256'],
            ]);
            $idFileBaru = (int) $db->insertID();

            $db->table('pesanan')->where('id_pesanan', $idPesanan)->update([
                'status_pesanan' => 'MENUNGGU_KONFIRMASI',
            ]);

            if ($db->tableExists('revisi_komplain') && $catatanRevisi !== '') {
                $db->table('revisi_komplain')->insert([
                    'id_pesanan'   => $idPesanan,
                    'id_pelanggan' => $idPelanggan,
                    'jenis'        => 'REVISI',
                    'pesan'        => $catatanRevisi,
                    'status'       => 'DIAJUKAN',
                ]);
            }

            if ($db->transStatus() === false) {
                throw new RuntimeException('Gagal menyimpan berkas revisi.');
            }
            $db->transCommit();

            return $this->respondJson(true, 'Berkas revisi berhasil diunggah.', [
                'id_file'        => $idFileBaru,
                'status_pesanan' => 'MENUNGGU_KONFIRMASI',
                'jumlah_halaman' => $uploadedMeta['jumlah_halaman'],
            ]);
        } catch (Throwable $e) {
            $db->transRollback();

            return $this->respondJson(false, $e->getMessage(), null, 422);
        }
    }

    public function komplain(int $idPesanan)
    {
        $idPelanggan = JwtPelangganFilter::getIdPelanggan();
        $db          = Database::connect();

        $pesanan = $db->table('pesanan')
            ->where('id_pesanan', $idPesanan)
            ->where('id_pelanggan', $idPelanggan)
            ->get()
            ->getRowArray();

        if (! $pesanan) {
            return $this->respondJson(false, 'Pesanan tidak ditemukan atau bukan milik Anda.', null, 404);
        }

        if (! $db->tableExists('revisi_komplain')) {
            return $this->respondJson(false, 'Tabel revisi_komplain belum tersedia di database.', null, 500);
        }

        $input = $this->getInputData();
        $jenis = strtoupper(trim((string) ($input['jenis'] ?? 'KOMPLAIN')));
        $pesan = trim((string) ($input['pesan'] ?? ''));

        if (! in_array($jenis, ['REVISI', 'KOMPLAIN'], true)) {
            return $this->respondJson(false, 'Jenis pengajuan harus REVISI atau KOMPLAIN.', null, 422);
        }
        if ($pesan === '') {
            return $this->respondJson(false, 'Pesan komplain/revisi wajib diisi.', null, 422);
        }

        $db->table('revisi_komplain')->insert([
            'id_pesanan'   => $idPesanan,
            'id_pelanggan' => $idPelanggan,
            'jenis'        => $jenis,
            'pesan'        => $pesan,
            'status'       => 'DIAJUKAN',
        ]);
        $idRk = (int) $db->insertID();

        return $this->respondJson(true, 'Pengajuan berhasil dikirim.', [
            'id_revisi_komplain' => $idRk,
            'jenis'              => $jenis,
            'status'             => 'DIAJUKAN',
            'dibuat_pada'        => date('Y-m-d H:i:s'),
        ], 201);
    }

    private function generateKodePesanan($db): string
    {
        $yymm   = date('ym');
        $prefix = "PSN-{$yymm}-";
        $row    = $db->table('pesanan')
            ->selectMax('kode_pesanan')
            ->like('kode_pesanan', $prefix, 'after')
            ->get()
            ->getRowArray();

        $lastSeq = 0;
        if (! empty($row['kode_pesanan'])) {
            $lastSeq = (int) substr((string) $row['kode_pesanan'], -4);
        }

        return sprintf('PSN-%s-%04d', $yymm, $lastSeq + 1);
    }

    private function generateOrderIdGateway($db): string
    {
        $row = $db->table('pembayaran')
            ->selectMax('order_id_gateway')
            ->like('order_id_gateway', 'ERK-', 'after')
            ->get()
            ->getRowArray();

        $lastNum = 8800;
        if (! empty($row['order_id_gateway'])) {
            $parsed = (int) str_replace('ERK-', '', (string) $row['order_id_gateway']);
            if ($parsed > 0) {
                $lastNum = $parsed;
            }
        }

        return sprintf('ERK-%04d', $lastNum + 1);
    }

    private function generateNomorAntrean($db): string
    {
        $today = date('Y-m-d');
        $row   = $db->table('antrean')
            ->selectMax('nomor_antrean')
            ->where('tanggal_antrean', $today)
            ->get()
            ->getRowArray();

        $lastNum = 0;
        if (! empty($row['nomor_antrean'])) {
            $lastNum = (int) str_replace('A-', '', (string) $row['nomor_antrean']);
        }

        return sprintf('A-%03d', $lastNum + 1);
    }
}
