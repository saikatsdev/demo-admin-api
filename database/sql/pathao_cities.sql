-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 13, 2025 at 07:31 PM
-- Server version: 10.11.9-MariaDB-cll-lve
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `core_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `pathao_cities`
--

CREATE TABLE `pathao_cities` (
  `id` bigint(20) NOT NULL,
  `city_id` bigint(20) NOT NULL,
  `city_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pathao_cities`
--

INSERT INTO `pathao_cities` (`id`, `city_id`, `city_name`) VALUES
(1, 52, 'Bagerhat'),
(2, 62, 'Bandarban '),
(3, 34, 'Barguna '),
(4, 17, 'Barisal'),
(5, 32, 'B. Baria'),
(6, 53, 'Bhola'),
(7, 9, 'Bogra'),
(8, 8, 'Chandpur'),
(9, 15, 'Chapainawabganj'),
(10, 2, 'Chittagong'),
(11, 61, 'Chuadanga'),
(12, 11, 'Cox\'s Bazar'),
(13, 5, 'Cumilla'),
(14, 1, 'Dhaka'),
(15, 35, 'Dinajpur'),
(16, 18, 'Faridpur'),
(17, 6, 'Feni'),
(18, 38, 'Gaibandha'),
(19, 22, 'Gazipur'),
(20, 56, 'Gopalgonj '),
(21, 30, 'Habiganj'),
(22, 41, 'Jamalpur'),
(23, 19, 'Jashore'),
(24, 27, 'Jhalokathi'),
(25, 49, 'Jhenidah'),
(26, 48, 'Joypurhat'),
(27, 63, 'Khagrachari'),
(28, 20, 'Khulna'),
(29, 42, 'Kishoreganj'),
(30, 55, 'Kurigram '),
(31, 28, 'Kushtia'),
(32, 40, 'Lakshmipur'),
(33, 57, 'Lalmonirhat '),
(34, 43, 'Madaripur'),
(35, 60, 'Magura '),
(36, 16, 'Manikganj'),
(37, 50, 'Meherpur'),
(38, 12, 'Moulvibazar'),
(39, 23, 'Munsiganj'),
(40, 26, 'Mymensingh'),
(41, 46, 'Naogaon'),
(42, 54, 'Narail '),
(43, 21, 'Narayanganj'),
(44, 47, 'Narshingdi'),
(45, 14, 'Natore'),
(46, 44, 'Netrakona'),
(47, 39, 'Nilphamari'),
(48, 7, 'Noakhali'),
(49, 24, 'Pabna'),
(50, 37, 'Panchagarh'),
(51, 29, 'Patuakhali'),
(52, 31, 'Pirojpur'),
(53, 58, 'Rajbari '),
(54, 4, 'Rajshahi'),
(55, 59, 'Rangamati '),
(56, 25, 'Rangpur'),
(57, 51, 'Satkhira'),
(58, 64, 'Shariatpur '),
(59, 33, 'Sherpur'),
(60, 10, 'Sirajganj'),
(61, 45, 'Sunamganj'),
(62, 3, 'Sylhet'),
(63, 13, 'Tangail'),
(64, 36, 'Thakurgaon ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pathao_cities`
--
ALTER TABLE `pathao_cities`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pathao_cities`
--
ALTER TABLE `pathao_cities`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
