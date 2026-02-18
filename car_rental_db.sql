-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql102.infinityfree.com
-- Czas generowania: 29 Sty 2026, 16:42
-- Wersja serwera: 11.4.9-MariaDB
-- Wersja PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `if0_40656079_car_rental_db`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `image_url` text NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('available','rented','maintenance') DEFAULT 'available',
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ;

--
-- Zrzut danych tabeli `cars`
--

INSERT INTO `cars` (`id`, `make`, `model`, `year`, `category`, `price_per_day`, `image_url`, `description`, `status`, `features`, `transmission`, `fuel_type`, `seats`, `acceleration`, `horsepower`, `license_plate`) VALUES
(1, 'BMW', 'M5 Competition', 2024, 'Luxury', '189.00', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'Experience the ultimate driving machine. Includes premium leather interior, advanced driver assistance, and track-ready performance.', 'available', '[\"Autopilot\", \"Leather Seats\", \"Premium Sound\", \"Heated Seats\"]', 'Automatic', 'Petrol', 4, '3.2s', '600 HP', 'BMW-M5-001'),
(2, 'Porsche', '911 Carrera', 2023, 'Convertible', '299.00', 'https://images.unsplash.com/photo-1580274455191-1c62238fa333?auto=format&fit=crop&q=80&w=1000', 'Iconic design meets powerful performance. Open-top driving freedom with a roar that turns heads.', 'available', '[\"Convertible Top\", \"Sport Mode\", \"Bose Sound\", \"Navigation\"]', 'Automatic', 'Petrol', 4, '3.8s', '450 HP', 'POR-911-002'),
(3, 'Land Rover', 'Range Rover Sport', 2024, 'SUV', '249.00', 'https://images.unsplash.com/photo-1609521263047-f8f205293f24?auto=format&fit=crop&q=80&w=1000', 'Luxury meets capability. Perfect for family adventures with unmatched off-road prowess and refined interior.', 'available', '[\"All-Wheel Drive\", \"Panoramic Sunroof\", \"7 Seats\", \"Wireless Charging\"]', 'Automatic', 'Diesel', 7, '5.3s', '400 HP', 'RNG-SPT-003'),
(4, 'Tesla', 'Model S Plaid', 2024, 'Electric', '199.00', 'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&q=80&w=1000', 'The future of driving. Mind-bending acceleration with zero emissions and cutting-edge autopilot technology.', 'available', '[\"Full Self-Driving\", \"Electric\", \"Glass Roof\", \"Gaming Computer\"]', 'Automatic', 'Electric', 5, '1.99s', '1020 HP', 'TSL-MDS-004'),
(5, 'Ford', 'Mustang GT', 2023, 'Sports', '129.00', 'https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&q=80&w=1000', 'Pure American muscle. Aggressive styling and raw power for those who appreciate the classics re-imagined.', 'available', '[\"V8 Engine\", \"Track Mode\", \"Recaro Seats\", \"Launch Control\"]', 'Manual', 'Petrol', 4, '3.2s', '600 HP', 'FRD-MST-005'),
(6, 'Mercedes-Benz', 'S-Class', 2024, 'Sedan', '229.00', 'https://images.unsplash.com/photo-1616788494707-ec28f08d05a1?auto=format&fit=crop&q=80&w=1000', 'The standard of luxury sedans. Executive comfort with massage seats, night vision, and air suspension.', 'available', '[\"Massage Seats\", \"Night Vision\", \"Rear Entertainment\", \"Air Suspension\"]', 'Automatic', 'Petrol', 5, '4.4s', '496 HP', 'MBZ-SCL-006');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `car_brands`
--

CREATE TABLE `car_brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo_url` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `car_brands`
--

