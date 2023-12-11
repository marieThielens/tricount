-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 24 mai 2023 à 09:00
-- Version du serveur : 10.4.24-MariaDB
-- Version de PHP : 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `prwb_2223_a01`
--

-- --------------------------------------------------------

--
-- Structure de la table `operations`
--

CREATE TABLE `operations` (
  `id` int(11) NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `tricount` int(11) NOT NULL,
  `amount` double NOT NULL,
  `operation_date` date NOT NULL,
  `initiator` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `operations`
--

INSERT INTO `operations` (`id`, `title`, `tricount`, `amount`, `operation_date`, `initiator`, `created_at`) VALUES
(1, 'Colruyt', 4, 100, '2022-10-13', 2, '2022-10-13 19:09:18'),
(2, 'Plein essence', 4, 75, '2022-10-13', 1, '2022-10-13 20:10:41'),
(3, 'Grosses courses LIDL', 4, 212.47, '2022-10-13', 3, '2022-10-13 21:23:49'),
(4, 'Apéros', 4, 31.897456217, '2022-10-13', 1, '2022-10-13 23:51:20'),
(5, 'Boucherie', 4, 25.5, '2022-10-26', 2, '2022-10-26 09:59:56'),
(6, 'Loterie', 4, 35, '2022-10-26', 1, '2022-10-26 10:02:24'),
(9, 'repas', 6, 400, '2023-05-23', 1, '2023-05-23 19:03:53'),
(10, 'soirée', 6, 600, '2023-05-23', 2, '2023-05-23 19:05:09');

-- --------------------------------------------------------

--
-- Structure de la table `repartitions`
--

CREATE TABLE `repartitions` (
  `operation` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `repartitions`
--

INSERT INTO `repartitions` (`operation`, `user`, `weight`) VALUES
(1, 1, 1),
(1, 2, 1),
(2, 1, 1),
(2, 2, 1),
(3, 1, 2),
(3, 2, 1),
(3, 3, 1),
(4, 1, 1),
(4, 2, 2),
(4, 3, 3),
(5, 1, 2),
(5, 2, 1),
(5, 3, 1),
(6, 1, 1),
(6, 3, 1),
(9, 1, 1),
(9, 2, 1),
(9, 3, 1),
(9, 4, 1),
(9, 5, 1),
(10, 1, 1),
(10, 2, 1),
(10, 3, 1),
(10, 4, 1),
(10, 5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `repartition_templates`
--

CREATE TABLE `repartition_templates` (
  `id` int(11) NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `tricount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `repartition_templates`
--

INSERT INTO `repartition_templates` (`id`, `title`, `tricount`) VALUES
(2, 'Benoit ne paye rien', 4),
(1, 'Boris paye double', 4);

-- --------------------------------------------------------

--
-- Structure de la table `repartition_template_items`
--

CREATE TABLE `repartition_template_items` (
  `user` int(11) NOT NULL,
  `repartition_template` int(11) NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `repartition_template_items`
--

INSERT INTO `repartition_template_items` (`user`, `repartition_template`, `weight`) VALUES
(1, 1, 2),
(1, 2, 1),
(2, 1, 1),
(3, 1, 1),
(3, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `tricount` int(11) NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `subscriptions`
--

INSERT INTO `subscriptions` (`tricount`, `user`) VALUES
(1, 1),
(2, 1),
(2, 2),
(4, 1),
(4, 2),
(4, 3),
(5, 6),
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(6, 6),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 6),
(8, 1),
(8, 5),
(8, 6),
(8, 7),
(9, 5);

-- --------------------------------------------------------

--
-- Structure de la table `tricounts`
--

CREATE TABLE `tricounts` (
  `id` int(11) NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `creator` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `tricounts`
--

INSERT INTO `tricounts` (`id`, `title`, `description`, `created_at`, `creator`) VALUES
(1, 'Gers 2022', NULL, '2022-10-10 18:42:24', 1),
(2, 'Resto badminton', NULL, '2022-10-10 19:25:10', 1),
(4, 'Vacances', 'A la mer du nord', '2022-10-10 19:31:09', 1),
(5, 'Week-end à la mer', 'Avec les copains', '2023-05-23 06:59:04', 6),
(6, 'Soirée anniversaire', 'Je paie pas', '2023-05-23 06:59:41', 6),
(7, 'Guingette', 'Ca sent les vacances', '2023-05-23 07:06:09', 6),
(8, 'Guingette', 'Ca sent les vacances', '2024-05-23 08:54:50', 5),
(9, 'Week-end à la mer', 'Alone in the dark', '2024-05-23 08:56:48', 5);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `mail` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `hashed_password` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `iban` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `mail`, `hashed_password`, `full_name`, `role`, `iban`) VALUES
(1, 'boverhaegen@epfc.eu', '56ce92d1de4f05017cf03d6cd514d6d1', 'Boris', 'user', NULL),
(2, 'bepenelle@epfc.eu', '56ce92d1de4f05017cf03d6cd514d6d1', 'Benoît', 'user', NULL),
(3, 'xapigeolet@epfc.eu', '56ce92d1de4f05017cf03d6cd514d6d1', 'Xavier', 'user', NULL),
(4, 'mamichel@epfc.eu', '56ce92d1de4f05017cf03d6cd514d6d1', 'Marc', 'user', '1234'),
(5, 'thielens.marie@gmail.com', 'a59f987929a5b83e061eae36d82bc36f', 'marie', 'user', 'aa11111111111111'),
(6, 'benben@test.com', 'e38002ffda4d1d2608a8e935bb39a8f1', 'ben', 'user', 'aa11111111111111'),
(7, 'pierre@gmail.com', '56ce92d1de4f05017cf03d6cd514d6d1', 'pierre', 'user', 'aa11111111111111');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Initiator` (`initiator`),
  ADD KEY `Tricount` (`tricount`);

--
-- Index pour la table `repartitions`
--
ALTER TABLE `repartitions`
  ADD PRIMARY KEY (`operation`,`user`),
  ADD KEY `User` (`user`);

--
-- Index pour la table `repartition_templates`
--
ALTER TABLE `repartition_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Title` (`title`,`tricount`),
  ADD KEY `Tricount` (`tricount`);

--
-- Index pour la table `repartition_template_items`
--
ALTER TABLE `repartition_template_items`
  ADD PRIMARY KEY (`user`,`repartition_template`),
  ADD KEY `Distribution` (`repartition_template`);

--
-- Index pour la table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`tricount`,`user`),
  ADD KEY `User` (`user`);

--
-- Index pour la table `tricounts`
--
ALTER TABLE `tricounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Title` (`title`,`creator`),
  ADD KEY `Creator` (`creator`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Mail` (`mail`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `operations`
--
ALTER TABLE `operations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `repartition_templates`
--
ALTER TABLE `repartition_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `tricounts`
--
ALTER TABLE `tricounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `operations`
--
ALTER TABLE `operations`
  ADD CONSTRAINT `operations_ibfk_1` FOREIGN KEY (`initiator`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `operations_ibfk_2` FOREIGN KEY (`tricount`) REFERENCES `tricounts` (`id`);

--
-- Contraintes pour la table `repartitions`
--
ALTER TABLE `repartitions`
  ADD CONSTRAINT `repartitions_ibfk_1` FOREIGN KEY (`operation`) REFERENCES `operations` (`id`),
  ADD CONSTRAINT `repartitions_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `repartition_templates`
--
ALTER TABLE `repartition_templates`
  ADD CONSTRAINT `repartition_templates_ibfk_1` FOREIGN KEY (`tricount`) REFERENCES `tricounts` (`id`);

--
-- Contraintes pour la table `repartition_template_items`
--
ALTER TABLE `repartition_template_items`
  ADD CONSTRAINT `repartition_template_items_ibfk_1` FOREIGN KEY (`repartition_template`) REFERENCES `repartition_templates` (`id`),
  ADD CONSTRAINT `repartition_template_items_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`tricount`) REFERENCES `tricounts` (`id`),
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `tricounts`
--
ALTER TABLE `tricounts`
  ADD CONSTRAINT `tricounts_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
