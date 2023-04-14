-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 14 2023 г., 22:30
-- Версия сервера: 8.0.30
-- Версия PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `chat`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dialogues`
--

CREATE TABLE `dialogues` (
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `dialogues`
--

INSERT INTO `dialogues` (`id`) VALUES
(1);

-- --------------------------------------------------------

--
-- Структура таблицы `statuses`
--

CREATE TABLE `statuses` (
  `id` int NOT NULL,
  `title` varchar(16) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `statuses`
--

INSERT INTO `statuses` (`id`, `title`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(26) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `hash` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status_id` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `hash`, `status_id`) VALUES
(12, 'admin', '$2y$10$HZuEH64ETCdVcglTNRmINuEVbrz.vAeAHKHtCYDOHcwx57qZhbmiK', '5e65862fde52c90f866cdf8ded80d5ef6e53a14cb6f5c1f1f971bde30231e9a0', 2),
(13, 'admin2', '$2y$10$5hsY.BeoqhLGCFIzc84qGee2S2XqqqTL4jas1pJ2QTFoudgngnW76', 'cc09411fd98f73fc8585537ea4495057f146bce180679f9fb2e9fafe84486f31', 2),
(14, 'admin222', '$2y$10$gvoihXIZ5/WLsFo9pf9Rp.ToBYwiXmZO65uFhu2C1lXwHIBoMAsQ.', 'c6a64e456bdbe9997659e3fad3e6f3312c4711619e87a5c364bbc698f057f31d', 2),
(15, 'user123', '$2y$10$aRXrScFCml.lKltWMk0LxeObQzVVdlzdDf4cnKMy5OHmtznsKuujm', 'bb3c90e7e9a15d78313e48bf767e5a62b92796de35cf88cf998c0ada051669bc', 2),
(16, 'newUser123', '$2y$10$3GJD6/FZoxQe1JLTsl6Gnen7I4PTflXeWARK2nZAq8Xup2.qBh5V2', 'bd77c0faa1e94e9d8d6fbd34e6e7e5a0dfbc3df5792160c4cfea5d285f42f2e9', 2),
(17, 'qwerty123', '$2y$10$SOXR6P4OxSzX7e.NYr13TeMSivyWuGS.07onxmR4w.L8S.MfrqLJS', '669ca96b12401b1e934f058b358490fd040fa8385074646b4b09452a6fef125a', 2),
(18, 'manager', '$2y$10$rr.fQf1gZpy3L5RWfM3Y/u4PUCBA7HVb79VU4QfBKAmpWWdePDX5i', 'e169428098e97a6fcf200ec8217db068d7cd440d7018aa9e4caae4e2d56d678e', 2);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dialogues`
--
ALTER TABLE `dialogues`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `dialogues`
--
ALTER TABLE `dialogues`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
