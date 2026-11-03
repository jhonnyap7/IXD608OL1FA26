-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 03, 2026 at 04:39 PM
-- Server version: 10.11.19-MariaDB-cll-lve
-- PHP Version: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jonathanacademyofart2028`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(64) NOT NULL,
  `scent` varchar(128) NOT NULL,
  `size` varchar(32) NOT NULL,
  `image` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `scent`, `size`, `image`) VALUES
(1, 'Obsidian', 'A deep, sophisticated candle inspired by the sea.', 24.99, 'candle', 'Black Sea', '10 oz', ''),
(2, 'Dune', 'A warm, elegant fragrance inspired by natural landscapes.', 24.99, 'candle', 'White Orris & Sandalwood', '10 oz', ''),
(3, 'Current', 'A clean, calming fragrance designed for everyday living.', 24.99, 'candle', 'White Tea', '10 oz', ''),
(4, 'Verdant', 'A fresh, woodsy fragrance inspired by evergreen forests.', 19.99, 'candle', 'Fraser Fir', '10 oz', ''),
(5, 'Still', 'A peaceful, grounding fragrance with woody and herbal notes.', 19.99, 'candle', 'Hinoki Cypress & White Eucalyptus', '10 oz', ''),
(6, 'Vapor', 'A refreshing fragrance combining citrus, resin, and eucalyptus.', 19.99, 'candle', 'White Copal & Bergamot + White Eucalyptus', '10 oz', ''),
(7, 'Holiday', 'A warm seasonal candle with comforting caramel and vanilla notes.', 19.99, 'candle', 'Sweet Vanilla Caramel', '10 oz', ''),
(8, 'Solace', 'A soft, comforting fragrance designed for quiet moments.', 19.99, 'concept', 'Cashmere & Amber', '10 oz', ''),
(9, 'Terra', 'An earthy fragrance inspired by natural materials and architecture.', 19.99, 'concept', 'Cedarwood & Vetiver', '10 oz', ''),
(10, 'Noir', 'A rich, atmospheric fragrance with warm and smoky notes.', 19.99, 'concept', 'Smoked Oud & Tonka', '10 oz', ''),
(11, 'Lumen', 'A bright, minimal fragrance inspired by morning light.', 19.99, 'concept', 'Neroli & White Musk', '10 oz', ''),
(12, 'Serein', 'A delicate, calming fragrance inspired by peaceful evenings.', 19.99, 'concept', 'Lavender & Sandalwood', '10 oz', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
