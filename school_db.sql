-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.0
-- Время создания: Фев 05 2026 г., 17:00
-- Версия сервера: 8.0.35
-- Версия PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Структура таблицы `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `hours` int DEFAULT NULL,
  `price` decimal(6,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `img` varchar(255) NOT NULL
) ;

--
-- Дамп данных таблицы `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `hours`, `price`, `start_date`, `end_date`, `img`) VALUES
(2, 'Python для начинающих', 'Основы программирования на Python', 6, 1200.00, '2026-02-10', '2026-03-20', 'python.jpg'),
(3, 'SQL и базы данных', 'Работа с MySQL и PostgreSQL', 5, 900.00, '2026-02-15', '2026-03-10', 'sql.jpg'),
(4, 'React.js Frontend', 'Современные фронтенд технологии', 10, 2000.00, '2026-03-01', '2026-04-10', 'react.jpg'),
(5, 'Node.js Backend', 'Серверная разработка JavaScript', 7, 1800.00, '2026-03-05', '2026-04-15', 'node.jpg'),
(6, 'Docker и DevOps', 'Контейнеризация приложений', 4, 2500.00, '2026-03-10', '2026-04-05', 'docker.jpg'),
(7, 'Алгоритмы и структуры данных', 'Подготовка к собеседованиям', 9, 2200.00, '2026-02-20', '2026-04-01', 'algo.jpg'),
(8, 'Тестирование ПО', 'Unit, Integration, E2E тесты', 5, 1100.00, '2026-02-25', '2026-03-25', 'test.jpg'),
(10, 'PHP and SQLрррррррр', 'PHP', 10, 323.00, '2026-02-12', '2026-02-13', 'mpic_1770054393_0.13978900 1770054393.jpg'),
(12, 'igliguihil', 'jjoikjl;k;kl;kl', 9, 1615.00, '2026-02-21', '2026-02-26', 'mpic_1770119978_0.49888200 1770119978.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `lessons`
--

CREATE TABLE `lessons` (
  `id` int NOT NULL,
  `id_course` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `video_link` varchar(255) DEFAULT NULL,
  `hours` int NOT NULL
) ;

--
-- Дамп данных таблицы `lessons`
--

INSERT INTO `lessons` (`id`, `id_course`, `name`, `description`, `video_link`, `hours`) VALUES
(5, 2, 'Функции и модули', 'ООП и библиотеки', 'https://youtube.com/watch?v=py2', 2),
(6, 2, 'Работа с файлами', 'JSON, CSV, базы данных', 'https://youtube.com/watch?v=py3', 2),
(7, 3, 'Основы SELECT', 'Запросы и фильтрация', NULL, 2),
(8, 3, 'JOIN и группировка', 'Объединение таблиц', 'https://youtube.com/watch?v=sql2', 2),
(9, 3, 'Индексы и оптимизация', 'Производительность запросов', NULL, 1),
(10, 4, 'React компоненты', 'JSX и props', 'https://youtube.com/watch?v=react1', 3),
(11, 4, 'React Hooks', 'useState, useEffect', 'https://youtube.com/watch?v=react2', 4),
(12, 4, 'React Router', 'Навигация SPA', 'https://youtube.com/watch?v=react3', 2),
(15, 3, 'кенекнкун', 'уененкунке', '', 3),
(16, 3, 'лонленгл', 'нелнеглн', '', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id_order` int NOT NULL,
  `id_user` int NOT NULL,
  `id_course` int NOT NULL,
  `id_status_payment` int NOT NULL DEFAULT '1',
  `date_order` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id_order`, `id_user`, `id_course`, `id_status_payment`, `date_order`) VALUES
(7, 1, 3, 1, '2026-01-29');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id_role` int NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id_role`, `role`) VALUES
(2, 'admin'),
(1, 'student');

-- --------------------------------------------------------

--
-- Структура таблицы `statuses_payment`
--

