-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 21 Cze 2020, 19:52
-- Wersja serwera: 10.4.11-MariaDB
-- Wersja PHP: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `expencemanager`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `expences`
--

CREATE TABLE `expences` (
  `expence_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `ammount` float NOT NULL,
  `category_id` int(11) NOT NULL COMMENT 'id_kategorii_wydatku',
  `user_id` int(11) NOT NULL,
  `comment` text COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `expences`
--

INSERT INTO `expences` (`expence_id`, `date`, `ammount`, `category_id`, `user_id`, `comment`) VALUES
(1, '2020-06-01', 20, 2, 1, 'chleb i masło'),
(2, '2020-06-02', 115.07, 2, 1, 'zakupy tygodniowe'),
(5, '2020-06-10', 80, 1, 1, ''),
(6, '2020-06-03', 99.03, 2, 1, ''),
(7, '2020-06-25', 34.67, 2, 1, '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `expence_categories`
--

CREATE TABLE `expence_categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` text COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `expence_categories`
--

INSERT INTO `expence_categories` (`cat_id`, `cat_name`) VALUES
(1, 'samochód'),
(2, 'jedzenie'),
(3, 'dom');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `incomes`
--

CREATE TABLE `incomes` (
  `income_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `ammount` float NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `income_categories`
--

CREATE TABLE `income_categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` text COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `login` text COLLATE utf8_polish_ci NOT NULL,
  `password` text COLLATE utf8_polish_ci NOT NULL,
  `name` text COLLATE utf8_polish_ci NOT NULL,
  `mail` text COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`user_id`, `login`, `password`, `name`, `mail`) VALUES
(1, 'endriu', '12345', 'Andrzej', 'konicki@gmail.com'),
(2, 'justyna', '12345', 'Justynka', 'justynka@gmail.com');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `expences`
--
ALTER TABLE `expences`
  ADD PRIMARY KEY (`expence_id`);

--
-- Indeksy dla tabeli `expence_categories`
--
ALTER TABLE `expence_categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indeksy dla tabeli `incomes`
--
ALTER TABLE `incomes`
  ADD PRIMARY KEY (`income_id`);

--
-- Indeksy dla tabeli `income_categories`
--
ALTER TABLE `income_categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `expences`
--
ALTER TABLE `expences`
  MODIFY `expence_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `expence_categories`
--
ALTER TABLE `expence_categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `incomes`
--
ALTER TABLE `incomes`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `income_categories`
--
ALTER TABLE `income_categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
