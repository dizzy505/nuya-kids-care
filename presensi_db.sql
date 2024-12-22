-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 12, 2024 at 07:23 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `presensi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `anak`
--

CREATE TABLE `anak` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `umur` int NOT NULL,
  `pengasuh` varchar(100) DEFAULT NULL,
  `kategori` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `anak`
--

INSERT INTO `anak` (`id`, `nama`, `umur`, `pengasuh`, `kategori`) VALUES
(1, 'naya', 1, 'Imron Fahroji', 'toddler'),
(2, 'yaya', 3, 'Mayang Andini', 'bayi');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `umur` int DEFAULT NULL,
  `agama` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nama`, `umur`, `agama`, `alamat`) VALUES
(15, 'Azis Setiawan', 35, 'Islam', 'Jl. Merdeka No. 10, Surabaya'),
(16, 'Osama Mangkuluhur', 40, 'Islam', 'Jl. Pahlawan No. 15, Jakarta'),
(17, 'Mayang Andini', 32, 'Islam', 'Jl. Sudirman No. 20, Bandung'),
(18, 'Imron Fahroji', 45, 'Islam', 'Jl. Asia Afrika No. 25, Jakarta'),
(19, 'Aqmal Zaenuri', 38, 'Islam', 'Jl. Diponegoro No. 30, Semarang'),
(20, 'Haura Zahra', 27, 'Islam', 'Jl. Pemuda No. 35, Surabaya'),
(21, 'Irfan Yusyron', 29, 'Islam', 'Jl. Veteran No. 40, Malang'),
(22, 'Arrafiu Anzar', 31, 'Islam', 'Jl. Ahmad Yani No. 45, Yogyakarta'),
(24, 'Irsyad Faruq Ardiansyah', 50, 'Islam', 'Jl. Thamrin No. 50, Jakarta');

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id` int NOT NULL,
  `karyawan_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `durasi_kerja` time GENERATED ALWAYS AS (timediff(`jam_pulang`,`jam_masuk`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi`
--

INSERT INTO `presensi` (`id`, `karyawan_id`, `tanggal`, `jam_masuk`, `jam_pulang`) VALUES
(59, 24, '2024-12-10', '17:59:59', '18:00:06'),
(60, 18, '2024-12-10', '18:00:17', '18:00:21'),
(61, 19, '2024-12-10', '18:00:28', '18:00:32'),
(62, 15, '2024-12-10', '18:00:48', '18:13:00'),
(63, 22, '2024-12-10', '18:00:54', '18:01:08'),
(64, 17, '2024-12-10', '18:01:14', '18:01:18'),
(65, 20, '2024-12-10', '18:01:25', '18:01:29'),
(66, 16, '2024-12-10', '18:02:24', '18:02:28'),
(67, 21, '2024-12-10', '18:02:35', '18:02:39'),
(68, 15, '2024-12-10', '18:11:24', '18:13:00'),
(69, 15, '2024-12-11', '06:03:31', '06:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(4, 'icad', 'icad', 'user'),
(5, 'admin', 'admin', 'admin'),
(6, 'sigit', 'sigit123', 'admin'),
(7, 'ajis', 'ajis123', 'user'),
(9, 'rayn', 'rayn123', 'user'),
(10, 'rayn', 'rayn123', 'admin'),
(11, 'icad', 'icad123', 'admin'),
(12, 'user', 'user123', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak`
--
ALTER TABLE `anak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `karyawan_id` (`karyawan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anak`
--
ALTER TABLE `anak`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `presensi`
--
ALTER TABLE `presensi`
  ADD CONSTRAINT `presensi_ibfk_1` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
