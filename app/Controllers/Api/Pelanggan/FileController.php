<?php

namespace App\Controllers\Api\Pelanggan;

use App\Filters\JwtPelangganFilter;
use Config\Database;

class FileController extends BaseApiController
{
    public function download(int $idFile)
    {
        $idPelanggan = JwtPelangganFilter::getIdPelanggan();
        $db          = Database::connect();

        $file = $db->table('file_pesanan f')
            ->select('f.*')
            ->join('detail_pesanan_layanan dpl', 'dpl.id_detail_layanan = f.id_detail_layanan', 'inner')
            ->join('pesanan p', 'p.id_pesanan = dpl.id_pesanan', 'inner')
            ->where('f.id_file', $idFile)
            ->where('f.dihapus_pada IS NULL', null, false)
            ->where('p.id_pelanggan', $idPelanggan)
            ->get()
            ->getRowArray();

        if (! $file) {
            return $this->respondJson(false, 'Akses ditolak atau berkas tidak ditemukan.', null, 403);
        }

        $relativePath = rtrim((string) $file['path_penyimpanan'], '/\\') . DIRECTORY_SEPARATOR . $file['nama_file_tersimpan'];
        $fullPath     = rtrim(ROOTPATH, '/\\') . DIRECTORY_SEPARATOR . ltrim($relativePath, '/\\');

        if (! is_file($fullPath)) {
            $fallback = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'dokumen' . DIRECTORY_SEPARATOR . $file['nama_file_tersimpan'];
            if (is_file($fallback)) {
                $fullPath = $fallback;
            } else {
                return $this->respondJson(false, 'Berkas fisik tidak ditemukan di penyimpanan server.', null, 404);
            }
        }

        return $this->response->download($fullPath, null)->setFileName((string) $file['nama_file_asli']);
    }
}
