<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Antarmuka Pelanggan (Halaman 01 - 11)
$routes->get('layanan', 'Home::layanan');
$routes->get('pesan/dokumen', 'Home::pesanDokumen');
$routes->get('pesan/spanduk', 'Home::pesanSpanduk');
$routes->get('pesan/undangan', 'Home::pesanUndangan');
$routes->get('pesan/banner', 'Home::pesanBanner');
$routes->get('pesan/(:num)', 'Home::pesan/$1');
$routes->get('keranjang', 'Home::keranjang');
$routes->get('pesanan-saya', 'Home::pesananSaya');
$routes->get('profil', 'Home::profil');

// A0 - Autentikasi Admin & Owner
$routes->get('admin/login', 'Admin\AuthController::login');
$routes->post('admin/login', 'Admin\AuthController::attemptLogin');
$routes->get('admin/logout', 'Admin\AuthController::logout');

// Panel Admin & Owner (Terproteksi Filter)
$routes->group('admin', ['filter' => 'adminauth'], function ($routes) {
    // A1 - Dashboard
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // A2 - Pesanan & A4 - Antrean FIFO
    $routes->get('pesanan', 'Admin\OperasionalController::pesanan');
    $routes->post('pesanan/update-status', 'Admin\OperasionalController::updateStatusPesanan');
    $routes->get('antrean', 'Admin\OperasionalController::antrean');
    $routes->post('antrean/update-status', 'Admin\OperasionalController::updateStatusAntrean');

    // A5 - Layanan & Opsi, A7 - Promo, A6 - Inventori, A8 - Pembelian
    $routes->get('layanan', 'Admin\KatalogInventoriController::layanan');
    $routes->post('layanan/store', 'Admin\KatalogInventoriController::storeLayanan');

    $routes->get('promo', 'Admin\KatalogInventoriController::promo');
    $routes->post('promo/store', 'Admin\KatalogInventoriController::storePromo');
    $routes->post('promo/toggle/(:num)', 'Admin\KatalogInventoriController::togglePromo/$1');

    $routes->get('inventori', 'Admin\KatalogInventoriController::inventori');
    $routes->post('inventori/store-bahan', 'Admin\KatalogInventoriController::storeBahan');
    $routes->post('inventori/penyesuaian', 'Admin\KatalogInventoriController::penyesuaianStok');

    $routes->get('pembelian', 'Admin\KatalogInventoriController::pembelian');
    $routes->post('pembelian/store-pemasok', 'Admin\KatalogInventoriController::storePemasok');
    $routes->post('pembelian/store', 'Admin\KatalogInventoriController::storePembelian');

    // A9 - Pembayaran
    $routes->get('pembayaran', 'Admin\OperasionalController::pembayaran');
    $routes->post('pembayaran/konfirmasi', 'Admin\OperasionalController::konfirmasiPembayaran');

    // A10 & A11 - Khusus Owner
    $routes->get('laporan', 'Admin\OwnerController::laporan', ['filter' => 'adminauth:OWNER']);
    $routes->get('laporan/export/(:segment)', 'Admin\OwnerController::exportLaporan/$1', ['filter' => 'adminauth:OWNER']);
    $routes->get('pengguna', 'Admin\OwnerController::pengguna', ['filter' => 'adminauth:OWNER']);
    $routes->post('pengguna/store-admin', 'Admin\OwnerController::storeAdmin', ['filter' => 'adminauth:OWNER']);
    $routes->post('pengguna/toggle-status/(:num)', 'Admin\OwnerController::toggleStatusAdmin/$1', ['filter' => 'adminauth:OWNER']);
});

// ============================================================
// API Pelanggan — Stateless JWT
// ============================================================
$routes->options('api/(:any)', static function () {
    return service('response')->setStatusCode(204);
});

// Rute API Publik (tanpa JWT)
$routes->post('api/pelanggan/register', 'Api\Pelanggan\AuthController::register');
$routes->post('api/pelanggan/login', 'Api\Pelanggan\AuthController::login');
$routes->get('api/pelanggan/katalog', 'Api\Pelanggan\KatalogController::index');
$routes->post('api/pelanggan/hitung-estimasi', 'Api\Pelanggan\KatalogController::hitungEstimasi');

// Rute API Terproteksi JWT
$routes->group('api/pelanggan', ['filter' => 'jwtpelanggan'], static function ($routes) {
    $routes->get('profil', 'Api\Pelanggan\AuthController::profil');
    $routes->post('profil/update', 'Api\Pelanggan\AuthController::updateProfil');
    $routes->get('file/(:num)', 'Api\Pelanggan\FileController::download/$1');

    $routes->post('cek-promo', 'Api\Pelanggan\PesananController::cekPromo');
    $routes->post('checkout', 'Api\Pelanggan\PesananController::checkout');
    $routes->get('pesanan', 'Api\Pelanggan\PesananController::index');
    $routes->get('pesanan/(:num)', 'Api\Pelanggan\PesananController::detail/$1');
    $routes->post('pesanan/(:num)/revisi-file', 'Api\Pelanggan\PesananController::revisiFile/$1');
    $routes->post('pesanan/(:num)/komplain', 'Api\Pelanggan\PesananController::komplain/$1');
});

// ============================================================
// API Pelanggan — Stateless JWT
// ============================================================
$routes->options('api/(:any)', static function () {
    return service('response')->setStatusCode(204);
});

// Rute API Publik (tanpa JWT)
$routes->post('api/pelanggan/register', 'Api\Pelanggan\AuthController::register');
$routes->post('api/pelanggan/login', 'Api\Pelanggan\AuthController::login');
$routes->get('api/pelanggan/katalog', 'Api\Pelanggan\KatalogController::index');
$routes->post('api/pelanggan/hitung-estimasi', 'Api\Pelanggan\KatalogController::hitungEstimasi');

// Rute API Terproteksi JWT
$routes->group('api/pelanggan', ['filter' => 'jwtpelanggan'], static function ($routes) {
    $routes->get('profil', 'Api\Pelanggan\AuthController::profil');
    $routes->post('profil/update', 'Api\Pelanggan\AuthController::updateProfil');
    $routes->get('file/(:num)', 'Api\Pelanggan\FileController::download/$1');

    $routes->post('cek-promo', 'Api\Pelanggan\PesananController::cekPromo');
    $routes->post('checkout', 'Api\Pelanggan\PesananController::checkout');
    $routes->get('pesanan', 'Api\Pelanggan\PesananController::index');
    $routes->get('pesanan/(:num)', 'Api\Pelanggan\PesananController::detail/$1');
    $routes->post('pesanan/(:num)/revisi-file', 'Api\Pelanggan\PesananController::revisiFile/$1');
    $routes->post('pesanan/(:num)/komplain', 'Api\Pelanggan\PesananController::komplain/$1');
});
