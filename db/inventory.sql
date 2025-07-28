-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Jul 2025 pada 07.11
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_eror`
--

CREATE TABLE `barang_eror` (
  `id` int(11) NOT NULL,
  `id_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id` int(11) NOT NULL,
  `id_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `tanggal_keluar` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id` int(11) NOT NULL,
  `id_barang` varchar(50) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_migrasi`
--

CREATE TABLE `barang_migrasi` (
  `id` int(11) NOT NULL,
  `id_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cicilan`
--

CREATE TABLE `cicilan` (
  `no_cicilan` varchar(20) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `tanggal_cicilan` date NOT NULL,
  `pokok_cicilan` int(11) NOT NULL,
  `bunga_cicilan` int(11) NOT NULL,
  `total_cicilan` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `login`
--

INSERT INTO `login` (`id`, `username`, `password`) VALUES
(1, 'admin2', '$2y$10$Yd.XOc8Ag9J52CuaQPcIgOrhLeUAM4mbaQmfVc44PM1R.05yQBlou'),
(3, 'admin1', '$2y$10$s56tZAiIrlWZaPqmKNqI5eJLRuKa0tBYll7ZTJp6ADh1l19qL2jaS');

-- --------------------------------------------------------

--
-- Struktur dari tabel `operasional`
--

CREATE TABLE `operasional` (
  `sku` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `nama` varchar(100) NOT NULL,
  `qty` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `harga` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `service`
--

CREATE TABLE `service` (
  `id_service` varchar(10) NOT NULL,
  `tanggal_service` date NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `biaya_service` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_bulanan`
--

CREATE TABLE `stok_bulanan` (
  `id` int(11) NOT NULL,
  `id_barang` varchar(20) DEFAULT NULL,
  `bulan` int(11) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL,
  `stok_awal` int(11) DEFAULT NULL,
  `barang_masuk` int(11) DEFAULT NULL,
  `barang_keluar` int(11) DEFAULT NULL,
  `barang_migrasi` int(11) DEFAULT NULL,
  `barang_eror` int(11) DEFAULT NULL,
  `stok_akhir` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indeks untuk tabel `barang_eror`
--
ALTER TABLE `barang_eror`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `barang_migrasi`
--
ALTER TABLE `barang_migrasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `cicilan`
--
ALTER TABLE `cicilan`
  ADD PRIMARY KEY (`no_cicilan`);

--
-- Indeks untuk tabel `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `operasional`
--
ALTER TABLE `operasional`
  ADD PRIMARY KEY (`sku`);

--
-- Indeks untuk tabel `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id_service`);

--
-- Indeks untuk tabel `stok_bulanan`
--
ALTER TABLE `stok_bulanan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang_eror`
--
ALTER TABLE `barang_eror`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT untuk tabel `barang_migrasi`
--
ALTER TABLE `barang_migrasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `stok_bulanan`
--
ALTER TABLE `stok_bulanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
