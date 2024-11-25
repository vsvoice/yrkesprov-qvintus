-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 25 nov 2024 kl 12:06
-- Serverversion: 10.4.32-MariaDB
-- PHP-version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `2024_yrkesprov_qvintus`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `t_age_ranges`
--

CREATE TABLE `t_age_ranges` (
  `age_range_id` int(11) NOT NULL,
  `age_range_name` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_articles`
--

CREATE TABLE `t_articles` (
  `article_id` int(11) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `date_published` date NOT NULL,
  `article_image` varchar(255) NOT NULL,
  `visibility` tinyint(1) NOT NULL,
  `display` tinyint(1) NOT NULL,
  `user_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_authors`
--

CREATE TABLE `t_authors` (
  `author_id` int(11) NOT NULL,
  `author_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_books`
--

CREATE TABLE `t_books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_published` date NOT NULL,
  `page_amount` int(11) NOT NULL,
  `publisher_id_fk` int(11) NOT NULL,
  `age_range_id_fk` int(11) NOT NULL,
  `series_id_fk` int(11) NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `visibility` tinyint(1) NOT NULL,
  `display` tinyint(1) NOT NULL,
  `user_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_book_authors`
--

CREATE TABLE `t_book_authors` (
  `book_id_fk` int(11) NOT NULL,
  `author_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_book_catagories`
--

CREATE TABLE `t_book_catagories` (
  `book_id_fk` int(11) NOT NULL,
  `category_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_book_genres`
--

CREATE TABLE `t_book_genres` (
  `book_id_fk` int(11) NOT NULL,
  `genre_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_book_languages`
--

CREATE TABLE `t_book_languages` (
  `book_id_fk` int(11) NOT NULL,
  `language_id_fk` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_categories`
--

CREATE TABLE `t_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_genres`
--

CREATE TABLE `t_genres` (
  `genre_id` int(11) NOT NULL,
  `genre_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_languages`
--

CREATE TABLE `t_languages` (
  `language_id` int(11) NOT NULL,
  `language_name` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_publishers`
--

CREATE TABLE `t_publishers` (
  `publisher_id` int(11) NOT NULL,
  `publisher_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_roles`
--

CREATE TABLE `t_roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `role_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumpning av Data i tabell `t_roles`
--

INSERT INTO `t_roles` (`role_id`, `role_name`, `role_level`) VALUES
(1, 'Mekaniker', 10),
(2, 'Fakturerare', 50),
(3, 'Administratör', 200),
(4, 'Gäst', 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `t_series`
--

CREATE TABLE `t_series` (
  `series_id` int(11) NOT NULL,
  `series_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `t_users`
--

CREATE TABLE `t_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(512) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role_id_fk` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumpning av Data i tabell `t_users`
--

INSERT INTO `t_users` (`user_id`, `username`, `password`, `email`, `role_id_fk`, `fname`, `lname`, `status`) VALUES
(7, 'Bossman', '$2y$10$DKiD5M0Zzeic5M3GMUSLVekbvZezq8MjH7NU1EkHHwLphqXHPmlt2', 'bossman@gmail.com', 3, 'Bossi', 'Manninen', 1),
(8, 'mechanic1', '$2y$10$AGYChnNscS3zbTacwkGTAewQ5PrbJ9hvMzwAUQwf2Gi7qeUERet9i', 'mechanic1@gmail.com', 1, 'Juissi', 'Mehulainen', 1),
(10, 'mechanic2', '$2y$10$DIBW13D4nkR5HWLlelR.peUGWkXevZtrlQBWb4rT1BqbzLAM98Mk.', 'mechanic2@gmail.com', 1, 'Ärsyttävä', 'Appelsiini', 1),
(11, 'Mechanic3', '$2y$10$t9SZoO7c.7P5x2nghHLvV.A0mj/fIE0LtyhijLyv0NjKK0G/N6xlO', 'mechanic3@gmail.com', 1, 'Byggare', 'Bob', 1),
(12, 'Mechanic4', '$2y$10$c2.xH9/T2vqC/CQzUMXKSuvuH2zrq3GcoCP2x153Ysfx6H.t3xrEO', 'mechanic4@gmail.com', 1, 'Wreck-It', 'Ralph', 0),
(13, 'bossmannen', '$2y$10$GW7byae3DcLkYnwKPbuZVOcvEtVr8osvVAXHtRwKY2XRWTGS4lY/u', 'bossman2@gmail.com', 3, 'Bossi', 'Manninen', 1),
(14, 'accountant2', '$2y$10$tlrp7Q1hFgyjCW42DO85aen.Se/2dlNhSkzRjvBTCEFYlYX9O24LO', 'akku@money.com', 2, 'Akku', 'Kanttu', 1),
(15, 'testuser', '$2y$10$Nb/NjweZmGB6kdg7WFot/.aM/b3CE9rDaiS7GCZZyy0JX8h4RRAYe', 'test2@user.com', 1, 'Testa', 'Mig', 1);

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `t_age_ranges`
--
ALTER TABLE `t_age_ranges`
  ADD PRIMARY KEY (`age_range_id`);

--
-- Index för tabell `t_articles`
--
ALTER TABLE `t_articles`
  ADD PRIMARY KEY (`article_id`),
  ADD KEY `user_id_fk` (`user_id_fk`);

--
-- Index för tabell `t_authors`
--
ALTER TABLE `t_authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Index för tabell `t_books`
--
ALTER TABLE `t_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `user_id_fk` (`user_id_fk`),
  ADD KEY `publisher_id_fk` (`publisher_id_fk`,`age_range_id_fk`,`series_id_fk`),
  ADD KEY `age_range_id_fk` (`age_range_id_fk`),
  ADD KEY `series_id_fk` (`series_id_fk`);

--
-- Index för tabell `t_book_authors`
--
ALTER TABLE `t_book_authors`
  ADD KEY `book_id_fk` (`book_id_fk`,`author_id_fk`),
  ADD KEY `author_id_fk` (`author_id_fk`);

--
-- Index för tabell `t_book_catagories`
--
ALTER TABLE `t_book_catagories`
  ADD KEY `book_id_fk` (`book_id_fk`,`category_id_fk`),
  ADD KEY `category_id_fk` (`category_id_fk`);

--
-- Index för tabell `t_book_genres`
--
ALTER TABLE `t_book_genres`
  ADD KEY `book_id_fk` (`book_id_fk`,`genre_id_fk`),
  ADD KEY `genre_id_fk` (`genre_id_fk`);

--
-- Index för tabell `t_book_languages`
--
ALTER TABLE `t_book_languages`
  ADD KEY `book_id_fk` (`book_id_fk`,`language_id_fk`),
  ADD KEY `language_id_fk` (`language_id_fk`);

--
-- Index för tabell `t_categories`
--
ALTER TABLE `t_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Index för tabell `t_genres`
--
ALTER TABLE `t_genres`
  ADD PRIMARY KEY (`genre_id`);

--
-- Index för tabell `t_languages`
--
ALTER TABLE `t_languages`
  ADD PRIMARY KEY (`language_id`);

--
-- Index för tabell `t_publishers`
--
ALTER TABLE `t_publishers`
  ADD PRIMARY KEY (`publisher_id`);

--
-- Index för tabell `t_roles`
--
ALTER TABLE `t_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Index för tabell `t_series`
--
ALTER TABLE `t_series`
  ADD PRIMARY KEY (`series_id`);

--
-- Index för tabell `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `u_role_fk` (`role_id_fk`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `t_age_ranges`
--
ALTER TABLE `t_age_ranges`
  MODIFY `age_range_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_articles`
--
ALTER TABLE `t_articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_authors`
--
ALTER TABLE `t_authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_books`
--
ALTER TABLE `t_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_categories`
--
ALTER TABLE `t_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_genres`
--
ALTER TABLE `t_genres`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_languages`
--
ALTER TABLE `t_languages`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_publishers`
--
ALTER TABLE `t_publishers`
  MODIFY `publisher_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_roles`
--
ALTER TABLE `t_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT för tabell `t_series`
--
ALTER TABLE `t_series`
  MODIFY `series_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT för tabell `t_users`
--
ALTER TABLE `t_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `t_articles`
--
ALTER TABLE `t_articles`
  ADD CONSTRAINT `t_articles_ibfk_1` FOREIGN KEY (`user_id_fk`) REFERENCES `t_users` (`user_id`);

--
-- Restriktioner för tabell `t_books`
--
ALTER TABLE `t_books`
  ADD CONSTRAINT `t_books_ibfk_1` FOREIGN KEY (`user_id_fk`) REFERENCES `t_users` (`user_id`),
  ADD CONSTRAINT `t_books_ibfk_2` FOREIGN KEY (`publisher_id_fk`) REFERENCES `t_publishers` (`publisher_id`),
  ADD CONSTRAINT `t_books_ibfk_3` FOREIGN KEY (`age_range_id_fk`) REFERENCES `t_age_ranges` (`age_range_id`),
  ADD CONSTRAINT `t_books_ibfk_4` FOREIGN KEY (`series_id_fk`) REFERENCES `t_series` (`series_id`);

--
-- Restriktioner för tabell `t_book_authors`
--
ALTER TABLE `t_book_authors`
  ADD CONSTRAINT `t_book_authors_ibfk_1` FOREIGN KEY (`book_id_fk`) REFERENCES `t_books` (`book_id`),
  ADD CONSTRAINT `t_book_authors_ibfk_2` FOREIGN KEY (`author_id_fk`) REFERENCES `t_authors` (`author_id`);

--
-- Restriktioner för tabell `t_book_catagories`
--
ALTER TABLE `t_book_catagories`
  ADD CONSTRAINT `t_book_catagories_ibfk_1` FOREIGN KEY (`book_id_fk`) REFERENCES `t_books` (`book_id`),
  ADD CONSTRAINT `t_book_catagories_ibfk_2` FOREIGN KEY (`category_id_fk`) REFERENCES `t_categories` (`category_id`);

--
-- Restriktioner för tabell `t_book_genres`
--
ALTER TABLE `t_book_genres`
  ADD CONSTRAINT `t_book_genres_ibfk_1` FOREIGN KEY (`book_id_fk`) REFERENCES `t_books` (`book_id`),
  ADD CONSTRAINT `t_book_genres_ibfk_2` FOREIGN KEY (`genre_id_fk`) REFERENCES `t_genres` (`genre_id`);

--
-- Restriktioner för tabell `t_book_languages`
--
ALTER TABLE `t_book_languages`
  ADD CONSTRAINT `t_book_languages_ibfk_1` FOREIGN KEY (`book_id_fk`) REFERENCES `t_books` (`book_id`),
  ADD CONSTRAINT `t_book_languages_ibfk_2` FOREIGN KEY (`language_id_fk`) REFERENCES `t_languages` (`language_id`);

--
-- Restriktioner för tabell `t_users`
--
ALTER TABLE `t_users`
  ADD CONSTRAINT `t_users_ibfk_1` FOREIGN KEY (`role_id_fk`) REFERENCES `t_roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
