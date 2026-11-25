<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;
use Smalot\PdfParser\Parser;
use Throwable;
use ZipArchive;

class FileUploadService
{
    private const MAX_DOKUMEN_BYTES = 52428800; // 50 MB
    private const MAX_BUKTI_BYTES   = 5242880;  // 5 MB
    private const MAX_PROFIL_BYTES  = 2097152;  // 2 MB

    private const ALLOWED_DOKUMEN_EXT = ['pdf', 'doc', 'docx'];
    private const ALLOWED_DOKUMEN_MIME = [
        'application/pdf',
        'application/x-pdf',
        'application/msword',
        'application/vnd.ms-word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
        'application/octet-stream',
    ];

    /**
     * Validasi dan simpan dokumen pesanan (.pdf, .doc, .docx maks 50 MB) ke writable/uploads/dokumen/.
     * Menghitung checksum SHA-256 dan mendeteksi jumlah halaman otomatis.
     *
     * @return array<string, mixed>
     */
    public function uploadDokumen(UploadedFile $file): array
    {
        if (! $file->isValid() || $file->hasMoved()) {
            throw new RuntimeException('Berkas dokumen tidak valid atau gagal diunggah.');
        }

        if ($file->getSize() > self::MAX_DOKUMEN_BYTES) {
            throw new RuntimeException('Ukuran berkas dokumen maksimal 50 MB.');
        }

        $ext  = strtolower($file->getClientExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        $mime = strtolower((string) $file->getMimeType());

        if (! in_array($ext, self::ALLOWED_DOKUMEN_EXT, true)) {
            throw new RuntimeException('Format berkas hanya boleh .pdf, .doc, atau .docx.');
        }
        if ($mime !== '' && ! in_array($mime, self::ALLOWED_DOKUMEN_MIME, true)) {
            throw new RuntimeException('Tipe MIME berkas dokumen tidak valid.');
        }

        $tmpPath = $file->getTempName();
        $checksum = hash_file('sha256', $tmpPath) ?: null;
        $pages    = $this->detectPageCount($tmpPath, $ext);

        $targetDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'dokumen';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $storedName = uniqid('dok_', true) . '.' . $ext;
        $file->move($targetDir, $storedName);

        return [
            'jenis_file'          => $ext,
            'nama_file_asli'      => $file->getClientName(),
            'nama_file_tersimpan' => $storedName,
            'path_penyimpanan'    => 'writable/uploads/dokumen/',
            'jumlah_halaman'      => $pages,
            'checksum_sha256'     => $checksum,
        ];
    }

    /**
     * Validasi dan simpan bukti pembayaran (.jpg, .jpeg, .png, .pdf maks 5 MB) ke writable/uploads/bukti/.
     */
    public function uploadBukti(UploadedFile $file): string
    {
        if (! $file->isValid() || $file->hasMoved()) {
            throw new RuntimeException('Berkas bukti pembayaran tidak valid.');
        }

        if ($file->getSize() > self::MAX_BUKTI_BYTES) {
            throw new RuntimeException('Ukuran bukti pembayaran maksimal 5 MB.');
        }

        $ext = strtolower($file->getClientExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
            throw new RuntimeException('Format bukti pembayaran hanya boleh JPG, PNG, atau PDF.');
        }

        $targetDir = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'bukti';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $storedName = 'bukti_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $file->move($targetDir, $storedName);

        return 'writable/uploads/bukti/' . $storedName;
    }

    /**
     * Validasi dan simpan foto profil pelanggan (.jpg, .jpeg, .png, .webp, .gif maks 2 MB) ke public/uploads/profil/.
     */
    public function uploadFotoProfil(UploadedFile $file, int $idPelanggan): string
    {
        if (! $file->isValid() || $file->hasMoved()) {
            throw new RuntimeException('Foto profil tidak valid.');
        }

        if ($file->getSize() > self::MAX_PROFIL_BYTES) {
            throw new RuntimeException('Ukuran foto profil maksimal 2 MB.');
        }

        $ext = strtolower($file->getClientExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            throw new RuntimeException('Format foto profil hanya boleh JPG, PNG, WebP, atau GIF.');
        }

        $targetDir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profil';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $storedName = 'profil_' . $idPelanggan . '_' . time() . '.' . $ext;
        $file->move($targetDir, $storedName);

        return 'uploads/profil/' . $storedName;
    }

    /**
     * Deteksi jumlah halaman otomatis:
     * - PDF: smalot/pdfparser
     * - DOCX: baca docProps/app.xml -> Pages
     * - DOC: default 1
     */
    private function detectPageCount(string $filePath, string $ext): int
    {
        if ($ext === 'pdf') {
            try {
                $parser = new Parser();
                $pdf    = $parser->parseFile($filePath);
                $count  = count($pdf->getPages());

                return $count > 0 ? $count : 1;
            } catch (Throwable $e) {
                return 1;
            }
        }

        if ($ext === 'docx' && class_exists(ZipArchive::class)) {
            try {
                $zip = new ZipArchive();
                if ($zip->open($filePath) === true) {
                    $appXml = $zip->getFromName('docProps/app.xml');
                    $zip->close();
                    if ($appXml !== false) {
                        $xml = @simplexml_load_string($appXml);
                        if ($xml !== false && isset($xml->Pages)) {
                            $pages = (int) $xml->Pages;

                            return $pages > 0 ? $pages : 1;
                        }
                    }
                }
            } catch (Throwable $e) {
                return 1;
            }
        }

        return 1;
    }
}
