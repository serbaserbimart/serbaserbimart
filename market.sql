-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2026 at 11:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `market`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'admin', '$2y$10$yzBWJsttJ.azrT1DaVxe2utmcA/RV.ifm6tM3vqzz/f0mDvI9pFpy', '2026-06-12 16:34:00');

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `urutan` int(11) DEFAULT 0,
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `gambar`, `urutan`, `aktif`, `created_at`) VALUES
(4, 'banner_1781339978_456.jpg', 1, 1, '2026-06-13 08:39:38'),
(5, 'banner_1781339983_997.jpg', 2, 1, '2026-06-13 08:39:43'),
(6, 'banner_1781339986_488.jpeg', 3, 1, '2026-06-13 08:39:46');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `nomor_pesanan` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `catatan` text DEFAULT NULL,
  `total` bigint(20) NOT NULL,
  `status` enum('Menunggu','Diproses','Selesai','Dibatalkan') DEFAULT 'Menunggu',
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `nomor_pesanan`, `nama`, `telepon`, `alamat`, `catatan`, `total`, `status`, `tanggal`) VALUES
(4, 'INV-20260613104946', 'Muhammad Rezki', '081366376992', 'bengkuylu', 'ad', 75000, 'Menunggu', '2026-06-13 15:49:46'),
(5, 'INV-20260613111917', 'Muhammad Rezki', '081366376992', 'bengkuylu', 'dsa', 45000, 'Menunggu', '2026-06-13 16:19:17');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` bigint(20) NOT NULL,
  `qty` int(11) NOT NULL,
  `subtotal` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id`, `order_id`, `produk_id`, `nama_produk`, `harga`, `qty`, `subtotal`) VALUES
(1, 1, 7, 'minyak goreng', 25000, 2, 50000),
(2, 2, 7, 'minyak goreng', 25000, 2, 50000),
(3, 3, 7, 'minyak goreng', 25000, 5, 125000),
(4, 3, 8, 'garam', 10000, 5, 50000),
(5, 4, 7, 'minyak goreng', 25000, 3, 75000),
(6, 5, 7, 'minyak goreng', 25000, 1, 25000),
(7, 5, 10, 'ayam  potong', 20000, 1, 20000);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `foto` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `harga`, `stok`, `foto`, `deskripsi`, `created_at`) VALUES
(7, 'minyak goreng', 25000, 79, '1781326619_6505.jpg', 'minyak 1 liter asli sawwit', '2026-06-12 17:39:07'),
(8, 'garam', 10000, 25, '1781326739_5214.jpg', 'bagus', '2026-06-13 04:58:59'),
(9, 'ikan segar (1kg)', 25000, 20, '1781341811_5959.jpg', 'ikan segar dari laut', '2026-06-13 09:10:11'),
(10, 'ayam  potong', 20000, 9, '1781341836_4206.jpg', 'ayam segar', '2026-06-13 09:10:36'),
(11, 'roti tawar', 10000, 50, '1781341872_4387.jpg', 'sehat', '2026-06-13 09:11:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
