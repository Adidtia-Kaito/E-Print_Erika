-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2026 at 03:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_eprint_erika`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_wa` varchar(20) DEFAULT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` enum('OWNER','ADMIN','KASIR') NOT NULL DEFAULT 'ADMIN',
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_lengkap`, `email`, `no_wa`, `kata_sandi`, `username`, `role`, `status_aktif`) VALUES
(1, 'Erika Siregar', 'erika@percetakan.id', '0812-0000-0001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'erika', 'OWNER', 1),
(2, 'Rina Pratiwi', 'rina@percetakan.id', '0812-0000-0002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rina.admin', 'ADMIN', 1),
(3, 'Joko Santoso', 'joko@percetakan.id', '0812-0000-0003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'joko.admin', 'ADMIN', 0);

-- --------------------------------------------------------

--
-- Table structure for table `antrean`
--

CREATE TABLE `antrean` (
  `id_antrean` int(11) NOT NULL,
  `tanggal_antrean` date NOT NULL,
  `waktu_mulai` datetime DEFAULT NULL,
  `nomor_antrean` varchar(20) NOT NULL,
  `status_antrean` enum('MENUNGGU','DIKERJAKAN','SIAP_DIAMBIL','SELESAI') NOT NULL DEFAULT 'MENUNGGU',
  `prioritas` enum('NORMAL','TINGGI') NOT NULL DEFAULT 'NORMAL',
  `waktu_masuk` datetime NOT NULL DEFAULT current_timestamp(),
  `id_pesanan` int(11) NOT NULL,
  `waktu_selesai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `antrean`
--

INSERT INTO `antrean` (`id_antrean`, `tanggal_antrean`, `waktu_mulai`, `nomor_antrean`, `status_antrean`, `prioritas`, `waktu_masuk`, `id_pesanan`, `waktu_selesai`) VALUES
(1, '2026-11-25', '2026-11-25 14:08:26', 'A-017', 'DIKERJAKAN', 'NORMAL', '2026-11-25 14:40:00', 5, NULL),
(2, '2026-11-25', '2026-11-25 14:45:00', 'A-014', 'MENUNGGU', 'NORMAL', '2026-11-25 14:20:00', 1, NULL),
(3, '2026-11-25', '2026-11-25 14:10:00', 'A-013', 'SIAP_DIAMBIL', 'NORMAL', '2026-11-25 11:40:00', 3, NULL),
(4, '2026-11-25', '2026-11-25 13:10:00', 'A-011', 'SIAP_DIAMBIL', 'NORMAL', '2026-11-25 13:05:00', 2, '2026-11-25 13:50:00'),
(5, '2026-11-25', '2026-11-25 11:30:00', 'A-009', 'MENUNGGU', 'NORMAL', '2026-11-25 11:00:00', 6, '2026-11-25 12:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id_bahan` int(11) NOT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `kode_bahan` varchar(30) NOT NULL,
  `nama_bahan` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(20) NOT NULL,
  `jenis_bahan` varchar(30) NOT NULL,
  `stok_minimum` int(11) NOT NULL DEFAULT 5,
  `harga_beli_terakhir` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bahan_baku`
--

INSERT INTO `bahan_baku` (`id_bahan`, `dibuat_pada`, `kode_bahan`, `nama_bahan`, `stok`, `satuan`, `jenis_bahan`, `stok_minimum`, `harga_beli_terakhir`, `status_aktif`) VALUES
(1, '2026-11-25 19:49:12', 'BHN-001', 'HVS 80 gr (rim)', 42, 'rim', 'Kertas', 10, 52000.00, 1),
(2, '2026-11-25 19:49:12', 'BHN-004', 'Art Carton 260 gr', 80, 'lbr', 'Kertas', 200, 1100.00, 1),
(3, '2026-11-25 19:49:12', 'BHN-007', 'Flexi 340 gr', 12, 'm', 'Spanduk', 30, 14500.00, 1),
(4, '2026-11-25 19:49:12', 'BHN-008', 'Albatros', 65, 'm', 'Spanduk', 30, 28000.00, 1),
(5, '2026-11-25 19:49:12', 'BHN-011', 'Tinta Cyan', 1, 'btl', 'Tinta', 3, 85000.00, 1),
(6, '2026-11-25 19:49:12', 'BHN-015', 'Kawat Spiral 10mm', 6, 'pak', 'Jilid', 10, 38000.00, 1),
(7, '2026-11-25 19:49:12', 'BHN-016', 'Lakban Jilid', 24, 'rol', 'Jilid', 10, 9000.00, 1),
(8, '2026-11-25 20:34:50', 'BHN-017', 'Kertas Jeruk', 10, 'rim', 'Kertas', 5, 20.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian`
--

CREATE TABLE `detail_pembelian` (
  `id_detail_pembelian` int(11) NOT NULL,
  `id_pembelian` int(11) NOT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `id_bahan` int(11) DEFAULT NULL,
  `harga_beli_satuan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sub_total` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_pembelian`
--

INSERT INTO `detail_pembelian` (`id_detail_pembelian`, `id_pembelian`, `id_produk`, `jumlah`, `id_bahan`, `harga_beli_satuan`, `sub_total`) VALUES
(1, 1, NULL, 20, 1, 52000.00, 1040000.00),
(2, 1, NULL, 400, 2, 1100.00, 440000.00),
(3, 1, 1, 2, NULL, 40000.00, 80000.00);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan_layanan`
--

CREATE TABLE `detail_pesanan_layanan` (
  `id_detail_layanan` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_layanan` int(11) NOT NULL,
  `panjang_cm` decimal(8,2) DEFAULT NULL,
  `jumlah_salinan` int(11) NOT NULL DEFAULT 1,
  `harga_satuan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_item` varchar(30) NOT NULL DEFAULT 'MENUNGGU',
  `judul_pekerjaan` varchar(150) DEFAULT NULL,
  `jumlah_halaman` int(11) NOT NULL DEFAULT 1,
  `sub_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `lebar_cm` decimal(8,2) DEFAULT NULL,
  `spesifikasi_tambahan` text DEFAULT NULL,
  `catatan_pekerjaan` text DEFAULT NULL,
  `kuantitas_hitung` int(11) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_pesanan_layanan`
--

INSERT INTO `detail_pesanan_layanan` (`id_detail_layanan`, `id_pesanan`, `id_layanan`, `panjang_cm`, `jumlah_salinan`, `harga_satuan`, `status_item`, `judul_pekerjaan`, `jumlah_halaman`, `sub_total`, `lebar_cm`, `spesifikasi_tambahan`, `catatan_pekerjaan`, `kuantitas_hitung`, `dibuat_pada`) VALUES
(1, 1, 4, NULL, 1, 25000.00, 'DIPROSES', 'Spanduk Grand Opening', 1, 98000.00, NULL, NULL, NULL, 4, '2026-11-25 19:49:13'),
(2, 2, 2, NULL, 1, 500.00, 'SELESAI', 'Skripsi Bab 1-3', 80, 45000.00, NULL, NULL, NULL, 80, '2026-11-25 19:49:13'),
(3, 3, 6, NULL, 2, 55000.00, 'DIPROSES', 'Banner Event Kampus', 1, 245000.00, NULL, NULL, NULL, 2, '2026-11-25 19:49:13'),
(4, 5, 5, NULL, 280, 1500.00, 'MENUNGGU', 'Undangan Anisa & Rizki', 1, 420000.00, NULL, NULL, NULL, 280, '2026-11-25 19:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan_produk`
--

CREATE TABLE `detail_pesanan_produk` (
  `id_detail_produk` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `harga_satuan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sub_total` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_pesanan`
--

CREATE TABLE `file_pesanan` (
  `id_file` int(11) NOT NULL,
  `id_detail_layanan` int(11) NOT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `jenis_file` varchar(20) NOT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `path_penyimpanan` varchar(255) NOT NULL,
  `nama_file_tersimpan` varchar(255) NOT NULL,
  `jumlah_halaman_terdeteksi` int(11) NOT NULL DEFAULT 1,
  `checksum_sha256` char(64) DEFAULT NULL,
  `waktu_unggah` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_layanan`
--

CREATE TABLE `kategori_layanan` (
  `id_kategori_layanan` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `deskripsi` text DEFAULT NULL,
  `ikon` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_layanan`
--

INSERT INTO `kategori_layanan` (`id_kategori_layanan`, `nama_kategori`, `slug`, `status_aktif`, `deskripsi`, `ikon`) VALUES
(1, 'Print Dokumen & Jilid', 'print-dokumen', 1, 'Cetak dokumen hitam putih, warna, dan aneka jilid', 'bi-printer'),
(2, 'Cetak Spanduk', 'cetak-spanduk', 1, 'Cetak spanduk outdoor & indoor bahan Flexi', 'bi-image'),
(3, 'Cetak Undangan', 'cetak-undangan', 1, 'Cetak undangan pernikahan, khitanan, dan acara', 'bi-envelope-heart'),
(4, 'Cetak Banner', 'cetak-banner', 1, 'X-Banner dan Roll-Banner promosi', 'bi-easel');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_produk`
--

CREATE TABLE `kategori_produk` (
  `id_kategori_produk` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_produk`
--

INSERT INTO `kategori_produk` (`id_kategori_produk`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Alat Tulis', 'Pulpen, pensil, penghapus, tipe-x'),
(2, 'Map & Kertas', 'Map plastik, map kertas, amplop');

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` int(11) NOT NULL,
  `gambar_contoh` varchar(255) DEFAULT NULL,
  `kode_layanan` varchar(30) NOT NULL,
  `harga_dasar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nama_layanan` varchar(100) NOT NULL,
  `satuan_hitung` varchar(20) NOT NULL,
  `id_kategori_layanan` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `butuh_file` tinyint(1) NOT NULL DEFAULT 1,
  `butuh_dimensi` tinyint(1) NOT NULL DEFAULT 0,
  `estimasi_durasi_menit` int(11) NOT NULL DEFAULT 30,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `gambar_contoh`, `kode_layanan`, `harga_dasar`, `nama_layanan`, `satuan_hitung`, `id_kategori_layanan`, `deskripsi`, `butuh_file`, `butuh_dimensi`, `estimasi_durasi_menit`, `status_aktif`, `dibuat_pada`) VALUES
(1, NULL, 'PRT-DOK', 500.00, 'Print Dokumen Biasa', 'lembar', 1, 'Cetak dokumen A4/F4 hitam putih atau warna', 1, 0, 15, 1, '2026-11-25 19:49:12'),
(2, NULL, 'PRT-JLD', 5000.00, 'Print + Jilid', 'jilid', 1, 'Cetak dokumen lengkap dengan jilid lakban/spiral', 1, 0, 60, 1, '2026-11-25 19:49:12'),
(3, NULL, 'PRT-UPL', 500.00, 'Upload Dokumen', 'lembar', 1, 'Pre-order cetak dokumen online', 1, 0, 30, 1, '2026-11-25 19:49:12'),
(4, NULL, 'SPD-001', 25000.00, 'Cetak Spanduk', 'm2', 2, 'Cetak spanduk per meter persegi', 0, 1, 1440, 1, '2026-11-25 19:49:12'),
(5, NULL, 'UND-001', 1500.00, 'Cetak Undangan', 'lembar', 3, 'Cetak undangan custom', 0, 0, 2880, 1, '2026-11-25 19:49:12'),
(6, NULL, 'BNR-001', 55000.00, 'Cetak Banner', 'm2', 4, 'Cetak X-Banner / Roll-Banner', 0, 1, 1440, 1, '2026-11-25 19:49:12');

-- --------------------------------------------------------

--
-- Table structure for table `layanan_bahan`
--

CREATE TABLE `layanan_bahan` (
  `id_layanan` int(11) NOT NULL,
  `id_bahan` int(11) NOT NULL,
  `jumlah_pemakaian` decimal(10,2) NOT NULL DEFAULT 1.00,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layanan_bahan`
--

INSERT INTO `layanan_bahan` (`id_layanan`, `id_bahan`, `jumlah_pemakaian`, `keterangan`) VALUES
(1, 1, 0.00, '1 lembar per halaman'),
(6, 3, 1.00, 'Bahan utama 1 m2/m2'),
(6, 5, 0.02, 'Cetak 0.02 btl/m2');

-- --------------------------------------------------------

--
-- Table structure for table `mutasi_stok`
--

CREATE TABLE `mutasi_stok` (
  `id_mutasi` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `id_pesanan` int(11) DEFAULT NULL,
  `id_bahan` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `jenis_mutasi` enum('TAMBAH','KURANG','KOREKSI') NOT NULL,
  `id_pembelian` int(11) DEFAULT NULL,
  `stok_sesudah` int(11) NOT NULL,
  `waktu_mutasi` datetime NOT NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL,
  `stok_sebelum` int(11) NOT NULL,
  `id_produk` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mutasi_stok`
--

INSERT INTO `mutasi_stok` (`id_mutasi`, `id_admin`, `id_pesanan`, `id_bahan`, `jumlah`, `jenis_mutasi`, `id_pembelian`, `stok_sesudah`, `waktu_mutasi`, `keterangan`, `stok_sebelum`, `id_produk`) VALUES
(1, 1, 1, 3, 3, 'KURANG', NULL, 12, '2026-11-25 14:45:00', 'PSN-2611-0042 - Cetak Spanduk', 15, NULL),
(2, 2, NULL, 1, 20, 'TAMBAH', 1, 42, '2026-11-25 10:10:00', 'FKT-2511-007 - Pembelian Kertas HVS', 22, NULL),
(3, 1, NULL, 5, 1, 'KOREKSI', NULL, 1, '2026-11-24 16:00:00', 'Stok opname bulanan', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `opsi_detail_pesanan`
--

CREATE TABLE `opsi_detail_pesanan` (
  `id_detail_pesanan` int(11) NOT NULL,
  `id_opsi` int(11) NOT NULL,
  `subtotal_opsi` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opsi_layanan`
--

CREATE TABLE `opsi_layanan` (
  `id_opsi` int(11) NOT NULL,
  `nama_opsi` varchar(100) NOT NULL,
  `grup_opsi` varchar(50) NOT NULL,
  `tipe_harga` varchar(20) NOT NULL DEFAULT 'TETAP',
  `wajib_dipilih` tinyint(1) NOT NULL DEFAULT 0,
  `harga_tambahan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `id_layanan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opsi_layanan`
--

INSERT INTO `opsi_layanan` (`id_opsi`, `nama_opsi`, `grup_opsi`, `tipe_harga`, `wajib_dipilih`, `harga_tambahan`, `status_aktif`, `id_layanan`) VALUES
(1, 'X-Banner', 'Rangka', 'TETAP', 1, 45000.00, 1, 6),
(2, 'Roll-Banner', 'Rangka', 'TETAP', 1, 120000.00, 1, 6),
(3, 'Flexi 340 gr', 'Bahan', 'PER_UNIT', 1, 5000.00, 1, 6),
(4, 'Albatros', 'Bahan', 'PER_UNIT', 1, 15000.00, 1, 6),
(5, 'Jilid Lakban', 'Jilid', 'TETAP', 0, 3000.00, 1, 2),
(6, 'Jilid Spiral Kawat', 'Jilid', 'TETAP', 0, 10000.00, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_wa` varchar(20) DEFAULT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `foto_profile` varchar(255) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_lengkap`, `email`, `no_wa`, `kata_sandi`, `foto_profile`, `dibuat_pada`) VALUES
(1, 'Siti Aminah', 'siti.aminah@email.com', '0812-7777-7890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-25 19:49:12'),
(2, 'Budi Santoso', 'budi.s@email.com', '0813-8888-1122', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-25 19:49:12'),
(3, 'CV Maju Jaya', 'majujaya@email.com', '0812-9999-3344', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-25 19:49:12'),
(4, 'Rina Wulandari', 'rina.wulan@email.com', '0852-1111-2233', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-25 19:49:12'),
(5, 'Andi Pratama', 'andi.p@email.com', '0821-4444-5566', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-25 19:49:12'),
(6, 'Dewi Lestari', 'dewi.l@email.com', '0813-6666-7788', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-25 19:49:12'),
(7, 'Hendra', 'hendra@email.com', '0812-3333-4455', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, '2026-11-25 19:49:12');

-- --------------------------------------------------------

--
-- Table structure for table `pemasok`
--

CREATE TABLE `pemasok` (
  `id_pemasok` int(11) NOT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `nama_pemasok` varchar(100) NOT NULL,
  `kontak_pemasok` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `jenis_pasokan` varchar(50) DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pemasok`
--

INSERT INTO `pemasok` (`id_pemasok`, `dibuat_pada`, `nama_pemasok`, `kontak_pemasok`, `email`, `no_telp`, `alamat`, `jenis_pasokan`, `status_aktif`) VALUES
(1, '2026-11-25 19:49:13', 'CV Sumber Kertas', 'Pak Hadi', 'sales@sumberkertas.id', '0812-5555-1111', 'Jl. Sutomo No. 45, Medan', 'Kertas', 1),
(2, '2026-11-25 19:49:13', 'UD Flexi Jaya', 'Bu Lina', 'flexijaya@mail.id', '0813-5555-2222', 'Jl. Gatot Subroto No. 112, Medan', 'Spanduk & Flexi', 1),
(3, '2026-11-25 19:49:13', 'Toko Tinta Prima', 'Pak Rudi', 'tintaprima@mail.id', '0812-5555-3333', 'Jl. Iskandar Muda No. 88, Medan', 'Tinta & ATK', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `order_id_gateway` varchar(100) DEFAULT NULL,
  `id_pesanan` int(11) NOT NULL,
  `nama_channel` varchar(50) DEFAULT NULL,
  `metode_pembayaran` enum('QRIS','TRANSFER_BANK','TUNAI') NOT NULL DEFAULT 'QRIS',
  `id_transaksi_gateway` int(11) DEFAULT NULL,
  `nomor_va` varchar(30) DEFAULT NULL,
  `jumlah_tagihan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `jumlah_dibayar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` enum('MENUNGGU_VERIFIKASI','TERKONFIRMASI','DIBATALKAN','KEDALUWARSA') NOT NULL DEFAULT 'MENUNGGU_VERIFIKASI',
  `token_pembayaran` text DEFAULT NULL,
  `url_pembayaran` text DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `waktu_dibayar` datetime DEFAULT NULL,
  `waktu_dibuat` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_admin`, `order_id_gateway`, `id_pesanan`, `nama_channel`, `metode_pembayaran`, `id_transaksi_gateway`, `nomor_va`, `jumlah_tagihan`, `jumlah_dibayar`, `status_pembayaran`, `token_pembayaran`, `url_pembayaran`, `bukti_pembayaran`, `waktu_dibayar`, `waktu_dibuat`) VALUES
(1, 1, 'ERK-8821', 1, 'Virtual Account BCA', 'TRANSFER_BANK', NULL, '8077 0812 3456', 98000.00, 98000.00, 'TERKONFIRMASI', NULL, NULL, NULL, '2026-11-25 14:32:00', '2026-11-25 19:49:13'),
(2, 2, 'ERK-8820', 2, 'QRIS', 'QRIS', NULL, NULL, 45000.00, 45000.00, 'TERKONFIRMASI', NULL, NULL, NULL, '2026-11-25 13:10:00', '2026-11-25 19:49:13'),
(3, 2, 'ERK-8819', 3, 'Virtual Account BRI', 'TRANSFER_BANK', NULL, '8830 0812 9981', 245000.00, 245000.00, 'TERKONFIRMASI', NULL, NULL, NULL, '2026-11-25 11:48:00', '2026-11-25 19:49:13'),
(4, 1, 'ERK-8815', 5, 'Virtual Account Mandiri', 'TRANSFER_BANK', NULL, '8908 1234 5566', 420000.00, 420000.00, 'TERKONFIRMASI', NULL, NULL, NULL, '2026-11-25 14:07:41', '2026-11-25 19:49:13'),
(5, NULL, 'ERK-8809', 7, 'E-Wallet GoPay', 'QRIS', NULL, NULL, 189000.00, 0.00, 'KEDALUWARSA', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id_pembelian` int(11) NOT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `id_pemasok` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `nomor_faktur` varchar(50) NOT NULL,
  `total_pembelian` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` enum('LUNAS','BELUM_LUNAS') NOT NULL DEFAULT 'LUNAS',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id_pembelian`, `dibuat_pada`, `id_pemasok`, `id_admin`, `tanggal_pembelian`, `nomor_faktur`, `total_pembelian`, `status_pembayaran`, `catatan`) VALUES
(1, '2026-11-25 19:49:13', 1, 2, '2026-11-25', 'FKT-2511-007', 1560000.00, 'LUNAS', 'Restok kertas HVS, Art Carton, dan pulpen'),
(2, '2026-11-25 19:49:13', 2, 1, '2026-11-22', 'FKT-2511-006', 870000.00, 'BELUM_LUNAS', 'Bahan Flexi spanduk'),
(3, '2026-11-25 19:49:13', 3, 2, '2026-11-18', 'FKT-2511-005', 1020000.00, 'LUNAS', 'Tinta printer CMYK');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `kode_pesanan` varchar(30) NOT NULL,
  `diskon` decimal(12,2) NOT NULL DEFAULT 0.00,
  `id_pelanggan` int(11) DEFAULT NULL,
  `nama_pesanan` varchar(100) NOT NULL,
  `tipe_pesanan` enum('ONLINE','WALKIN') NOT NULL DEFAULT 'ONLINE',
  `no_kontak` varchar(20) DEFAULT NULL,
  `status_pesanan` enum('MENUNGGU_KONFIRMASI','DIPROSES_DICETAK','PERLU_REVISI','SIAP_DIAMBIL','SELESAI','DIBATALKAN') NOT NULL DEFAULT 'MENUNGGU_KONFIRMASI',
  `status_pembayaran` enum('BELUM_BAYAR','MENUNGGU_VERIFIKASI','LUNAS','REFUND','DIBATALKAN') NOT NULL DEFAULT 'BELUM_BAYAR',
  `id_admin` int(11) DEFAULT NULL,
  `id_promo` int(11) DEFAULT NULL,
  `subtotal_layanan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal_produk` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_bayar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `alasan_batal` text DEFAULT NULL,
  `biaya_gateway` decimal(12,2) NOT NULL DEFAULT 0.00,
  `catatan_pelanggan` text DEFAULT NULL,
  `waktu_pesan` datetime NOT NULL DEFAULT current_timestamp(),
  `estimasi_selesai` datetime DEFAULT NULL,
  `waktu_siap` datetime DEFAULT NULL,
  `waktu_diambil` datetime DEFAULT NULL,
  `nama_pengambil` varchar(100) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `kode_pesanan`, `diskon`, `id_pelanggan`, `nama_pesanan`, `tipe_pesanan`, `no_kontak`, `status_pesanan`, `status_pembayaran`, `id_admin`, `id_promo`, `subtotal_layanan`, `subtotal_produk`, `total_bayar`, `alasan_batal`, `biaya_gateway`, `catatan_pelanggan`, `waktu_pesan`, `estimasi_selesai`, `waktu_siap`, `waktu_diambil`, `nama_pengambil`, `dibuat_pada`) VALUES
(1, 'PSN-2611-0042', 0.00, 1, 'Cetak Spanduk Grand Opening', 'ONLINE', '0812-7777-7890', 'DIPROSES_DICETAK', 'LUNAS', 1, NULL, 98000.00, 0.00, 98000.00, NULL, 0.00, NULL, '2026-11-25 14:20:00', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13'),
(2, 'PSN-2611-0041', 0.00, 2, 'Print Dokumen Skripsi Bab 1-3', 'ONLINE', '0813-8888-1122', 'SIAP_DIAMBIL', 'LUNAS', 2, NULL, 45000.00, 0.00, 45000.00, NULL, 0.00, NULL, '2026-11-25 13:05:00', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13'),
(3, 'PSN-2611-0040', 0.00, 3, 'Cetak Banner Event Kampus', 'ONLINE', '0812-9999-3344', 'DIPROSES_DICETAK', 'LUNAS', 2, NULL, 245000.00, 0.00, 245000.00, NULL, 0.00, NULL, '2026-11-25 11:40:00', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13'),
(4, 'PSN-2611-0039', 0.00, 1, 'Print Dokumen Laporan Praktikum', 'WALKIN', '0812-7777-7890', 'SIAP_DIAMBIL', 'LUNAS', 2, NULL, 87000.00, 0.00, 87000.00, NULL, 0.00, NULL, '2026-11-24 09:05:00', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13'),
(5, 'PSN-2611-0038', 0.00, 4, 'Cetak Undangan Pernikahan', 'ONLINE', '0852-1111-2233', 'MENUNGGU_KONFIRMASI', 'LUNAS', NULL, NULL, 420000.00, 0.00, 420000.00, NULL, 0.00, NULL, '2026-11-24 08:30:00', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13'),
(6, 'PSN-2611-0037', 0.00, 5, 'Cetak Spanduk Wisuda', 'ONLINE', '0821-4444-5566', 'SELESAI', 'LUNAS', 1, NULL, 150000.00, 0.00, 150000.00, NULL, 0.00, NULL, '2026-11-23 15:10:00', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13'),
(7, 'PSN-2611-0036', 0.00, 6, 'Cetak Undangan Khitanan', 'ONLINE', '0813-6666-7788', 'DIBATALKAN', 'REFUND', 1, NULL, 189000.00, 0.00, 189000.00, NULL, 0.00, NULL, '2026-11-23 10:22:00', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13'),
(8, 'PSN-2611-0035', 0.00, 7, 'Print Dokumen Makalah', 'WALKIN', '0812-3333-4455', 'SELESAI', 'LUNAS', 2, NULL, 22500.00, 0.00, 22500.00, NULL, 0.00, NULL, '2026-11-22 16:45:00', NULL, NULL, NULL, NULL, '2026-11-25 19:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `stok_minimum` int(11) NOT NULL DEFAULT 5,
  `sku` varchar(40) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `nama_produk` varchar(100) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `id_kategori_produk` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `merek` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_beli` decimal(12,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 0,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `stok_minimum`, `sku`, `status_aktif`, `nama_produk`, `gambar`, `id_kategori_produk`, `satuan`, `merek`, `deskripsi`, `harga_beli`, `harga_jual`, `stok`, `dibuat_pada`) VALUES
(1, 5, 'ATK-001', 1, 'Pulpen Standard AE7', NULL, 1, 'lusin', 'Standard', NULL, 40000.00, 48000.00, 15, '2026-11-25 19:49:12'),
(2, 10, 'ATK-002', 1, 'Map Plastik Kancing', NULL, 2, 'pcs', 'Bambi', NULL, 2500.00, 4000.00, 40, '2026-11-25 19:49:12');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id_promo` int(11) NOT NULL,
  `berlaku_dari` datetime NOT NULL,
  `kode_promo` varchar(30) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `nama_promo` varchar(100) NOT NULL,
  `berlaku_sampai` datetime NOT NULL,
  `id_kategori_layanan` int(11) DEFAULT NULL,
  `maks_diskon` decimal(12,2) DEFAULT NULL,
  `nilai_diskon` decimal(12,2) NOT NULL DEFAULT 0.00,
  `jenis_diskon` enum('PERSEN','NOMINAL') NOT NULL DEFAULT 'PERSEN',
  `minimal_belanja` decimal(12,2) NOT NULL DEFAULT 0.00,
  `kuota` int(11) NOT NULL DEFAULT 100,
  `jumlah_terpakai` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promo`
--

INSERT INTO `promo` (`id_promo`, `berlaku_dari`, `kode_promo`, `status_aktif`, `nama_promo`, `berlaku_sampai`, `id_kategori_layanan`, `maks_diskon`, `nilai_diskon`, `jenis_diskon`, `minimal_belanja`, `kuota`, `jumlah_terpakai`) VALUES
(1, '2026-11-01 00:00:00', 'UNDANG10', 1, 'Diskon Undangan 10%', '2026-12-31 23:59:59', 3, 50000.00, 10.00, 'PERSEN', 150000.00, 100, 58),
(2, '2026-11-01 00:00:00', 'CETAK15', 1, 'Potongan Spanduk & Banner', '2026-12-31 23:59:59', 2, 15000.00, 15000.00, 'NOMINAL', 100000.00, 100, 20),
(3, '2026-11-01 00:00:00', 'JILID5', 1, 'Diskon Jilid Mahasiswa', '2026-11-30 23:59:59', 1, 10000.00, 5.00, 'PERSEN', 20000.00, 100, 90),
(4, '2026-08-01 00:00:00', 'MERDEKA', 0, 'Promo 17 Agustus', '2026-08-20 23:59:59', NULL, 50000.00, 15.00, 'PERSEN', 50000.00, 100, 100);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `uk_admin_username` (`username`),
  ADD UNIQUE KEY `uk_admin_email` (`email`);

--
-- Indexes for table `antrean`
--
ALTER TABLE `antrean`
  ADD PRIMARY KEY (`id_antrean`),
  ADD KEY `fk_antrean_pesanan` (`id_pesanan`);

--
-- Indexes for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id_bahan`),
  ADD UNIQUE KEY `uk_bahan_kode` (`kode_bahan`);

--
-- Indexes for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY (`id_detail_pembelian`),
  ADD KEY `fk_dpembelian_pembelian` (`id_pembelian`),
  ADD KEY `fk_dpembelian_produk` (`id_produk`),
  ADD KEY `fk_dpembelian_bahan` (`id_bahan`);

--
-- Indexes for table `detail_pesanan_layanan`
--
ALTER TABLE `detail_pesanan_layanan`
  ADD PRIMARY KEY (`id_detail_layanan`),
  ADD KEY `fk_dpl_pesanan` (`id_pesanan`),
  ADD KEY `fk_dpl_layanan` (`id_layanan`);

--
-- Indexes for table `detail_pesanan_produk`
--
ALTER TABLE `detail_pesanan_produk`
  ADD PRIMARY KEY (`id_detail_produk`),
  ADD KEY `fk_dpp_pesanan` (`id_pesanan`),
  ADD KEY `fk_dpp_produk` (`id_produk`);

--
-- Indexes for table `file_pesanan`
--
ALTER TABLE `file_pesanan`
  ADD PRIMARY KEY (`id_file`),
  ADD KEY `fk_file_detail_layanan` (`id_detail_layanan`);

--
-- Indexes for table `kategori_layanan`
--
ALTER TABLE `kategori_layanan`
  ADD PRIMARY KEY (`id_kategori_layanan`),
  ADD UNIQUE KEY `uk_kategori_layanan_slug` (`slug`);

--
-- Indexes for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  ADD PRIMARY KEY (`id_kategori_produk`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`),
  ADD UNIQUE KEY `uk_layanan_kode` (`kode_layanan`),
  ADD KEY `fk_layanan_kategori` (`id_kategori_layanan`);

--
-- Indexes for table `layanan_bahan`
--
ALTER TABLE `layanan_bahan`
  ADD PRIMARY KEY (`id_layanan`,`id_bahan`),
  ADD KEY `fk_lb_bahan` (`id_bahan`);

--
-- Indexes for table `mutasi_stok`
--
ALTER TABLE `mutasi_stok`
  ADD PRIMARY KEY (`id_mutasi`),
  ADD KEY `fk_mutasi_admin` (`id_admin`),
  ADD KEY `fk_mutasi_pesanan` (`id_pesanan`),
  ADD KEY `fk_mutasi_bahan` (`id_bahan`),
  ADD KEY `fk_mutasi_pembelian` (`id_pembelian`),
  ADD KEY `fk_mutasi_produk` (`id_produk`);

--
-- Indexes for table `opsi_detail_pesanan`
--
ALTER TABLE `opsi_detail_pesanan`
  ADD PRIMARY KEY (`id_detail_pesanan`,`id_opsi`),
  ADD KEY `fk_odp_opsi` (`id_opsi`);

--
-- Indexes for table `opsi_layanan`
--
ALTER TABLE `opsi_layanan`
  ADD PRIMARY KEY (`id_opsi`),
  ADD KEY `fk_opsi_layanan` (`id_layanan`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `uk_pelanggan_email` (`email`),
  ADD UNIQUE KEY `uk_pelanggan_nowa` (`no_wa`);

--
-- Indexes for table `pemasok`
--
ALTER TABLE `pemasok`
  ADD PRIMARY KEY (`id_pemasok`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `fk_pembayaran_pesanan` (`id_pesanan`),
  ADD KEY `fk_pembayaran_admin` (`id_admin`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id_pembelian`),
  ADD UNIQUE KEY `uk_pembelian_faktur` (`nomor_faktur`),
  ADD KEY `fk_pembelian_pemasok` (`id_pemasok`),
  ADD KEY `fk_pembelian_admin` (`id_admin`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD UNIQUE KEY `uk_pesanan_kode` (`kode_pesanan`),
  ADD KEY `fk_pesanan_pelanggan` (`id_pelanggan`),
  ADD KEY `fk_pesanan_admin` (`id_admin`),
  ADD KEY `fk_pesanan_promo` (`id_promo`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD UNIQUE KEY `uk_produk_sku` (`sku`),
  ADD KEY `fk_produk_kategori` (`id_kategori_produk`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id_promo`),
  ADD UNIQUE KEY `uk_promo_kode` (`kode_promo`),
  ADD KEY `fk_promo_kategori` (`id_kategori_layanan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `antrean`
--
ALTER TABLE `antrean`
  MODIFY `id_antrean` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bahan_baku`
--
ALTER TABLE `bahan_baku`
  MODIFY `id_bahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id_detail_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `detail_pesanan_layanan`
--
ALTER TABLE `detail_pesanan_layanan`
  MODIFY `id_detail_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `detail_pesanan_produk`
--
ALTER TABLE `detail_pesanan_produk`
  MODIFY `id_detail_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_pesanan`
--
ALTER TABLE `file_pesanan`
  MODIFY `id_file` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_layanan`
--
ALTER TABLE `kategori_layanan`
  MODIFY `id_kategori_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  MODIFY `id_kategori_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mutasi_stok`
--
ALTER TABLE `mutasi_stok`
  MODIFY `id_mutasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `opsi_layanan`
--
ALTER TABLE `opsi_layanan`
  MODIFY `id_opsi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pemasok`
--
ALTER TABLE `pemasok`
  MODIFY `id_pemasok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id_promo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `antrean`
--
ALTER TABLE `antrean`
  ADD CONSTRAINT `fk_antrean_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `fk_dpembelian_bahan` FOREIGN KEY (`id_bahan`) REFERENCES `bahan_baku` (`id_bahan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dpembelian_pembelian` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dpembelian_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `detail_pesanan_layanan`
--
ALTER TABLE `detail_pesanan_layanan`
  ADD CONSTRAINT `fk_dpl_layanan` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dpl_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_pesanan_produk`
--
ALTER TABLE `detail_pesanan_produk`
  ADD CONSTRAINT `fk_dpp_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dpp_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `file_pesanan`
--
ALTER TABLE `file_pesanan`
  ADD CONSTRAINT `fk_file_detail_layanan` FOREIGN KEY (`id_detail_layanan`) REFERENCES `detail_pesanan_layanan` (`id_detail_layanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `layanan`
--
ALTER TABLE `layanan`
  ADD CONSTRAINT `fk_layanan_kategori` FOREIGN KEY (`id_kategori_layanan`) REFERENCES `kategori_layanan` (`id_kategori_layanan`) ON UPDATE CASCADE;

--
-- Constraints for table `layanan_bahan`
--
ALTER TABLE `layanan_bahan`
  ADD CONSTRAINT `fk_lb_bahan` FOREIGN KEY (`id_bahan`) REFERENCES `bahan_baku` (`id_bahan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lb_layanan` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mutasi_stok`
--
ALTER TABLE `mutasi_stok`
  ADD CONSTRAINT `fk_mutasi_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mutasi_bahan` FOREIGN KEY (`id_bahan`) REFERENCES `bahan_baku` (`id_bahan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mutasi_pembelian` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mutasi_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mutasi_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `opsi_detail_pesanan`
--
ALTER TABLE `opsi_detail_pesanan`
  ADD CONSTRAINT `fk_odp_detail` FOREIGN KEY (`id_detail_pesanan`) REFERENCES `detail_pesanan_layanan` (`id_detail_layanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_odp_opsi` FOREIGN KEY (`id_opsi`) REFERENCES `opsi_layanan` (`id_opsi`) ON UPDATE CASCADE;

--
-- Constraints for table `opsi_layanan`
--
ALTER TABLE `opsi_layanan`
  ADD CONSTRAINT `fk_opsi_layanan` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembayaran_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `fk_pembelian_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembelian_pemasok` FOREIGN KEY (`id_pemasok`) REFERENCES `pemasok` (`id_pemasok`) ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pesanan_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pesanan_promo` FOREIGN KEY (`id_promo`) REFERENCES `promo` (`id_promo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori_produk`) REFERENCES `kategori_produk` (`id_kategori_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `promo`
--
ALTER TABLE `promo`
  ADD CONSTRAINT `fk_promo_kategori` FOREIGN KEY (`id_kategori_layanan`) REFERENCES `kategori_layanan` (`id_kategori_layanan`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
