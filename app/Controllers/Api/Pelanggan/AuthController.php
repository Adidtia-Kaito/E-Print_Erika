<?php

namespace App\Controllers\Api\Pelanggan;

use App\Filters\JwtPelangganFilter;
use App\Libraries\FileUploadService;
use App\Models\PelangganModel;
use Firebase\JWT\JWT;
use Throwable;

class AuthController extends BaseApiController
{
    public function register()
    {
        $input = $this->getInputData();

        $namaLengkap = trim((string) ($input['nama_lengkap'] ?? ''));
        $email       = strtolower(trim((string) ($input['email'] ?? '')));
        $noWa        = trim((string) ($input['no_wa'] ?? ''));
        $kataSandi   = (string) ($input['kata_sandi'] ?? '');

        if (mb_strlen($namaLengkap) < 3) {
            return $this->respondJson(false, 'Nama lengkap minimal 3 karakter.', null, 422);
        }
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respondJson(false, 'Format email tidak valid.', null, 422);
        }
        if ($noWa === '' || mb_strlen($noWa) < 8) {
            return $this->respondJson(false, 'Nomor WhatsApp wajib diisi minimal 8 digit.', null, 422);
        }
        if (mb_strlen($kataSandi) < 8) {
            return $this->respondJson(false, 'Kata sandi minimal 8 karakter.', null, 422);
        }

        $model = new PelangganModel();
        if ($model->where('email', $email)->first()) {
            return $this->respondJson(false, 'Email sudah terdaftar. Silakan masuk.', null, 409);
        }
        if ($model->where('no_wa', $noWa)->first()) {
            return $this->respondJson(false, 'Nomor WhatsApp sudah terdaftar.', null, 409);
        }

        $id = $model->insert([
            'nama_lengkap' => $namaLengkap,
            'email'        => $email,
            'no_wa'        => $noWa,
            'kata_sandi'   => password_hash($kataSandi, PASSWORD_BCRYPT),
        ], true);

        if (! $id) {
            return $this->respondJson(false, 'Gagal menyimpan data pelanggan.', null, 500);
        }

        $pelanggan = $this->sanitizeProfil($model->find($id));
        $token     = $this->generateJwt($pelanggan);

