-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 01, 2026 at 06:13 AM
-- Server version: 8.0.46-0ubuntu0.24.04.3
-- PHP Version: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project1`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','approved','rejected','hidden') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `user_id`, `category_id`, `title`, `slug`, `thumbnail`, `description`, `status`, `created_at`) VALUES
(2, 11, 5, 'test 2', 'test-2-1781152863', 'img_6a2a3c5facc166.86694935.jpeg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam congue eros tortor, quis consequat massa tincidunt non. Suspendisse potenti. Maecenas quis luctus mauris, nec mattis eros. Duis pulvinar ipsum vitae dui molestie, a sagittis arcu ornare. Quisque scelerisque mi a imperdiet dapibus. Quisque ac ultricies mauris, nec semper enim. Duis vitae felis libero. Duis tincidunt quam mollis ultricies porta. Pellentesque sed ante feugiat diam semper mollis. Vestibulum a iaculis lorem. Praesent semper elementum blandit. Curabitur ultrices augue elit, ut sodales diam ullamcorper vitae. Etiam eu sapien arcu. Suspendisse ut convallis mauris. Fusce sed auctor enim. Morbi non mi sed lectus tempus finibus.\r\n\r\nFusce elementum pulvinar erat, id venenatis risus bibendum eu. Vestibulum pulvinar ornare sem at tincidunt. Nulla ipsum ex, dignissim vel nulla eu, vehicula pellentesque orci. Aenean eget porta tellus. Ut sit amet purus ac felis lacinia facilisis ac in elit. Morbi eu urna lobortis, volutpat dolor et, vehicula lorem. Vivamus vulputate hendrerit odio, ut aliquam sapien tristique ac. Fusce vitae eleifend diam, eget maximus est. In malesuada sapien orci, vitae molestie sapien imperdiet maximus. Proin ex magna, sollicitudin vitae enim et, pulvinar fringilla magna. Cras pretium ante nunc, eu ultricies sem tempor in. Proin porta ex consectetur libero ultrices, sed mattis dui luctus. Ut eget commodo nisi. Aenean commodo nisl nibh, nec mollis metus tempor ac.\r\n\r\nDuis laoreet lorem et quam consequat, vel viverra magna volutpat. Proin egestas at ipsum quis accumsan. Integer sit amet urna euismod quam euismod vulputate nec non purus. Suspendisse ac consectetur enim. Proin dignissim erat vel gravida interdum. Quisque aliquet gravida urna at lobortis. Donec a semper risus. Donec sed tellus ut sapien vehicula cursus. Integer a dignissim mauris, eu varius dui. Suspendisse suscipit at libero non luctus. Integer dictum vehicula finibus. Phasellus mattis libero ipsum. Nunc quis malesuada dui.\r\n\r\nCurabitur non porta est. Sed dictum mauris eu ante luctus, et laoreet sem auctor. Suspendisse potenti. Quisque luctus elit ipsum, feugiat pharetra urna ultrices quis. Cras in magna rutrum, ullamcorper diam ut, aliquet ante. Etiam ut congue mi. Suspendisse eu felis non est elementum iaculis eu at ipsum. Pellentesque sit amet eros venenatis, iaculis augue vitae, laoreet mi. Fusce lacinia eleifend lacinia. Praesent tellus ligula, imperdiet aliquam lacus in, condimentum volutpat purus. Nulla suscipit libero ligula, vitae consectetur neque lobortis sed. Integer egestas sem id lorem efficitur, eu pellentesque neque volutpat. Fusce porttitor, libero a tristique rhoncus, est dui iaculis mi, non fermentum augue sapien eget felis. Vestibulum vel metus et massa vestibulum mollis. Sed urna velit, luctus eget massa in, hendrerit finibus elit.\r\n\r\nPraesent rhoncus mollis libero sit amet sodales. Phasellus nec arcu posuere, fermentum leo sed, ornare ipsum. Ut id dolor malesuada lorem volutpat congue. Suspendisse sit amet massa consectetur, vulputate sapien a, volutpat dui. Donec hendrerit vel diam eu gravida. Nam scelerisque tristique magna, nec posuere neque molestie at. Etiam sollicitudin ligula orci, vitae lacinia velit sollicitudin eu. Nullam eu finibus tortor. Sed et ornare lectus.\r\n\r\nProin sed volutpat tellus. Proin hendrerit sagittis diam, et sagittis dui porttitor quis. Aenean at dui elit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus laoreet justo eget risus ornare scelerisque. Fusce velit arcu, laoreet non facilisis in, accumsan at lectus. Cras eget aliquet orci. Aliquam laoreet porta ex, non auctor sem. Phasellus vitae lacinia lacus. Suspendisse mollis lacinia sodales. Nulla convallis velit arcu, ut laoreet nulla commodo at.\r\n\r\nAenean ultrices elit id tortor faucibus, at dignissim lorem sollicitudin. Sed sagittis volutpat dui, sed placerat lorem accumsan eu. Nullam at sem eu eros blandit varius. In hac habitasse platea dictumst. Cras tristique tristique tortor, ornare fringilla mauris pretium sed. Donec varius sollicitudin massa non egestas. Nam sagittis dui velit, in aliquet libero facilisis vel. Aliquam mattis in velit non accumsan. Suspendisse pellentesque congue nulla, sit amet lobortis eros fringilla ut. Nulla cursus lectus ut molestie hendrerit. Duis vel eros sed arcu porta fermentum eu ut tellus. Donec eu pulvinar est. Nullam vitae dolor sodales tellus iaculis venenatis eget at nibh.\r\n\r\nDonec eu elit sagittis elit viverra fringilla at sit amet ligula. Integer molestie augue quis nisl viverra tristique. Nam in bibendum diam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis porta risus velit, nec dapibus quam sagittis vel. Integer convallis egestas augue, vel consequat magna suscipit non. Nulla fermentum hendrerit maximus.\r\n\r\nNam quis interdum tortor. Praesent sollicitudin ligula at facilisis tempor. Mauris id efficitur lacus. Ut nec rutrum nunc. Fusce in vestibulum est, lobortis vestibulum mi. Morbi ullamcorper sodales laoreet. Duis sit amet leo ac odio dictum rutrum. Curabitur luctus commodo odio, ut hendrerit magna pretium quis. Phasellus in dolor facilisis, egestas ante dictum, auctor tellus. Aenean non rutrum felis. Fusce at dictum justo, dignissim malesuada elit. Nam efficitur neque vel lorem pharetra, ut pretium nisi ullamcorper. Ut ornare sagittis massa a pulvinar. Nunc lacinia ligula non varius iaculis. Phasellus eu risus eu justo elementum ultrices.\r\n\r\nPhasellus et augue aliquet, congue eros a, tincidunt neque. Suspendisse feugiat vehicula arcu, vel pulvinar orci facilisis facilisis. Fusce eget scelerisque odio. Suspendisse laoreet pellentesque convallis. Donec eu dapibus tellus, eleifend luctus ligula. Duis mi enim, vestibulum sit amet luctus at, commodo vel odio. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce a leo non sem venenatis mollis convallis sed justo. Cras quis nibh quis elit consectetur elementum. Fusce cursus augue vel leo tincidunt tristique. In ut pulvinar ipsum, sed sagittis turpis. Mauris lobortis libero et purus consequat, vitae convallis risus viverra. Integer pellentesque dolor id enim gravida, ut congue libero elementum. Integer ac auctor risus.', 'hidden', '2026-06-11 04:41:03'),
(3, 11, 4, 'test 3', 'test-3-1781202709', 'img_6a2aff150b5730.84189815.jpeg', 'lorem', 'approved', '2026-06-11 18:31:49'),
(4, 11, 7, 'test 4', 'test-4-1781202734', 'img_6a2aff2e993701.19979842.jpeg', 'lorem123', 'approved', '2026-06-11 18:32:14'),
(5, 12, 6, 'test4', 'test4-1782187352', 'img_6a3a0558c65906.21615033.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque id orci eget sapien interdum sodales et efficitur libero. Quisque a congue arcu. Suspendisse aliquam commodo quam, sodales tincidunt mauris vulputate eget. Aliquam porttitor vitae urna a ullamcorper. Suspendisse tempus iaculis tincidunt. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam tincidunt lectus id ex finibus, eu iaculis libero bibendum. In hac habitasse platea dictumst. Quisque non leo massa. Ut malesuada erat eu sapien rutrum, ultricies vestibulum sapien euismod. Sed sit amet rhoncus arcu. Donec in eros nunc.\r\n\r\nNunc condimentum varius varius. Sed dapibus pharetra dui vitae cursus. Morbi in consectetur elit. Morbi tempor ex augue, sed efficitur nunc placerat vehicula. Nulla in tristique justo. Duis eu nisi in felis euismod dapibus. Nunc tincidunt ante non convallis tempor. Curabitur nunc nunc, cursus quis mauris id, aliquam ornare diam.\r\n\r\nNullam vel mollis magna, a gravida sapien. Suspendisse potenti. Integer interdum sapien ac tellus lacinia, non finibus augue maximus. In eleifend, erat a dapibus auctor, ante ex tincidunt enim, sed venenatis magna elit sed diam. Etiam fringilla viverra quam sit amet hendrerit. Mauris gravida tincidunt felis, et facilisis diam facilisis non. Vestibulum augue nibh, vestibulum vitae sem cursus, pulvinar cursus velit. Maecenas dictum nisi ut vehicula placerat. Nulla ut elementum justo. Nunc sed tortor ligula.\r\n\r\nVestibulum gravida eros varius orci consequat, quis laoreet nulla molestie. Proin ullamcorper, urna ac tempus molestie, nunc ligula malesuada tortor, eu efficitur est lacus sed lorem. Donec vestibulum vehicula arcu eget bibendum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse suscipit ante ut enim efficitur ornare. Sed varius ut sem ac cursus. Nunc sit amet malesuada eros. Aliquam eu ultrices sem, a mollis est. Interdum et malesuada fames ac ante ipsum primis in faucibus.\r\n\r\nEtiam fringilla sagittis pellentesque. Sed scelerisque faucibus efficitur. Nam eget imperdiet est, eu dignissim urna. Donec maximus rutrum erat, non bibendum sapien. Cras id luctus ante. Vivamus id faucibus augue. Cras mauris erat, lacinia a orci at, molestie sagittis eros. Mauris fermentum diam sed arcu placerat, vitae iaculis risus suscipit. Mauris vestibulum mauris sit amet purus malesuada accumsan. Morbi ut nibh cursus, scelerisque velit id, cursus nibh. Nulla auctor ipsum vestibulum, gravida elit non, ornare sapien. Fusce id nisl vel nisi semper ornare. Donec urna eros, hendrerit non fermentum nec, tristique eget diam. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'approved', '2026-06-23 04:02:32'),
(6, 11, 7, 'test5', 'test5-1782192701', 'img_6a3a1a3d5809f8.22601176.avif', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec dictum ligula ut lorem mollis, nec eleifend sapien euismod. Cras non ipsum sed libero tempor egestas. Vestibulum mollis enim ut mi lacinia tincidunt. Nam at orci eu felis bibendum tincidunt quis at est. Donec odio magna, ullamcorper eget volutpat et, efficitur nec nisi. Nulla facilisi. Fusce aliquet dui sit amet pretium venenatis. Mauris feugiat, ligula ut condimentum convallis, urna velit ullamcorper massa, quis dictum augue nulla nec turpis. Aenean sed nisl dui. Suspendisse pharetra interdum semper.\r\n\r\nMauris at faucibus massa. Curabitur imperdiet laoreet ex et accumsan. Sed interdum metus diam, quis congue diam consequat in. Vivamus suscipit eros vitae volutpat lacinia. Etiam dapibus tempus ex, vel sodales magna tincidunt sit amet. Phasellus interdum molestie fermentum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Mauris accumsan nisi a elit lobortis, ut elementum quam posuere. Etiam lorem risus, dictum a dictum in, condimentum et purus.\r\n\r\nCras laoreet, lectus semper dapibus tristique, augue mauris varius sem, eu volutpat ante tortor id felis. Vivamus fermentum sagittis est id dictum. Maecenas maximus gravida nulla ac facilisis. Suspendisse sit amet placerat eros. Donec accumsan, magna vitae consectetur suscipit, enim felis varius velit, non finibus libero ante eget libero. Phasellus nec dolor diam. Vestibulum sed faucibus tellus, a hendrerit justo. Cras id nulla consectetur, ornare lorem at, consequat felis. Donec sit amet consequat dui, a ornare turpis. Pellentesque at lobortis eros. Nam pulvinar, leo non gravida sodales, diam ipsum varius erat, et venenatis est lacus porta enim.\r\n\r\nIn varius orci a turpis sagittis, sed luctus orci condimentum. Nulla commodo mauris a magna consequat fringilla. In blandit ligula sapien, blandit auctor dolor commodo vitae. Nulla efficitur, quam quis mollis efficitur, felis quam faucibus elit, vel tincidunt augue nulla vel est. Nulla tincidunt, elit eu condimentum malesuada, ipsum risus suscipit justo, lacinia gravida leo enim at mi. Curabitur vitae ipsum sed nunc dictum vulputate non ut purus. Aliquam feugiat lectus ac nisl imperdiet, nec vestibulum odio commodo. Aliquam ac ornare justo. Morbi odio ante, maximus et imperdiet nec, accumsan in quam. In convallis arcu non augue iaculis, eu posuere ligula egestas. Aliquam in ante vestibulum tellus molestie luctus. Ut gravida felis magna, at dictum dui feugiat eu. Curabitur eleifend risus non nisl dignissim, eget viverra mauris viverra. Donec ullamcorper lorem nibh, ut finibus nisl finibus sed. Etiam libero elit, consectetur ac ante ac, tristique euismod orci.\r\n\r\nFusce elit purus, aliquet at urna eget, tempus cursus felis. Quisque efficitur egestas neque, id faucibus leo finibus ut. Praesent sit amet condimentum eros, ac luctus ipsum. Nam placerat erat lacus, ac pretium lorem euismod at. Mauris convallis dolor ac luctus mollis. Fusce vel suscipit nisi. Pellentesque vitae finibus velit. Integer viverra elit in magna malesuada porta. Interdum et malesuada fames ac ante ipsum primis in faucibus. Morbi vitae turpis mi. Vivamus interdum auctor lacus et suscipit. Integer nec augue eu nunc scelerisque scelerisque.\r\n\r\nVivamus eros sapien, interdum sit amet turpis ut, porttitor gravida leo. Curabitur ut orci et lectus sollicitudin vestibulum. Fusce luctus elit vel sem auctor commodo. Mauris elit nibh, fringilla quis venenatis non, suscipit eget est. Integer elementum justo non luctus finibus. Integer mollis nisl ligula, sit amet lacinia risus sodales venenatis. Curabitur pulvinar leo ac ante porta, ac porta enim ullamcorper. Vivamus suscipit massa at nibh sagittis imperdiet. Aliquam erat volutpat. Suspendisse sit amet varius ipsum. Praesent pellentesque velit ligula, ac consectetur nunc euismod semper. Suspendisse nisl leo, pulvinar sed nibh vitae, interdum pharetra nisi. Donec tempus neque mi. Cras in facilisis purus. Nunc placerat tortor enim.\r\n\r\nInterdum et malesuada fames ac ante ipsum primis in faucibus. Sed eu ligula ut metus blandit fermentum. Curabitur luctus, mi in volutpat maximus, diam odio condimentum tortor, et aliquam ante sapien in erat. Ut euismod tortor nec eros pellentesque luctus. Curabitur cursus est metus, eget commodo tortor vestibulum in. Nulla sollicitudin quis leo vel porttitor. Duis vel dui eu purus consectetur consequat. In sapien tortor, gravida et varius nec, efficitur convallis nibh. Nullam finibus non magna suscipit auctor. Vivamus felis libero, tempus non neque at, fermentum gravida nunc. Donec convallis arcu id libero mollis dignissim.\r\n\r\nPellentesque tempus est et libero fermentum commodo. Sed non tempus lacus. Aliquam et consectetur orci, egestas dapibus lacus. Aliquam sodales ultricies pulvinar. Vivamus in enim massa. Aenean dignissim bibendum tellus at sagittis. In hac habitasse platea dictumst. Nulla dolor tellus, malesuada at ullamcorper nec, varius a erat. Quisque mattis consectetur finibus.', 'approved', '2026-06-23 05:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `slug`, `created_at`) VALUES
(1, 'Technology', 'technology', '2026-06-11 03:23:54'),
(2, 'Health', 'health', '2026-06-11 03:23:54'),
(4, 'Business', 'business', '2026-06-11 03:23:54'),
(5, 'Education', 'education', '2026-06-11 03:23:54'),
(6, 'Games', 'games', '2026-06-11 03:42:39'),
(7, 'Fitness', 'fitness', '2026-06-11 04:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `blog_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `blog_id`, `user_id`, `comment_text`, `created_at`) VALUES
(1, 2, 11, 'aaaaaaaaa aaaaaa', '2026-06-11 16:54:46'),
(2, 2, 10, 'zzz zdae', '2026-06-11 16:55:21'),
(3, 3, 11, 'xxfg', '2026-06-12 06:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','author') NOT NULL DEFAULT 'author',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(10, 'admin', 'admin@gmail.com', '$2y$12$A09MfK5.frekpNvd2nIyLu0H8QDUoKfqi78zCbsbIjS515rQJOCk6', 'admin', '2026-06-10 14:41:40'),
(11, 'ranajit', 'ranajit@gmail.com', '$2y$12$94teEpzYkFGkOlPMi3lTL.2gd7QFZeoafjst7LE7uxiWVSFe18Z9m', 'author', '2026-06-10 14:51:19'),
(12, 'robert', 'robert@gmail.com', '$2y$12$DMi5eMsvNHulSDZ0xt8bpeB4Q7F6lABuKDYczLxPHz5LXuYL2sF.O', 'author', '2026-06-12 02:20:03'),
(13, 'Riti', 'riti@gmail.com', '$2y$12$AkTOepOBAxwulXn4dbelO.ObTdSJDhv6jF9lB6068xCqRfgFgWANC', 'author', '2026-06-12 05:54:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blog_id` (`blog_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blogs_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