INSERT INTO `car_brands` (`id`, `name`, `logo_url`, `created_at`) VALUES
(1, 'BMW', NULL, '2025-12-14 16:59:52'),
(2, 'Porsche', NULL, '2025-12-14 16:59:52'),
(3, 'Land Rover', NULL, '2025-12-14 16:59:52'),
(4, 'Tesla', NULL, '2025-12-14 16:59:52'),
(5, 'Ford', NULL, '2025-12-14 16:59:52'),
(6, 'Mercedes-Benz', NULL, '2025-12-14 16:59:52'),
(7, 'Audi', NULL, '2025-12-14 16:59:52'),
(8, 'Lamborghini', NULL, '2025-12-14 16:59:52'),
(9, 'Ferrari', NULL, '2025-12-14 16:59:52'),
(10, 'Bentley', NULL, '2025-12-14 16:59:52');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'JOSEPH KAYIJUKA', 'joekayijuka@gmail.com', '+48698967882', 'Support', 'hiiii', 'new', '2025-12-13 13:35:06'),
(2, 'Feedback User', 'joekayijuka@gmail.com', NULL, 'Website Feedback', 'hi\r\n', 'new', '2025-12-13 14:36:24'),
(3, 'Feedback User', 'joekayijuka@gmail.com', NULL, 'Website Feedback', 'oooooop', 'new', '2025-12-13 14:56:24'),
(4, 'Feedback User', 'joekayijuka@gmail.com', NULL, 'Website Feedback', 'nice uvs', 'new', '2025-12-13 16:21:33'),
(5, 'Frederic Semungambi', 'fredericsemungambi@gmail.com', '+48889875370', 'Rental Question', 'i the car ready', 'new', '2025-12-14 17:23:56'),
(6, 'Lycée de Kigali', 'info@ldk.rw', '', 'Support', 'I need to fix my SUV', 'new', '2025-12-16 07:49:16');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `rental_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `card_last_four` varchar(4) DEFAULT NULL,
  `card_type` varchar(20) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'pending',
  `payment_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `billing_name` varchar(100) DEFAULT NULL,
  `billing_email` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `payments`
--

