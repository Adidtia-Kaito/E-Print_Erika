CREATE TABLE IF NOT EXISTS `revisi_komplain` (
  `id_revisi_komplain` int(11) NOT NULL AUTO_INCREMENT,
  `id_pesanan`         int(11) NOT NULL,
  `id_pelanggan`       int(11) NOT NULL,
  `jenis`              enum('REVISI','KOMPLAIN') NOT NULL DEFAULT 'KOMPLAIN',
  `pesan`              text NOT NULL,
  `file_lampiran`      varchar(255) DEFAULT NULL,
  `status`             enum('DIAJUKAN','DITANGGAPI','SELESAI') NOT NULL DEFAULT 'DIAJUKAN',
  `tanggapan_admin`    text DEFAULT NULL,
  `id_admin`           int(11) DEFAULT NULL,
  `dibuat_pada`        datetime NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada`    datetime DEFAULT NULL,
  PRIMARY KEY (`id_revisi_komplain`),
  KEY `fk_rk_pesanan` (`id_pesanan`),
  KEY `fk_rk_pelanggan` (`id_pelanggan`),
  KEY `fk_rk_admin` (`id_admin`),
  CONSTRAINT `fk_rk_pesanan` FOREIGN KEY (`id_pesanan`)
    REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rk_pelanggan` FOREIGN KEY (`id_pelanggan`)
    REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rk_admin` FOREIGN KEY (`id_admin`)
    REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