        return $this->respondJson(true, 'Registrasi berhasil.', [
            'token'  => $token,
            'profil' => $pelanggan,
        ], 201);
    }

    public function login()
    {
        $input      = $this->getInputData();
        $identifier = trim((string) ($input['identifier'] ?? $input['email'] ?? $input['no_wa'] ?? ''));
        $kataSandi  = (string) ($input['kata_sandi'] ?? '');

        if ($identifier === '' || $kataSandi === '') {
            return $this->respondJson(false, 'Email / No. WA dan kata sandi wajib diisi.', null, 422);
        }

        $model     = new PelangganModel();
        $pelanggan = $model->groupStart()
            ->where('email', strtolower($identifier))
            ->orWhere('no_wa', $identifier)
            ->groupEnd()
            ->first();

        if (! $pelanggan || ! password_verify($kataSandi, (string) $pelanggan['kata_sandi'])) {
            return $this->respondJson(false, 'Email/No. WhatsApp atau kata sandi salah.', null, 401);
        }

        $profil = $this->sanitizeProfil($pelanggan);
        $token  = $this->generateJwt($profil);

        return $this->respondJson(true, 'Login berhasil.', [
            'token'  => $token,
            'profil' => $profil,
        ]);
    }

    public function profil()
    {
        $idPelanggan = JwtPelangganFilter::getIdPelanggan();
        $model       = new PelangganModel();
        $pelanggan   = $model->find($idPelanggan);

        if (! $pelanggan) {
            return $this->respondJson(false, 'Data pelanggan tidak ditemukan.', null, 404);
        }

        return $this->respondJson(true, 'Profil pelanggan berhasil diambil.', $this->sanitizeProfil($pelanggan));
    }

    public function updateProfil()
    {
        $idPelanggan = JwtPelangganFilter::getIdPelanggan();
        $model       = new PelangganModel();
        $pelanggan   = $model->find($idPelanggan);

        if (! $pelanggan) {
            return $this->respondJson(false, 'Data pelanggan tidak ditemukan.', null, 404);
        }

        $input       = $this->getInputData();
        $namaLengkap = trim((string) ($input['nama_lengkap'] ?? $pelanggan['nama_lengkap']));
        $email       = strtolower(trim((string) ($input['email'] ?? $pelanggan['email'])));
        $noWa        = trim((string) ($input['no_wa'] ?? $pelanggan['no_wa']));

        if (mb_strlen($namaLengkap) < 3) {
            return $this->respondJson(false, 'Nama lengkap minimal 3 karakter.', null, 422);
        }
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respondJson(false, 'Format email tidak valid.', null, 422);
        }
        if ($noWa === '' || mb_strlen($noWa) < 8) {
            return $this->respondJson(false, 'Nomor WhatsApp minimal 8 digit.', null, 422);
        }

        $dupEmail = $model->where('email', $email)->where('id_pelanggan !=', $idPelanggan)->first();
        if ($dupEmail) {
            return $this->respondJson(false, 'Email sudah digunakan oleh akun lain.', null, 409);
        }

        $dupWa = $model->where('no_wa', $noWa)->where('id_pelanggan !=', $idPelanggan)->first();
        if ($dupWa) {
            return $this->respondJson(false, 'Nomor WhatsApp sudah digunakan oleh akun lain.', null, 409);
        }

        $updateData = [
            'nama_lengkap' => $namaLengkap,
            'email'        => $email,
            'no_wa'        => $noWa,
        ];

        $kataSandiBaru = (string) ($input['kata_sandi_baru'] ?? '');
        if ($kataSandiBaru !== '') {
            $kataSandiLama = (string) ($input['kata_sandi_lama'] ?? '');
            if (! password_verify($kataSandiLama, (string) $pelanggan['kata_sandi'])) {
                return $this->respondJson(false, 'Kata sandi lama tidak sesuai.', null, 422);
            }
            if (mb_strlen($kataSandiBaru) < 8) {
                return $this->respondJson(false, 'Kata sandi baru minimal 8 karakter.', null, 422);
            }
            $updateData['kata_sandi'] = password_hash($kataSandiBaru, PASSWORD_BCRYPT);
        }

        $fotoFile = $this->request->getFile('foto_profile');
        if ($fotoFile && $fotoFile->getError() !== UPLOAD_ERR_NO_FILE) {
            try {
                $uploadService = new FileUploadService();
                $newFotoPath   = $uploadService->uploadFotoProfil($fotoFile, $idPelanggan);

                if (! empty($pelanggan['foto_profile'])) {
                    $oldPath = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . ltrim((string) $pelanggan['foto_profile'], '/\\');
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $updateData['foto_profile'] = $newFotoPath;
            } catch (Throwable $e) {
                return $this->respondJson(false, $e->getMessage(), null, 422);
            }
        }

        $model->update($idPelanggan, $updateData);
        $updated = $this->sanitizeProfil($model->find($idPelanggan));

        return $this->respondJson(true, 'Profil berhasil diperbarui.', $updated);
    }

    /**
     * @param array<string, mixed> $row
     *
     * @return array<string, mixed>
     */
    private function sanitizeProfil(array $row): array
    {
        unset($row['kata_sandi']);

        return $row;
    }

    /**
     * @param array<string, mixed> $profil
     */
    private function generateJwt(array $profil): string
    {
        $secret = (string) (env('JWT_SECRET') ?: 'eprint_erika_jwt_fallback_secret');
        $ttl    = (int) (env('JWT_TTL') ?: 86400);
        $now    = time();

        $payload = [
            'iss'   => base_url(),
            'sub'   => (int) $profil['id_pelanggan'],
            'nama'  => $profil['nama_lengkap'] ?? '',
            'email' => $profil['email'] ?? '',
            'no_wa' => $profil['no_wa'] ?? '',
            'iat'   => $now,
            'exp'   => $now + $ttl,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }
}
