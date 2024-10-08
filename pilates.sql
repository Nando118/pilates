-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2024 at 12:13 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pilates`
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
(1, 2, 'STOTT Intensive Mat Pilates (IMP)', '2019-10-07', 'Japan Conditioning Academy', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(2, 2, 'Bachelor of Physiotherapy', '2023-10-07', 'Japan Conditioning Academy', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(3, 2, 'STOTT Intensive Reformer (IR)', '2023-10-07', 'Japan Conditioning Academy', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(4, 2, 'Pre Natal Pilates Reformer', '2022-10-07', 'Japan Conditioning Academy', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(5, 4, 'Pre Natal Pilates Cadillac', '2019-10-07', 'Pilates Institute', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(6, 4, 'STOTT Intensive Chair Cadilac & Barrel (ICCB)', '2019-10-07', 'Pilates Institute', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(7, 4, 'Optimization Lumbo-Pelvic Region', '2019-10-07', 'Pilates Institute', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(8, 4, 'STOTT Intensive Chair Cadilac & Barrel (ICCB)', '2021-10-07', 'Pilates Institute', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL);

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
(1, 'All Level', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(2, 'Bootcamp', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(3, 'Booty & Core', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(4, 'Abs & Back', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL);

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
(1, '2024-10-07', 1, 1, 1, 2, 1, 6, 'Available', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(2, '2024-10-07', 1, 2, 2, 4, 2, 3, 'Available', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(3, '2024-10-07', 2, 4, 1, 4, 1, 5, 'Available', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(4, '2024-10-08', 1, 4, 1, 2, 1, 5, 'Available', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(5, '2024-10-08', 3, 2, 2, 4, 2, 3, 'Available', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL);

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
(1, 'Reformer', 5, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(2, 'Private', 3, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL);

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
(1, 'admin', '2024-10-07 10:13:26', '2024-10-07 10:13:26', NULL),
(2, 'coach', '2024-10-07 10:13:26', '2024-10-07 10:13:26', NULL),
(3, 'client', '2024-10-07 10:13:26', '2024-10-07 10:13:26', NULL);

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
(1, 'Room Alpha', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(2, 'Room Beta', '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL);

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
(1, '09:00:00', '10:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(2, '10:00:00', '11:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(3, '11:00:00', '12:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(4, '12:00:00', '13:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(5, '13:00:00', '14:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(6, '14:00:00', '15:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(7, '15:00:00', '16:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(8, '16:00:00', '17:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(9, '17:00:00', '18:00:00', 50, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL);

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
(1, 'Admin User', 'admin@admin.com', '2024-10-07 10:13:27', '$2y$12$THZOiLi7vBzfyB8FoG/UM.1jpN1YyyuSTePVvKYN7Sb1oU/RFtE6e', 'form', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(2, 'Justin', 'justin@coach.com', '2024-10-07 10:13:27', '$2y$12$PhSeKwvFriTfdEYe4Im3XemvX7KcAuhvDWRiTvSulLtrsROszGdC6', 'form', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(3, 'Adam Levin', 'adam.levin@client.com', '2024-10-07 10:13:27', '$2y$12$/JWmBKgiR/kHCXmyEpBbT.k84HdgNin4ikBBiHHrF8hv01CP/dem.', 'form', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(4, 'Helga', 'helga@coach.com', '2024-10-07 10:13:27', '$2y$12$JbUQfBzyj.6OSw34j0ZyJOTC7iRu4QQ3nW9dRs8X9SgXG3QSRj/F2', 'form', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(5, 'Sumiko', 'sumiko@client.com', '2024-10-07 10:13:27', '$2y$12$OPGCeXxGQKZCAGC73zLD7.ibZ7UavGl1Y6C1pybc9Rn6f3VpZUJBq', 'form', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(6, 'Tatang', 'mastatang@client.com', '2024-10-07 10:13:27', '$2y$12$2q7t1z5o8AXovLX4nXC/ju7yvfcIHyU3pifo7gzisg/yAKccu8..i', 'form', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL);

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
(1, 1, 'adminuser', 'male', '1234567890', 'Tangerang', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(2, 2, 'coachjustin', 'male', '0987654321', 'Jakarta', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(3, 3, 'adamlevin', 'male', '1122334455', 'California', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(4, 4, 'helga', 'female', '0987654321', 'Tangerang', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(5, 5, 'sumikojp', 'female', '1122334455', 'Yokohama', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL),
(6, 6, 'mastatang', 'male', '1122334455', 'Bandung', NULL, '2024-10-07 10:13:27', '2024-10-07 10:13:27', NULL);

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
(6, 6, 3, NULL, NULL, NULL);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lesson_types_name_unique` (`name`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coach_certifications`
--
ALTER TABLE `coach_certifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lesson_schedules`
--
ALTER TABLE `lesson_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lesson_types`
--
ALTER TABLE `lesson_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `social_accounts`
--
ALTER TABLE `social_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
