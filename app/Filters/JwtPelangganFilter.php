<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class JwtPelangganFilter implements FilterInterface
{
    /**
     * Payload pelanggan terautentikasi untuk request saat ini.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $pelanggan = null;

    public static function getPelanggan(): ?array
    {
        return self::$pelanggan;
    }

    public static function getIdPelanggan(): int
    {
        return (int) (self::$pelanggan['sub'] ?? 0);
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        self::$pelanggan = null;

        if (strtoupper($request->getMethod()) === 'OPTIONS') {
            return null;
        }

        $authHeader = $request->getHeaderLine('Authorization');
        if ($authHeader === '') {
            $authHeader = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
        }

        if (! preg_match('/^\s*Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Token autentikasi tidak ditemukan.',
                    'data'    => null,
                ]);
        }

        $token  = trim($matches[1]);
        $secret = (string) (env('JWT_SECRET') ?: '');
        if ($secret === '') {
            return service('response')
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Konfigurasi JWT_SECRET belum diatur di server.',
                    'data'    => null,
                ]);
        }

        try {
            $decoded         = JWT::decode($token, new Key($secret, 'HS256'));
            self::$pelanggan = (array) $decoded;
        } catch (ExpiredException $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Sesi login telah kedaluwarsa. Silakan masuk kembali.',
                    'data'    => null,
                ]);
        } catch (Throwable $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Token autentikasi tidak valid.',
                    'data'    => null,
                ]);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
