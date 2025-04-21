-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 19, 2025 at 01:29 PM
-- Server version: 5.7.24
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mythic_prints`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `addressId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zipCode` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `addressType` enum('Home','Work','Billing','Shipping') NOT NULL DEFAULT 'Shipping'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`addressId`, `userId`, `street`, `city`, `state`, `zipCode`, `country`, `addressType`) VALUES
(1, 4, '306 Washington Ave', 'Providence', 'RI', '02905', 'United States', 'Shipping'),
(2, 5, '123 Test Lane', 'Testville', 'TS', '12345', 'Testland', 'Shipping'),
(3, 6, '456 Updated St', 'Updatetown', 'UP', '54321', 'Updatedland', 'Home'),
(4, 7, '306 Washington Ave', 'Providence', 'RI', '02905', 'United States', 'Shipping');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `orderId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `shippingdetails` text NOT NULL,
  `paymentmethod` enum('Credit Card','PayPal','Bank Transfer') NOT NULL,
  `totalprice` decimal(10,2) NOT NULL,
  `status` enum('Pending','Shipped','Delivered','Cancelled','Returned') NOT NULL DEFAULT 'Pending',
  `orderdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trackingnumber` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`orderId`, `userId`, `shippingdetails`, `paymentmethod`, `totalprice`, `status`, `orderdate`, `trackingnumber`) VALUES
(1, 4, '306 Washington Ave, Providence, RI, 02905, United States', 'Credit Card', '23.85', 'Pending', '2025-04-09 15:36:04', NULL),
(2, 4, '306 Washington Ave, Providence, RI, 02905, United States', 'Credit Card', '13.90', 'Pending', '2025-04-09 15:38:24', NULL),
(4, 4, '123 Test Lane, Testville, TS, 12345, Testland', 'Credit Card', '33.80', 'Pending', '2025-04-15 14:36:57', NULL),
(5, 7, '306 Washington Ave, Providence, RI, 02905, United States', 'Credit Card', '13.90', 'Pending', '2025-04-19 12:49:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `orderDetailsId` int(11) NOT NULL,
  `orderId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unitPrice` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS ((`quantity` * `unitPrice`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`orderDetailsId`, `orderId`, `productId`, `quantity`, `unitPrice`) VALUES
(1, 1, 8, 1, '9.95'),
(2, 1, 2, 1, '9.95'),
(3, 2, 7, 1, '9.95'),
(4, 4, 1, 1, '9.95'),
(5, 4, 2, 2, '9.95'),
(6, 5, 8, 1, '9.95');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `dateAdded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amountSold` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productId`, `name`, `price`, `image`, `dateAdded`, `amountSold`) VALUES
(1, 'Armoni Armed', '9.95', 'Armoni_Armed.jpg', '2025-02-14 19:51:23', 76),
(2, 'Clarissa', '9.95', 'Clarissa.jpg', '2025-04-13 13:50:12', 1000),
(3, 'Jelixto', '9.95', 'Jelixto.jpg', '2025-02-14 19:51:23', 30),
(4, 'Armoni', '9.95', 'Armoni.jpg', '2025-02-15 00:52:28', 151),
(5, 'Ornus', '9.95', 'Ornus.jpg', '2025-02-15 00:52:28', 300),
(6, 'Argenta Pirate', '9.95', 'Argenta_Pirate.jpg', '2025-02-15 00:52:28', 150),
(7, 'Ixia', '9.95', 'Ixia.jpg', '2025-04-13 13:50:12', 100),
(8, 'Morwyn', '9.95', 'Morwyn.jpg', '2025-04-13 13:50:12', 500),
(9, 'Pitora', '9.95', 'Pitora.jpg', '2025-02-15 00:52:28', 10),
(10, 'Acheron', '9.95', 'Acheron.jpg', '2025-04-13 13:50:12', 0),
(11, 'Andras', '9.95', 'Andras.jpg', '2025-04-13 15:52:31', 0),
(12, 'Argenta Bard', '9.95', 'Argenta_Bard.jpg', '2025-03-13 15:52:31', 0),
(13, 'Argenta Samurai', '9.95', 'Argenta_Samurai.jpg', '2025-03-13 15:52:31', 0),
(14, 'Siegfried', '9.95', 'Siegfried.jpg', '2025-03-13 15:52:31', 0),
(15, 'Veliana', '9.95', 'Veliana.jpg', '2025-04-13 13:50:12', 0),
(16, 'Bashira', '9.95', 'Bashira.jpg', '2025-03-13 15:52:31', 0);

-- --------------------------------------------------------

--
-- Table structure for table `producttag`
--

CREATE TABLE `producttag` (
  `productId` int(11) NOT NULL,
  `tagId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `producttag`
--

INSERT INTO `producttag` (`productId`, `tagId`) VALUES
(7, 1),
(8, 1),
(9, 1),
(1, 3),
(3, 3),
(4, 3),
(2, 4),
(6, 5),
(8, 5),
(5, 6);

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `tagId` int(11) NOT NULL,
  `tagName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`tagId`, `tagName`) VALUES
(6, 'Animal'),
(1, 'Elf'),
(4, 'Mage'),
(5, 'Thief'),
(3, 'Warrior');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userId` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userId`, `email`, `firstName`, `lastName`, `password`, `phoneNumber`) VALUES
(4, 'rpruittfths@gmail.com', 'Richard', 'Pruitt', '$2y$10$oFGy8ikagqUSgWmZxRTudOKiIBp1fcB2y1TUdU7kYD3OMmHPB3E7G', '2679699129'),
(5, 'testuser@example.com', 'Testy', 'McTestface', '$2y$10$a29pdoDNqcRhWbYjWQEDmOb1gJI/NnGSoz5Fl5HoOZQNbFlRlbx8K', '5551234567'),
(6, 'verify1847@example.com', 'Testy', 'McTestface', '$2y$10$/HOHxYW8qTH8EyyU9IIk2.9vGriQJRHI.Uk6TXljMBUXJygv2nW3K', '5551234567'),
(7, 'rpruittfth1s@gmail.com', 'Richard', 'Pruitt', '$2y$10$ym1RtZtWcJlK5H1lzik04.map3HuhZttz9FYwsWSgCdjtdL4DMCBy', '2679699129');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`addressId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`orderId`),
  ADD UNIQUE KEY `trackingnumber` (`trackingnumber`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD PRIMARY KEY (`orderDetailsId`),
  ADD KEY `orderId` (`orderId`),
  ADD KEY `productId` (`productId`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productId`);

--
-- Indexes for table `producttag`
--
ALTER TABLE `producttag`
  ADD PRIMARY KEY (`productId`,`tagId`),
  ADD KEY `tagId` (`tagId`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`tagId`),
  ADD UNIQUE KEY `tagname` (`tagName`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uniqueEmail` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `addressId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `orderId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orderdetails`
--
ALTER TABLE `orderdetails`
  MODIFY `orderDetailsId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `productId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `tagId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `addressIbfk1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `orderIbfk1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE CASCADE;

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`orderId`) REFERENCES `order` (`orderId`) ON DELETE CASCADE,
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `product` (`productId`) ON DELETE CASCADE;

--
-- Constraints for table `producttag`
--
ALTER TABLE `producttag`
  ADD CONSTRAINT `producttagIbfk1` FOREIGN KEY (`productId`) REFERENCES `product` (`productId`) ON DELETE CASCADE,
  ADD CONSTRAINT `producttagIbfk2` FOREIGN KEY (`tagId`) REFERENCES `tag` (`tagId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
