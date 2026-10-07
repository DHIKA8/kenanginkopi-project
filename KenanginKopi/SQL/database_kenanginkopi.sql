-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2025 at 05:16 AM
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
-- Database: `database_kenanginkopi`
--

-- --------------------------------------------------------

--
-- Table structure for table `coffee`
--

CREATE TABLE `coffee` (
  `CoffeeID` char(5) NOT NULL,
  `CoffeeName` varchar(50) NOT NULL,
  `CoffeeDesc` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coffee`
--

INSERT INTO `coffee` (`CoffeeID`, `CoffeeName`, `CoffeeDesc`) VALUES
('C0001', 'Espresso', 'Strong black coffee'),
('C0002', 'Latte', 'Coffee with milk'),
('C0003', 'Cappuccino', 'Coffee with milk foam'),
('C0004', 'Americano', 'Diluted espresso');

-- --------------------------------------------------------

--
-- Table structure for table `store`
--

CREATE TABLE `store` (
  `StoreID` char(5) NOT NULL,
  `StoreName` varchar(50) NOT NULL,
  `StoreLocation` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store`
--

INSERT INTO `store` (`StoreID`, `StoreName`, `StoreLocation`) VALUES
('S0001', 'Kenangin Kopi Jakarta', 'Jakarta'),
('S0002', 'Kenangin Kopi Bandung', 'Bandung'),
('S0003', 'Kenangin Kopi Bali', 'Bali');

-- --------------------------------------------------------

--
-- Table structure for table `storecoffee`
--

CREATE TABLE `storecoffee` (
  `StoreID` char(5) NOT NULL,
  `CoffeeID` char(5) NOT NULL,
  `Price` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `storecoffee`
--

INSERT INTO `storecoffee` (`StoreID`, `CoffeeID`, `Price`) VALUES
('S0001', 'C0001', 20000.00),
('S0001', 'C0002', 25000.00),
('S0001', 'C0003', 28000.00),
('S0002', 'C0001', 22000.00),
('S0002', 'C0004', 24000.00),
('S0003', 'C0002', 26000.00),
('S0003', 'C0003', 30000.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactiondetails`
--

CREATE TABLE `transactiondetails` (
  `TransactionID` char(5) NOT NULL,
  `CoffeeID` char(5) NOT NULL,
  `Qty` int(2) NOT NULL,
  `Subtotal` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactiondetails`
--

INSERT INTO `transactiondetails` (`TransactionID`, `CoffeeID`, `Qty`, `Subtotal`) VALUES
('T0001', 'C0001', 1, 20000.00),
('T0001', 'C0002', 1, 25000.00),
('T0002', 'C0001', 1, 22000.00),
('T0002', 'C0004', 1, 24000.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` char(5) NOT NULL,
  `UserID` char(5) DEFAULT NULL,
  `StoreID` char(5) DEFAULT NULL,
  `TransactionDate` date NOT NULL,
  `TotalPrice` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `UserID`, `StoreID`, `TransactionDate`, `TotalPrice`) VALUES
('T0001', 'U0002', 'S0001', '2025-12-01', 45000.00),
('T0002', 'U0002', 'S0002', '2025-12-02', 46000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` char(5) NOT NULL,
  `FullName` varchar(50) NOT NULL,
  `UserName` varchar(50) NOT NULL,
  `UserEmail` varchar(50) NOT NULL,
  `UserPassword` varchar(100) NOT NULL,
  `UserRole` char(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `UserName`, `UserEmail`, `UserPassword`, `UserRole`) VALUES
('U0001', 'Admin', 'Admin', 'admin@gmail.com', 'User1234', 'Admin'),
('U0002', 'User Satu', 'usersatu', 'user@kenangin.com', 'user1234', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coffee`
--
ALTER TABLE `coffee`
  ADD PRIMARY KEY (`CoffeeID`);

--
-- Indexes for table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`StoreID`);

--
-- Indexes for table `storecoffee`
--
ALTER TABLE `storecoffee`
  ADD PRIMARY KEY (`StoreID`,`CoffeeID`),
  ADD KEY `CoffeeID` (`CoffeeID`);

--
-- Indexes for table `transactiondetails`
--
ALTER TABLE `transactiondetails`
  ADD PRIMARY KEY (`TransactionID`,`CoffeeID`),
  ADD KEY `CoffeeID` (`CoffeeID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `StoreID` (`StoreID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `UserName` (`UserName`),
  ADD UNIQUE KEY `UserEmail` (`UserEmail`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `storecoffee`
--
ALTER TABLE `storecoffee`
  ADD CONSTRAINT `fk_storecoffee_coffee` FOREIGN KEY (`CoffeeID`) REFERENCES `coffee` (`CoffeeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_storecoffee_store` FOREIGN KEY (`StoreID`) REFERENCES `store` (`StoreID`) ON DELETE CASCADE;

--
-- Constraints for table `transactiondetails`
--
ALTER TABLE `transactiondetails`
  ADD CONSTRAINT `fk_td_coffee` FOREIGN KEY (`CoffeeID`) REFERENCES `coffee` (`CoffeeID`),
  ADD CONSTRAINT `fk_td_transactions` FOREIGN KEY (`TransactionID`) REFERENCES `transactions` (`TransactionID`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_t_store` FOREIGN KEY (`StoreID`) REFERENCES `store` (`StoreID`),
  ADD CONSTRAINT `fk_t_users` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
