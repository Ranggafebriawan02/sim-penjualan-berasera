-- =====================================================================
-- DATABASE: db_penjualan_berasera (SISTEM FIFO & MULTI-CHANNEL PAYMENT)
-- Mata Kuliah: Basis Data Lanjut
-- Studi Kasus: SIM Penjualan Beras Organik Berasera (Poktan Gapsera Sejahtera Mandiri)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `db_penjualan_berasera`;
USE `db_penjualan_berasera`;

-- Hapus tabel lama jika ada (urutan child ke parent)
DROP TABLE IF EXISTS `tb_pengeluaran_batch`;
DROP TABLE IF EXISTS `tb_riwayat_poin`;
DROP TABLE IF EXISTS `tb_pembayaran`;
DROP TABLE IF EXISTS `tb_penjualan_detail`;
DROP TABLE IF EXISTS `tb_penjualan`;
DROP TABLE IF EXISTS `tb_pasokan`;
DROP TABLE IF EXISTS `tb_batch_beras`;
DROP TABLE IF EXISTS `tb_produk`;
DROP TABLE IF EXISTS `tb_kategori`;
DROP TABLE IF EXISTS `tb_kualitas`;
DROP TABLE IF EXISTS `tb_pemasok`;
DROP TABLE IF EXISTS `tb_customer`;
DROP TABLE IF EXISTS `tb_pengiriman`;
DROP TABLE IF EXISTS `tb_user`;

