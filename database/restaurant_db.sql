-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2026 at 11:32 AM
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
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`id`, `menu_item_id`, `name`, `price`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 4, 'Side Salad', 3.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(2, 4, 'Extra Sauce', 1.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(3, 4, 'Extra Fries', 3.00, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(4, 5, 'Side Salad', 3.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(5, 5, 'Extra Sauce', 1.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(6, 5, 'Extra Fries', 3.00, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(7, 6, 'Side Salad', 3.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(8, 6, 'Extra Sauce', 1.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(9, 6, 'Extra Fries', 3.00, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(10, 7, 'Side Salad', 3.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(11, 7, 'Extra Sauce', 1.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(12, 7, 'Extra Fries', 3.00, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(13, 7, 'Extra Patty', 4.00, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(14, 7, 'Bacon', 2.00, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(15, 7, 'Extra Cheese', 1.50, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Starters', 'starters', 'Light bites to begin your meal', NULL, 1, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(2, 'Main Courses', 'main-courses', 'Hearty and delicious main dishes', NULL, 2, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(3, 'Drinks', 'drinks', 'Refreshing beverages', NULL, 3, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(4, 'Desserts', 'desserts', 'Sweet treats to finish', NULL, 4, 1, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL DEFAULT 'fixed',
  `value` decimal(8,2) NOT NULL,
  `min_order_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `max_discount_amount` decimal(8,2) DEFAULT NULL,
  `is_single_use` tinyint(1) NOT NULL DEFAULT 1,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `used_by` bigint(20) UNSIGNED DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `min_order_amount`, `max_discount_amount`, `is_single_use`, `is_used`, `used_by`, `used_at`, `expires_at`, `is_active`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'percentage', 10.00, 20.00, NULL, 1, 0, NULL, NULL, NULL, 1, 'Welcome discount for new customers', '2026-03-20 10:11:17', '2026-03-20 10:11:17'),
(2, 'SAVE5', 'fixed', 5.00, 30.00, NULL, 1, 0, NULL, NULL, NULL, 1, '€5 off orders over €30', '2026-03-20 10:11:17', '2026-03-20 10:11:17');

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
-- Table structure for table `gdpr_requests`
--

CREATE TABLE `gdpr_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('export','delete') NOT NULL DEFAULT 'export',
  `status` enum('pending','processed') NOT NULL DEFAULT 'pending',
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `allergens` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allergens`)),
  `preparation_time` int(11) DEFAULT NULL COMMENT 'minutes',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `image`, `is_available`, `is_featured`, `allergens`, `preparation_time`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Soup of the Day', 'soup-of-the-day', 'Freshly made daily, served with crusty bread', 8.50, NULL, 1, 0, '[\"Gluten\",\"Dairy\"]', 11, 0, '2026-03-20 10:11:17', '2026-03-27 10:22:37', NULL),
(2, 1, 'Garlic Bruschetta', 'garlic-bruschetta', 'Toasted sourdough with roasted garlic and cherry tomatoes', 7.50, NULL, 1, 0, '[\"Gluten\"]', 25, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(3, 1, 'Chicken Wings', 'chicken-wings', 'Crispy wings tossed in buffalo sauce, served with blue cheese dip', 9.95, NULL, 1, 1, '[\"Dairy\"]', 25, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(4, 2, '8oz Ribeye Steak', '8oz-ribeye-steak', 'Prime Irish beef, served with fries and peppercorn sauce', 28.95, NULL, 1, 1, '[\"Dairy\"]', 16, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(5, 2, 'Atlantic Salmon', 'atlantic-salmon', 'Pan-seared fillet with lemon butter and seasonal vegetables', 22.95, NULL, 1, 0, '[\"Fish\",\"Dairy\"]', 24, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(6, 2, 'Mushroom Risotto', 'mushroom-risotto', 'Arborio rice with wild mushrooms, parmesan and truffle oil', 17.95, NULL, 1, 0, '[\"Dairy\"]', 21, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(7, 2, 'Classic Burger', 'classic-burger', '6oz beef patty, lettuce, tomato, pickles, burger sauce', 15.95, NULL, 1, 1, '[\"Gluten\",\"Dairy\",\"Eggs\"]', 16, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(8, 3, 'Still Water (500ml)', 'still-water-500ml', '', 2.50, NULL, 1, 0, '[]', 16, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(9, 3, 'Sparkling Water', 'sparkling-water', '', 2.50, NULL, 1, 0, '[]', 24, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(10, 3, 'Fresh Orange Juice', 'fresh-orange-juice', 'Freshly squeezed', 4.50, NULL, 1, 0, '[]', 10, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(11, 3, 'House Lemonade', 'house-lemonade', 'Homemade with fresh lemons', 4.00, NULL, 1, 0, '[]', 18, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(12, 4, 'Chocolate Lava Cake', 'chocolate-lava-cake', 'Warm chocolate cake with a molten centre, vanilla ice cream', 8.50, NULL, 1, 1, '[\"Gluten\",\"Dairy\",\"Eggs\"]', 16, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(13, 4, 'Cheesecake', 'cheesecake', 'New York style with berry coulis', 7.95, NULL, 1, 0, '[\"Gluten\",\"Dairy\",\"Eggs\"]', 18, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL);

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
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2024_01_01_000001_create_users_table', 1),
(4, '2024_01_01_000002_create_categories_table', 1),
(5, '2024_01_01_000003_create_orders_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscriptions`
--

CREATE TABLE `newsletter_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `newsletter_subscriptions`
--

INSERT INTO `newsletter_subscriptions` (`id`, `email`, `name`, `token`, `is_active`, `confirmed_at`, `unsubscribed_at`, `created_at`, `updated_at`) VALUES
(1, 'demo@newsletter.com', 'Demo Subscriber', 'd5yJiFy1viil8FnvAentDtApxfd2r6PtsR53OKrYKy7BBX7qfzsdCswQZdoBoazo', 1, '2026-03-20 10:11:17', NULL, '2026-03-20 10:11:17', '2026-03-20 10:11:17');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `coupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','confirmed','preparing','ready','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `subtotal` decimal(8,2) NOT NULL,
  `discount_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total` decimal(8,2) NOT NULL,
  `delivery_address` text NOT NULL,
  `delivery_lat` decimal(10,8) DEFAULT NULL,
  `delivery_lng` decimal(11,8) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` enum('cash_on_delivery') NOT NULL DEFAULT 'cash_on_delivery',
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `is_printed` tinyint(1) NOT NULL DEFAULT 0,
  `printed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `coupon_id`, `status`, `subtotal`, `discount_amount`, `total`, `delivery_address`, `delivery_lat`, `delivery_lng`, `notes`, `payment_method`, `payment_status`, `is_printed`, `printed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'ORD-69C648317773F', 2, NULL, 'delivered', 48.85, 0.00, 48.85, 'Ru, Berlin, 0496', NULL, NULL, NULL, 'cash_on_delivery', 'pending', 0, NULL, '2026-03-27 01:04:49', '2026-03-28 02:18:24', NULL),
(4, 'ORD-69C7A88786ACC', 3, NULL, 'confirmed', 39.95, 0.00, 39.95, 'Bastos, Yaoundé, Cameroon, Yaoundé, 0000', 3.89234130, 11.51142150, 'with some salt', 'cash_on_delivery', 'pending', 0, NULL, '2026-03-28 02:08:07', '2026-03-28 02:17:50', NULL),
(5, 'ORD-69C7AA04D207A', 3, NULL, 'ready', 48.45, 0.00, 48.45, 'Quartier Bastos, Yaoundé, Cameroon, Yaoundé, 0000', NULL, NULL, NULL, 'cash_on_delivery', 'pending', 0, NULL, '2026-03-28 02:14:28', '2026-03-28 02:19:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(8,2) NOT NULL,
  `selected_addons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_addons`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `price`, `quantity`, `subtotal`, `selected_addons`, `notes`, `created_at`, `updated_at`) VALUES
(9, 3, 1, 'Soup of the Day', 6.50, 1, 6.50, NULL, NULL, '2026-03-27 01:04:49', '2026-03-27 01:04:49'),
(10, 3, 3, 'Chicken Wings', 9.95, 2, 19.90, NULL, NULL, '2026-03-27 01:04:49', '2026-03-27 01:04:49'),
(11, 3, 10, 'Fresh Orange Juice', 4.50, 1, 4.50, NULL, NULL, '2026-03-27 01:04:49', '2026-03-27 01:04:49'),
(12, 3, 6, 'Mushroom Risotto', 17.95, 1, 17.95, NULL, NULL, '2026-03-27 01:04:49', '2026-03-27 01:04:49'),
(13, 4, 4, '8oz Ribeye Steak', 28.95, 1, 28.95, NULL, NULL, '2026-03-28 02:08:07', '2026-03-28 02:08:07'),
(14, 4, 8, 'Still Water (500ml)', 2.50, 1, 2.50, NULL, NULL, '2026-03-28 02:08:07', '2026-03-28 02:08:07'),
(15, 4, 12, 'Chocolate Lava Cake', 8.50, 1, 8.50, NULL, NULL, '2026-03-28 02:08:07', '2026-03-28 02:08:07'),
(16, 5, 1, 'Soup of the Day', 8.50, 1, 8.50, NULL, NULL, '2026-03-28 02:14:28', '2026-03-28 02:14:28'),
(17, 5, 4, '8oz Ribeye Steak', 28.95, 1, 28.95, NULL, NULL, '2026-03-28 02:14:28', '2026-03-28 02:14:28'),
(18, 5, 12, 'Chocolate Lava Cake', 8.50, 1, 8.50, NULL, NULL, '2026-03-28 02:14:28', '2026-03-28 02:14:28'),
(19, 5, 9, 'Sparkling Water', 2.50, 1, 2.50, NULL, NULL, '2026-03-28 02:14:28', '2026-03-28 02:14:28');

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
-- Table structure for table `printer_configs`
--

CREATE TABLE `printer_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('network','usb','file') NOT NULL DEFAULT 'network',
  `host` varchar(255) DEFAULT NULL,
  `port` int(11) DEFAULT 9100,
  `usb_device` varchar(255) DEFAULT NULL,
  `paper_width` int(11) NOT NULL DEFAULT 80,
  `auto_print` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `printer_configs`
--

INSERT INTO `printer_configs` (`id`, `name`, `type`, `host`, `port`, `usb_device`, `paper_width`, `auto_print`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Main Printer', 'network', '192.168.1.100', 9100, NULL, 80, 0, 0, '2026-03-20 10:11:17', '2026-03-20 10:11:17');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Zpj8NLVI2rMaBY0BA4NzFVFjRXs73Qf113fgmrdp', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSzJmMFBUM1FBbVliV3V3akhHUzhmRlAwdjc1bms3YVV2WllqTzE2ZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7czo1OiJyb3V0ZSI7czoxNToiYWRtaW4uZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1774693855);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  `status` enum('pending','approved','suspended') NOT NULL DEFAULT 'pending',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `newsletter_subscribed` tinyint(1) NOT NULL DEFAULT 0,
  `gdpr_consent` tinyint(1) NOT NULL DEFAULT 0,
  `gdpr_consent_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `status`, `phone`, `address`, `lat`, `lng`, `newsletter_subscribed`, `gdpr_consent`, `gdpr_consent_at`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Restaurant Admin', 'admin@yourdomain.com', NULL, '$2y$12$e9PY8i5sn7cyNTgKrSprpu3Tq7pWdFhL3VQWffqnArtHC1elHPWqq', 'admin', 'approved', NULL, NULL, NULL, NULL, 0, 1, '2026-03-20 10:11:17', NULL, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(2, 'Demo Customer', 'customer@demo.com', NULL, '$2y$12$zMPSLixidH/J2nFF3L3wvOGUDREb8lAVYnN7bgg/u/dImmK26HAjq', 'customer', 'approved', '+353 87 000 0001', NULL, NULL, NULL, 0, 1, '2026-03-20 10:11:17', NULL, '2026-03-20 10:11:17', '2026-03-20 10:11:17', NULL),
(3, 'Herman Steve', 'kamdemherman9@gmail.com', NULL, '$2y$12$7UYkyrG8OEynpCeSmkWq9Ot6TOtSHrywnD/95EKKv.unpH8I9Hcnq', 'customer', 'approved', '655702447', 'Quartier Bastos, Yaoundé, Cameroon', 3.89046820, 11.50491630, 0, 1, '2026-03-27 10:29:06', NULL, '2026-03-27 10:29:06', '2026-03-28 02:11:05', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addons_menu_item_id_foreign` (`menu_item_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`),
  ADD KEY `coupons_used_by_foreign` (`used_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gdpr_requests`
--
ALTER TABLE `gdpr_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gdpr_requests_user_id_foreign` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_items_slug_unique` (`slug`),
  ADD KEY `menu_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscriptions`
--
ALTER TABLE `newsletter_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `newsletter_subscriptions_email_unique` (`email`),
  ADD UNIQUE KEY `newsletter_subscriptions_token_unique` (`token`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_coupon_id_foreign` (`coupon_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_menu_item_id_foreign` (`menu_item_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `printer_configs`
--
ALTER TABLE `printer_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gdpr_requests`
--
ALTER TABLE `gdpr_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `newsletter_subscriptions`
--
ALTER TABLE `newsletter_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `printer_configs`
--
ALTER TABLE `printer_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addons`
--
ALTER TABLE `addons`
  ADD CONSTRAINT `addons_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupons`
--
ALTER TABLE `coupons`
  ADD CONSTRAINT `coupons_used_by_foreign` FOREIGN KEY (`used_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gdpr_requests`
--
ALTER TABLE `gdpr_requests`
  ADD CONSTRAINT `gdpr_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
