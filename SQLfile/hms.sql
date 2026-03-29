-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 29 2026 г., 15:55
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `hms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `updationDate` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `updationDate`) VALUES
(1, 'admin', 'Test@12345', '04-03-2024 11:42:05 AM');

-- --------------------------------------------------------

--
-- Структура таблицы `appointment`
--

CREATE TABLE `appointment` (
  `id` int NOT NULL,
  `doctorSpecializationId` int DEFAULT NULL,
  `doctorId` int DEFAULT NULL,
  `userId` int DEFAULT NULL,
  `appointmentDate` date DEFAULT NULL,
  `appointmentTime` time DEFAULT NULL,
  `isCompleted` tinyint(1) DEFAULT NULL,
  `updationDate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `appointment`
--

INSERT INTO `appointment` (`id`, `doctorSpecializationId`, `doctorId`, `userId`, `appointmentDate`, `appointmentTime`, `isCompleted`, `updationDate`) VALUES
(1, 5, 1, 1, '2025-05-30', '09:15:00', 1, NULL),
(2, 7, 2, 2, '2025-05-31', '14:45:00', 1, NULL),
(12, 6, 7, 9, '2025-06-12', NULL, 0, NULL),
(18, 6, 7, 9, '2025-06-12', NULL, 0, NULL),
(23, 5, 6, 10, '2025-06-12', NULL, 0, NULL),
(24, 6, 7, 10, '2025-06-12', NULL, 0, NULL),
(29, 5, 7, 8, '2025-06-12', NULL, 0, NULL),
(30, 6, 8, 8, '2025-06-12', NULL, 0, NULL),
(31, 7, 9, 8, '2025-06-12', NULL, 0, NULL),
(32, 8, 3, 8, '2025-06-12', NULL, 0, NULL),
(33, 9, 4, 8, '2025-06-12', NULL, 0, NULL),
(47, 5, 7, 6, '2025-06-04', NULL, 0, NULL),
(48, 6, 8, 6, '2025-06-04', NULL, 0, NULL),
(49, 7, 9, 6, '2025-06-04', NULL, 0, NULL),
(50, 8, 3, 6, '2025-06-04', NULL, 0, NULL),
(51, 9, 4, 6, '2025-06-04', NULL, 0, NULL),
(57, 6, 8, 3, '2025-06-04', NULL, 1, NULL),
(58, 7, 9, 3, '2025-06-04', NULL, 1, NULL),
(59, 8, 3, 3, '2025-06-04', NULL, 1, NULL),
(60, 9, 4, 3, '2025-06-04', NULL, 1, NULL),
(66, 6, 8, 2, '2025-06-05', NULL, 1, NULL),
(67, 7, 9, 2, '2025-06-05', NULL, 1, NULL),
(68, 8, 3, 2, '2025-06-05', NULL, 1, NULL),
(69, 9, 4, 2, '2025-06-05', NULL, 1, NULL),
(75, 6, 8, 10, '2025-06-05', NULL, 0, NULL),
(76, 7, 9, 10, '2025-06-05', NULL, 0, NULL),
(77, 8, 3, 10, '2025-06-05', NULL, 0, NULL),
(78, 9, 4, 10, '2025-06-05', NULL, 0, NULL),
(84, 6, 8, 9, '2025-06-06', NULL, 0, NULL),
(85, 7, 9, 9, '2025-06-06', NULL, 0, NULL),
(86, 8, 3, 9, '2025-06-06', NULL, 0, NULL),
(87, 9, 4, 9, '2025-06-06', NULL, 0, NULL),
(92, 5, 7, 6, '2025-06-06', NULL, 0, NULL),
(93, 6, 8, 6, '2025-06-06', NULL, 0, NULL),
(94, 7, 9, 6, '2025-06-06', NULL, 0, NULL),
(95, 8, 3, 6, '2025-06-06', NULL, 0, NULL),
(96, 9, 4, 6, '2025-06-06', NULL, 0, NULL),
(106, 1, 1, 3, '2025-06-06', NULL, 0, NULL),
(107, 2, 5, 3, '2025-06-06', NULL, 0, NULL),
(108, 3, 2, 3, '2025-06-06', NULL, 0, NULL),
(109, 4, 6, 3, '2025-06-06', NULL, 0, NULL),
(110, 5, 7, 3, '2025-06-06', NULL, 0, NULL),
(111, 6, 8, 3, '2025-06-06', NULL, 0, NULL),
(112, 7, 9, 3, '2025-06-06', NULL, 0, NULL),
(113, 8, 3, 3, '2025-06-06', NULL, 0, NULL),
(114, 9, 4, 3, '2025-06-06', NULL, 0, NULL),
(115, 1, 1, 4, '2025-06-06', NULL, 0, NULL),
(116, 2, 5, 4, '2025-06-06', NULL, 0, NULL),
(117, 3, 2, 4, '2025-06-06', NULL, 0, NULL),
(118, 4, 6, 4, '2025-06-06', NULL, 0, NULL),
(119, 5, 7, 4, '2025-06-06', NULL, 0, NULL),
(120, 6, 8, 4, '2025-06-06', NULL, 0, NULL),
(121, 7, 9, 4, '2025-06-06', NULL, 0, NULL),
(122, 8, 3, 4, '2025-06-06', NULL, 0, NULL),
(123, 9, 4, 4, '2025-06-06', NULL, 0, NULL),
(124, 1, 1, 9, '2025-06-06', NULL, 0, NULL),
(125, 2, 5, 9, '2025-06-06', NULL, 0, NULL),
(126, 3, 2, 9, '2025-06-06', NULL, 0, NULL),
(127, 4, 6, 9, '2025-06-06', NULL, 0, NULL),
(128, 5, 7, 9, '2025-06-06', NULL, 0, NULL),
(129, 6, 8, 9, '2025-06-06', NULL, 0, NULL),
(130, 7, 9, 9, '2025-06-06', NULL, 1, NULL),
(131, 8, 3, 9, '2025-06-06', NULL, 0, NULL),
(132, 9, 4, 9, '2025-06-06', NULL, 0, NULL),
(133, 1, 1, 7, '2025-06-06', NULL, 0, NULL),
(134, 3, 2, 7, '2025-06-06', NULL, 0, NULL),
(135, 4, 6, 7, '2025-06-06', NULL, 0, NULL),
(136, 5, 7, 7, '2025-06-06', NULL, 0, NULL),
(137, 6, 8, 7, '2025-06-06', NULL, 0, NULL),
(138, 7, 9, 7, '2025-06-06', NULL, 0, NULL),
(139, 8, 3, 7, '2025-06-06', NULL, 0, NULL),
(140, 9, 4, 7, '2025-06-06', NULL, 0, NULL),
(141, 1, 1, 8, '2025-06-06', NULL, 0, NULL),
(142, 2, 5, 8, '2025-06-06', NULL, 0, NULL),
(143, 3, 2, 8, '2025-06-06', NULL, 0, NULL),
(144, 4, 6, 8, '2025-06-06', NULL, 0, NULL),
(145, 5, 7, 8, '2025-06-06', NULL, 0, NULL),
(146, 6, 8, 8, '2025-06-06', NULL, 0, NULL),
(147, 7, 9, 8, '2025-06-06', NULL, 0, NULL),
(148, 8, 3, 8, '2025-06-06', NULL, 0, NULL),
(149, 1, 1, 11, '2025-06-09', NULL, 0, NULL),
(150, 2, 5, 11, '2025-06-09', NULL, 1, NULL),
(151, 3, 2, 11, '2025-06-09', NULL, 1, NULL),
(152, 4, 6, 11, '2025-06-09', NULL, 1, NULL),
(153, 5, 7, 11, '2025-06-09', NULL, 1, NULL),
(154, 6, 8, 11, '2025-06-09', NULL, 1, NULL),
(155, 7, 9, 11, '2025-06-09', NULL, 1, NULL),
(156, 8, 3, 11, '2025-06-09', NULL, 1, NULL),
(157, 1, 1, 9, '2025-06-19', NULL, 0, NULL),
(158, 3, 2, 9, '2025-06-19', NULL, 0, NULL),
(159, 4, 6, 9, '2025-06-19', NULL, 0, NULL),
(160, 5, 7, 9, '2025-06-19', NULL, 0, NULL),
(161, 6, 8, 9, '2025-06-19', NULL, 0, NULL),
(162, 7, 9, 9, '2025-06-19', NULL, 0, NULL),
(163, 8, 3, 9, '2025-06-19', NULL, 0, NULL),
(164, 9, 4, 9, '2025-06-19', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `dispensarization`
--

CREATE TABLE `dispensarization` (
  `id` int NOT NULL,
  `patientId` int NOT NULL,
  `dispDate` date NOT NULL,
  `status` enum('in_progress','completed') DEFAULT 'in_progress',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `dispensarization`
--

INSERT INTO `dispensarization` (`id`, `patientId`, `dispDate`, `status`, `created_at`) VALUES
(32, 3, '2025-06-06', 'in_progress', '2025-06-06 15:52:22'),
(33, 4, '2025-06-06', 'in_progress', '2025-06-06 15:54:19'),
(34, 9, '2025-06-06', 'in_progress', '2025-06-06 15:54:29'),
(35, 7, '2025-06-06', 'in_progress', '2025-06-06 15:56:51'),
(37, 11, '2025-06-09', 'in_progress', '2025-06-09 14:40:03'),
(38, 9, '2025-06-19', 'in_progress', '2025-06-19 00:20:19');

-- --------------------------------------------------------

--
-- Структура таблицы `dispensary_session`
--

CREATE TABLE `dispensary_session` (
  `ID` int NOT NULL,
  `PatientID` int NOT NULL,
  `Date` date NOT NULL,
  `CreatedBy` int DEFAULT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `doctors`
--

CREATE TABLE `doctors` (
  `id` int NOT NULL,
  `doctorSpecializationId` int DEFAULT NULL,
  `doctorName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `address` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `contactno` bigint DEFAULT NULL,
  `docEmail` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updationDate` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `doctors`
--

INSERT INTO `doctors` (`id`, `doctorSpecializationId`, `doctorName`, `address`, `contactno`, `docEmail`, `password`, `creationDate`, `updationDate`) VALUES
(1, 1, 'Лобанов Семён ', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 142536250, 'bashkaizkartoshki@test.com', 'f925916e2754e5e03f75dd58a5733251', '2024-04-10 18:16:52', '2024-05-14 09:26:17'),
(2, 3, 'Черноус Варвара', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 1231231230, 'nebuduyasmotret@test.com', 'ceb6c970658f31504a901b89dcd3e461', '2024-04-11 01:06:41', '2024-05-14 09:26:28'),
(3, 8, 'Мальцев Алексей', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 875654326476, 'yaiztorshka@test.ru', '3122d44a050e086ffe4acfe92aac3166', '2025-06-04 10:04:31', NULL),
(4, 9, 'Купитман Иван Натанович', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 74561235, 'kupitmankupitdog@t.com', 'af928558e138146c8e6e3469fedb9681', '2024-05-16 09:12:23', NULL),
(5, 2, 'Ричардс Фил', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 95214563210, 'americanboy@gmail.com', 'a26532644dd9cdc46c24d6a351ab98fd', '2024-05-16 09:13:11', NULL),
(6, 4, 'Быков Андрей Евгеньевич', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 8563214751, 'goyda@gmail.com', '314a0b9d5d0953d55076da4df9caf9e3', '2024-05-16 09:14:11', NULL),
(7, 5, 'Романенко Глеб', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 745621330, 'minuspervyy@tt.com', 'bfa95429ab529b7b06d70f99df8cfebe', '2024-05-16 09:15:18', NULL),
(8, 6, 'Кисегач Анастасия Константиновна', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 89005553535, 'kissegach@test.ru', '47ec2dd791e31e2ef2076caf64ed9b3d', '2025-06-04 09:53:06', NULL),
(9, 7, 'Левин Борис Аркадьевич', 'Республиканская клиническая больница ул. Оренбургский Тракт, 138, корп. А', 87327463243, 'yanelenin@test.ru', 'b6d52bcef12759ebfce537bf326d72ef', '2025-06-04 09:53:06', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `doctorslog`
--

CREATE TABLE `doctorslog` (
  `id` int NOT NULL,
  `uid` int DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `userip` binary(16) DEFAULT NULL,
  `loginTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `logout` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `doctorslog`
--

INSERT INTO `doctorslog` (`id`, `uid`, `username`, `userip`, `loginTime`, `logout`, `status`) VALUES
(26, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-04 10:49:57', '04-06-2025 04:21:20 PM', 1),
(27, 2, 'nebuduyasmotret@test.com', 0x3132372e302e302e3100000000000000, '2025-06-04 10:51:27', '04-06-2025 04:21:30 PM', 1),
(28, 1, 'bashkaizkartoshki@test.com', 0x3132372e302e302e3100000000000000, '2025-06-04 10:51:58', '04-06-2025 04:22:22 PM', 1),
(29, 3, 'yaiztorshka@test.ru', 0x3132372e302e302e3100000000000000, '2025-06-04 10:52:50', NULL, 1),
(30, 1, 'bashkaizkartoshki@test.com', 0x3132372e302e302e3100000000000000, '2025-06-04 11:04:36', '04-06-2025 04:35:22 PM', 1),
(31, 2, 'nebuduyasmotret@test.com', 0x3132372e302e302e3100000000000000, '2025-06-04 11:05:30', '04-06-2025 04:35:46 PM', 1),
(32, 3, 'yaiztorshka@test.ru', 0x3132372e302e302e3100000000000000, '2025-06-04 11:06:05', '04-06-2025 04:36:33 PM', 1),
(33, 4, 'kupitmankupitdog@t.com', 0x3132372e302e302e3100000000000000, '2025-06-04 11:07:02', '04-06-2025 04:37:27 PM', 1),
(34, 5, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-04 11:07:38', '04-06-2025 04:37:56 PM', 1),
(35, 6, 'goyda@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-04 11:09:15', '04-06-2025 04:39:31 PM', 1),
(36, 7, 'minuspervyy@tt.com', 0x3132372e302e302e3100000000000000, '2025-06-04 11:09:51', '04-06-2025 04:40:05 PM', 1),
(37, 8, 'kissegach@test.ru', 0x3132372e302e302e3100000000000000, '2025-06-04 11:10:30', '04-06-2025 04:40:43 PM', 1),
(38, NULL, 'yalenin@test.ru', 0x3132372e302e302e3100000000000000, '2025-06-04 11:10:54', NULL, 0),
(39, 9, 'yanelenin@test.ru', 0x3132372e302e302e3100000000000000, '2025-06-04 11:11:52', NULL, 1),
(40, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-04 11:12:40', NULL, 1),
(41, 2, 'nebuduyasmotret@test.com', 0x3132372e302e302e3100000000000000, '2025-06-04 20:11:04', NULL, 1),
(42, 2, 'nebuduyasmotret@test.com', 0x3132372e302e302e3100000000000000, '2025-06-05 17:00:49', NULL, 1),
(43, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-05 17:01:01', NULL, 1),
(44, 5, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-05 18:44:14', NULL, 1),
(45, 6, 'goyda@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-05 18:46:12', NULL, 1),
(46, 9, 'yanelenin@test.ru', 0x3132372e302e302e3100000000000000, '2025-06-05 18:46:40', NULL, 1),
(47, 3, 'yaiztorshka@test.ru', 0x3132372e302e302e3100000000000000, '2025-06-05 18:47:07', NULL, 1),
(48, 2, 'nebuduyasmotret@test.com', 0x3132372e302e302e3100000000000000, '2025-06-05 18:47:34', NULL, 1),
(49, 7, 'minuspervyy@tt.com', 0x3132372e302e302e3100000000000000, '2025-06-05 18:48:01', NULL, 1),
(50, 4, 'kupitmankupitdog@t.com', 0x3132372e302e302e3100000000000000, '2025-06-05 18:48:31', NULL, 1),
(51, 8, 'kissegach@test.ru', 0x3132372e302e302e3100000000000000, '2025-06-05 18:48:52', NULL, 1),
(52, 1, 'bashkaizkartoshki@test.com', 0x3132372e302e302e3100000000000000, '2025-06-05 18:49:22', NULL, 1),
(53, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-05 18:49:48', NULL, 1),
(54, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-05 18:53:49', NULL, 1),
(55, 1, 'bashkaizkartoshki@test.com', 0x3132372e302e302e3100000000000000, '2025-06-05 19:24:52', NULL, 1),
(56, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-05 19:25:06', NULL, 1),
(57, NULL, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-06 10:12:18', NULL, 0),
(58, NULL, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-06 10:14:51', NULL, 0),
(59, NULL, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-06 10:14:56', NULL, 0),
(60, 1, 'bashkaizkartoshki@test.com', 0x3132372e302e302e3100000000000000, '2025-06-06 12:01:25', NULL, 1),
(61, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-06 12:01:48', NULL, 1),
(62, 1, 'bashkaizkartoshki@test.com', 0x3132372e302e302e3100000000000000, '2025-06-06 12:11:35', '06-06-2025 05:44:55 PM', 1),
(63, 5, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-06 12:15:02', '06-06-2025 05:45:23 PM', 1),
(105, NULL, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-11 11:24:58', NULL, 0),
(106, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-11 12:37:24', '11-06-2025 06:28:08 PM', 1),
(107, 5, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-11 12:58:15', NULL, 1),
(108, NULL, 'admin', 0x3132372e302e302e3100000000000000, '2025-06-11 13:11:51', NULL, 0),
(109, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-11 13:11:57', NULL, 1),
(110, 1, 'bashkaizkartoshki@test.com', 0x3132372e302e302e3100000000000000, '2025-06-11 13:12:10', '11-06-2025 06:42:12 PM', 1),
(111, 5, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-11 15:57:44', NULL, 1),
(112, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-11 15:58:29', '11-06-2025 09:55:14 PM', 1),
(113, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-11 16:55:27', NULL, 1),
(114, NULL, 'americanboy@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-11 18:46:57', NULL, 0),
(115, 1, 'm.severeva@clinic.ru', 0x3132372e302e302e3100000000000000, '2025-06-18 21:18:51', NULL, 1),
(116, 6, 'goyda@gmail.com', 0x3132372e302e302e3100000000000000, '2025-06-18 21:20:29', '19-06-2025 02:51:32 AM', 1),
(117, 1, 'bashkaizkartoshki@test.com', 0x3132372e302e302e3100000000000000, '2025-06-18 21:21:41', NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `doctorspecilization`
--

CREATE TABLE `doctorspecilization` (
  `doctorSpecializationId` int NOT NULL,
  `doctorSpecialization` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `doctorspecilization`
--

INSERT INTO `doctorspecilization` (`doctorSpecializationId`, `doctorSpecialization`, `creationDate`, `updationDate`) VALUES
(1, 'Терапевт', '2024-04-09 18:09:46', '2025-05-29 15:48:51'),
(2, 'Акушер-гинеколог', '2024-04-09 18:09:46', '2025-05-29 15:48:55'),
(3, 'Хирург', '2024-04-09 18:09:46', '2025-05-29 15:48:58'),
(4, 'Офтальмолог', '2024-04-09 18:09:46', '2025-05-29 15:49:02'),
(5, 'Оториноларинголог', '2024-04-09 18:09:46', '2025-05-29 15:49:06'),
(6, 'Невролог', '2024-04-09 18:09:46', '2025-05-29 15:49:10'),
(7, 'Эндокринолог', '2024-04-09 18:09:46', '2025-05-29 15:49:13'),
(8, 'Кардиолог', '2025-05-13 09:52:09', '2025-05-29 15:49:18'),
(9, 'Уролог', '2025-05-29 15:48:31', '2025-05-29 15:49:22');

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `reception`
--

CREATE TABLE `reception` (
  `id` int NOT NULL,
  `fullName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `contactNo` bigint DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `reception`
--

INSERT INTO `reception` (`id`, `fullName`, `dob`, `address`, `contactNo`, `email`, `password`, `creationDate`, `updationDate`) VALUES
(1, 'Скрябина Любовь Михайловна ', '1985-03-12', 'г. Казань, ул. Пушкина, д. 45', 79035554433, 'm.severeva@clinic.ru', 'e58aaacd102d22053d8eb66c84a45e64', '2025-06-03 11:22:26', '2025-06-04 20:07:18'),
(2, 'Гараева Диана Ильнуровна', '2005-01-28', 'г. Казань, ул. Гагарина, д. 17', 79031237654, 'e.orlova@clinic.ru', '3f16f31dcd8ee71a4b07c70285337ff9', '2025-06-03 11:22:26', '2025-06-04 20:05:53');

-- --------------------------------------------------------

--
-- Структура таблицы `tblmedicalhistory`
--

CREATE TABLE `tblmedicalhistory` (
  `ID` int NOT NULL,
  `PatientID` int DEFAULT NULL,
  `DoctorID` int DEFAULT NULL,
  `DispensaryID` int DEFAULT NULL,
  `BloodPressure` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `BloodSugar` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `Weight` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Temperature` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `MedicalPres` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `FinalConclusion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `CreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `tblmedicalhistory`
--

INSERT INTO `tblmedicalhistory` (`ID`, `PatientID`, `DoctorID`, `DispensaryID`, `BloodPressure`, `BloodSugar`, `Weight`, `Temperature`, `MedicalPres`, `FinalConclusion`, `CreationDate`) VALUES
(1, 2, NULL, NULL, '80/120', '110', '85', '97', 'Dolo,\r\nLevocit 5mg', '0', '2024-05-16 09:07:16'),
(2, 9, 1, NULL, '120/80', '110', '85', '35,7', 'Заключение от терапевта', '0', '2025-06-06 12:12:15'),
(3, 9, 2, NULL, '130/70', '110', '75', '36,9', 'Заключение от хирурга', '0', '2025-06-06 12:15:43'),
(4, 9, 9, NULL, '120/80', '95', '90', '37,6', 'Заключение от эндокринолога', '', '2025-06-06 19:59:48'),
(5, 3, NULL, NULL, '120/80', '95', '80', '85', 'Ограничение физических и эмоциональных нагрузок.\r\n\r\nСон не менее 7–8 часов в сутки.', NULL, '2025-06-09 17:57:42'),
(6, 11, 5, NULL, '', '', '', '', 'Заключение акушера-гинеколога', '', '2025-06-09 18:05:56'),
(7, 11, 6, NULL, '', '', '', '', 'Заключение офтальмолога', '', '2025-06-09 18:06:22'),
(8, 11, 8, NULL, '', '', '', '', 'Заключение невролога', '', '2025-06-09 18:06:41'),
(9, 11, 7, NULL, '', '', '', '', 'Заключение оториноларинголога', '', '2025-06-09 18:07:27'),
(10, 11, 2, NULL, '', '', '', '', 'Заключение хирурга', '', '2025-06-09 18:07:48'),
(11, 11, 3, NULL, '', '', '', '', 'Заключение кардиолога', '', '2025-06-09 18:08:05'),
(12, 11, 9, NULL, '', '', '', '', 'Заключение эндокриниолога', '', '2025-06-09 18:08:22');

-- --------------------------------------------------------

--
-- Структура таблицы `tblpatient`
--

CREATE TABLE `tblpatient` (
  `ID` int NOT NULL,
  `Docid` int DEFAULT NULL,
  `PatientName` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `PatientContno` bigint DEFAULT NULL,
  `policy_number` varchar(20) DEFAULT NULL,
  `PatientEmail` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `passwords` varchar(255) DEFAULT NULL,
  `status` enum('active','blocked') DEFAULT 'active',
  `PatientGender` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `PatientAdd` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `PatientDOB` date DEFAULT NULL,
  `PatientMedhis` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL,
  `consent_given` tinyint(1) DEFAULT '0' COMMENT 'Согласие на обработку ПДн',
  `consent_date` datetime DEFAULT NULL COMMENT 'Дата согласия',
  `consent_ip` varchar(45) DEFAULT NULL COMMENT 'IP адрес при согласии'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `tblpatient`
--

INSERT INTO `tblpatient` (`ID`, `Docid`, `PatientName`, `PatientContno`, `policy_number`, `PatientEmail`, `login`, `passwords`, `status`, `PatientGender`, `PatientAdd`, `PatientDOB`, `PatientMedhis`, `CreationDate`, `UpdationDate`, `consent_given`, `consent_date`, `consent_ip`) VALUES
(1, 1, 'Мухаева Алина Сергеевна', 452463210, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$4AgWQ8xDoKm8G1AKeM3/juMCHL/b.zVkrt37zG3nrCitCcEcDCoVu', 'active', 'female', 'Приволжский район, ул. Гарифьянова, д. 8А', '2004-02-19', 'Эссенциальная гипертензия; Боль в пояснице', '2024-05-16 05:23:35', '2025-05-15 20:45:37', 0, NULL, NULL),
(2, 1, 'Князева Ирина Сергеевна', 4545454545, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$N7UutY5eD30GZ6tbCtZBM.osl0IE.z9SmNRK9l.uigPLSEVrCiDb.', 'active', 'female', 'Вахитовский район, ул. Пушкина, д. 32', '2005-07-06', 'Межрёберная невралгия; Боль в пояснице', '2024-05-16 09:01:26', '2025-05-15 20:45:46', 0, NULL, NULL),
(3, 1, 'Иванов Петр Сергеевич', 79151234567, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$tGkrQv9XyQdc0Te78god5eQ1xX.z1eiupucs.cD8gd8oSGs11MJI6', 'active', 'male', 'Советский район, ул. Хусаина Мавлютова, д. 35', '2000-06-03', 'Пневмония неуточненная; Сахарный диабет 2 типа без осложнений', '2025-06-03 09:41:58', NULL, 0, NULL, NULL),
(4, 2, 'Смирнова Анна Владимировна', 79261234567, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$R4oyGVZhksy56t5M1YsH/emVp3dmgg0QUXgnvwOQClDYl49cBCqZG', 'active', 'female', 'Московский район, ул. Восстания, д. 62', '1993-06-03', 'Ожирение; Сахарный диабет 2 типа', '2025-06-03 09:41:58', NULL, 0, NULL, NULL),
(5, 3, 'Козлов Дмитрий Игоревич', 79031234567, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$2F8KKwvG7RwdDYjy2h/wHuWR.ELeH.etnWvHe7iNvh6VJxPUVZd5a', 'active', 'male', 'Ново-Савиновский район, ул. Четаева, д. 10', '1980-06-03', 'Межрёберная невралгия; Сахарный диабет 2 типа', '2025-06-03 09:41:58', NULL, 0, NULL, NULL),
(6, 4, 'Петрова Елена Александровна', 79161234567, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$zShZ90vwHU9neNUViGC9d.4z.zmUO6v1sANia/t65aSfkG5l55IDO', 'active', 'female', 'Авиастроительный район, ул. Лукина, д. 5', '1997-06-03', 'Бронхиальная астма', '2025-06-03 09:41:58', NULL, 0, NULL, NULL),
(7, 5, 'Сидоров Артем Олегович', 79091234567, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$9oBMccULRSjLPhoEVSMKo.Nl9uDqFhUNySz07ACwbplwjE6Jy4xFq', 'active', 'male', 'Кировский район, ул. Васильченко, д. 14Б', '2007-06-03', 'Острая инфекция верхних дыхательных путей неуточненная', '2025-06-03 09:41:58', NULL, 0, NULL, NULL),
(8, 6, 'Николаева Татьяна Викторовна', 79201234567, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$fNoCj3Z.Omyf0zIr3euGz.y.cb6YV9OwedN66o0m7R5ThTx49aTEO', 'active', 'female', 'Ново-Савиновский район, ул. Короленко, д. 58', '1970-06-03', 'Межрёберная невралгия; Сахарный диабет 2 типа', '2025-06-03 09:41:58', NULL, 0, NULL, NULL),
(9, 7, 'Федоров Максим Андреевич', 79181234567, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$C5wnezpifARSFdCqsH.TN.fuF2R68PAVUnv4Lzg17xpxMcDLa18P6', 'active', 'male', 'Авиастроительный район, ул. Побежимова, д. 47', '1987-06-03', 'Бронхиальная астма', '2025-06-03 09:41:58', NULL, 0, NULL, NULL),
(10, 8, 'Волкова Ольга Сергеевна', 79131234567, NULL, 'dinahidarova@yandex.ru', NULL, '$2y$10$L0P2Qbn969ptU6lA2dbLOeXynJ/8kFiI662OsaU.ye3ZYxglgk/h6', 'active', 'female', 'Советский район, ул. Завойского, д. 18А', '2003-06-03', 'Железодефицитная анемия неуточненная; Атопический дерматит неуточненный', '2025-06-03 09:41:58', NULL, 0, NULL, NULL),
(11, 1, 'Хайдарова Дина Рустемовна', 9600875907, NULL, 'hajdarovadina229@gmail.com', NULL, '$2y$10$lRQzK17ztRPBlYvh3rCnOOzObxa3qJcqwJCkbhEVm59F8vHyknFs6', 'active', 'female', 'Приволжский район, тер. Деревни Универсиады, д. 3/1', '2004-04-12', 'Межрёберная невралгия', '2025-06-05 19:59:43', NULL, 0, NULL, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dispensarization`
--
ALTER TABLE `dispensarization`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `dispensary_session`
--
ALTER TABLE `dispensary_session`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `PatientID` (`PatientID`);

--
-- Индексы таблицы `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctors_ibfk_1` (`doctorSpecializationId`);

--
-- Индексы таблицы `doctorslog`
--
ALTER TABLE `doctorslog`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `doctorspecilization`
--
ALTER TABLE `doctorspecilization`
  ADD PRIMARY KEY (`doctorSpecializationId`);

--
-- Индексы таблицы `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Индексы таблицы `reception`
--
ALTER TABLE `reception`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_medhist_doctor` (`DoctorID`),
  ADD KEY `fk_medhist_dispensary` (`DispensaryID`);

--
-- Индексы таблицы `tblpatient`
--
ALTER TABLE `tblpatient`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT для таблицы `dispensarization`
--
ALTER TABLE `dispensarization`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT для таблицы `dispensary_session`
--
ALTER TABLE `dispensary_session`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `doctorslog`
--
ALTER TABLE `doctorslog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT для таблицы `doctorspecilization`
--
ALTER TABLE `doctorspecilization`
  MODIFY `doctorSpecializationId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT для таблицы `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `reception`
--
ALTER TABLE `reception`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `tblpatient`
--
ALTER TABLE `tblpatient`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `dispensary_session`
--
ALTER TABLE `dispensary_session`
  ADD CONSTRAINT `dispensary_session_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `tblpatient` (`ID`);

--
-- Ограничения внешнего ключа таблицы `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`doctorSpecializationId`) REFERENCES `doctorspecilization` (`doctorSpecializationId`);

--
-- Ограничения внешнего ключа таблицы `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  ADD CONSTRAINT `fk_medhist_dispensary` FOREIGN KEY (`DispensaryID`) REFERENCES `dispensary_session` (`ID`),
  ADD CONSTRAINT `fk_medhist_doctor` FOREIGN KEY (`DoctorID`) REFERENCES `doctors` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
