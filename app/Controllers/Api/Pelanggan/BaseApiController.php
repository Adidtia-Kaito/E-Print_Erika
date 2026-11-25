<?php

namespace App\Controllers\Api\Pelanggan;

use App\Controllers\BaseController;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

class BaseApiController extends BaseController
{
    /**
     * Format response JSON standar:
     * { "status": true/false, "message": "...", "data": ... }
     */
    protected function respondJson(bool $status, string $message, $data = null, int $httpCode = 200): ResponseInterface
    {
        return $this->response
            ->setStatusCode($httpCode)
            ->setJSON([
                'status'  => $status,
                'message' => $message,
                'data'    => $data,
            ]);
    }

    /**
     * Ambil input request dari JSON body maupun form POST secara seragam.
     *
     * @return array<string, mixed>
     */
    protected function getInputData(): array
    {
        $contentType = strtolower((string) $this->request->getHeaderLine('Content-Type'));
        if (str_contains($contentType, 'application/json')) {
            $decoded = $this->request->getJSON(true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $post = $this->request->getPost();

        return is_array($post) ? $post : [];
    }

    /**
     * Hitung estimasi biaya layanan dan rincian opsi (dipakai bersama oleh hitungEstimasi & checkout).
     *
     * @param array<string, mixed> $layanan
     * @param list<int>            $opsiIds
     *
     * @return array<string, mixed>
     */
    protected function calculateLayananEstimate(
        BaseConnection $db,
        array $layanan,
        int $jumlahHalaman,
        int $jumlahSalinan,
        ?float $panjangCm,
        ?float $lebarCm,
        array $opsiIds
    ): array {
        $butuhDimensi = (int) ($layanan['butuh_dimensi'] ?? 0) === 1;
        $jumlahSalinan = max(1, $jumlahSalinan);
        $jumlahHalaman = max(1, $jumlahHalaman);

        $luasM2 = null;
        if ($butuhDimensi) {
            $p = max(1.0, (float) ($panjangCm ?? 100));
            $l = max(1.0, (float) ($lebarCm ?? 100));
            $luasM2 = round(($p / 100.0) * ($l / 100.0), 4);
            $kuantitasHitung = $luasM2 * $jumlahSalinan;
        } else {
            $kuantitasHitung = (float) ($jumlahHalaman * $jumlahSalinan);
        }

        $hargaDasar = (float) ($layanan['harga_dasar'] ?? 0);
        $biayaDasar = round($hargaDasar * $kuantitasHitung, 2);

        $opsiIds = array_values(array_unique(array_filter(array_map('intval', $opsiIds), static fn ($v) => $v > 0)));
        $detailOpsi = [];
        $biayaOpsiTotal = 0.0;

        if (! empty($opsiIds)) {
            $opsiRows = $db->table('opsi_layanan')
                ->where('id_layanan', (int) $layanan['id_layanan'])
                ->where('status_aktif', 1)
                ->whereIn('id_opsi', $opsiIds)
                ->get()
                ->getResultArray();

            foreach ($opsiRows as $opsi) {
                $hargaTambahan = (float) $opsi['harga_tambahan'];
                $subtotalOpsi  = strtoupper((string) $opsi['tipe_harga']) === 'PER_UNIT'
                    ? round($hargaTambahan * $kuantitasHitung, 2)
                    : round($hargaTambahan, 2);

                $biayaOpsiTotal += $subtotalOpsi;
                $detailOpsi[] = [
                    'id_opsi'    => (int) $opsi['id_opsi'],
                    'nama_opsi'  => $opsi['nama_opsi'],
                    'grup_opsi'  => $opsi['grup_opsi'],
                    'tipe_harga' => $opsi['tipe_harga'],
                    'biaya'      => $subtotalOpsi,
                ];
            }
        }

        $subTotal = round($biayaDasar + $biayaOpsiTotal, 2);

        return [
            'layanan'          => $layanan['nama_layanan'],
            'kuantitas_hitung' => $kuantitasHitung,
            'luas_m2'          => $luasM2,
            'biaya_dasar'      => $biayaDasar,
            'detail_opsi'      => $detailOpsi,
            'biaya_opsi_total' => round($biayaOpsiTotal, 2),
            'sub_total'        => $subTotal,
        ];
    }

    /**
     * Validasi dan hitung nominal diskon promo (dipakai bersama oleh cekPromo & checkout).
     *
     * @param list<int> $kategoriIds
     *
     * @return array<string, mixed>
     */
    protected function validateAndComputePromo(
        BaseConnection $db,
        string $kodePromo,
        float $subtotal,
        array $kategoriIds = []
    ): array {
        $kode = strtoupper(trim($kodePromo));
        if ($kode === '') {
            throw new RuntimeException('Kode promo wajib diisi.');
        }

        $promo = $db->table('promo')
            ->where('UPPER(kode_promo)', $kode)
            ->get()
            ->getRowArray();

        if (! $promo) {
            throw new RuntimeException('Kode promo tidak ditemukan.');
        }

        if ((int) $promo['status_aktif'] !== 1) {
            throw new RuntimeException('Promo saat ini tidak aktif.');
        }

        $now = date('Y-m-d H:i:s');
        if ($now < $promo['berlaku_dari'] || $now > $promo['berlaku_sampai']) {
            throw new RuntimeException('Promo belum berlaku atau sudah kedaluwarsa.');
        }

        if ((int) $promo['jumlah_terpakai'] >= (int) $promo['kuota']) {
            throw new RuntimeException('Kuota penggunaan promo sudah habis.');
        }

        $minimalBelanja = (float) $promo['minimal_belanja'];
        if ($subtotal < $minimalBelanja) {
            throw new RuntimeException('Minimal belanja Rp ' . number_format($minimalBelanja, 0, ',', '.') . ' belum terpenuhi.');
        }

        if (! empty($promo['id_kategori_layanan'])) {
            $kategoriIdsInt = array_map('intval', $kategoriIds);
            if (! in_array((int) $promo['id_kategori_layanan'], $kategoriIdsInt, true)) {
                $kat = $db->table('kategori_layanan')
                    ->where('id_kategori_layanan', (int) $promo['id_kategori_layanan'])
                    ->get()
                    ->getRowArray();
                $namaKat = $kat['nama_kategori'] ?? 'tertentu';

                throw new RuntimeException('Promo ini hanya berlaku untuk kategori ' . $namaKat . '.');
            }
        }

        $nilai = (float) $promo['nilai_diskon'];
        if (strtoupper((string) $promo['jenis_diskon']) === 'PERSEN') {
            $diskon = round($subtotal * ($nilai / 100.0), 2);
            if (! empty($promo['maks_diskon']) && $diskon > (float) $promo['maks_diskon']) {
                $diskon = (float) $promo['maks_diskon'];
            }
        } else {
            $diskon = round(min($nilai, $subtotal), 2);
        }

        return [
            'id_promo'          => (int) $promo['id_promo'],
            'kode_promo'        => $promo['kode_promo'],
            'nama_promo'        => $promo['nama_promo'],
            'jenis_diskon'      => $promo['jenis_diskon'],
            'nominal_diskon'    => $diskon,
            'subtotal_sebelum'  => round($subtotal, 2),
            'subtotal_setelah'  => round(max(0.0, $subtotal - $diskon), 2),
        ];
    }
}