CREATE TABLE `statuses_payment` (
  `id_status_payment` int NOT NULL,
  `status_payment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `statuses_payment`
--

INSERT INTO `statuses_payment` (`id_status_payment`, `status_payment`) VALUES
(1, 'ожидает оплаты'),
(2, 'оплачено'),
(3, 'ошибка оплаты');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `id_role` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_user`, `id_role`, `name`, `email`, `password`) VALUES
(1, 1, 'Алексей', 'alex@mail.ru', '$2y$10$4Pv6rafPOct9wATR/o7tJOl2AHuk63Z1u4Bs4ivDhYsR39pJ3yZAm');

-- --------------------------------------------------------

--
-- Структура таблицы `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id_token` int NOT NULL,
  `id_user` int NOT NULL,
  `token` varchar(500) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user_tokens`
--

INSERT INTO `user_tokens` (`id_token`, `id_user`, `token`, `created_at`, `expires_at`) VALUES
(1, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3Njk2ODM5MDIsImV4cCI6MTc2OTY4NzUwMiwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.-Sk5PoGGJ6TQiVgi7b8XkvjoBc6aDs0WjkUt1v8pwX4', '2026-01-29 13:51:42', '2026-01-29 14:51:42'),
(2, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3Njk2ODg0NjEsImV4cCI6MTc2OTY5MjA2MSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.KR8-OzMC6c9ZVAZdZJXg_JU8dZVeqTn664JB5dQkPCg', '2026-01-29 15:07:41', '2026-01-29 16:07:41'),
(3, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3Njk2OTIzOTIsImV4cCI6MTc2OTY5NTk5MiwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.xbko2rTeRRDxX82yte76arXh84TIVl4qVorerEarnSM', '2026-01-29 16:13:12', '2026-01-29 17:13:12'),
(4, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDAzNDUsImV4cCI6MTc3MDA0Mzk0NSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.mfBHy5WBCLHSihiLTh317UhsvSP6D_f86W8BSJV0w5w', '2026-02-02 16:52:25', '2026-02-02 17:52:25'),
(5, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDE2ODksImV4cCI6MTc3MDA0NTI4OSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.oE5ypk9-XOeEYxQZnsNvK4PYJ2xpdukZvPgU0lHbFl0', '2026-02-02 14:14:49', '2026-02-02 15:14:49'),
(6, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDE3MDEsImV4cCI6MTc3MDA0NTMwMSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.z_BPEvPYJP2DyG8wc6mhstTCus3y9NBycbOgbUHKB_c', '2026-02-02 14:15:01', '2026-02-02 15:15:01'),
(7, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDIxODUsImV4cCI6MTc3MDA0NTc4NSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.LXgcvSqB-AEWNC8wMzmk9gVwhY30GO0JKIKRtKM1L3k', '2026-02-02 17:23:05', '2026-02-02 18:23:05'),
(8, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDIxOTEsImV4cCI6MTc3MDA0NTc5MSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.wqJfIEY_cBLtEgndYZYw4PFvh93kA-cA22f_aA9DEyg', '2026-02-02 17:23:11', '2026-02-02 18:23:11'),
(9, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDIyMjMsImV4cCI6MTc3MDA0NTgyMywiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.rZyFIZsKCLGM9k_WKsQtcqJxNoyi6UFIcuSMVXZdjZo', '2026-02-02 17:23:44', '2026-02-02 18:23:44'),
(10, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDIyOTksImV4cCI6MTc3MDA0NTg5OSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.ZlrLjRtY7TMkk6BFLxfvSDTSjgFcFaKNFzWjK2Pb-3g', '2026-02-02 17:24:59', '2026-02-02 18:24:59'),
(11, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDIzMTcsImV4cCI6MTc3MDA0NTkxNywiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.N6QnmIRi55Heo2MenKU6RWbqJ2pt0uZXHExuaaxYcHE', '2026-02-02 17:25:17', '2026-02-02 18:25:17'),
(12, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDIzMzAsImV4cCI6MTc3MDA0NTkzMCwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.64foLBZvrEycuV5Lz_FtMq-m4BokciqClxMtC-j4uMo', '2026-02-02 17:25:30', '2026-02-02 18:25:30'),
(13, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDMwMzUsImV4cCI6MTc3MDA0NjYzNSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.T1iAkDsXfw_sP1FwyBTNac3593qmzBaUl3VJGQ8Btfs', '2026-02-02 17:37:15', '2026-02-02 18:37:15'),
(14, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDMwNjUsImV4cCI6MTc3MDA0NjY2NSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.wmGT1NQrKTs9SZDhuds0QnT6MtP70_gAd3B2IiQQYUE', '2026-02-02 17:37:45', '2026-02-02 18:37:45'),
(15, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNDM3MzIsImV4cCI6MTc3MDA0NzMzMiwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.9ELybITWKXJTLQCVXOPN9WLtDFf70nvkO3eIytWRPYs', '2026-02-02 17:48:52', '2026-02-02 18:48:52'),
(16, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAwNTQxNzcsImV4cCI6MTc3MDA1Nzc3NywiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.8bIVITdj18Lcx-y3WCud8P4gcSCbSJfH-oQ6x234ks8', '2026-02-02 20:42:57', '2026-02-02 21:42:57'),
(17, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAxMDU2MzUsImV4cCI6MTc3MDEwOTIzNSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.nPLAv6wlPXdDleTF9jm0MhSAfMEsvtmPJK0YpmDYqt8', '2026-02-03 11:00:35', '2026-02-03 12:00:35'),
(18, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAxMDcwODYsImV4cCI6MTc3MDExMDY4NiwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.xGCecSecg4s5X0IWaBAqy0xnBnqGKthSn4aBbqbLHKA', '2026-02-03 11:24:46', '2026-02-03 12:24:46'),
(19, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAxOTc4MjgsImV4cCI6MTc3MDIwMTQyOCwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.wQO8POz5M4CtRTpr48AV7lVCD7edmKN27Lux4pkQQS0', '2026-02-04 12:37:09', '2026-02-04 13:37:09'),
(20, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAxOTc4MjksImV4cCI6MTc3MDIwMTQyOSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.e-zF-NBW1gWdmeEKb8Uz8P3uJ1JNLxph8QK88Sj6_JA', '2026-02-04 12:37:09', '2026-02-04 13:37:09'),
(21, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAxOTgwMDQsImV4cCI6MTc3MDIwMTYwNCwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.dffmGT_9gJiWoPjsCcugL-BNN_JMZnvuO5EKZNAzunk', '2026-02-04 12:40:04', '2026-02-04 13:40:04'),
(22, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAyNzQ4NzksImV4cCI6MTc3MDI3ODQ3OSwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.f6_WTo471NwnYMD2FoaEb2nZdhfNmJRMzTLl25GRcCQ', '2026-02-05 10:01:19', '2026-02-05 11:01:19'),
(23, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NzAyNzY0NDAsImV4cCI6MTc3MDI4MDA0MCwiZGF0YSI6eyJpZF91c2VyIjoxLCJuYW1lIjoiXHUwNDEwXHUwNDNiXHUwNDM1XHUwNDNhXHUwNDQxXHUwNDM1XHUwNDM5IiwiZW1haWwiOiJhbGV4QG1haWwucnUifX0.svexGiSNIBTSqtHTU67HKwaUimylb95CDasZokUCos4', '2026-02-05 10:27:20', '2026-02-05 11:27:20');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_course` (`id_course`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_course` (`id_course`),
  ADD KEY `id_status_payment` (`id_status_payment`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `role` (`role`);

--
-- Индексы таблицы `statuses_payment`
--
ALTER TABLE `statuses_payment`
  ADD PRIMARY KEY (`id_status_payment`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- Индексы таблицы `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `id_user` (`id_user`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `statuses_payment`
--
ALTER TABLE `statuses_payment`
  MODIFY `id_status_payment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id_token` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`id_course`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id_course`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`id_status_payment`) REFERENCES `statuses_payment` (`id_status_payment`) ON DELETE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