INSERT INTO `payments` (`id`, `rental_id`, `user_id`, `amount`, `payment_method`, `card_last_four`, `card_type`, `transaction_id`, `payment_status`, `payment_date`, `created_at`, `billing_name`, `billing_email`) VALUES
(1, 19, 1, '1935.00', 'credit_card', '4242', NULL, 'TXN17658220175231', 'paid', '2025-12-15 10:06:57', '2025-12-15 18:06:57', NULL, NULL),
(2, 20, 1, '398.00', 'credit_card', '5029', NULL, 'TXN17658265467635', 'paid', '2025-12-15 11:22:26', '2025-12-15 19:22:26', NULL, NULL),
(3, 21, 1, '258.00', 'credit_card', '5029', NULL, 'TXN17658273235416', 'paid', '2025-12-15 11:35:23', '2025-12-15 19:35:23', NULL, NULL),
(4, 22, 1, '598.00', 'credit_card', '8888', NULL, 'TXN17658368949798', 'paid', '2025-12-15 14:14:54', '2025-12-15 22:14:54', NULL, NULL),
(5, 23, 1, '1374.00', 'credit_card', '9999', NULL, 'TXN17685248807978', 'paid', '2026-01-15 16:54:40', '2026-01-16 00:54:40', NULL, NULL),
(6, 24, 7, '2519.00', 'credit_card', '4242', NULL, 'TXN17690026137849', 'paid', '2026-01-21 05:36:53', '2026-01-21 13:36:53', NULL, NULL),
(7, 25, 8, '2290.00', 'credit_card', '2313', NULL, 'TXN17690027922892', 'paid', '2026-01-21 05:39:52', '2026-01-21 13:39:52', NULL, NULL),
(8, 26, 9, '2061.00', 'credit_card', '1111', NULL, 'TXN17690037331919', 'paid', '2026-01-21 05:55:33', '2026-01-21 13:55:33', NULL, NULL),
(9, 27, 3, '458.00', 'credit_card', '0004', NULL, 'TXN17691080494087', 'paid', '2026-01-22 10:54:09', '2026-01-22 18:54:09', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `admin_notes` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `rentals`
--

INSERT INTO `rentals` (`id`, `user_id`, `car_id`, `start_date`, `end_date`, `total_price`, `status`, `created_at`, `admin_notes`, `updated_at`, `approved_by`, `approved_at`, `payment_method`, `payment_status`, `transaction_id`, `payment_date`, `amount_paid`) VALUES
(16, 1, 5, '2025-12-17', '2025-12-25', '1161.00', '', '2025-12-15 17:51:33', NULL, '2025-12-15 17:54:04', NULL, NULL, 'credit_card', 'paid', 'TXN17658210937007', NULL, '1161.00'),
(2, 3, 5, '2025-12-18', '2025-12-14', '645.00', '', '2025-12-13 13:21:29', NULL, '2025-12-13 15:21:44', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(3, 1, 1, '2025-12-16', '2025-12-31', '3024.00', '', '2025-12-13 15:03:52', NULL, '2025-12-13 15:21:44', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(4, 1, 5, '2025-12-24', '2025-12-31', '1032.00', '', '2025-12-13 15:17:47', NULL, '2025-12-13 15:21:44', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(5, 1, 2, '2025-12-16', '2025-12-31', '4784.00', '', '2025-12-13 15:54:46', NULL, '2025-12-13 15:54:46', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(6, 3, 4, '2026-06-13', '2026-06-09', '995.00', '', '2025-12-13 16:04:18', NULL, '2025-12-13 16:04:18', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(7, 1, 2, '2025-12-25', '2025-12-26', '598.00', '', '2025-12-13 16:19:59', NULL, '2025-12-13 16:19:59', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(8, 4, 5, '2025-12-26', '2025-12-31', '774.00', '', '2025-12-14 16:21:00', NULL, '2025-12-14 16:21:00', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(9, 1, 6, '2025-12-16', '2025-12-17', '458.00', '', '2025-12-15 08:26:57', NULL, '2025-12-15 08:26:57', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(10, 3, 6, '2025-12-18', '2025-12-19', '458.00', '', '2025-12-15 08:43:33', NULL, '2025-12-15 17:08:28', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(11, 1, 5, '2026-01-03', '2026-01-30', '3612.00', '', '2025-12-15 09:02:35', NULL, '2025-12-15 09:16:09', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(12, 1, 5, '2025-12-16', '2025-12-31', '2064.00', '', '2025-12-15 09:16:28', NULL, '2025-12-15 17:08:09', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(13, 1, 6, '2025-12-18', '2025-12-18', '229.00', '', '2025-12-15 16:04:48', NULL, '2025-12-15 17:08:02', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(14, 3, 4, '2025-12-17', '2025-12-31', '2985.00', '', '2025-12-15 17:21:22', NULL, '2025-12-15 17:26:57', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(15, 1, 5, '2025-12-25', '2025-12-26', '258.00', '', '2025-12-15 17:29:10', NULL, '2025-12-15 17:33:05', NULL, NULL, NULL, 'pending', NULL, NULL, NULL),
(17, 1, 5, '2025-12-17', '2025-12-19', '387.00', '', '2025-12-15 17:52:40', NULL, '2025-12-15 17:54:00', NULL, NULL, 'credit_card', 'paid', 'TXN17658211603231', NULL, '387.00'),
(18, 1, 4, '2025-12-17', '2025-12-19', '597.00', '', '2025-12-15 17:58:19', NULL, '2025-12-15 18:54:45', NULL, NULL, 'credit_card', 'paid', 'TXN17658214994188', NULL, '597.00'),
(19, 1, 5, '2025-12-17', '2025-12-31', '1935.00', '', '2025-12-15 18:06:57', NULL, '2025-12-15 18:54:41', NULL, NULL, 'credit_card', 'paid', 'TXN17658220175231', NULL, '1935.00'),
(20, 1, 4, '2025-12-27', '2025-12-28', '398.00', 'pending', '2025-12-15 19:22:26', NULL, '2025-12-15 19:22:26', NULL, NULL, 'credit_card', 'paid', 'TXN17658265467635', NULL, '398.00'),
(21, 1, 5, '2025-12-15', '2025-12-16', '258.00', 'pending', '2025-12-15 19:35:23', NULL, '2025-12-15 19:35:23', NULL, NULL, 'credit_card', 'paid', 'TXN17658273235416', NULL, '258.00'),
(22, 1, 2, '2025-12-31', '2026-01-01', '598.00', '', '2025-12-15 22:14:54', NULL, '2026-01-19 16:51:42', NULL, NULL, 'credit_card', 'paid', 'TXN17658368949798', NULL, '598.00'),
(23, 1, 6, '2026-01-22', '2026-01-27', '1374.00', 'pending', '2026-01-16 00:54:40', NULL, '2026-01-16 00:54:40', NULL, NULL, 'credit_card', 'paid', 'TXN17685248807978', NULL, '1374.00'),
(24, 7, 6, '2026-01-21', '2026-01-31', '2519.00', '', '2026-01-21 13:36:53', NULL, '2026-01-21 13:58:04', NULL, NULL, 'credit_card', 'paid', 'TXN17690026137849', NULL, '2519.00'),
(25, 8, 6, '2026-01-21', '2026-01-30', '2290.00', '', '2026-01-21 13:39:52', NULL, '2026-01-21 13:57:41', NULL, NULL, 'credit_card', 'paid', 'TXN17690027922892', NULL, '2290.00'),
(27, 3, 6, '2026-01-23', '2026-01-24', '458.00', '', '2026-01-22 18:54:09', NULL, '2026-01-22 18:55:21', NULL, NULL, 'credit_card', 'paid', 'TXN17691080494087', NULL, '458.00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `building_number` varchar(20) DEFAULT NULL,
  `street_name` varchar(255) DEFAULT NULL,
  `apartment_unit` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `role` enum('customer','admin') DEFAULT 'customer'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `created_at`, `phone`, `address`, `building_number`, `street_name`, `apartment_unit`, `city`, `state`, `zip_code`, `license_number`, `date_of_birth`, `is_admin`, `role`) VALUES
(1, 'Joseph Kayijuka', 'joekayijuka@gmail.com', '$2y$10$Cm5l8xT4hVwG40E8XQCA4.AesmsrFwiIcjh8q/r0rchRnlBDz5F5e', '2025-12-13 12:26:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'customer'),
(2, 'Admin User', 'admin@luxedrive.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-12-13 13:05:27', '+1 (305) 555-0000', 'miami', '123', 'Admin St', NULL, 'Miami', 'FL', NULL, NULL, NULL, 1, 'admin'),
(3, 'kendrick James', 'joseph.kayijuka@s2025.tu-chemnitz.de', '$2y$10$XNLXbdJGsz5Sf4OElH92P.5ua86CwX0jWh4YPANR6rcLWxLafXsXm', '2025-12-13 13:17:49', '+4915203578718', 'Chemnitz, Saxony', NULL, NULL, NULL, 'Chemnitz', 'POLAND', '09126', 'MZ77889', '2002-01-10', 0, 'customer'),
(4, 'Dagmawi Yosef', 'danielthongai@gmail.com', '$2y$10$PsRe2KLUubwTO3h11lvtcOx7Bj.L6I3wR064r8j/cD8DvPFjirEQC', '2025-12-14 16:20:17', '+48452296733', 'ul Wróbla', NULL, NULL, NULL, 'Wroc?aw', 'Pl', '53-407', 'jajsddlad', '2000-06-01', 0, 'customer'),
(5, 'Agnieszka Lasota', 'a.lasota@im.uz.zgora.pl', '$2y$10$AR4OXfMTrwCnAtpiCbDrHuADs2t/qunDKrDK364lCUnrt5b8Hpisq', '2026-01-18 21:45:50', '+48111111111', 'Szafrana', NULL, NULL, NULL, 'Zielona Góra', 'PL', '65-615', '11111111', '1111-11-11', 0, 'customer'),
(6, 'Mucyo Derrick', 'travelerjose1@gmail.com', '$2y$10$B7HuA.cU7Dyf2JFRpKkGTenXqefwqmVaA3JtkUW.TgleIwsKRoi1C', '2026-01-19 16:34:36', '+3014534578', 'Szafrana Zygmunta', '8', NULL, NULL, 'zielona góra', 'Pl', '65-516', 'MZ77889', '2008-01-10', 0, 'customer'),
(7, 'Skskdk', 'dzixitkontakt@gmail.com', '$2y$10$ArY2DI9sm3DCNmGPEokIs.opDIw0eI8xZCt.2I9N05/I3LjemHSQq', '2026-01-21 13:35:49', '4858646595', 'Driver Street, al', NULL, NULL, NULL, 'wrszw', 'maz', '77389', 'd3828', '1986-02-22', 0, 'customer'),
(8, 'janpawel@gmail.com', 'janpawel@gmail.com', '$2y$10$jNnfOF7yxsP/ucSQ0faig.jEb2q.UtocwBPj7rrSm8wyLt0/5HndO', '2026-01-21 13:39:27', '+32 324 212 213', '123 Mn, 1', NULL, NULL, NULL, 'grzw', 'asd', '32421', 'd31231', '2008-01-01', 0, 'customer'),
(9, 'Marek\\\'); DROP TABLE Users; --', 'xadsasd@gml.com', '$2y$10$oZNgxbnPOD1KrCy9lc3Wqu6gFNhDqgEeIbuV9lajC2trV8jDvrHHC', '2026-01-21 13:52:54', 'xadsasd@gml.com', 'xadsasd@gml.com xadsasd@gml.com, xadsasd@gml.com', NULL, NULL, NULL, 'xadsasd@gml.com', 'xadsasd@gml.com', '4344232', 'xadsasd@gml.com', '2008-01-01', 0, 'customer');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indeksy dla tabeli `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeksy dla tabeli `car_brands`
--
ALTER TABLE `car_brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeksy dla tabeli `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rental_id` (`rental_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `car_brands`
--
ALTER TABLE `car_brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
