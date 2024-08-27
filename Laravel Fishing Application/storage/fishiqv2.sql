-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2024 at 09:01 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fishiqv2`
--

-- --------------------------------------------------------

--
-- Table structure for table `alocare_stands`
--

CREATE TABLE `alocare_stands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `stand_id` bigint(20) UNSIGNED NOT NULL,
  `pescar_id` bigint(20) UNSIGNED NOT NULL,
  `sector_id` bigint(20) UNSIGNED NOT NULL,
  `concurs_id` bigint(20) UNSIGNED NOT NULL,
  `lac_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alocare_stands`
--

INSERT INTO `alocare_stands` (`id`, `created_by`, `stand_id`, `pescar_id`, `sector_id`, `concurs_id`, `lac_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 50, 1, '2024-02-28 21:52:17', '2024-02-28 21:52:17'),
(2, 1, 3, 1, 3, 50, 2, '2024-02-28 22:09:35', '2024-02-28 22:09:58'),
(3, 4, 1, 4, 1, 50, 1, '2024-02-28 22:15:35', '2024-02-28 22:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `cantars`
--

CREATE TABLE `cantars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `stand_id` bigint(20) UNSIGNED NOT NULL,
  `concurs_id` bigint(20) UNSIGNED NOT NULL,
  `lac_id` bigint(20) UNSIGNED NOT NULL,
  `cantitate` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cantars`
--

INSERT INTO `cantars` (`id`, `created_by`, `stand_id`, `concurs_id`, `lac_id`, `cantitate`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 50, 1, '12.4', '2024-02-28 21:59:19', '2024-02-28 21:59:19'),
(2, 4, 1, 50, 1, '31', '2024-02-28 22:16:04', '2024-02-28 22:16:04');

-- --------------------------------------------------------

--
-- Table structure for table `concurs`
--

CREATE TABLE `concurs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `nume` varchar(255) NOT NULL,
  `organizator_id` bigint(20) UNSIGNED NOT NULL,
  `descriere` text NOT NULL,
  `regulament` text NOT NULL,
  `poza` text NOT NULL,
  `start` text NOT NULL,
  `stop` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `concurs`
--

INSERT INTO `concurs` (`id`, `created_by`, `nume`, `organizator_id`, `descriere`, `regulament`, `poza`, `start`, `stop`, `created_at`, `updated_at`) VALUES
(1, 1, 'Concurs Tiana Cummerata', 1, 'Ut est vel aspernatur ipsam aliquid sed cum. Quaerat provident natus esse dolorum. Delectus fugit non dolore eius non hic.', 'Quo molestias delectus culpa natus qui error voluptas. Reiciendis accusamus autem consequatur officiis sunt optio sit. Magnam sunt numquam expedita facere.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(2, 1, 'Concurs Merle Feeney', 1, 'Dicta in earum maiores ut. Nulla deleniti tempore maiores eos maxime. Eius quia quia assumenda dolor aperiam quo a.', 'Quisquam officiis ut sit debitis omnis et ut. Qui labore reiciendis asperiores consectetur eveniet consectetur. Facere magni est sapiente voluptatem ad.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(3, 1, 'Concurs Blanca McKenzie V', 1, 'Eum odit temporibus voluptate explicabo quam velit voluptates enim. Rerum beatae nam rerum repudiandae vel ad. Sint fuga quas adipisci at qui.', 'Iusto neque vero libero aut voluptatibus. Voluptas dolor qui sit alias tempora alias incidunt. Soluta et perferendis dolorem consequuntur qui inventore reprehenderit.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(4, 1, 'Concurs Larry Kunze', 1, 'Aspernatur aperiam sequi atque id. Amet error itaque ut.', 'Voluptas quis quis quos quis. Eum ut corporis autem qui numquam ut. Qui aliquam deserunt sunt iste dignissimos sed. In quibusdam recusandae reprehenderit occaecati rem sed.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(5, 1, 'Concurs Titus Emard', 1, 'Qui non rem cumque occaecati quaerat suscipit inventore. Voluptatum similique possimus qui sit reprehenderit perspiciatis qui. Provident at omnis fuga maxime aut.', 'Tempora soluta et omnis qui ut qui incidunt ad. Sit sint quia nam in voluptatem cum iure. Corrupti laborum sunt minus animi ipsum libero. Ea ullam pariatur et voluptatem.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(6, 1, 'Concurs Ila Keebler IV', 1, 'Ipsa quod illum et quis amet. Nemo aut repellat debitis ut. Provident aspernatur laborum modi maiores recusandae.', 'Error vel reiciendis occaecati incidunt. Voluptatem quo illum totam quod omnis dolorum. Earum quas nobis nobis fugiat placeat. Sit quo ullam possimus dolor.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(7, 1, 'Concurs Bret Christiansen V', 1, 'Quis quos molestiae voluptatem eius velit qui. Eos enim rerum qui sequi voluptas. Facere minus suscipit necessitatibus eos. Sunt sit quis ut corporis. Aut quia ducimus quia voluptatem.', 'Totam at harum repudiandae culpa ut. Vel aut optio quos laudantium. Molestiae quo quod doloribus eos doloribus sed esse.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(8, 1, 'Concurs Prof. Sonya Bruen', 1, 'Autem voluptatem similique dolores omnis. Modi illum sit quam doloribus vel saepe. Et quia nesciunt vel omnis totam odit.', 'Explicabo voluptates est id quia occaecati reiciendis. Dicta a et distinctio et. Suscipit magni odio rerum laudantium in quia. Dolore vero sit iure quo dolorum nemo. Eos sed ut voluptas qui aut.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(9, 1, 'Concurs Adriel Jakubowski', 1, 'Esse quidem tenetur voluptatem. Totam reiciendis laudantium vel ipsam. Nisi qui ipsa dolor et ullam maiores.', 'Fugit atque minus provident qui. Hic est veniam vel. Dicta sit minus enim delectus.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(10, 1, 'Concurs Dr. Lincoln Bode', 1, 'Et est voluptatum sed ut ea officia illo. Et aut temporibus dolorum ipsa omnis eos praesentium. Dolorem incidunt dolores voluptatibus. Aliquam ipsam quis ducimus tempora perspiciatis.', 'Quia aut vero veritatis. Necessitatibus facilis nihil saepe iste. Maiores recusandae cupiditate commodi enim ut et. Facilis quia maiores voluptatem rerum.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(11, 1, 'Concurs Mr. Olen Fritsch', 1, 'Occaecati quis quis qui voluptas at tempore ut labore. Rerum vero quibusdam non cum fugiat. Sed veritatis dicta fugiat qui non sunt.', 'Itaque quas aut veritatis et est cupiditate omnis. Molestias officiis vero et iure et iusto expedita. Enim magnam illum cum asperiores autem.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(12, 1, 'Concurs Ansel Eichmann', 1, 'Est sapiente tempora quas aperiam. A maiores sint qui. Qui veniam natus est minus eos. Molestiae et dolore deleniti tempore. Nesciunt ipsam praesentium veritatis cumque nostrum nihil.', 'Dicta et repudiandae non commodi vitae. Qui sed voluptatem et aspernatur ea sit ut voluptatem. Est delectus consectetur sit quia natus. Voluptatem omnis nemo commodi sint.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(13, 1, 'Concurs Ahmad Collins', 1, 'Optio a quo sit aut. Eius voluptas nulla culpa. Consequatur accusamus iste velit laudantium nemo. Ut et quidem placeat in quisquam est quis amet.', 'Eum accusantium a fugit recusandae quia. Esse aut sint aut adipisci officiis dolorem dolorum. Dolore iure nam provident eveniet.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(14, 1, 'Concurs Elvie Pacocha', 1, 'Aperiam voluptas cupiditate vel in natus est dolorem. Odit rerum facere accusamus quia. Quis fugiat enim deserunt non dolorem ducimus.', 'Odit et adipisci et et repudiandae fugiat veritatis. Quia perferendis veritatis quo consequatur facilis error veniam dolor. Ea quo ut qui voluptatibus et labore rerum.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(15, 1, 'Concurs Cindy Champlin', 1, 'Modi vel non totam harum culpa quisquam voluptas perspiciatis. Exercitationem quisquam non accusamus commodi amet. Dicta ducimus accusamus sit minima. Animi iste voluptatum quis iste quo eos.', 'Explicabo sint sunt itaque placeat est commodi aut. Molestiae dolores sequi a quidem. A quam laudantium exercitationem qui nobis.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(16, 1, 'Concurs Prof. Viola Bosco III', 1, 'Eveniet ipsa qui dolor ut non consectetur dicta. Ex fugiat commodi maxime dolorum. Unde sapiente tempora debitis officiis velit. Possimus voluptatem sed voluptates consequuntur quia eum omnis.', 'Voluptatibus quia eaque autem exercitationem laborum nemo. Repellendus ducimus dolor sapiente sequi harum.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(17, 1, 'Concurs Marisa Kiehn', 1, 'Quia nobis optio laboriosam ipsum officia minus. Illo sed totam quibusdam neque molestiae. A soluta inventore voluptatem natus et quo.', 'Ipsum quidem odio aut sunt modi voluptate. Et explicabo perferendis quis non. Voluptatem inventore ratione sit atque vero. Id facere qui a minima tenetur.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(18, 1, 'Concurs Kian Nienow', 1, 'Minima provident quia est ex aliquam quae. Unde distinctio molestiae quos recusandae harum est. Consequatur id numquam architecto fugiat.', 'Qui tenetur molestiae quibusdam nobis saepe corrupti odio. Quam dolor in dignissimos eum beatae alias.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(19, 1, 'Concurs Eliane McLaughlin', 1, 'Minus et consequatur aut et. At necessitatibus atque enim vel. Voluptas quas sed tenetur non distinctio. Distinctio officia facilis qui velit inventore provident. Cum esse qui assumenda autem.', 'Sequi aut porro ab et. Non veniam dolores commodi voluptas. Accusamus et nobis rerum consectetur nihil odit et.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(20, 1, 'Concurs Dr. Urban Gottlieb', 1, 'Asperiores quia deserunt sunt dicta. Perspiciatis officiis perspiciatis ut qui et. Qui fugiat labore velit. Animi consequatur qui vel ipsum ea voluptatem nobis.', 'Modi voluptas id aliquam voluptas. Quaerat et totam nobis autem sint modi est tenetur. Iusto animi repellendus repudiandae consequatur. Blanditiis animi voluptatibus quo consequatur.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(21, 1, 'Concurs Erwin Powlowski', 1, 'Sit ipsam consectetur reiciendis nam suscipit animi. Veritatis vel quod voluptatibus et vel qui deserunt fugiat. Voluptatem assumenda quam corrupti illo. Vero voluptas cumque eum ea praesentium vero.', 'Occaecati est et ut. Est labore et iste quia voluptatem. Sed aliquam in aliquid officiis vel. Sed vero perferendis culpa provident.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(22, 1, 'Concurs Adeline Abshire', 1, 'Quis perspiciatis voluptatem iusto amet quia maiores. Blanditiis veritatis magni provident. Quia praesentium recusandae possimus sunt ea.', 'Ut eaque voluptatem id doloremque illum a. Et excepturi ratione perspiciatis hic.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(23, 1, 'Concurs Santino Bartell', 1, 'Eos voluptatem doloribus sit ab vel commodi. Voluptas eos amet dolorem omnis vero unde impedit dolor. Expedita at ullam illum.', 'Quia labore ipsam id quibusdam recusandae qui laudantium. Illum incidunt dolorem eos sapiente. Tenetur nihil ad laborum dolor et. Accusamus laboriosam qui provident beatae temporibus.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(24, 1, 'Concurs Mrs. Shayna Pouros Jr.', 1, 'Cum quaerat at ut aut qui et repudiandae quod. Aliquam et dolorem et. At consequatur doloribus rerum necessitatibus quis qui. Culpa a temporibus fugiat ut.', 'Et adipisci repudiandae fuga harum adipisci perspiciatis tenetur. Consequatur excepturi ad laudantium mollitia. Et rem optio nemo unde iste.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(25, 1, 'Concurs Brady Runolfsdottir', 1, 'Doloribus officia enim ut dolor. Possimus sed iusto sunt in consequuntur fugit vero. Voluptate sint odit eum nostrum et id ipsam similique.', 'Reiciendis voluptas odio rerum voluptatem incidunt est vitae. Cum molestiae aut asperiores. Quia quidem quidem inventore praesentium quos. Magni qui sed qui impedit.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(26, 1, 'Concurs Bobby Ritchie', 1, 'Explicabo ut eum dolores dolorum iure. Officia et illum velit dignissimos culpa facilis. Odit et saepe aut porro adipisci placeat veniam. Libero dolore expedita omnis rerum voluptatum quisquam rem.', 'Id qui rerum quaerat et architecto error. Odio est excepturi sed minima ipsum voluptatem ipsa at.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(27, 1, 'Concurs Bertrand Greenholt', 1, 'Consequatur quia et quisquam voluptas aut doloribus dolorum suscipit. Ut qui qui itaque ipsam. Quis rerum nesciunt dicta quo blanditiis qui nostrum.', 'Ut velit aut nulla sit. Neque ut aut ratione ex. Exercitationem suscipit necessitatibus porro et corporis qui. Facere repellendus veniam expedita qui ut id eligendi fuga.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(28, 1, 'Concurs Rudolph Daugherty V', 1, 'Numquam pariatur non amet natus rerum. Eius accusantium non ipsam similique optio. Dolorem recusandae culpa enim omnis placeat officia. Voluptate est harum facilis quam vero exercitationem quidem.', 'Sit omnis eum eum at aut tenetur. Sunt voluptatum voluptatem facilis. Et aut id quia sint sed praesentium voluptatem. Vitae qui ipsa aspernatur repellendus fugiat impedit est.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(29, 1, 'Concurs Damaris Berge III', 1, 'Est ut est voluptatem sequi quo. A autem nihil aut ad voluptatem. Rem sint quia officia consequatur modi voluptatem sequi.', 'Neque modi et aut blanditiis natus aut. Saepe et et vitae nisi aut ad vitae. Sunt impedit inventore veniam nihil sint ut nihil. Maxime rerum at consequatur ad asperiores.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(30, 1, 'Concurs Lorine Kassulke V', 1, 'Laborum dolor aut laborum autem soluta ducimus. Enim qui porro eveniet laborum neque sit. Quia fuga ipsum animi facilis eius qui est.', 'Occaecati ab rerum officiis explicabo. Voluptatem quis in unde quia occaecati. Temporibus ratione nesciunt fugit porro.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(31, 1, 'Concurs Miss Frida Reinger MD', 1, 'Fugit quasi in laboriosam optio id omnis quaerat. Maiores qui laborum quo sint et placeat eaque nihil. Magni consequatur nesciunt consectetur modi.', 'Iusto vel nisi accusamus exercitationem eligendi. Et omnis facilis nostrum voluptatem deserunt. Perspiciatis cumque distinctio nobis.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(32, 1, 'Concurs Georgianna Grady', 1, 'Ea et quam expedita iste reiciendis doloribus adipisci. Ea odit pariatur rerum laudantium dolores laborum. Et optio aperiam quia vero quisquam repudiandae.', 'Odit qui qui aut eveniet voluptas. Qui quidem iure minus.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(33, 1, 'Concurs Letha Mitchell', 1, 'Magni iure eius ut blanditiis. Quos doloremque corrupti explicabo odio et. Culpa accusantium quia nostrum neque consequatur.', 'Enim corrupti at ut asperiores ipsa. Modi beatae deserunt id error sit explicabo.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(34, 1, 'Concurs Liliane Schmidt DDS', 1, 'Ut quam et doloribus officiis asperiores natus quaerat. Non dolorem dolorum qui unde magni. Ex officiis sit iure harum est alias. Quae ipsam modi sed aut ea.', 'Aut praesentium et ad dolores ratione quo. Est minus magni et hic ipsum et doloribus. Nisi atque ex et illo aut. Et rem et voluptas occaecati velit reiciendis. Et veritatis ut odit alias.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(35, 1, 'Concurs Linda Cassin', 1, 'Soluta alias hic aspernatur rem velit. Qui eum quibusdam quam laborum cumque ipsum incidunt maiores.', 'Quasi sunt id est quod ullam. Optio aliquid eum quis voluptatem. Dicta eaque velit velit non. Est magnam magnam esse.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(36, 1, 'Concurs Emiliano Thompson', 1, 'Aut perspiciatis perspiciatis sequi hic sint nulla. Voluptatum reiciendis cum quia ratione non et. Reprehenderit quod cupiditate quaerat maiores repellat ea.', 'Modi officia sint et rerum delectus. Vitae impedit odit perspiciatis quas enim laborum libero voluptas. Et non similique omnis nihil est quo.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(37, 1, 'Concurs Desiree Kunde', 1, 'Ut voluptas eos quo nobis omnis laudantium sit odio. Voluptatum accusantium maxime atque qui. Voluptatibus ut doloribus soluta et voluptas aliquam.', 'Placeat animi error qui nisi non. Consequatur atque repellendus aut excepturi.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(38, 1, 'Concurs Hassie Moore', 1, 'Quas recusandae et explicabo accusamus saepe eius. Voluptatem aut ipsa delectus accusamus.', 'Dolorum dicta similique ipsa amet esse quo sed. Est et maxime a natus maiores ullam aliquid et. Itaque rem et aliquam sit unde quo eligendi.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(39, 1, 'Concurs June Mayer', 1, 'Eaque quo ut aperiam quos qui. Maiores et velit harum laudantium. Fuga doloremque qui et corrupti aspernatur.', 'Ut repellat officiis modi amet atque fugit provident atque. Qui provident unde possimus quia. Ullam maxime alias ipsa quam voluptatum omnis cupiditate.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(40, 1, 'Concurs Mrs. Lera Stanton', 1, 'Facere debitis tenetur dicta voluptatem aut quibusdam. Voluptate nostrum laboriosam ipsum aut ut excepturi aut.', 'Odio accusamus amet soluta labore dolores. Doloremque possimus quo sint dolorem. Quibusdam sint dolorem facilis natus quo quis. Natus et reiciendis qui commodi magnam.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(41, 1, 'Concurs Prof. Juanita Breitenberg', 1, 'Inventore asperiores voluptas aperiam itaque nihil. Totam quae laborum nihil quod ab facere. Omnis dignissimos velit eum eum in voluptates. Beatae omnis qui sunt iure ipsam sit numquam.', 'Tempore voluptas labore sed aut occaecati qui alias. Beatae voluptatem ut voluptatibus. Dolorem voluptas aliquam mollitia.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(42, 1, 'Concurs Mr. Osbaldo Kovacek', 1, 'Aliquid quo unde nam sunt molestiae nobis atque. Aut facilis modi non cupiditate est sunt.', 'Nulla odit debitis nisi qui. Eveniet deserunt consequatur placeat non ad illo libero iure. Porro voluptatem enim expedita.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(43, 1, 'Concurs Ms. Andreane Toy', 1, 'Maiores laboriosam voluptatibus vitae praesentium voluptate impedit aperiam. Voluptas aut qui possimus sit. Et culpa aliquid hic nulla repudiandae sint.', 'Optio aut aut odit nobis fugiat nihil omnis. Iure magnam dolores at ea corrupti. Doloremque et molestiae architecto ut et.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(44, 1, 'Concurs Eleonore Jacobson', 1, 'Eveniet illo aliquid aut officiis iste. Adipisci nisi in in atque amet. Nostrum quam praesentium omnis maxime voluptatum eum laborum error.', 'Quisquam rerum iure aperiam consequatur voluptatibus qui. Nisi eum dolor quis eum. Id perspiciatis consequatur voluptatum voluptatem unde sed totam.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(45, 1, 'Concurs Iliana Franecki', 1, 'Similique consequatur quaerat id aut veniam. Nisi eos quod nostrum ea voluptatem veniam. Ut autem illo atque sint et et vitae. Error quia inventore perferendis possimus.', 'Sunt omnis illum et molestiae aut sint. Animi eius voluptatem sequi deleniti dolor. Qui nam eveniet ipsum beatae voluptas quis sit. Ut accusantium beatae ipsam numquam iste quos non sunt.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(46, 1, 'Concurs Mrs. Maddison Bruen', 1, 'Asperiores nihil dolores dolore autem non a aut. Et iste culpa ipsum nostrum qui. Quaerat placeat et nesciunt voluptatibus quaerat quia.', 'Sit eaque aut ad veniam aut. Sed repellendus velit expedita accusamus.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(47, 1, 'Concurs Evangeline Hickle IV', 1, 'Natus ut quidem expedita qui nobis recusandae. Consequatur id totam natus corporis aperiam quas. Nam ipsa error qui inventore in impedit voluptas. Quidem atque ut eos consequatur modi.', 'Aspernatur eos nihil explicabo quod recusandae nulla amet. Porro in excepturi facilis illum sed ea libero. Consectetur dolorem assumenda optio error eveniet atque.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(48, 1, 'Concurs Angel Russel', 1, 'Doloribus delectus pariatur vel fugiat dolorem excepturi. Deserunt autem doloremque voluptas dignissimos natus facere dolorem. Laboriosam id corrupti modi accusantium ut.', 'Porro autem rerum dicta iste. Odio excepturi magnam incidunt autem quia est doloremque. Non explicabo consectetur porro quam.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(49, 1, 'Concurs Darrell Howe', 1, 'Quo dolores eveniet veniam ad quo. Quae a libero velit qui repellendus placeat. Illo et est harum expedita deleniti cumque nihil.', 'Ut qui aspernatur veniam amet. Voluptas facere esse dolorem est. Id sint rerum itaque voluptatem et iusto.', '5992037.jpg', '2024-02-28 23:44:37', '2024-02-28 23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:44:37'),
(50, 1, 'Concurs 1', 1, 'Velit qui nulla vero quasi. Quia autem error animi officiis nihil.', 'Doloribus magni qui itaque unde sed tempore. Laboriosam eum laborum non aspernatur quas. Qui quis quis est vitae. Ut in veniam animi ducimus rerum iusto. Consequatur cumque optio cumque ut.', 'ABLVV8~1.PNG', '2024-02-28T23:44:37', '2024-02-28T23:44:37', '2024-02-28 21:44:37', '2024-02-28 21:45:32');

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
-- Table structure for table `inscrieres`
--

CREATE TABLE `inscrieres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `pescar_id` bigint(20) UNSIGNED NOT NULL,
  `concurs_id` bigint(20) UNSIGNED NOT NULL,
  `mansa_id` bigint(20) UNSIGNED NOT NULL,
  `stand_id` bigint(20) UNSIGNED NOT NULL,
  `lac_id` bigint(20) UNSIGNED NOT NULL,
  `sector_id` bigint(20) UNSIGNED NOT NULL,
  `puncte_penalizare` varchar(255) NOT NULL,
  `nume_trofeu` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lacs`
--

CREATE TABLE `lacs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `nume` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lacs`
--

INSERT INTO `lacs` (`id`, `created_by`, `nume`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lac 1', '2024-02-28 21:46:06', '2024-02-28 21:46:06'),
(2, 1, 'Lac 2', '2024-02-28 21:46:14', '2024-02-28 21:46:14'),
(3, 1, 'Lac 3', '2024-02-28 21:46:22', '2024-02-28 21:46:22');

-- --------------------------------------------------------

--
-- Table structure for table `mansas`
--

CREATE TABLE `mansas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `concurs_id` bigint(20) UNSIGNED NOT NULL,
  `lac_id` bigint(20) UNSIGNED NOT NULL,
  `nume` varchar(255) NOT NULL,
  `start_mansa` varchar(255) NOT NULL,
  `stop_mansa` varchar(255) NOT NULL,
  `status_mansa` varchar(255) NOT NULL,
  `participanti` int(11) NOT NULL DEFAULT 0,
  `participanti_max` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mansas`
--

INSERT INTO `mansas` (`id`, `created_by`, `concurs_id`, `lac_id`, `nume`, `start_mansa`, `stop_mansa`, `status_mansa`, `participanti`, `participanti_max`, `created_at`, `updated_at`) VALUES
(1, 1, 50, 1, 'Mansa 1', '2024-02-29T12:12', '2024-03-01T12:12', 'Status 1', 0, 20, '2024-02-28 21:47:46', '2024-02-28 21:47:46'),
(2, 1, 50, 2, 'Mansa 2', '2024-03-02T12:12', '2024-03-03T12:12', 'Status 1', 0, 20, '2024-02-28 21:48:07', '2024-02-28 21:48:07');

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
(5, '2024_02_17_173747_create_lacs_table', 1),
(6, '2024_02_17_174036_create_mansas_table', 1),
(7, '2024_02_17_174649_create_stands_table', 1),
(8, '2024_02_17_175752_create_concurs_table', 1),
(9, '2024_02_17_192447_create_inscrieres_table', 1),
(10, '2024_02_17_220926_create_sectors_table', 1),
(11, '2024_02_18_004639_create_tokens_table', 1),
(12, '2024_02_18_011337_create_palmares_table', 1),
(13, '2024_02_27_151744_create_alocare_stands_table', 1),
(14, '2024_02_28_165631_create_cantars_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `palmares`
--

CREATE TABLE `palmares` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `luna` varchar(255) NOT NULL,
  `an` varchar(255) NOT NULL,
  `organizator` varchar(255) NOT NULL,
  `pescar` varchar(255) NOT NULL,
  `data_concurs` varchar(255) NOT NULL,
  `lac` varchar(255) NOT NULL,
  `nume_concurs` varchar(255) NOT NULL,
  `mansa` varchar(255) NOT NULL,
  `stand` varchar(255) NOT NULL,
  `cantitate` varchar(255) NOT NULL,
  `puncte` varchar(255) NOT NULL,
  `loc_sector` varchar(255) NOT NULL,
  `loc_general` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `sectors`
--

CREATE TABLE `sectors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `nume` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sectors`
--

INSERT INTO `sectors` (`id`, `created_by`, `nume`, `created_at`, `updated_at`) VALUES
(1, 1, 'A', '2024-02-28 21:50:03', '2024-02-28 21:50:03'),
(2, 1, 'B', '2024-02-28 21:50:07', '2024-02-28 21:50:07'),
(3, 1, 'C', '2024-02-28 21:50:11', '2024-02-28 21:50:11'),
(4, 1, 'D', '2024-02-28 21:50:15', '2024-02-28 21:50:15'),
(5, 1, 'E', '2024-02-28 21:50:19', '2024-02-28 21:50:19'),
(6, 1, 'F', '2024-02-28 21:50:23', '2024-02-28 21:50:23');

-- --------------------------------------------------------

--
-- Table structure for table `stands`
--

CREATE TABLE `stands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `nume` varchar(255) NOT NULL,
  `lac_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stands`
--

INSERT INTO `stands` (`id`, `created_by`, `nume`, `lac_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Stand 1', 1, '2024-02-28 21:46:40', '2024-02-28 21:48:56'),
(2, 1, 'Stand 1/2', 1, '2024-02-28 21:46:45', '2024-02-28 21:49:06'),
(3, 1, 'Stand 2', 2, '2024-02-28 21:46:52', '2024-02-28 21:49:15'),
(4, 1, 'Stand 2/2', 2, '2024-02-28 21:46:57', '2024-02-28 21:49:28'),
(5, 1, 'Stand 3', 3, '2024-02-28 21:47:02', '2024-02-28 21:49:39'),
(6, 1, 'Stand 3/2', 3, '2024-02-28 21:47:07', '2024-02-28 21:49:48');

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `expire_at` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prenume` varchar(255) NOT NULL,
  `nume` varchar(255) NOT NULL,
  `tip` varchar(255) NOT NULL DEFAULT 'Pescar',
  `email` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `data_nasterii` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `istoric_asociere` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `prenume`, `nume`, `tip`, `email`, `mobile`, `data_nasterii`, `sex`, `google_id`, `istoric_asociere`, `password`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Angel', 'Angel', 'Pescar', 'angelplaiesu@gmail.com', 'mobile', 'data', '1', NULL, NULL, '$2y$12$R2WhsXv.zxhYDwJA4okfAeD6A1xAp/vR.91G9eyImgztJAIUKw7B2', NULL, NULL, '2024-02-28 21:44:34', '2024-02-28 21:44:34'),
(4, 'Sergiu', 'Strugariu', 'Pescar', 'sstrugariu.7@icloud.com', 'mobile1', 'data', '1', NULL, NULL, '$2y$12$MeeQV0x7uBq5R4U4zp2lB.h26vifLtHe7s5Lr5GT1a6Ks4LnD95qa', NULL, NULL, '2024-02-28 22:14:45', '2024-02-28 22:14:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alocare_stands`
--
ALTER TABLE `alocare_stands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cantars`
--
ALTER TABLE `cantars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `concurs`
--
ALTER TABLE `concurs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inscrieres`
--
ALTER TABLE `inscrieres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lacs`
--
ALTER TABLE `lacs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mansas`
--
ALTER TABLE `mansas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `palmares`
--
ALTER TABLE `palmares`
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
-- Indexes for table `sectors`
--
ALTER TABLE `sectors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stands`
--
ALTER TABLE `stands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_mobile_unique` (`mobile`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alocare_stands`
--
ALTER TABLE `alocare_stands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cantars`
--
ALTER TABLE `cantars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `concurs`
--
ALTER TABLE `concurs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inscrieres`
--
ALTER TABLE `inscrieres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lacs`
--
ALTER TABLE `lacs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mansas`
--
ALTER TABLE `mansas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `palmares`
--
ALTER TABLE `palmares`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sectors`
--
ALTER TABLE `sectors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `stands`
--
ALTER TABLE `stands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