-- ---------------------------------------------------------------------
-- 1. TABEL: tb_kategori (Jenis Varietas Beras Organik)
-- PK: id_kategori
-- ---------------------------------------------------------------------
CREATE TABLE `tb_kategori` (
    `id_kategori` INT(10) UNSIGNED AUTO_INCREMENT,
    `nama_kategori` VARCHAR(50) NOT NULL COMMENT 'Beras Merah, Hitam, Mentik Wangi, dll.',
    `deskripsi_kategori` TEXT NULL,
    PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 2. TABEL: tb_kualitas (Standar Mutu Beras Organik)
-- PK: id_kualitas
-- ---------------------------------------------------------------------
CREATE TABLE `tb_kualitas` (
    `id_kualitas` INT(10) UNSIGNED AUTO_INCREMENT,
    `nama_kualitas` VARCHAR(50) NOT NULL COMMENT 'Grade A (Premium), Grade B (Medium), Grade C (Standar)',
    `standar_butir_patah` VARCHAR(20) NOT NULL COMMENT 'Maks 5%, 15%, dll.',
    `kadar_air_maks` DECIMAL(4,1) NOT NULL DEFAULT 14.0,
    `derajat_sosoh_min` INT(3) NOT NULL DEFAULT 95,
    `keterangan_kemasan` VARCHAR(100) NOT NULL COMMENT 'Kemasan Vakum Kedap Udara, dll.',
    PRIMARY KEY (`id_kualitas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 3. TABEL: tb_produk (Katalog Produk Beras)
-- PK: id_produk
-- FK: id_kategori -> tb_kategori(id_kategori)
-- FK: id_kualitas -> tb_kualitas(id_kualitas)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_produk` (
    `id_produk` INT(10) UNSIGNED AUTO_INCREMENT,
    `id_kategori` INT(10) UNSIGNED NOT NULL,
    `id_kualitas` INT(10) UNSIGNED NOT NULL,
    `nama_produk` VARCHAR(100) NOT NULL,
    `deskripsi` TEXT NULL,
    `satuan` VARCHAR(20) NOT NULL DEFAULT 'Kg',
    `harga` INT(10) UNSIGNED NOT NULL COMMENT 'Harga jual ke konsumen per unit',
    `stok` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total stok sisa dari seluruh batch aktif',
    `gambar` VARCHAR(255) NULL,
    PRIMARY KEY (`id_produk`),
    CONSTRAINT `fk_produk_kategori` 
        FOREIGN KEY (`id_kategori`) REFERENCES `tb_kategori` (`id_kategori`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_produk_kualitas` 
        FOREIGN KEY (`id_kualitas`) REFERENCES `tb_kualitas` (`id_kualitas`) 
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 4. TABEL: tb_pemasok (Petani Mitra Gapsera Sejahtera Mandiri)
-- PK: id_pemasok
-- ---------------------------------------------------------------------
CREATE TABLE `tb_pemasok` (
    `id_pemasok` INT(10) UNSIGNED AUTO_INCREMENT,
    `nama_pemasok` VARCHAR(100) NOT NULL,
    `telepon` VARCHAR(25) NOT NULL,
    `alamat` TEXT NOT NULL,
    `rekening_bank` VARCHAR(100) NULL COMMENT 'Nama Bank & No Rekening Petani',
    `status_pemasok` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    PRIMARY KEY (`id_pemasok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 5. TABEL: tb_batch_beras (Inti Sistem Persediaan FIFO)
-- PK: id_batch
-- FK: id_produk -> tb_produk(id_produk)
-- Kunci FIFO: tgl_masuk ASC
-- ---------------------------------------------------------------------
CREATE TABLE `tb_batch_beras` (
    `id_batch` INT(10) UNSIGNED AUTO_INCREMENT,
    `id_produk` INT(10) UNSIGNED NOT NULL,
    `kode_batch` VARCHAR(30) NOT NULL UNIQUE,
    `tgl_masuk` DATE NOT NULL COMMENT 'KUNCI UTAMA URUTAN FIFO (ASC)',
    `tgl_panen_giling` DATE NOT NULL,
    `tgl_kadaluarsa` DATE NOT NULL,
    `stok_awal` INT(10) UNSIGNED NOT NULL COMMENT 'Jumlah Kg awal saat masuk gudang',
    `stok_sisa` INT(10) UNSIGNED NOT NULL COMMENT 'Sisa Kg yang belum terjual',
    `harga_pokok_beli` INT(10) UNSIGNED NOT NULL COMMENT 'HPP modal per Kg dari petani',
    `status_batch` ENUM('tersedia', 'habis') NOT NULL DEFAULT 'tersedia',
    PRIMARY KEY (`id_batch`),
    CONSTRAINT `fk_batch_produk` 
        FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) 
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 6. TABEL: tb_user (Pengurus: Ketua & Bendahara Poktan)
-- PK: id_user
-- ---------------------------------------------------------------------
CREATE TABLE `tb_user` (
    `id_user` INT(10) UNSIGNED AUTO_INCREMENT,
    `nama` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `gambar` VARCHAR(255) NULL,
    `level` ENUM('ketua', 'bendahara') NOT NULL,
    PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 7. TABEL: tb_pasokan (Penerimaan Beras dari Petani per Kg)
-- PK: id_pasokan
-- FK: id_pemasok -> tb_pemasok(id_pemasok)
-- FK: id_produk  -> tb_produk(id_produk)
-- FK: id_batch   -> tb_batch_beras(id_batch)
-- FK: id_user    -> tb_user(id_user)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_pasokan` (
    `id_pasokan` INT(10) UNSIGNED AUTO_INCREMENT,
    `id_pemasok` INT(10) UNSIGNED NOT NULL,
    `id_produk` INT(10) UNSIGNED NOT NULL,
    `id_batch` INT(10) UNSIGNED NULL,
    `tgl_pasok` DATE NOT NULL,
    `jumlah_kg` INT(10) UNSIGNED NOT NULL COMMENT 'Total Kg beras yang dipasok',
    `harga_per_kg` INT(10) UNSIGNED NOT NULL COMMENT 'Tarif modal per kg',
    `total_bayar` INT(10) UNSIGNED NOT NULL COMMENT 'jumlah_kg * harga_per_kg',
    `status_pembayaran_petani` ENUM('lunas', 'belum_lunas') NOT NULL DEFAULT 'belum_lunas',
    `id_user` INT(10) UNSIGNED NULL COMMENT 'Bendahara penerima pasokan',
    PRIMARY KEY (`id_pasokan`),
    CONSTRAINT `fk_pasokan_pemasok` 
        FOREIGN KEY (`id_pemasok`) REFERENCES `tb_pemasok` (`id_pemasok`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_pasokan_produk` 
        FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_pasokan_batch` 
        FOREIGN KEY (`id_batch`) REFERENCES `tb_batch_beras` (`id_batch`) 
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `fk_pasokan_user` 
        FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) 
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 8. TABEL: tb_customer (Data Pembeli & Poin Loyalitas)
-- PK: id_customer
-- ---------------------------------------------------------------------
CREATE TABLE `tb_customer` (
    `id_customer` INT(10) UNSIGNED AUTO_INCREMENT,
    `nama_customer` VARCHAR(100) NOT NULL,
    `alamat` TEXT NOT NULL,
    `telepon` VARCHAR(25) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `total_poin` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Saldo poin loyalitas aktif',
    `tier_membership` ENUM('Silver', 'Gold', 'Platinum') NOT NULL DEFAULT 'Silver',
    PRIMARY KEY (`id_customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 9. TABEL: tb_pengiriman (Jasa Ekspedisi / Kurir)
-- PK: id_pengiriman
-- ---------------------------------------------------------------------
CREATE TABLE `tb_pengiriman` (
    `id_pengiriman` INT(10) UNSIGNED AUTO_INCREMENT,
    `nama_pengiriman` VARCHAR(50) NOT NULL,
    `minimal_ongkir` INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_pengiriman`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 10. TABEL: tb_penjualan (Header Transaksi Penjualan)
-- PK: id_penjualan
-- FK: id_customer   -> tb_customer(id_customer)
-- FK: id_pengiriman -> tb_pengiriman(id_pengiriman)
-- FK: id_user       -> tb_user(id_user)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_penjualan` (
    `id_penjualan` VARCHAR(30) NOT NULL,
    `tgl_jual` DATE NOT NULL,
    `id_customer` INT(10) UNSIGNED NOT NULL,
    `total` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total akhir yang wajib dibayar',
    `ongkir` INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `poin_digunakan` INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `diskon_poin` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Nilai potongan belanja dari poin',
    `poin_didapat` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Poin reward yang didapat dari order ini',
    `note` TEXT NULL,
    `alamat_pengiriman` TEXT NOT NULL,
    `status_penjualan` ENUM('menunggu_pembayaran', 'diverifikasi', 'dikirim', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran',
    `metode_transaksi` ENUM('midtrans_qris', 'midtrans_va', 'cash') NOT NULL DEFAULT 'midtrans_qris',
    `id_pengiriman` INT(10) UNSIGNED NOT NULL,
    `id_user` INT(10) UNSIGNED NULL COMMENT 'Petugas/Bendahara validasi',
    PRIMARY KEY (`id_penjualan`),
    CONSTRAINT `fk_penjualan_customer` 
        FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id_customer`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_penjualan_pengiriman` 
        FOREIGN KEY (`id_pengiriman`) REFERENCES `tb_pengiriman` (`id_pengiriman`) 
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_penjualan_user` 
        FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) 
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 11. TABEL: tb_penjualan_detail (Rincian Item Belanja Beras)
-- PK: id_penjualan_detail
-- FK: id_penjualan -> tb_penjualan(id_penjualan)
-- FK: id_produk    -> tb_produk(id_produk)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_penjualan_detail` (
    `id_penjualan_detail` INT(10) UNSIGNED AUTO_INCREMENT,
    `id_penjualan` VARCHAR(30) NOT NULL,
    `id_produk` INT(10) UNSIGNED NOT NULL,
    `jumlah` INT(10) UNSIGNED NOT NULL COMMENT 'Kuantitas Kg beras dibeli',
    `harga_satuan` INT(10) UNSIGNED NOT NULL COMMENT 'Freeze harga transaksi saat order dibuat',
    `sub_total` INT(10) UNSIGNED NOT NULL,
    `status_detail` VARCHAR(20) NOT NULL DEFAULT 'aktif',
    PRIMARY KEY (`id_penjualan_detail`),
    CONSTRAINT `fk_detail_penjualan` 
        FOREIGN KEY (`id_penjualan`) REFERENCES `tb_penjualan` (`id_penjualan`) 
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_detail_produk` 
        FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) 
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 12. TABEL: tb_pengeluaran_batch (Jejak Mutasi Alokasi FIFO & HPP Riil)
-- PK: id_pengeluaran_batch
-- FK: id_penjualan_detail -> tb_penjualan_detail(id_penjualan_detail)
-- FK: id_batch            -> tb_batch_beras(id_batch)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_pengeluaran_batch` (
    `id_pengeluaran_batch` INT(10) UNSIGNED AUTO_INCREMENT,
    `id_penjualan_detail` INT(10) UNSIGNED NOT NULL,
    `id_batch` INT(10) UNSIGNED NOT NULL,
    `jumlah_keluar` INT(10) UNSIGNED NOT NULL COMMENT 'Jumlah Kg yang diambil dari batch ini',
    `hpp_satuan` INT(10) UNSIGNED NOT NULL COMMENT 'Modal beli per kg dari batch ini',
    `subtotal_hpp` INT(10) UNSIGNED NOT NULL COMMENT 'jumlah_keluar * hpp_satuan (Dasar Laba Kotor)',
    PRIMARY KEY (`id_pengeluaran_batch`),
    CONSTRAINT `fk_alokasi_detail` 
        FOREIGN KEY (`id_penjualan_detail`) REFERENCES `tb_penjualan_detail` (`id_penjualan_detail`) 
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_alokasi_batch` 
        FOREIGN KEY (`id_batch`) REFERENCES `tb_batch_beras` (`id_batch`) 
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 13. TABEL: tb_riwayat_poin (Audit Trail Poin Loyalitas Pelanggan)
-- PK: id_riwayat_poin
-- FK: id_customer  -> tb_customer(id_customer)
-- FK: id_penjualan -> tb_penjualan(id_penjualan)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_riwayat_poin` (
    `id_riwayat_poin` INT(10) UNSIGNED AUTO_INCREMENT,
    `id_customer` INT(10) UNSIGNED NOT NULL,
    `id_penjualan` VARCHAR(30) NULL,
    `jenis_mutasi` ENUM('masuk', 'keluar') NOT NULL,
    `jumlah_poin` INT(10) UNSIGNED NOT NULL,
    `saldo_akhir` INT(10) UNSIGNED NOT NULL,
    `keterangan` VARCHAR(150) NOT NULL,
    `tgl_mutasi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_riwayat_poin`),
    CONSTRAINT `fk_poin_customer` 
        FOREIGN KEY (`id_customer`) REFERENCES `tb_customer` (`id_customer`) 
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_poin_penjualan` 
        FOREIGN KEY (`id_penjualan`) REFERENCES `tb_penjualan` (`id_penjualan`) 
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- 14. TABEL: tb_pembayaran (Multi-Channel Payment: Midtrans QRIS, VA, Cash)
-- PK: id_pembayaran
-- FK: id_penjualan -> tb_penjualan(id_penjualan)
-- FK: id_user      -> tb_user(id_user)
-- ---------------------------------------------------------------------
CREATE TABLE `tb_pembayaran` (
    `id_pembayaran` INT(10) UNSIGNED AUTO_INCREMENT,
    `id_penjualan` VARCHAR(30) NOT NULL,
    `gateway_provider` ENUM('midtrans', 'cash') NOT NULL DEFAULT 'midtrans',
    `transaction_id` VARCHAR(100) NULL COMMENT 'ID transaksi Midtrans jika online',
    `metode_pembayaran` VARCHAR(50) NOT NULL COMMENT 'qris, bank_transfer, cash, gopay, dll.',
    `va_number` VARCHAR(50) NULL COMMENT 'Nomor Virtual Account bank',
    `snap_token` VARCHAR(255) NULL COMMENT 'Token checkout Midtrans Snap',
    `nominal_bayar` INT(10) UNSIGNED NOT NULL,
    `status_pembayaran` ENUM('pending', 'settlement', 'expire', 'cancel', 'deny') NOT NULL DEFAULT 'pending',
    `waktu_transaksi` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `waktu_bayar` DATETIME NULL COMMENT 'Terisi saat webhook Midtrans atau kasir konfirmasi tunai',
    `payload_callback` TEXT NULL COMMENT 'JSON respon webhook Midtrans',
    `id_user` INT(10) UNSIGNED NULL COMMENT 'Bendahara jika penerimaan cash',
    PRIMARY KEY (`id_pembayaran`),
    CONSTRAINT `fk_pembayaran_penjualan` 
        FOREIGN KEY (`id_penjualan`) REFERENCES `tb_penjualan` (`id_penjualan`) 
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_pembayaran_user` 
        FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) 
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- FITUR BASIS DATA LANJUT: VIEW, TRIGGER & STORED PROCEDURE
-- =====================================================================

-- 1. VIEW: Laporan Laba Rugi Komprehensif Berbasis HPP FIFO
CREATE OR REPLACE VIEW `view_laporan_laba_rugi_fifo` AS
SELECT 
    p.id_penjualan,
    p.tgl_jual,
    c.nama_customer,
    p.total AS omset_penjualan,
    p.ongkir,
    (p.total - p.ongkir) AS pendapatan_bersih_beras,
    COALESCE(SUM(pb.subtotal_hpp), 0) AS total_hpp_fifo,
    ((p.total - p.ongkir) - COALESCE(SUM(pb.subtotal_hpp), 0)) AS laba_kotor_fifo,
    p.status_penjualan,
    p.metode_transaksi
FROM `tb_penjualan` p
JOIN `tb_customer` c ON p.id_customer = c.id_customer
LEFT JOIN `tb_penjualan_detail` pd ON p.id_penjualan = pd.id_penjualan
LEFT JOIN `tb_pengeluaran_batch` pb ON pd.id_penjualan_detail = pb.id_penjualan_detail
WHERE p.status_penjualan IN ('diverifikasi', 'dikirim', 'selesai')
GROUP BY p.id_penjualan, p.tgl_jual, c.nama_customer, p.total, p.ongkir, p.status_penjualan, p.metode_transaksi;

-- 2. VIEW: Kartu Antrean FIFO & Sisa Stok Gudang
CREATE OR REPLACE VIEW `view_kartu_stok_fifo` AS
SELECT 
    b.id_batch,
    b.kode_batch,
    p.id_produk,
    p.nama_produk,
    k.nama_kategori,
    q.nama_kualitas,
    b.tgl_masuk AS urutan_fifo_tgl,
    b.stok_awal,
    b.stok_sisa,
    (b.stok_awal - b.stok_sisa) AS stok_keluar_terjual,
    b.harga_pokok_beli AS hpp_per_kg,
    (b.stok_sisa * b.harga_pokok_beli) AS nilai_aset_persediaan,
    b.status_batch
FROM `tb_batch_beras` b
JOIN `tb_produk` p ON b.id_produk = p.id_produk
JOIN `tb_kategori` k ON p.id_kategori = k.id_kategori
JOIN `tb_kualitas` q ON p.id_kualitas = q.id_kualitas
ORDER BY b.id_produk ASC, b.tgl_masuk ASC, b.id_batch ASC;

-- ---------------------------------------------------------------------
-- DATABASE TRIGGERS
-- ---------------------------------------------------------------------
DELIMITER $$

-- TRIGGER 1: Tambah Stok Produk Otomatis Saat Batch Masuk Baru Dibuat
CREATE TRIGGER `trg_tambah_stok_dari_batch`
AFTER INSERT ON `tb_batch_beras`
FOR EACH ROW
BEGIN
    UPDATE `tb_produk`
    SET `stok` = `stok` + NEW.stok_awal
    WHERE `id_produk` = NEW.id_produk;
END$$

-- TRIGGER 2: Update Status Penjualan & Tambah Poin Loyalitas Saat Pembayaran Settlement
CREATE TRIGGER `trg_proses_settlement_pembayaran`
AFTER UPDATE ON `tb_pembayaran`
FOR EACH ROW
BEGIN
    DECLARE v_id_customer INT;
    DECLARE v_poin_didapat INT;
    DECLARE v_poin_sekarang INT;

    IF NEW.status_pembayaran = 'settlement' AND OLD.status_pembayaran <> 'settlement' THEN
        -- 1. Ubah status penjualan menjadi diverifikasi (lunas)
        UPDATE `tb_penjualan`
        SET `status_penjualan` = 'diverifikasi'
        WHERE `id_penjualan` = NEW.id_penjualan;

        -- 2. Ambil data customer dan poin reward
        SELECT id_customer, poin_didapat 
        INTO v_id_customer, v_poin_didapat
        FROM `tb_penjualan`
        WHERE `id_penjualan` = NEW.id_penjualan;

        -- 3. Tambahkan poin ke saldo customer jika ada poin_didapat > 0
        IF v_poin_didapat > 0 AND v_id_customer IS NOT NULL THEN
            UPDATE `tb_customer`
            SET `total_poin` = `total_poin` + v_poin_didapat
            WHERE `id_customer` = v_id_customer;

            SELECT total_poin INTO v_poin_sekarang 
            FROM `tb_customer` 
            WHERE `id_customer` = v_id_customer;

            -- Catat ke riwayat poin
            INSERT INTO `tb_riwayat_poin` (
                `id_customer`, `id_penjualan`, `jenis_mutasi`, 
                `jumlah_poin`, `saldo_akhir`, `keterangan`, `tgl_mutasi`
            ) VALUES (
                v_id_customer, NEW.id_penjualan, 'masuk', 
                v_poin_didapat, v_poin_sekarang, 
                CONCAT('Reward pembayaran lunas order ', NEW.id_penjualan), 
                NOW()
            );
        END IF;

    ELSEIF NEW.status_pembayaran IN ('cancel', 'expire', 'deny') AND OLD.status_pembayaran NOT IN ('cancel', 'expire', 'deny') THEN
        UPDATE `tb_penjualan`
        SET `status_penjualan` = 'dibatalkan'
        WHERE `id_penjualan` = NEW.id_penjualan;
    END IF;
END$$

DELIMITER ;

-- =====================================================================
-- DUMMY SEED DATA (DATA AWAL LENGKAP)
-- =====================================================================

-- 1. Kategori Beras
INSERT INTO `tb_kategori` (`id_kategori`, `nama_kategori`, `deskripsi_kategori`) VALUES
(1, 'Beras Merah Organik', 'Kaya serat pangan dan antioksidan antosianin alami'),
(2, 'Beras Hitam Organik', 'Varietas berkhasiat tinggi dengan indeks glikemik sangat rendah'),
(3, 'Beras Mentik Wangi Organik', 'Beras putih pulen lokal pegunungan dengan aroma wangi pandan alami');

-- 2. Standar Kualitas
INSERT INTO `tb_kualitas` (`id_kualitas`, `nama_kualitas`, `standar_butir_patah`, `kadar_air_maks`, `derajat_sosoh_min`, `keterangan_kemasan`) VALUES
(1, 'Grade A (Premium)', '<= 5%', 14.0, 100, 'Kemasan Vakum Kedap Udara'),
(2, 'Grade B (Medium Super)', '6% - 15%', 14.5, 90, 'Kemasan Standing Pouch Berperekat'),
(3, 'Grade C (Standar)', '16% - 25%', 15.0, 85, 'Kemasan Karung Standar');

-- 3. Master Produk
INSERT INTO `tb_produk` (`id_produk`, `id_kategori`, `id_kualitas`, `nama_produk`, `deskripsi`, `satuan`, `harga`, `stok`, `gambar`) VALUES
(1, 1, 1, 'Beras Merah Organik Premium Berasera 1 Kg', 'Beras merah organik Grade A kemasan vakum 1 kg', 'Kg', 22000, 0, 'beras_merah_1kg.jpg'),
(2, 2, 1, 'Beras Hitam Organik Premium Berasera 1 Kg', 'Beras hitam organik Grade A kemasan vakum 1 kg', 'Kg', 28000, 0, 'beras_hitam_1kg.jpg'),
(3, 3, 1, 'Beras Mentik Wangi Organik Berasera 5 Kg', 'Beras mentik wangi Grade A kemasan 5 kg', 'Karung 5 Kg', 90000, 0, 'beras_mentik_5kg.jpg');

-- 4. Pemasok (Petani Anggota Poktan)
INSERT INTO `tb_pemasok` (`id_pemasok`, `nama_pemasok`, `telepon`, `alamat`, `rekening_bank`, `status_pemasok`) VALUES
(1, 'Pak Joko Santoso (Petani Mitra Metro Timur)', '081298765432', 'Jl. Sukarno Hatta RT 04, Metro Timur', 'BRI 0123-01-009876-50-1 an Joko Santoso', 'aktif'),
(2, 'Pak Martono (Petani Mitra Ganjarasri)', '085712345678', 'Kelurahan Ganjarasri, Metro Barat', 'BCA 1234567890 an Martono', 'aktif');

-- 5. Batch Beras Masuk (Simulasi FIFO Masuk Gudang)
-- Batch 1: Masuk tgl 10 Oktober (Tertua)
INSERT INTO `tb_batch_beras` (`id_batch`, `id_produk`, `kode_batch`, `tgl_masuk`, `tgl_panen_giling`, `tgl_kadaluarsa`, `stok_awal`, `stok_sisa`, `harga_pokok_beli`, `status_batch`) VALUES
(1, 1, 'BATCH-MRH-202610-01', '2026-10-10', '2026-10-08', '2027-10-10', 50, 50, 16000, 'tersedia'),
-- Batch 2: Masuk tgl 18 Oktober (Lebih baru)
(2, 1, 'BATCH-MRH-202610-02', '2026-10-18', '2026-10-16', '2027-10-18', 60, 60, 16500, 'tersedia'),
-- Batch Beras Hitam
(3, 2, 'BATCH-HTM-202610-01', '2026-10-12', '2026-10-10', '2027-10-12', 40, 40, 20000, 'tersedia'),
-- Batch Mentik Wangi
(4, 3, 'BATCH-MTK-202610-01', '2026-10-15', '2026-10-12', '2027-10-15', 30, 30, 65000, 'tersedia');

-- 6. Pasokan Beras Masuk
INSERT INTO `tb_pasokan` (`id_pasokan`, `id_pemasok`, `id_produk`, `id_batch`, `tgl_pasok`, `jumlah_kg`, `harga_per_kg`, `total_bayar`, `status_pembayaran_petani`) VALUES
(1, 1, 1, 1, '2026-10-10', 50, 16000, 800000, 'lunas'),
(2, 1, 1, 2, '2026-10-18', 60, 16500, 990000, 'lunas'),
(3, 2, 2, 3, '2026-10-12', 40, 20000, 800000, 'lunas'),
(4, 2, 3, 4, '2026-10-15', 30, 65000, 1950000, 'lunas');

-- 7. User Pengurus
INSERT INTO `tb_user` (`id_user`, `nama`, `username`, `password`, `level`) VALUES
(1, 'H. Slamet Riyadi', 'ketua_gapsera', '$2y$12$eImiTXuWVxfM37uY4JANjOL.oJnptQ5e5yS0.3E1qXn4Y8g6lq0G6', 'ketua'),
(2, 'Siti Nurhaliza', 'bendahara_gapsera', '$2y$12$eImiTXuWVxfM37uY4JANjOL.oJnptQ5e5yS0.3E1qXn4Y8g6lq0G6', 'bendahara');

-- 8. Customer (Pembeli)
INSERT INTO `tb_customer` (`id_customer`, `nama_customer`, `alamat`, `telepon`, `email`, `username`, `password`, `total_poin`, `tier_membership`) VALUES
(1, 'Budi Santoso', 'Jl. Jenderal Sudirman No. 45, Kota Metro, Lampung', '081234567890', 'budi@gmail.com', 'budi', '$2y$12$eImiTXuWVxfM37uY4JANjOL.oJnptQ5e5yS0.3E1qXn4Y8g6lq0G6', 25, 'Silver');

-- 9. Kurir Pengiriman
INSERT INTO `tb_pengiriman` (`id_pengiriman`, `nama_pengiriman`, `minimal_ongkir`) VALUES
(1, 'Kurir Internal Gapsera Sejahtera', 10000),
(2, 'JNE Reguler', 15000),
(3, 'J&T Express', 14000);
