-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 15, 2024 at 10:55 AM
-- Server version: 10.5.26-MariaDB-cll-lve
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `celyncaith_uohana`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lesson_schedule_id` bigint(20) UNSIGNED NOT NULL,
  `booked_by_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `lesson_schedule_id`, `booked_by_name`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 8, 'Adam Levin', 3, '2024-10-08 07:36:59', '2024-10-08 09:23:08', '2024-10-08 09:23:08'),
(2, 9, 'Adam Levin', 3, '2024-10-08 08:11:12', '2024-10-08 09:21:35', '2024-10-08 09:21:35'),
(3, 10, 'Adam Levin', 3, '2024-10-08 09:10:48', '2024-10-08 09:10:48', NULL),
(4, 10, 'Sumiko', 5, '2024-10-08 09:10:48', '2024-10-08 09:10:48', NULL),
(5, 12, 'Cherry', 9, '2024-10-12 08:40:39', '2024-10-12 08:40:39', NULL),
(6, 11, 'Cherry', 9, '2024-10-12 08:41:11', '2024-10-12 08:41:11', NULL),
(7, 11, 'Nando', 8, '2024-10-12 08:41:11', '2024-10-12 08:41:11', NULL),
(8, 13, 'Sumiko', 5, '2024-11-01 09:27:27', '2024-11-02 09:18:18', '2024-11-02 09:18:18'),
(9, 13, 'Adam Levin', 3, '2024-11-01 09:42:21', '2024-11-02 09:17:52', '2024-11-02 09:17:52'),
(10, 13, 'Nando', 8, '2024-11-01 09:44:43', '2024-11-02 09:17:48', '2024-11-02 09:17:48'),
(11, 14, 'Adam Levin', 3, '2024-11-01 09:45:54', '2024-11-02 09:18:15', '2024-11-02 09:18:15'),
(12, 14, 'Sumiko', 5, '2024-11-01 09:46:11', '2024-11-02 09:18:40', '2024-11-02 09:18:40'),
(13, 14, 'Cherry', 9, '2024-11-01 09:46:27', '2024-11-02 09:17:34', '2024-11-02 09:17:34'),
(14, 13, 'Cherry', 9, '2024-11-01 09:47:25', '2024-11-02 09:17:45', '2024-11-02 09:17:45'),
(15, 13, 'Tatang', 6, '2024-11-01 09:47:38', '2024-11-02 09:17:41', '2024-11-02 09:17:41'),
(16, 13, 'Fernando Verdy', 7, '2024-11-01 09:47:49', '2024-11-02 09:17:38', '2024-11-02 09:17:38'),
(17, 14, 'Tatang', 6, '2024-11-01 09:48:16', '2024-11-02 09:18:36', '2024-11-02 09:18:36'),
(18, 14, 'Fernando Verdy', 7, '2024-11-01 09:48:34', '2024-11-02 09:18:33', '2024-11-02 09:18:33'),
(19, 15, 'Sumiko', 5, '2024-11-01 09:50:18', '2024-11-02 09:18:22', '2024-11-02 09:18:22'),
(20, 15, 'Tatang', 6, '2024-11-01 09:50:18', '2024-11-02 09:18:26', '2024-11-02 09:18:26'),
(21, 14, 'Testing', 10, '2024-11-01 10:57:37', '2024-11-02 09:18:29', '2024-11-02 09:18:29'),
(22, 16, 'Jenny', 12, '2024-11-01 12:17:44', '2024-11-02 09:18:03', '2024-11-02 09:18:03'),
(23, 16, 'Cherry', 9, '2024-11-01 12:20:33', '2024-11-02 09:18:10', '2024-11-02 09:18:10'),
(24, 13, 'Jenny', 12, '2024-11-01 12:29:49', '2024-11-01 12:30:27', '2024-11-01 12:30:27'),
(25, 17, 'Felice Zhuang', 15, '2024-11-02 14:34:15', '2024-11-02 14:34:15', NULL),
(26, 17, 'JENNY CHIA', 16, '2024-11-02 14:34:15', '2024-11-02 14:34:15', NULL),
(27, 17, 'JENWI', 13, '2024-11-02 14:34:15', '2024-11-02 14:34:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coach_certifications`
--

CREATE TABLE `coach_certifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `certification_name` varchar(255) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `issuing_organization` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coach_certifications`
--

INSERT INTO `coach_certifications` (`id`, `user_id`, `certification_name`, `date_received`, `issuing_organization`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'STOTT Intensive Reformer (IR)', '2023-10-08', 'Japan Conditioning Academy', '2024-10-08 07:32:00', '2024-11-02 09:15:32', '2024-11-02 09:15:32'),
(2, 2, 'STOTT Intensive Reformer (IR)', '2021-10-08', 'Japan Conditioning Academy', '2024-10-08 07:32:00', '2024-11-02 09:15:39', '2024-11-02 09:15:39'),
(3, 2, 'Total Barre Amplified', '2023-10-08', 'Japan Conditioning Academy', '2024-10-08 07:32:00', '2024-11-02 09:15:46', '2024-11-02 09:15:46'),
(4, 2, 'Optimization Lumbo-Pelvic Region', '2021-10-08', 'Japan Conditioning Academy', '2024-10-08 07:32:00', '2024-11-02 09:15:53', '2024-11-02 09:15:53'),
(5, 2, 'Total Barre Amplified', '2019-10-08', 'Japan Conditioning Academy', '2024-10-08 07:32:00', '2024-11-02 09:16:11', '2024-11-02 09:16:11'),
(6, 4, 'Total Barre Amplified', '2019-10-08', 'Pilates Institute', '2024-10-08 07:32:00', '2024-11-02 09:15:58', '2024-11-02 09:15:58'),
(7, 4, 'Pre Natal Pilates Reformer', '2023-10-08', 'Pilates Institute', '2024-10-08 07:32:00', '2024-11-02 09:16:16', '2024-11-02 09:16:16'),
(8, 4, 'Optimization Lumbo-Pelvic Region', '2023-10-08', 'Pilates Institute', '2024-10-08 07:32:00', '2024-11-02 09:16:28', '2024-11-02 09:16:28'),
(9, 4, 'Optimization Lumbo-Pelvic Region', '2019-10-08', 'Pilates Institute', '2024-10-08 07:32:00', '2024-11-02 09:16:32', '2024-11-02 09:16:32'),
(10, 4, 'Total Barre Amplified', '2020-10-08', 'Pilates Institute', '2024-10-08 07:32:00', '2024-11-02 09:16:35', '2024-11-02 09:16:35'),
(11, 11, 'test', '2024-11-01', 'rcdv', '2024-11-01 12:13:49', '2024-11-01 12:13:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'All Level', '2024-10-08 07:32:00', '2024-11-01 12:08:12', '2024-11-01 12:08:12'),
(2, 'Bootcamp', '2024-10-08 07:32:00', '2024-11-01 12:08:55', '2024-11-01 12:08:55'),
(3, 'Booty & Core', '2024-10-08 07:32:00', '2024-11-01 12:09:15', '2024-11-01 12:09:15'),
(4, 'Abs & Back', '2024-10-08 07:32:00', '2024-11-01 12:09:19', '2024-11-01 12:09:19'),
(5, 'Reformer', '2024-11-01 12:08:36', '2024-11-01 12:08:36', NULL),
(6, 'Tower', '2024-11-01 12:08:45', '2024-11-01 12:08:45', NULL),
(7, 'Chair', '2024-11-01 12:09:06', '2024-11-01 12:09:06', NULL),
(8, 'Cadilac', '2024-11-01 12:09:39', '2024-11-01 12:09:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_schedules`
--

CREATE TABLE `lesson_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time_slot_id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `lesson_type_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `quota` int(11) NOT NULL DEFAULT 0,
  `status` enum('Available','Full Booked') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lesson_schedules`
--

INSERT INTO `lesson_schedules` (`id`, `date`, `time_slot_id`, `lesson_id`, `lesson_type_id`, `user_id`, `room_id`, `quota`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2024-10-08', 1, 1, 1, 2, 1, 6, 'Available', '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(2, '2024-10-08', 1, 2, 2, 4, 2, 3, 'Available', '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(3, '2024-10-08', 2, 4, 1, 4, 1, 5, 'Available', '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(4, '2024-10-09', 1, 4, 1, 2, 1, 5, 'Available', '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(5, '2024-10-09', 4, 2, 2, 2, 2, 3, 'Available', '2024-10-08 07:32:00', '2024-10-08 07:36:15', NULL),
(6, '2024-10-08', 6, 2, 2, 2, 1, 3, 'Available', '2024-10-08 07:33:33', '2024-10-08 07:33:52', NULL),
(7, '2024-10-09', 2, 1, 1, 2, 1, 5, 'Available', '2024-10-08 07:35:31', '2024-10-08 07:35:31', NULL),
(8, '2024-10-10', 2, 1, 2, 2, 1, 3, 'Available', '2024-10-08 07:36:36', '2024-10-08 09:23:08', NULL),
(9, '2024-10-09', 9, 3, 2, 2, 1, 3, 'Available', '2024-10-08 08:10:52', '2024-10-08 09:21:35', NULL),
(10, '2024-10-11', 2, 3, 2, 2, 2, 1, 'Available', '2024-10-08 09:10:27', '2024-10-08 09:10:48', NULL),
(11, '2024-10-13', 2, 1, 1, 2, 1, 3, 'Available', '2024-10-12 08:29:04', '2024-10-12 08:41:11', NULL),
(12, '2024-10-13', 3, 2, 4, 4, 2, 5, 'Full Booked', '2024-10-12 08:29:34', '2024-11-01 08:35:21', NULL),
(13, '2024-11-04', 2, 5, 3, 4, 2, 7, 'Full Booked', '2024-11-01 09:22:09', '2024-11-02 09:19:18', '2024-11-02 09:19:18'),
(14, '2024-11-02', 2, 1, 4, 2, 1, 6, 'Full Booked', '2024-11-01 09:45:28', '2024-11-02 09:19:24', '2024-11-02 09:19:24'),
(15, '2024-11-03', 4, 1, 4, 4, 2, 2, 'Full Booked', '2024-11-01 09:49:48', '2024-11-02 09:19:21', '2024-11-02 09:19:21'),
(16, '2024-11-02', 1, 5, 4, 11, 1, 3, 'Full Booked', '2024-11-01 12:16:56', '2024-11-02 09:19:28', '2024-11-02 09:19:28'),
(17, '2024-11-11', 11, 5, 3, 11, 1, 2, 'Available', '2024-11-02 09:21:42', '2024-11-02 14:34:15', NULL),
(18, '2024-11-11', 12, 5, 3, 11, 1, 5, 'Available', '2024-11-02 09:32:22', '2024-11-02 09:35:48', NULL),
(19, '2024-11-11', 1, 5, 3, 11, 1, 5, 'Available', '2024-11-02 09:33:30', '2024-11-02 09:36:04', NULL),
(20, '2024-11-11', 2, 5, 3, 11, 1, 5, 'Available', '2024-11-02 09:37:32', '2024-11-02 09:37:32', NULL),
(21, '2024-11-11', 3, 5, 3, 11, 1, 5, 'Available', '2024-11-02 09:38:22', '2024-11-02 09:38:22', NULL),
(22, '2024-11-11', 4, 5, 3, 11, 1, 5, 'Available', '2024-11-02 09:38:58', '2024-11-02 09:38:58', NULL),
(23, '2024-11-11', 6, 5, 3, 11, 1, 5, 'Available', '2024-11-02 09:40:44', '2024-11-02 09:40:44', NULL),
(24, '2024-11-11', 7, 5, 3, 11, 1, 5, 'Available', '2024-11-02 09:41:08', '2024-11-02 09:41:08', NULL),
(25, '2024-11-11', 8, 5, 3, 11, 1, 5, 'Available', '2024-11-02 09:41:32', '2024-11-02 14:23:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_types`
--

CREATE TABLE `lesson_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quota` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lesson_types`
--

INSERT INTO `lesson_types` (`id`, `name`, `quota`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Reformer', 5, '2024-10-08 07:32:00', '2024-11-01 08:03:54', '2024-11-01 08:03:54'),
(2, 'Private', 3, '2024-10-08 07:32:00', '2024-11-01 08:03:36', '2024-11-01 08:03:36'),
(3, 'Group', 5, '2024-11-01 08:04:00', '2024-11-01 08:26:59', NULL),
(4, 'Private', 1, '2024-11-01 08:26:24', '2024-11-01 08:26:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_08_29_025244_create_user_profiles_table', 1),
(6, '2024_08_29_025916_create_social_accounts_table', 1),
(7, '2024_08_29_030000_create_roles_table', 1),
(8, '2024_08_29_030024_create_user_roles_table', 1),
(9, '2024_09_05_171003_create_rooms_table', 1),
(10, '2024_09_05_171245_create_time_slots_table', 1),
(11, '2024_09_14_061212_create_lesson_types_table', 1),
(12, '2024_09_14_092054_create_lessons_table', 1),
(13, '2024_09_15_061929_create_lesson_schedules_table', 1),
(14, '2024_09_20_111157_create_bookings_table', 1),
(15, '2024_09_28_072000_create_coach_certifications_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', '2024-10-08 07:31:59', '2024-10-08 07:31:59', NULL),
(2, 'coach', '2024-10-08 07:31:59', '2024-10-08 07:31:59', NULL),
(3, 'client', '2024-10-08 07:31:59', '2024-10-08 07:31:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Modernland', '2024-10-08 07:32:00', '2024-11-01 07:59:45', NULL),
(2, 'Serpong', '2024-10-08 07:32:00', '2024-11-01 07:59:54', NULL),
(3, 'Citra Raya', '2024-11-01 12:06:25', '2024-11-01 12:06:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `social_accounts`
--

CREATE TABLE `social_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(255) NOT NULL,
  `provider_id` varchar(255) NOT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_accounts`
--

INSERT INTO `social_accounts` (`id`, `user_id`, `provider`, `provider_id`, `access_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 7, 'google', '105782590597345329906', 'ya29.a0AcM612wsn6cVBvUp0NfBrOzD5tv9LyHYJwcIA6-8fUo1tHhG1LNUom54h2_N1e397hmfs7hAXWqvowmMwfdf-bLZC2oBQAFMd-NTbw38emngj2EovwkJMVUbY-0jUWD_sS3OV6DZBp9LnbMKuaX6H0_HT_WGhyNRbekaCgYKAZoSARASFQHGX2MiGOjVifpOXPSlgD-w55rbfQ0170', '2024-10-08 10:19:31', '2024-10-08 10:19:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `start_time`, `end_time`, `duration`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '09:00:00', '10:00:00', 50, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(2, '10:00:00', '11:00:00', 50, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(3, '11:00:00', '12:00:00', 50, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(4, '12:00:00', '13:00:00', 50, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(5, '13:00:00', '14:00:00', 50, '2024-10-08 07:32:00', '2024-11-02 08:30:34', '2024-11-02 08:30:34'),
(6, '14:00:00', '15:00:00', 50, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(7, '15:00:00', '16:00:00', 50, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(8, '16:00:00', '17:00:00', 50, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(9, '17:00:00', '18:00:00', 50, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(10, '18:00:00', '19:00:00', 50, '2024-11-01 12:07:35', '2024-11-01 12:07:35', NULL),
(11, '07:00:00', '08:00:00', 50, '2024-11-02 08:33:03', '2024-11-02 08:33:03', NULL),
(12, '08:00:00', '09:00:00', 50, '2024-11-02 08:33:42', '2024-11-02 08:33:42', NULL),
(13, '19:00:00', '20:00:00', 50, '2024-11-02 08:34:22', '2024-11-02 08:34:22', NULL),
(14, '20:00:00', '21:00:00', 50, '2024-11-02 08:34:43', '2024-11-02 08:34:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `registration_type` enum('form','social') NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `registration_type`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin User', 'admin@admin.com', '2024-10-08 07:32:00', '$2y$12$yxqQL4I1d8/f4SHL/yaLOOmwM.HODRdwKNpkm1LrNUfkEIpaWXmPC', 'form', 'OasPLvuifouAtbYkD1vRp06ExhaNAPAZxRadsOQKV4tROTJWfEErOLNTIhJg', '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(2, 'Justin', 'justin@coach.com', '2024-10-08 07:32:00', '$2y$12$D9LF7jaaOWunayE7ecOUmupZKszEp..IQxFXBe1eK5BIJCbGOrWt.', 'form', NULL, '2024-10-08 07:32:00', '2024-11-02 08:35:59', '2024-11-02 08:35:59'),
(3, 'Adam Levin', 'adam.levin@client.com', '2024-10-08 07:32:00', '$2y$12$0QlKmUenVX.49JBvdE03buaBFJjS4p5ZIwudWkHG5vC1uNkQEg9Ky', 'form', NULL, '2024-10-08 07:32:00', '2024-11-02 08:35:53', '2024-11-02 08:35:53'),
(4, 'Helga', 'helga@coach.com', '2024-10-08 07:32:00', '$2y$12$66e4QXE6W1irhxAMRx0KpOrK7csOhFAbsy3ZAosfwH1SHUGHWyvCK', 'form', NULL, '2024-10-08 07:32:00', '2024-11-02 08:35:46', '2024-11-02 08:35:46'),
(5, 'Sumiko', 'sumiko@client.com', '2024-10-08 07:32:00', '$2y$12$iYJLNvjIIr8RaaGG.zOlTOp7HEYBjwFoKeGqI2YSuMjorws9Zxx/a', 'form', NULL, '2024-10-08 07:32:00', '2024-11-02 08:35:40', '2024-11-02 08:35:40'),
(6, 'Tatang', 'mastatang@client.com', '2024-10-08 07:32:00', '$2y$12$Pewg486r7.nSI54F.2.hlO1yVRkUJr9VXqjxJ6Hbo26gClujT7tQS', 'form', NULL, '2024-10-08 07:32:00', '2024-11-02 08:35:34', '2024-11-02 08:35:34'),
(7, 'Fernando Verdy', 'fernandoverdysunata18@gmail.com', '2024-10-08 10:20:00', NULL, 'social', NULL, '2024-10-08 10:19:31', '2024-11-02 08:35:24', '2024-11-02 08:35:24'),
(8, 'Nando', 'fernandoverdysunata0118@gmail.com', '2024-10-08 10:21:38', '$2y$12$XV.fTAtpvg1uUz1Yd8RCZORDUzyl3O.P6FW139zERexF5KdJ7KUQK', 'form', NULL, '2024-10-08 10:21:28', '2024-11-02 08:36:04', '2024-11-02 08:36:04'),
(9, 'Cherry', 'ei.cherrywulan@gmail.com', '2024-10-12 08:28:04', '$2y$12$IS3fiN1o0145FNECRPrxFO6Xijh7h/0uZG0.NGqKi512zC1eEN8Jq', 'form', NULL, '2024-10-12 08:26:49', '2024-10-12 08:28:04', NULL),
(10, 'Testing', 'agustian.p@gmail.com', '2024-11-01 10:57:24', '$2y$12$aemtMyEl6knubvKH/IONX.p17TAPawQv4HATakMmUehGOZc1nXcNu', 'form', NULL, '2024-11-01 10:57:24', '2024-11-02 08:36:12', '2024-11-02 08:36:12'),
(11, 'Velly', 'velly@test.com', '2024-11-01 12:11:56', '$2y$12$FXBpiyCr/Q75I.WLanX82OH8ey.mWTOftHWkb8EpXpOfJUZwba89e', 'form', NULL, '2024-11-01 12:11:56', '2024-11-01 12:11:56', NULL),
(12, 'Jenny', 'jennychia@hotmail.com', '2024-11-01 12:12:56', '$2y$12$hfl.93ifjGG9bGS0Sk5.OOAC6I42wzdRK1wLen6v7Q.UsPgQl80FC', 'form', NULL, '2024-11-01 12:12:56', '2024-11-02 08:35:12', '2024-11-02 08:35:12'),
(13, 'JENWI', 'j3nw1@yahoo.com', '2024-11-02 08:58:52', '$2y$12$Gw5RodmMyjrjZtDf8TCtUeD88/S0fNyvt8v3mPTqUHcydvyPKpxIK', 'form', NULL, '2024-11-02 08:58:52', '2024-11-02 08:58:52', NULL),
(14, 'VIVI CHANDRA', 'v1chansard1an@yahoo.com', '2024-11-02 09:01:08', '$2y$12$m0HxEDoVkbMo3.l18uk24ukN9IS7igMleR6vvlMZoQFP16hkcheBS', 'form', NULL, '2024-11-02 09:01:08', '2024-11-02 09:01:08', NULL),
(15, 'Felice Zhuang', 'felicezhuang26@gmail.com', '2024-11-02 09:06:38', '$2y$12$TcWRmreisuXhnOSfr/vyj.Pe8vdFZZwhrzRUZXICSRUuBQzHdtLuu', 'form', NULL, '2024-11-02 09:06:38', '2024-11-02 09:06:38', NULL),
(16, 'JENNY CHIA', 'jennycutelabels@gmail.com', '2024-11-02 09:07:58', '$2y$12$0JoZCa5maqepieZ2BLls4.EoFw/OyR0NMlBJBMe.vvUXSkiPgi14W', 'form', NULL, '2024-11-02 09:07:58', '2024-11-02 09:07:58', NULL),
(17, 'Kelly Valen', 'kellyvalenzhuang2005@gmail.com', '2024-11-02 15:07:04', '$2y$12$lEMTWlKyJzD9JbykIqB.GOSDJuV39HiO6NXnsKSMrZ6TqsO5ECTaa', 'form', NULL, '2024-11-02 15:07:04', '2024-11-02 15:07:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT '-',
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `username`, `gender`, `phone`, `address`, `profile_picture`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'adminuser', 'male', '1234567890', 'Tangerang', NULL, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(2, 2, 'coachjustin', 'male', '0987654321', 'Jakarta', 'images/profile/6704f770a59d7.jpg', '2024-10-08 07:32:00', '2024-10-08 09:12:16', NULL),
(3, 3, 'adamlevin', 'male', '1122334455', 'California', NULL, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(4, 4, 'helga', 'female', '0987654321', 'Tangerang', NULL, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(5, 5, 'sumikojp', 'female', '1122334455', 'Yokohama', NULL, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(6, 6, 'mastatang', 'male', '1122334455', 'Bandung', NULL, '2024-10-08 07:32:00', '2024-10-08 07:32:00', NULL),
(7, 7, 'nando118', 'male', '1234567890', NULL, NULL, '2024-10-08 10:19:31', '2024-10-08 10:19:45', NULL),
(8, 8, 'ndo118', 'male', '1234567890', NULL, NULL, '2024-10-08 10:21:28', '2024-10-08 10:21:28', NULL),
(9, 9, 'Cher', 'female', '08118881903', 'Premier Park 2 blok N3', NULL, '2024-10-12 08:26:49', '2024-10-12 08:26:49', NULL),
(10, 10, 'agustian', 'male', '08118880959', 'testing', NULL, '2024-11-01 10:57:24', '2024-11-01 10:57:24', NULL),
(11, 11, 'Velly', 'female', '082222222222', NULL, NULL, '2024-11-01 12:11:56', '2024-11-01 12:11:56', NULL),
(12, 12, 'Jenny', 'female', '08158306016', NULL, NULL, '2024-11-01 12:12:56', '2024-11-01 12:12:56', NULL),
(13, 13, 'Jenwi', 'female', '6287878860888', NULL, NULL, '2024-11-02 08:58:52', '2024-11-02 08:58:52', NULL),
(14, 14, 'Vivichandra', 'female', '081216668899', NULL, NULL, '2024-11-02 09:01:08', '2024-11-02 09:01:08', NULL),
(15, 15, 'Felicezhuang', 'female', '089513077833', NULL, NULL, '2024-11-02 09:06:38', '2024-11-02 09:06:38', NULL),
(16, 16, 'Jennychia', 'female', '0815830606', 'Jl RAYA TAMAN GOLF EG2 NO 8', NULL, '2024-11-02 09:07:58', '2024-11-02 09:07:58', NULL),
(17, 17, 'Kellyvalen', 'female', '089513077855', NULL, NULL, '2024-11-02 15:07:04', '2024-11-02 15:07:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, NULL, NULL),
(2, 2, 2, NULL, NULL, NULL),
(3, 3, 3, NULL, NULL, NULL),
(4, 4, 2, NULL, NULL, NULL),
(5, 5, 3, NULL, NULL, NULL),
(6, 6, 3, NULL, NULL, NULL),
(7, 7, 3, NULL, NULL, NULL),
(8, 8, 3, NULL, NULL, NULL),
(9, 9, 3, NULL, NULL, NULL),
(10, 10, 3, NULL, NULL, NULL),
(11, 11, 2, NULL, NULL, NULL),
(12, 12, 3, NULL, NULL, NULL),
(13, 13, 3, NULL, '2024-11-02 08:59:49', NULL),
(14, 14, 3, NULL, NULL, NULL),
(15, 15, 3, NULL, NULL, NULL),
(16, 16, 3, NULL, NULL, NULL),
(17, 17, 3, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_lesson_schedule_id_foreign` (`lesson_schedule_id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`);

--
-- Indexes for table `coach_certifications`
--
ALTER TABLE `coach_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coach_certifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lessons_name_unique` (`name`);

--
-- Indexes for table `lesson_schedules`
--
ALTER TABLE `lesson_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_schedules_time_slot_id_foreign` (`time_slot_id`),
  ADD KEY `lesson_schedules_lesson_id_foreign` (`lesson_id`),
  ADD KEY `lesson_schedules_lesson_type_id_foreign` (`lesson_type_id`),
  ADD KEY `lesson_schedules_user_id_foreign` (`user_id`),
  ADD KEY `lesson_schedules_room_id_foreign` (`room_id`);

--
-- Indexes for table `lesson_types`
--
ALTER TABLE `lesson_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rooms_name_unique` (`name`);

--
-- Indexes for table `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `social_accounts_provider_id_unique` (`provider_id`),
  ADD KEY `social_accounts_user_id_foreign` (`user_id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_profiles_username_unique` (`username`),
  ADD KEY `user_profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_roles_user_id_foreign` (`user_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `coach_certifications`
--
ALTER TABLE `coach_certifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lesson_schedules`
--
ALTER TABLE `lesson_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `lesson_types`
--
ALTER TABLE `lesson_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `social_accounts`
--
ALTER TABLE `social_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_lesson_schedule_id_foreign` FOREIGN KEY (`lesson_schedule_id`) REFERENCES `lesson_schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `coach_certifications`
--
ALTER TABLE `coach_certifications`
  ADD CONSTRAINT `coach_certifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_schedules`
--
ALTER TABLE `lesson_schedules`
  ADD CONSTRAINT `lesson_schedules_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_schedules_lesson_type_id_foreign` FOREIGN KEY (`lesson_type_id`) REFERENCES `lesson_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_schedules_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_schedules_time_slot_id_foreign` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `social_accounts`
--
ALTER TABLE `social_accounts`
  ADD CONSTRAINT `social_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
