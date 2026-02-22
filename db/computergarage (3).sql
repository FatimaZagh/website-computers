-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 09:03 AM
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
-- Database: `computergarage`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `CartID` int(100) NOT NULL,
  `ProductID` int(100) NOT NULL,
  `ProdPic` varchar(500) NOT NULL,
  `ProdName` varchar(500) NOT NULL,
  `Rate` float NOT NULL,
  `Qty` int(100) NOT NULL,
  `TotalCost` float NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `selected_color_id` int(11) DEFAULT NULL,
  `selected_color_name` varchar(100) DEFAULT NULL,
  `selected_color_code` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`CartID`, `ProductID`, `ProdPic`, `ProdName`, `Rate`, `Qty`, `TotalCost`, `UserName`, `selected_color_id`, `selected_color_name`, `selected_color_code`) VALUES
(33, 2024, '1658330049na-gaming-laptop-hp-original-imag7a7fgvrae7uu.webp', 'HP HP Pavilion Ryzen 5 Hexa Core 4600H', 53214, 1, 53214, 'xyz@gmail.com', NULL, NULL, NULL),
(38, 2040, '1747778429_principal_2000.jpg', 'head- buy', 160, 2, 320, 'unknown.user.un.90@gmail.com', NULL, NULL, NULL),
(62, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, '0', 46, 'White', '#ffffff'),
(63, 2059, '1748256055_0_BlackHead.png', 'Testinggggggggggg', 475, 1, 475, '0', 50, 'Green', '#00ff11'),
(64, 2057, '1748253805_0_BlackHead.png', 'Testing', 260.68, 1, 260.68, '0', 45, 'Red', '#ff0000'),
(65, 2029, '1658330749microsoft-original-imafr9qywz88mrfz.webp', 'MICROSOFT Surface Laptop 3 Core i5 10th Gen', 100799, 1, 100799, '0', NULL, NULL, NULL),
(66, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, '0', 45, 'Red', '#ff0000'),
(67, 2009, '1658228903-original-imagcxd2pbujqqjr.webp', 'ASUS Core i5-11400F', 30160, 1, 30160, '0', NULL, NULL, NULL),
(68, 2009, '1658228903-original-imagcxd2pbujqqjr.webp', 'ASUS Core i5-11400F', 30160, 1, 30160, '0', NULL, NULL, NULL),
(69, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, '0', 44, 'Black', '#000000'),
(70, 2009, '1658228903-original-imagcxd2pbujqqjr.webp', 'ASUS Core i5-11400F', 30160, 1, 30160, '0', NULL, NULL, NULL),
(71, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, '0', 44, 'Black', '#000000'),
(72, 2039, '1747516583DSC00368.webp', 'gaming key', 72, 2, 144, '0', NULL, NULL, NULL),
(73, 2030, '1658330833microsoft-original-imafr9qyecvfeyta.webp', 'MICROSOFT Surface Laptop 3 Ryzen 5 Quad Core 3580U', 99999.2, 1, 99999.2, '0', NULL, NULL, NULL),
(74, 2012, '1658312010apple-imac-original-imaejxpjgm4xrtym.webp', 'APPLE All-in-One Core i5 (6th Gen)', 181344, 1, 181344, '0', NULL, NULL, NULL),
(75, 2029, '1658330749microsoft-original-imafr9qywz88mrfz.webp', 'MICROSOFT Surface Laptop 3 Core i5 10th Gen', 100799, 1, 100799, '0', NULL, NULL, NULL),
(76, 2029, '1658330749microsoft-original-imafr9qywz88mrfz.webp', 'MICROSOFT Surface Laptop 3 Core i5 10th Gen', 100799, 1, 100799, '0', NULL, NULL, NULL),
(77, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, '0', 44, 'Black', '#000000'),
(78, 2030, '1658330833microsoft-original-imafr9qyecvfeyta.webp', 'MICROSOFT Surface Laptop 3 Ryzen 5 Quad Core 3580U', 99999.2, 1, 99999.2, '0', NULL, NULL, NULL),
(79, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, '0', 45, 'Red', '#ff0000'),
(80, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, '0', 45, 'Red', '#ff0000'),
(81, 2011, '1658296880-original-imagamagsbrkjnma.webp', 'MSI Core i7 11Gen (11700F) ', 117000, 1, 117000, '0', NULL, NULL, NULL),
(82, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, '0', 44, 'Black', '#000000'),
(84, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 2, 548.8, '0', 45, 'Red', '#ff0000'),
(89, 2023, '165832954371xD9dtfhjL._SL1500_.jpg', 'Acer ED320QR Full HD', 18560, 1, 18560, '0', NULL, NULL, NULL),
(95, 2057, '1748253805_0_BlackHead.png', 'Testing', 0, 1, 0, '0', 46, 'White', '#ffffff'),
(104, 2061, '1748288362_0_BlackHead.png', 'Hesh Evo Wireless Headphones', 390, 2, 780, 'abbasmashaqi86@gmail.com', 57, 'Red', '#ff0000'),
(105, 2018, '1658327840g35dx-in045t-asus-original-imag4xyybm7smhde.webp', 'ASUS Ryzen 7-3700X', 123436, 1, 123436, 'tasu30349@gmail.com', NULL, NULL, NULL),
(106, 2062, '1748376920_0_BlackHead.png', 'Hesh Evo Wireless Headphones', 82, 2, 164, 'tasu30349@gmail.com', 60, 'Red', '#ff0000'),
(107, 2009, '1658228903-original-imagcxd2pbujqqjr.webp', 'ASUS Core i5-11400F', 30160, 2, 60320, 'tasu30349@gmail.com', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percent','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_codes`
--

INSERT INTO `discount_codes` (`id`, `code`, `description`, `discount_type`, `discount_value`, `usage_limit`, `used_count`, `expiry_date`, `active`) VALUES
(2, '2', 'fgdf', 'fixed', 50.00, NULL, 8, '2025-06-07', 1),
(3, 'cc', 'ff', 'percent', 20.00, 5, 4, '2025-05-30', 0),
(7, 'SAVE20', 'dhhshh', 'fixed', 500.00, NULL, 5, '2025-05-30', 1),
(8, 'WWW', 'hi', 'percent', 5.00, 3, 2, '2025-05-31', 1),
(9, 'ABBAS77', 'Abbas sponsered discount', 'fixed', 100.00, 3, 1, '2025-06-26', 1),
(10, 'DIS20', 'ABCDEFGH', 'percent', 5.00, 5, 1, '2025-06-07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_messages`
--

CREATE TABLE `feedback_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submission_date` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_messages`
--

INSERT INTO `feedback_messages` (`id`, `name`, `email`, `subject`, `message`, `submission_date`, `is_read`) VALUES
(1, 'fatima', 'fatima.a.zaghlol@gmail.com', 'no', 'return', '2025-05-18 10:52:33', 1),
(2, 'fatima2', 'fatima.a.zaghlol@gmail.com', 'fail', 'hi, i want a message back', '2025-05-18 10:56:08', 1),
(3, 'Fatima3', 'fatima.a.zaghlol@gmail.com', 'new try', 'message', '2025-05-18 11:19:31', 1),
(7, 'Hi', 'tasu30349@gmail.com', 'HHHH', 'LFDFL;SLD;', '2025-05-24 10:55:10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `managecat`
--

CREATE TABLE `managecat` (
  `catid` int(11) NOT NULL,
  `catname` varchar(100) NOT NULL,
  `catpic` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `managecat`
--

INSERT INTO `managecat` (`catid`, `catname`, `catpic`) VALUES
(18, 'Desktop', '1658055149pngwing.com(7).png'),
(19, 'Laptops', '1658217219pngfind.com-laptop-png-602361.png'),
(20, 'Keyboard', '16582172968-pc-keyboard-png-image.png'),
(21, 'Mouse', '1658226270computer_mouse_PNG7683.png'),
(22, 'Speakers', '1658229913audio_speakers_PNG11130.png'),
(24, 'Storage Devices', '1748074699_SSD.png'),
(25, 'Cables and Adapters', '1748076416Cables_AdaptersCAT.png');

-- --------------------------------------------------------

--
-- Table structure for table `manageproduct`
--

CREATE TABLE `manageproduct` (
  `CatID` int(100) NOT NULL,
  `SubcatID` int(100) NOT NULL,
  `ProductID` int(100) NOT NULL,
  `ProductName` varchar(500) NOT NULL,
  `Rate` int(100) NOT NULL,
  `Discount` int(100) NOT NULL,
  `Description` text NOT NULL,
  `Stock` int(100) NOT NULL,
  `ProductPic` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manageproduct`
--

INSERT INTO `manageproduct` (`CatID`, `SubcatID`, `ProductID`, `ProductName`, `Rate`, `Discount`, `Description`, `Stock`, `ProductPic`) VALUES
(18, 15, 2009, 'ASUS Core i5-11400F', 52000, 42, 'Experience luxury without any apprehension with the Strix G10CE which boasts a stylish design and promising performance. With just 27 litres, the Strix G10CE crams essential components into a compact design that takes up less space on or under your desk. Additionally, it boasts an 11th Gen Intel Core CPU and powerful NVIDIA GeForce graphics that help in experiencing immersive gameplay. (8 GB RAM/NVIDIA GeForce GTX1650 Graphics/1 TB Hard Disk/512 GB SSD Capacity/Windows 10 Home (64-bit)/4 GB Graphics Memory) Gaming Tower  (G10CE-51140F283T)', 19, '1658228903-original-imagcxd2pbujqqjr.webp'),
(18, 15, 2011, 'MSI Core i7 11Gen (11700F) ', 195000, 40, 'MSI Core i7 11Gen (11700F) (16 GB RAM/Nvidia GeForce RTX 3060 VENTUS Graphics/1 TB Hard Disk/1 TB SSD Capacity/Windows 11 Home (64-bit)/12 GB Graphics Memory) Gaming Tower  (MPG Trident A 11TC-2292IN-B71170F3061216G)\r\n    Processor Type: Intel 4.9 GHz\r\n    12 GB Nvidia GeForce RTX 3060 VENTUS Graphics\r\n    Octa Core Gaming Tower\r\n    16 GB DDR4 RAM\r\n    Hard Disk Capacity: 1 TB\r\n    SSD Capacity: 1 TB', 8, '1658296880-original-imagamagsbrkjnma.webp'),
(18, 16, 2012, 'APPLE All-in-One Core i5 (6th Gen)', 188900, 4, 'APPLE All-in-One Core i5 (6th Gen) (8 GB DDR3/2 TB/Mac OS X Lion/2 GB/27 Inch Screen/MK482HN/A)  (Silver)', 54, '1658312010apple-imac-original-imaejxpjgm4xrtym.webp'),
(18, 16, 2013, 'APPLE 2021 iMac with 4.5K Retina display M1 ', 139900, 7, 'APPLE 2021 iMac with 4.5K Retina display M1 (8 GB Unified/256 GB SSD/Mac OS Big Sur/24 Inch Screen/MGPH3HN/A)  (Green, 461 mm x 547 mm x 130 mm, 4.48 kg)\r\n    Mac OS Big Sur\r\n    Apple M1\r\n    RAM 8 GB Unified\r\n    24 inch Display', 2, '1658313299mgph3hn-a-apple-original-imag3d5ttap34g3m.webp'),
(18, 16, 2014, 'APPLE 2021 iMac with 4.5K Retina display M1 ', 139900, 7, 'APPLE 2021 iMac with 4.5K Retina display M1 (8 GB Unified/256 GB SSD/Mac OS Big Sur/24 Inch Screen/MGPM3HN/A)  (Pink, 461 mm x 547 mm x 130 mm, 4.48 kg)\r\n    Mac OS Big Sur\r\n    Apple M1\r\n    RAM 8 GB Unified\r\n    24 inch Display', 2, '1658313425mjva3hn-a-apple-original-imag3d5tvzyza8cv.webp'),
(18, 16, 2015, 'APPLE iMac Core i5 (7th Gen) ', 190400, 7, 'APPLE iMac Core i5 (7th Gen) (8 GB DDR4/1 TB/Mac OS X Sierra/8 GB/27 Inch Screen/MNED2HN/A)  (White, 516 mm x 650 mm x 203 mm, 9.44 kg)    Mac OS X Sierra\r\n    Intel Core i5 (7th Gen)\r\n    HDD Capacity 1 TB\r\n    RAM 8 GB DDR4\r\n    27 inch Display', 0, '1658313534apple-mned2hn-a-original-imaevdwvynk7vmdc.webp'),
(18, 16, 2016, 'APPLE 2021 iMac with 4.5K Retina display M1', 139900, 9, 'APPLE 2021 iMac with 4.5K Retina display M1 (8 GB Unified/256 GB SSD/Mac OS Big Sur/24 Inch Screen/MGPC3HN/A)  (Silver, 461 mm x 547 mm x 130 mm, 4.48 kg)\r\n    Mac OS Big Sur\r\n    Apple M1\r\n    RAM 8 GB Unified\r\n    24 inch Display', 7, '1658313667mgtf3hn-a-apple-original-imag3d5thhhfgtyg.webp'),
(18, 15, 2018, 'ASUS Ryzen 7-3700X', 177990, 27, 'ASUS Ryzen 7-3700X (8 GB RAM/NVIDIA GeForce RTX 2060S Graphics/1 TB Hard Disk/512 GB SSD Capacity/Windows 10 Home (64-bit)/8 GB Graphics Memory) Gaming Tower  (G35DX-IN035T)\r\n    Processor Type: AMD 3.6 GHz\r\n    8 GB NVIDIA GeForce RTX 2060S Graphics\r\n    Octa Core Gaming Tower\r\n    8 GB DDR4 RAM\r\n    Hard Disk Capacity: 1 TB\r\n    SSD Capacity: 512 GB', 47, '1658327840g35dx-in045t-asus-original-imag4xyybm7smhde.webp'),
(18, 15, 2019, 'ASUS Ryzen 7 5800X ', 292990, 40, 'ASUS Ryzen 7 5800X (16 GB RAM/NVIDIA GeForce RTX3070 Graphics/1 TB Hard Disk/1 TB SSD Capacity/Windows 10 Home (64-bit)/8 GB Graphics Memory) Gaming Tower  (G15DK-R5800X298T)\r\n    Processor Type: AMD 3.8 Ghz\r\n    8 GB NVIDIA GeForce RTX3070 Graphics\r\n    Octa Core Gaming Tower\r\n    16 GB DDR4 RAM\r\n    Hard Disk Capacity: 1 TB\r\n    SSD Capacity: 1 TB', 3, '1658327996-original-imagcxd2f8sdbmwn.webp'),
(18, 15, 2020, 'HP Core i7', 146195, 0, 'HP Core i7 (16 GB RAM/NVIDIA GeForce RTX 3060 Ti Graphics/1 TB Hard Disk/1 TB SSD Capacity/Windows 11 Home (64-bit)/8 GB Graphics Memory) Gaming Tower  (TG01-2005in)\r\n    Processor Type: Intel 2.5 GHz\r\n    8 GB NVIDIA GeForce RTX 3060 Ti Graphics\r\n    Octa Core Gaming Tower\r\n    16 GB DDR4 RAM\r\n    Hard Disk Capacity: 1 TB\r\n    SSD Capacity: 1 TB', 18, '16583281272-5-tg01-2005in-1-hp-original-imag8vyayr5dkeda.webp'),
(18, 18, 2021, 'Lenovo 23.8 inch Full HD (D24-20)', 15790, 46, 'Lenovo 23.8 inch Full HD VA Panel 3-Side Near Edgeless with TUV Eye Care Monitor (D24-20)  (Response Time: 4 ms, 60 Hz Refresh Rate)\r\n    Panel Type: VA Panel\r\n    Screen Resolution Type: Full HD\r\n    Response Time: 4 ms | Refresh Rate: 60 Hz', 45, '1659009080d24-20-66aekac1in-lenovo-original-imag2qwzazcdmqtb.webp'),
(18, 18, 2022, 'SAMSUNG 24 inch Full HD (LF24T350FHWXXL)', 19100, 42, 'SAMSUNG 24 inch Full HD LED Backlit IPS Panel with 3-Sided Borderless Display, Game & Free Sync Mode, Eye Saver Mode & Flicker Free Monitor (LF24T350FHWXXL)  (AMD Free Sync, Response Time: 5 ms, 75 Hz Refresh Rate)\r\n    Panel Type: IPS Panel\r\n    Screen Resolution Type: Full HD\r\n    Brightness: 250 nits\r\n    Response Time: 5 ms | Refresh Rate: 75 Hz\r\n    HDMI Ports - 1', 6, '1658329327-original-imagg897ufhyvwqq.webp'),
(18, 18, 2023, 'Acer ED320QR Full HD', 29000, 36, 'Acer ED320QR Full HD VA Panel LED Curved Gaming Monitor with 165Hz Refresh Rate AMD Free Sync and 2 X HDMI 1 X Display Port (Black, 31.5 Inch, 1920 x 1080 Pixels) ', 3, '165832954371xD9dtfhjL._SL1500_.jpg'),
(19, 17, 2024, 'HP HP Pavilion Ryzen 5 Hexa Core 4600H', 76020, 30, 'HP HP Pavilion Ryzen 5 Hexa Core 4600H - (8 GB/512 GB SSD/Windows 10 Home/4 GB Graphics/NVIDIA GeForce GTX 1650Ti/144 Hz) 15-ec1025AX Gaming Laptop  (15.6 inch, Shadow Black, 1.98 kg)\r\n    NVIDIA GeForce GTX 1650Ti\r\n    15.6 inch Full HD IPS Micro-Edge Anti-Glare Display (250 nits Brightness, 141 PPI, 45% NTSC Color Gamut)\r\n    Light Laptop without Optical Disk Drive\r\n    Pre-installed Genuine Windows 10 OS', 0, '1658330049na-gaming-laptop-hp-original-imag7a7fgvrae7uu.webp'),
(19, 17, 2025, 'acer Aspire 7 Ryzen 5 Hexa Core 5500U', 89999, 38, 'acer Aspire 7 Ryzen 5 Hexa Core 5500U - (8 GB/512 GB SSD/Windows 10 Home/4 GB Graphics/NVIDIA GeForce GTX 1650) A715-42G Gaming Laptop  (15.6 inch, Black, 2.15 kg)\r\n    NVIDIA GeForce GTX 1650\r\n    15.6 inch Full HD LED Backlit IPS ComfyView Display (16:9 Aspect Ratio, 45% NTSC Color Gamut)\r\n    Light Laptop without Optical Disk Drive\r\n    Pre-installed Genuine Windows 10 OS', 4, '1658330177na-gaming-laptop-acer-original-imagyhwfgwhkf3vv.webp'),
(19, 17, 2026, 'MSI GF63 Thin Core i7 10th Gen', 95990, 23, 'MSI GF63 Thin Core i7 10th Gen - (16 GB/512 GB SSD/Windows 10 Home/4 GB Graphics/NVIDIA GeForce RTX 3050/144 Hz) GF63 Thin 10UC-606IN Gaming Laptop  (15.6 inch, Black, 1.86 kg)', 5, '1658330258gf63-thin-10uc-606in-gaming-laptop-msi-original-imag6xfufgkdahu8.webp'),
(19, 17, 2027, 'Lenovo IdeaPad Gaming Core i5 11th Gen', 76890, 33, 'Lenovo IdeaPad Gaming Core i5 11th Gen - (8 GB/512 GB SSD/Windows 11 Home/4 GB Graphics/NVIDIA GeForce GTX 1650) 15IHU6 Gaming Laptop  (15.6 Inch, Shadow Black, 2.25 kg)\r\n    NVIDIA GeForce GTX 1650\r\n    15.6 Inch Full HD IPS 250nits Anti-glare, 60Hz, 45% NTSC, DC dimmer\r\n    Light Laptop without Optical Disk Drive', 5, '1658330324na-gaming-laptop-lenovo-original-imag5ve3jvhgvsnx.webp'),
(19, 17, 2028, 'ASUS ROG Strix G15 Ryzen 7 Octa Core 4800H', 76990, 1, 'ASUS ROG Strix G15 Ryzen 7 Octa Core 4800H - (8 GB/512 GB SSD/Windows 10 Home/4 GB Graphics/NVIDIA GeForce GTX 1650/144 Hz) G513IH-HN086T Gaming Laptop  (15.6 inch, Eclipse Gray, 2.10 Kg)\r\n    NVIDIA GeForce GTX 1650\r\n    15.6 inch Full HD Anti-glare Display (16:9, 170 Viewing Angle, Value IPS-level Panel, 250nits Brightness, 1:1000 Contrast, 45% NTSC, 62.5% SRGB, 47.34% Adobe)\r\n    Light Laptop without Optical Disk Drive\r\n    Pre-installed Genuine Windows 10 OS', 0, '1658330388-original-imagewgtfgzf8fdd.webp'),
(19, 19, 2029, 'MICROSOFT Surface Laptop 3 Core i5 10th Gen', 104999, 4, 'MICROSOFT Surface Laptop 3 Core i5 10th Gen - (8 GB/128 GB SSD/Windows 10 Home) 1867 Laptop  (13 inch, Platinum, 1.27 kg)\r\n    Pre-installed Genuine Windows 10 OS\r\n    Light Laptop without Optical Disk Drive\r\n    13 inch Full HD+ LED Backlit PixelSense with 10 Point Multi-touch Display (3:2 Aspect Ratio, Surface Pen Enabled)', 8, '1658330749microsoft-original-imafr9qywz88mrfz.webp'),
(19, 19, 2030, 'MICROSOFT Surface Laptop 3 Ryzen 5 Quad Core 3580U', 124999, 20, 'MICROSOFT Surface Laptop 3 Ryzen 5 Quad Core 3580U - (8 GB/128 GB SSD/Windows 10 Home) 1873 Laptop  (15 inch, Platinum, 1.54 kg)\r\n    Pre-installed Genuine Windows 10 OS\r\n    Light Laptop without Optical Disk Drive\r\n    15 inch Quad HD+ LED Backlit PixelSense with 10 Point Multi-touch Display (3:2 Aspect Ratio, Surface Pen Enabled)', 3, '1658330833microsoft-original-imafr9qyecvfeyta.webp'),
(19, 20, 2031, 'APPLE 2020 Macbook Air M1', 117900, 10, 'APPLE 2020 Macbook Air M1 - (8 GB/512 GB SSD/Mac OS Big Sur) MGN73HN/A  (13.3 inch, Space Grey, 1.29 kg)\r\n    Stylish & Portable Thin and Light Laptop\r\n    13.3 inch Quad LED Backlit IPS Display (227 PPI, 400 nits Brightness, Wide Colour (P3), True Tone Technology)\r\n    Light Laptop without Optical Disk Drive', 14, '1658661651apple-original-imafxfyqkdfxqjab.webp'),
(19, 20, 2032, 'APPLE MacBook Pro with Touch Bar Core i5 10th Gen', 194900, 6, 'APPLE MacBook Pro with Touch Bar Core i5 10th Gen - (16 GB/1 TB SSD/Mac OS Catalina) MWP52HN/A  (13 inch, Space Grey, 1.4 kg)\r\n    Stylish & Portable Thin and Light Laptop\r\n    13 inch Full HD+ LED Backlit IPS Retina Display (227 PPI, 500 nits Brightness, Wide Color (P3), True Tone Technology)\r\n    Light Laptop without Optical Disk Drive', 0, '1658661803apple-na-thin-and-light-laptop-original-imafs5nmg3kxcqnz.webp'),
(20, 0, 2033, 'APPLE 2021 Macbook Pro M1 Max ', 50000, 7, 'APPLE 2021 Macbook Pro M1 Max - (32 GB/1 TB SSD/Mac OS Monterey) MK1A3HN/A  (16.2 inch, Space Grey�, 2.2 kg)\r\n    Light Laptop without Optical Disk Drive\r\n    16.2 inch Liquid Retina XDR display, Native resolution at 254 pixels per inch, Up to 1,000 nits sustained (full-screen) brightness, 1,600 nits peak brightness, 10,00,000:1 contrast ratio', 5, '1658662433mk183hn-a-laptop-apple-original-imag7yzkbgbwvwq3.webp'),
(20, 21, 2039, 'gaming key', 90, 20, 'new key added', 6, '1747516583DSC00368.webp'),
(23, 27, 2040, 'head- buy', 200, 50, 'new', 3, '1747778429_principal_2000.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orderproducts`
--

CREATE TABLE `orderproducts` (
  `SrNo` int(10) NOT NULL,
  `ProductID` int(10) NOT NULL,
  `ProdPic` varchar(100) NOT NULL,
  `ProdName` varchar(500) NOT NULL,
  `Rate` float NOT NULL,
  `Qty` int(10) NOT NULL,
  `TotalCost` float NOT NULL,
  `OrderNo` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderproducts`
--

INSERT INTO `orderproducts` (`SrNo`, `ProductID`, `ProdPic`, `ProdName`, `Rate`, `Qty`, `TotalCost`, `OrderNo`) VALUES
(15, 2020, '16583281272-5-tg01-2005in-1-hp-original-imag8vyayr5dkeda.webp', 'HP Core i7', 146195, 1, 146195, 2011),
(16, 2029, '1658330749microsoft-original-imafr9qywz88mrfz.webp', 'MICROSOFT Surface Laptop 3 Core i5 10th Gen', 100799, 1, 100799, 2012),
(17, 2039, '1747516583DSC00368.webp', 'gaming key', 72, 1, 72, 2013),
(18, 2032, '1658661803apple-na-thin-and-light-laptop-original-imafs5nmg3kxcqnz.webp', 'APPLE MacBook Pro with Touch Bar Core i5 10th Gen', 183206, 5, 916030, 2014),
(19, 2024, '1658330049na-gaming-laptop-hp-original-imag7a7fgvrae7uu.webp', 'HP HP Pavilion Ryzen 5 Hexa Core 4600H', 53214, 1, 53214, 2015),
(20, 2039, '1747516583DSC00368.webp', 'gaming key', 72, 3, 216, 2016),
(21, 2029, '1658330749microsoft-original-imafr9qywz88mrfz.webp', 'MICROSOFT Surface Laptop 3 Core i5 10th Gen', 100799, 1, 100799, 2017),
(22, 2057, '1748253805_0_BlackHead.png', 'Testing', 274.4, 1, 274.4, 2018),
(23, 2030, '1658330833microsoft-original-imafr9qyecvfeyta.webp', 'MICROSOFT Surface Laptop 3 Ryzen 5 Quad Core 3580U', 94999.2, 1, 94999.2, 2018);

-- --------------------------------------------------------

--
-- Table structure for table `ordertable`
--

CREATE TABLE `ordertable` (
  `OrderID` int(100) NOT NULL,
  `FullName` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `ShippingAddress` varchar(100) NOT NULL,
  `PaymentMethod` varchar(100) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `OrderDate` datetime NOT NULL,
  `BillAmount` int(100) NOT NULL,
  `Status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordertable`
--

INSERT INTO `ordertable` (`OrderID`, `FullName`, `Email`, `PhoneNumber`, `ShippingAddress`, `PaymentMethod`, `Username`, `OrderDate`, `BillAmount`, `Status`) VALUES
(2011, NULL, NULL, NULL, 'new state', 'Cash on Delivery', 'xyz@gmail.com', '2022-07-28 06:28:25', 146195, 'Order Placed'),
(2012, NULL, NULL, NULL, 'im rich', 'Cash on Delivery', 'xyz@gmail.com', '2022-07-28 06:30:01', 100799, 'Order Placed'),
(2013, NULL, NULL, NULL, 'nablus', 'Cash on Delivery', 'unknown.user.un.90@gmail.com', '2025-05-18 12:19:19', 72, 'Order Placed'),
(2014, 'fatima zaghlol', 'unknown.user.un.90@gmail.com', '1234567891', 'street\nNablus, palestine - 123456', 'Cash on Delivery', 'unknown.user.un.90@gmail.com', '2025-05-18 12:32:45', 916030, 'Delivered'),
(2015, 'user', 'unknown.user.un.90@gmail.com', '1234567891', 'nablus\nnablus, palestine - 123456', 'Cash on Delivery', 'unknown.user.un.90@gmail.com', '2025-05-21 03:10:34', 53214, 'Cancelled'),
(2016, 'user', 'unknown.user.un.90@gmail.com', '1234567891', 'lukhAFd\nlajkehf, aekjbf - 678901', 'Cash on Delivery', 'unknown.user.un.90@gmail.com', '2025-05-21 03:18:11', 216, 'Order Placed'),
(2017, 'MMM', 'tasu30349@gmail.com', '5555522222', 'Street 51, 654\nRamallah, Ramallah - 231659', 'Cash on Delivery', 'tasu30349@gmail.com', '2025-05-24 14:30:40', 100799, 'Confirmed'),
(2018, 'DDDDD', 'tasu30349@gmail.com', '1234567890', 'Yaseed\nNablus, Nablus - 123456', 'Cash on Delivery', 'tasu30349@gmail.com', '2025-05-27 01:03:30', 95274, 'Confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `product_colors`
--

CREATE TABLE `product_colors` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `color_code` varchar(7) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`id`, `product_id`, `color_name`, `color_code`, `is_default`, `created_at`, `stock_quantity`) VALUES
(1, 2041, 'RED', '#b61b1b', 1, '2025-05-25 23:33:09', 0),
(2, 2042, 'Black', '#000000', 1, '2025-05-25 23:35:15', 0),
(3, 2042, 'White', '#ffffff', 0, '2025-05-25 23:35:15', 0),
(4, 2042, 'Red', '#e70808', 0, '2025-05-25 23:35:15', 0),
(5, 2043, 'Black', '#000000', 1, '2025-05-25 23:55:43', 0),
(6, 2043, 'White', '#ffffff', 0, '2025-05-25 23:55:43', 0),
(7, 2043, 'Red', '#ff0000', 0, '2025-05-25 23:55:43', 0),
(8, 2044, 'Black', '#000000', 1, '2025-05-26 07:43:39', 0),
(9, 2044, 'White', '#ffffff', 0, '2025-05-26 07:43:39', 0),
(10, 2044, 'Red', '#f70202', 0, '2025-05-26 07:43:39', 0),
(11, 2046, 'Black', '#000000', 1, '2025-05-26 07:58:44', 0),
(12, 2046, 'Red', '#fb0404', 0, '2025-05-26 07:58:44', 0),
(13, 2046, 'White', '#ffffff', 0, '2025-05-26 07:58:44', 0),
(14, 2047, 'Black', '#000000', 1, '2025-05-26 08:14:25', 0),
(15, 2047, 'Red', '#ff0505', 0, '2025-05-26 08:14:25', 0),
(16, 2047, 'White', '#ffffff', 0, '2025-05-26 08:14:25', 0),
(17, 2048, 'Black', '#000000', 1, '2025-05-26 08:19:51', 0),
(18, 2048, 'Red', '#ff0000', 0, '2025-05-26 08:19:51', 0),
(19, 2048, 'White', '#ffffff', 0, '2025-05-26 08:19:51', 0),
(20, 2049, 'Black', '#000000', 1, '2025-05-26 08:26:01', 0),
(21, 2049, 'Red', '#ff0000', 0, '2025-05-26 08:26:01', 0),
(22, 2049, 'White', '#ffffff', 0, '2025-05-26 08:26:01', 0),
(23, 2050, 'Black', '#000000', 1, '2025-05-26 08:35:36', 20),
(24, 2050, 'Red', '#ff0000', 0, '2025-05-26 08:35:36', 15),
(25, 2050, 'White', '#ffffff', 0, '2025-05-26 08:35:36', 15),
(26, 2051, 'Black', '#000000', 1, '2025-05-26 08:59:54', 3),
(27, 2051, 'Red', '#ff0000', 0, '2025-05-26 08:59:54', 2),
(28, 2051, 'White', '#ffffff', 0, '2025-05-26 08:59:54', 2),
(29, 2052, 'Black', '#000000', 1, '2025-05-26 09:14:06', 2),
(30, 2052, 'White', '#ffffff', 0, '2025-05-26 09:14:06', 3),
(31, 2052, 'Red', '#ff0000', 0, '2025-05-26 09:14:06', 2),
(32, 2053, 'Black', '#000000', 1, '2025-05-26 09:23:31', 3),
(33, 2053, 'White', '#ffffff', 0, '2025-05-26 09:23:31', 4),
(34, 2053, 'Red', '#ff0000', 0, '2025-05-26 09:23:31', 2),
(35, 2054, 'Black', '#000000', 1, '2025-05-26 09:34:15', 4),
(36, 2054, 'Red', '#ff0000', 0, '2025-05-26 09:34:16', 3),
(37, 2054, 'White', '#ffffff', 0, '2025-05-26 09:34:16', 2),
(38, 2055, 'Black', '#000000', 1, '2025-05-26 09:43:30', 4),
(39, 2055, 'White', '#ffffff', 0, '2025-05-26 09:43:30', 3),
(40, 2055, 'Red', '#ff0000', 0, '2025-05-26 09:43:30', 1),
(41, 2056, 'Red', '#ff0000', 1, '2025-05-26 09:47:20', 4),
(42, 2056, 'White', '#ffffff', 0, '2025-05-26 09:47:20', 2),
(43, 2056, 'Black', '#000000', 0, '2025-05-26 09:47:20', 3),
(44, 2057, 'Black', '#000000', 1, '2025-05-26 10:03:25', 2),
(45, 2057, 'Red', '#ff0000', 0, '2025-05-26 10:03:25', 4),
(46, 2057, 'White', '#ffffff', 0, '2025-05-26 10:03:25', 3),
(47, 2058, 'Blue', '#0008ff', 1, '2025-05-26 10:28:45', 5),
(48, 2058, 'White', '#ffffff', 0, '2025-05-26 10:28:45', 4),
(49, 2058, 'Green', '#00ff11', 0, '2025-05-26 10:28:45', 2),
(50, 2059, 'Green', '#00ff11', 1, '2025-05-26 10:40:55', 4),
(51, 2059, 'Yellow', '#fff700', 0, '2025-05-26 10:40:55', 2),
(52, 2059, 'Blue', '#0011ff', 0, '2025-05-26 10:40:55', 6),
(53, 2059, 'Red', '#ff0000', 0, '2025-05-26 11:12:35', 4),
(54, 2057, 'Orange', '#fab700', 0, '2025-05-26 15:11:45', 1),
(55, 2061, 'Black', '#000000', 1, '2025-05-26 19:39:22', 6),
(56, 2061, 'White', '#ffffff', 0, '2025-05-26 19:39:22', 7),
(57, 2061, 'Red', '#ff0000', 0, '2025-05-26 20:58:13', 2),
(59, 2062, 'Black', '#000000', 1, '2025-05-27 20:15:20', 4),
(60, 2062, 'Red', '#ff0000', 0, '2025-05-27 20:15:20', 6),
(61, 2062, 'White', '#ffffff', 0, '2025-05-27 20:15:20', 5),
(62, 2062, 'Orange', '#ffc905', 0, '2025-05-27 20:18:11', 6);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color_id` int(11) DEFAULT NULL,
  `image_path` varchar(500) NOT NULL,
  `image_order` int(11) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `color_id`, `image_path`, `image_order`, `is_primary`, `created_at`) VALUES
(1, 2041, 1, '1748215989_0_RedHead.png', 0, 1, '2025-05-25 23:33:09'),
(2, 2042, 2, '1748216115_0_BlackHead.png', 0, 1, '2025-05-25 23:35:15'),
(3, 2042, 3, '1748216115_1_WhiteHead.png', 1, 0, '2025-05-25 23:35:15'),
(4, 2042, 4, '1748216115_2_RedHead.png', 2, 0, '2025-05-25 23:35:15'),
(5, 2043, 5, '1748217343_0_BlackHead.png', 0, 1, '2025-05-25 23:55:43'),
(6, 2044, 8, '1748245419_0_BlackHead.png', 0, 1, '2025-05-26 07:43:39'),
(7, 2046, 11, '1748246324_0_BlackHead.png', 0, 1, '2025-05-26 07:58:44'),
(8, 2047, 14, '1748247265_0_BlackHead.png', 0, 1, '2025-05-26 08:14:25'),
(9, 2048, 17, '1748247591_0_BlackHead.png', 0, 1, '2025-05-26 08:19:51'),
(10, 2049, 20, '1748247961_0_BlackHead.png', 0, 1, '2025-05-26 08:26:01'),
(11, 2050, 23, '1748248536_0_BlackHead.png', 0, 1, '2025-05-26 08:35:36'),
(12, 2050, 24, '1748248536_0_BlackHead.png', 0, 0, '2025-05-26 08:52:13'),
(13, 2050, 25, '1748248536_0_BlackHead.png', 0, 0, '2025-05-26 08:52:13'),
(14, 2051, 26, '1748249994_0_BlackHead.png', 0, 1, '2025-05-26 08:59:54'),
(15, 2052, NULL, '1748250846_0_BlackHead.png', 0, 1, '2025-05-26 09:14:06'),
(16, 2053, 32, '1748251411_0_BlackHead.png', 0, 1, '2025-05-26 09:23:31'),
(18, 2055, NULL, '1748252610_0_BlackHead.png', 0, 1, '2025-05-26 09:43:30'),
(19, 2050, 23, 'your_image_2.jpg', 1, 0, '2025-05-26 09:44:25'),
(20, 2050, 24, 'your_image_3.jpg', 2, 0, '2025-05-26 09:44:25'),
(21, 2050, 24, '1748252710_fix_0_RedHead.png', 1, 0, '2025-05-26 09:45:10'),
(22, 2050, 25, 'your_second_image.jpg', 1, 0, '2025-05-26 09:46:11'),
(23, 2050, 24, 'your_third_image.jpg', 2, 0, '2025-05-26 09:46:11'),
(24, 2056, NULL, '1748252840_0_RedHead.png', 0, 1, '2025-05-26 09:47:20'),
(25, 2057, 44, '1748253805_0_BlackHead.png', 0, 1, '2025-05-26 10:03:25'),
(26, 2057, 46, '1748253805_1_WhiteHead.png', 1, 0, '2025-05-26 10:03:25'),
(27, 2057, 45, '1748253805_2_RedHead.png', 2, 0, '2025-05-26 10:03:25'),
(28, 2058, 49, '1748255325_0_BlackHead.png', 0, 1, '2025-05-26 10:28:45'),
(29, 2059, 50, '1748256055_0_BlackHead.png', 0, 1, '2025-05-26 10:40:55'),
(30, 2059, 51, '1748256055_1_WhiteHead.png', 1, 0, '2025-05-26 10:40:55'),
(31, 2059, 52, '1748256055_2_RedHead.png', 2, 0, '2025-05-26 10:40:55'),
(39, 2057, 54, '1748272356_0_WiredHead.png', 3, 0, '2025-05-26 15:12:36'),
(40, 2060, NULL, 'product_0_1748278065_BlackHead.png', 0, 1, '2025-05-26 16:47:45'),
(41, 2060, NULL, 'product_1_1748278065_WhiteHead.png', 1, 0, '2025-05-26 16:47:45'),
(42, 2060, NULL, 'product_2_1748278065_RedHead.png', 2, 0, '2025-05-26 16:47:45'),
(43, 2060, NULL, 'additional_0_1748278065_RedHead.png', 3, 0, '2025-05-26 16:47:45'),
(44, 2061, 55, '1748288362_0_BlackHead.png', 0, 1, '2025-05-26 19:39:22'),
(45, 2061, 56, '1748288393_0_WhiteHead.png', 1, 0, '2025-05-26 19:39:53'),
(46, 2061, 57, '1748293124_0_RedHead.png', 2, 0, '2025-05-26 20:58:44'),
(48, 2062, 59, '1748376920_0_BlackHead.png', 0, 1, '2025-05-27 20:15:20'),
(51, 2062, 61, '1748377033_0_WhiteHead.png', 1, 0, '2025-05-27 20:17:13'),
(52, 2062, 60, '1748377053_0_RedHead.png', 2, 0, '2025-05-27 20:17:33'),
(53, 2062, 62, '1748377120_0_WiredHead.png', 3, 0, '2025-05-27 20:18:40');

-- --------------------------------------------------------

--
-- Table structure for table `signup_page`
--

CREATE TABLE `signup_page` (
  `Name` varchar(100) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Phone Number` varchar(10) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Usertype` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup_page`
--

INSERT INTO `signup_page` (`Name`, `Username`, `Phone Number`, `Password`, `Usertype`) VALUES
('fatima zaghlol', 'fatima.a.zaghlol@gmail.com', '0594422751', '$2y$10$9N0l3fEG2sch.EgVVgEvleICyp89RqGCNGMT4RC5SNCWkCiI/YOnG', 'admin'),
('mohammed', 'mbk91011@gmail.com', '0599999999', '$2y$10$0rBK.LFQxnQ5548xl8iyI.b7qCmFb7.T8N4bRbeWFwVwqm03Ebmga', 'admin'),
('ffff', 'tasu30349@gmail.com', '55555', '$2y$10$3EVWRvg0fPZewsEk4rM3KuXU0u7RX46buwW1ZN8/xITc3GKC3.uey', 'normal');

-- --------------------------------------------------------

--
-- Table structure for table `subcat`
--

CREATE TABLE `subcat` (
  `SubCatID` int(100) NOT NULL,
  `CatID` int(100) NOT NULL,
  `SubcatName` varchar(500) NOT NULL,
  `SubCatPic` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcat`
--

INSERT INTO `subcat` (`SubCatID`, `CatID`, `SubcatName`, `SubCatPic`) VALUES
(15, 18, 'Gaming PC', '1658055820pngwing.com(8).png'),
(16, 18, 'Apple-iMac', '1658328631apple-imac-24-inch-2021-mediumlogo.png'),
(17, 19, 'Gaming Laptops', '1658329923pngkey.com-laptop-frame-png-9433041.png'),
(18, 18, 'Monitors', '1658328738pngkey.com-computer-screen-png-82207.png'),
(19, 19, 'Microsoft Surface', '1658330615kindpng_3368709.png'),
(20, 19, 'MacBook', '1658661500pngfind.com-macbook-pro-png-383908.png'),
(21, 20, 'Gaming Keyboards', '17471158186k.jpg'),
(22, 20, 'Wired Keyboards', '17471160861k.jpg'),
(23, 20, 'Bluetooth Keyboards', '17471161172k.jpeg'),
(24, 20, 'Membrane Keyboards', '17471161553k.jpg'),
(25, 20, 'Ergonomic Keyboards', '17471161784k.webp'),
(28, 24, 'SSDs', '1748074909_ssdCAT.png'),
(29, 25, 'Video Cables', '1748076790HDMI.png'),
(30, 25, 'USB Cables & Hubs', '1748076877_USB_Hub.png'),
(31, 25, 'Power Cables & Chargers', '1748076994_Power_Cable.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_recently_viewed`
--

CREATE TABLE `user_recently_viewed` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(500) NOT NULL,
  `product_image` varchar(500) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_discount` int(11) DEFAULT 0,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_recently_viewed`
--

INSERT INTO `user_recently_viewed` (`id`, `user_email`, `product_id`, `product_name`, `product_image`, `product_price`, `product_discount`, `viewed_at`) VALUES
(6, 'tasu30349@gmail.com', 2030, 'MICROSOFT Surface Laptop 3 Ryzen 5 Quad Core 3580U', '1658330833microsoft-original-imafr9qyecvfeyta.webp', 124999.00, 20, '2025-05-28 00:27:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`CartID`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `managecat`
--
ALTER TABLE `managecat`
  ADD PRIMARY KEY (`catid`);

--
-- Indexes for table `manageproduct`
--
ALTER TABLE `manageproduct`
  ADD PRIMARY KEY (`ProductID`);

--
-- Indexes for table `orderproducts`
--
ALTER TABLE `orderproducts`
  ADD PRIMARY KEY (`SrNo`);

--
-- Indexes for table `ordertable`
--
ALTER TABLE `ordertable`
  ADD PRIMARY KEY (`OrderID`);

--
-- Indexes for table `product_colors`
--
ALTER TABLE `product_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_color_id` (`color_id`);

--
-- Indexes for table `signup_page`
--
ALTER TABLE `signup_page`
  ADD PRIMARY KEY (`Username`),
  ADD UNIQUE KEY `Phone Number` (`Phone Number`);

--
-- Indexes for table `subcat`
--
ALTER TABLE `subcat`
  ADD PRIMARY KEY (`SubCatID`);

--
-- Indexes for table `user_recently_viewed`
--
ALTER TABLE `user_recently_viewed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_email`,`product_id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_viewed_at` (`viewed_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `CartID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedback_messages`
--
ALTER TABLE `feedback_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `managecat`
--
ALTER TABLE `managecat`
  MODIFY `catid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `manageproduct`
--
ALTER TABLE `manageproduct`
  MODIFY `ProductID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2063;

--
-- AUTO_INCREMENT for table `orderproducts`
--
ALTER TABLE `orderproducts`
  MODIFY `SrNo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `ordertable`
--
ALTER TABLE `ordertable`
  MODIFY `OrderID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2019;

--
-- AUTO_INCREMENT for table `product_colors`
--
ALTER TABLE `product_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `subcat`
--
ALTER TABLE `subcat`
  MODIFY `SubCatID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `user_recently_viewed`
--
ALTER TABLE `user_recently_viewed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`color_id`) REFERENCES `product_colors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
