-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ceramic_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `items_summary` text DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `order_ref_code` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Processing',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'2026-03-20 04:36:30','The \"Tapo Hyper-Suction\" Robot Vacuum & Mop (x1)',32999.00,'pay_STLmBBntu1hVqr',NULL,'Processing');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `review_text` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES (2,5,1,4,'2026-02-26 07:10:58',NULL),(3,13,2,5,'2026-02-26 12:03:10',NULL),(4,15,2,4,'2026-02-26 12:03:22',NULL),(5,9,2,3,'2026-02-26 12:03:33',NULL),(6,14,2,4,'2026-02-27 04:12:18',NULL),(7,11,2,5,'2026-03-10 04:50:02',NULL),(8,15,2,4,'2026-03-11 10:25:13',NULL),(9,15,2,4,'2026-03-11 10:29:29','Its a great product\r\n'),(10,15,2,4,'2026-03-11 10:57:32','Its nice product'),(11,14,2,5,'2026-03-12 09:21:56','love this product'),(12,13,2,4,'2026-03-12 09:24:44','Works efficiently'),(14,12,2,4,'2026-03-12 09:25:00','Crazy product'),(15,11,2,5,'2026-03-12 09:25:20','keeps water cool'),(17,10,2,5,'2026-03-12 09:25:41','keeps air clean at my house'),(18,9,2,5,'2026-03-12 09:26:12','delicate product\r\n'),(20,8,2,5,'2026-03-12 09:26:34','sound is too good'),(22,7,2,4,'2026-03-12 09:28:18','love the product'),(24,6,2,5,'2026-03-12 09:29:29','beautiful'),(25,11,2,4,'2026-03-12 11:14:08',''),(26,11,2,0,'2026-03-12 11:14:38','very innovative');
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (5,'Obsidian Zenith Vase','6199.00','A hand carved ceramic vessel featuring a metallic obsidian glaze. This piece undergoes a 48 hour burnishing process to achieve a mirror like finish, bridging the gap between raw earth and modern luxury.','Screenshot 2026-02-26 123937.png','12'),(6,'Azure Earth Ember Bowl','4999.00','A striking centerpiece that captures the intersection of volcanic earth and coastal tides. This wide rimmed bowl features a raw, textured exterior with copper oxide accents, contrasted by a deep turquoise crackle glaze interior. Each piece is kiln fired at high temperatures to ensure a unique, organic flow of colors, making it a functional work of art for the modern collector','Gemini_Generated_Image_pxnerapxnerapxne.png','19'),(7,'The \"Mitti-Flux\" Ritual Station','3999.00','The Mitti-Flux Station is a handcrafted, multi-use ceramic piece that blends ancient Indian pottery traditions with 2026\'s \"Organic Brutalist\" aesthetic. Designed for the urban professional looking to create a \"meditation corner\" at home, it replaces cluttered plastic stands with a single, sculptural statement.\r\nEach piece is wheel-thrown in Khurja using a custom blend of dark manganese clay and local stoneware. The exterior is left unglazed and raw to celebrate the natural \"Mitti\" (earth) feel, while the interior \"river\" path is finished in a high-gloss Emerald Forest reactive glaze.','Gemini_Generated_Image_lef7vjlef7vjlef7.png','12'),(8,'The \"Swar-Dhvani\" Ceramic Acoustic Amplifier','5499.00','The Swar Dhvani (meaning \"Sound of the Soul\") is a non electronic, passive sound amplifier that uses the natural resonance of high density ceramic to boost your smartphone\'s audio. In a world of \"digital fatigue,\" this product offers a 2026 solution: a way to enjoy music and podcasts without batteries, wires, or Bluetooth radiation.\r\nInspired by the \"Acoustic Design\" trend, the station features a hollow, double-walled chamber shaped like a futuristic conch. When a phone is placed in the top slot, the sound waves are compressed and then expanded through the flared \"bell\" of the ceramic, naturally increasing volume by up to 15 decibels while adding a rich, warm tone to the audio.','Gemini_Generated_Image_u7pl3wu7pl3wu7pl.png','11'),(9,'The \"Anna-Purna\" Smart Fermenting Crock','6999.00','The Anna Purna Crock is a modern reimagining of the traditional Indian martaban. In 2026, as gut health and \"slow food\" become central to urban Indian lifestyles, this crock offers a fail-proof way to ferment pickles, kimchi, or kombucha while looking like a piece of high end art.\r\nIt is crafted from high density vitrified porcelain, which is naturally non porous and acid resistant critical for long term fermentation. Unlike plastic or glass, the thick ceramic walls provide superior thermal insulation, protecting the delicate probiotics from the temperature fluctuations common in Indian kitchens.','Gemini_Generated_Image_7nx8wq7nx8wq7nx8.png','12'),(10,'The \"Prana-Vayu\" Living Air Purifier','14999.00','The Prana-Vayu is a \"Living Machine\" that replaces the sterile, plastic look of traditional air purifiers with a sculptural, ceramic hydroponic system. In 2026, as air quality remains a top concern in Indian metros, this product uses Phytoremediation the natural ability of plants to scrub toxins like formaldehyde and carbon monoxide boosted by an ultra quiet, AI driven airflow system.\r\nUnlike standard purifiers that rely solely on HEPA filters, the Prana-Vayu uses a porous terracotta \"Lung\" structure. Air is drawn into the base, passed through a water cooled ceramic chamber, and pushed through the root zone of specialized indoor plants (like Snake Plants or Peace Lilies), where microbes neutralize pollutants.','Gemini_Generated_Image_miprqpmiprqpmipr.png','8'),(11,'The \"Soma-Scale\" Smart Copper Hydration Hub','11499.00','The Soma Scale is a 2026 reimagining of the traditional Indian copper matka (water pot). In an era where \"bio hacking\" and water quality are top priorities, this hub doesn\'t just store water; it optimizes it.\r\nCrafted from 99.9% antimicrobial copper, the hub utilizes the oligodynamic effect to naturally purify water. However, the \"Smart\" element lies in its integrated AI-Hydration Base. The base features a precision weight sensor and a non invasive optical sensor that tracks not just how much you drink, but the mineral saturation and temperature of the water.','Gemini_Generated_Image_uhw4jfuhw4jfuhw4.png','6'),(12,'The \"Yale Zuri\" Bio-Aesthetic Smart Lock','14998.99','Moving beyond the industrial look of traditional smart locks, the Yale Zuri features a 2026 \"Warm Minimalism\" aesthetic. It replaces cold steel with an Antique Bronze and matte black finish that complements modern wooden doors.','Gemini_Generated_Image_5j5u0r5j5u0r5j5u.png','6'),(13,'The \"Tapo Hyper-Suction\" Robot Vacuum & Mop','32999.00','Designed specifically for the challenges of Indian households which often face high dust levels and a mix of hard floors and rugs this robot features 5300Pa hyper suction and a dual spin mopping system','Gemini_Generated_Image_bud49dbud49dbud4.png','4'),(14,'The \"Eco-Mists\" Sculptural Water Faucet','12499.00','A centerpiece for the 2026 \"Sensory Bathroom,\" this faucet uses advanced aeration technology to provide a rich, full pressure water experience while consuming 50% less water than standard taps.','Gemini_Generated_Image_iw17seiw17seiw17.png','2'),(15,'The \"Dhwani-Kosh\" Ceramic Resonance Speaker','6999.00','In 2026, as \"digital detox\" zones become standard in Indian homes, the Dhwani-Kosh (Sound Cell) offers a zero-electricity audio boost. This sculptural piece is crafted from high-fire sonorous stoneware, specifically engineered with a hollow, double-walled interior that amplifies smartphone audio by 12–15 decibels through natural resonance.','Gemini_Generated_Image_315lh3315lh3315l.png','4');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','admin') DEFAULT 'client',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'TSC Admin',NULL,'admin@tsc.com','admin123','admin'),(2,'Pranjal Aggarwal',NULL,'Pranjalaggarwal706@gmail.com','abcdefg','client'),(3,'vipansh',NULL,'vipansh.vimal@gmail.com','abcdefg','client');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-04 21:31:58
