-- MySQL dump 10.13  Distrib 8.0.21, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: hubsolidariedade
-- ------------------------------------------------------
-- Server version	5.7.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_categories`
--

DROP TABLE IF EXISTS `_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent` bigint(20) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_categories`
--

LOCK TABLES `_categories` WRITE;
/*!40000 ALTER TABLE `_categories` DISABLE KEYS */;
INSERT INTO `_categories` VALUES (1,'categoria teste','categoria-teste',0);
/*!40000 ALTER TABLE `_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_home_banners`
--

DROP TABLE IF EXISTS `_home_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_home_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_atualizacao` datetime DEFAULT NULL,
  `banner_mbl` varchar(45) DEFAULT NULL,
  `banner_dsk` varchar(45) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `ativo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_home_banners`
--

LOCK TABLES `_home_banners` WRITE;
/*!40000 ALTER TABLE `_home_banners` DISABLE KEYS */;
INSERT INTO `_home_banners` VALUES (1,NULL,'b-mbls-1.jpg','b-dsk-1.jpg','',NULL,1),(2,NULL,'b-mbls-2.jpg','b-dsk-2.jpg',NULL,NULL,1),(3,NULL,'b-mbls-3.jpg','b-dsk-3.jpg',NULL,NULL,1);
/*!40000 ALTER TABLE `_home_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_news`
--

DROP TABLE IF EXISTS `_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `publish_date` datetime DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `img_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `img_metadata` longtext COLLATE utf8mb4_unicode_ci,
  `tag_title` text COLLATE utf8mb4_unicode_ci,
  `tag_description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_news`
--

LOCK TABLES `_news` WRITE;
/*!40000 ALTER TABLE `_news` DISABLE KEYS */;
INSERT INTO `_news` VALUES (1,'Trote Solidário 2021: Estudantes unidos na arrecadação de alimentos e doação de sangue','trote-solidario-2021:-estudantes-unidos-na-arrecadacao-de-alimentos-e-doacao-de-sangue','<p>A pandemia do Coronav&iacute;rus trouxe a necessidade de mudan&ccedil;as em algumas atividades do j&aacute; tradicional Trote Solid&aacute;rio desenvolvido pelo N&uacute;cleo Acad&ecirc;mico do Simers, parceiro do Hub de Solidariedade. Mesmo assim, os resultados obtidos na edi&ccedil;&atilde;o de 2021 foram surpreendentes. Quase nove toneladas de alimentos foram arrecadadas durante os dois meses de realiza&ccedil;&atilde;o do evento. Somente no dia 1&ordm; de maio, data em que os m&eacute;dicos receberam a segunda dose de vacina&ccedil;&atilde;o contra a Covid-19 na sede da entidade sindical, 1,8 toneladas foram doadas presencialmente. Desde o in&iacute;cio da a&ccedil;&atilde;o, os mantimentos tinham um destino certo: o Banco de Alimentos do Rio Grande do Sul.</p>\r\n\r\n<p>Al&eacute;m da arrecada&ccedil;&atilde;o de alimentos, a doa&ccedil;&atilde;o de sangue tamb&eacute;m marcou o Trote Solid&aacute;rio. Devido &agrave; pandemia do Coronav&iacute;rus, foi necess&aacute;rio reformular algumas atividades. Assim, os estudantes tiveram a oportunidade de preencher manifesta&ccedil;&otilde;es de interesse e, posteriormente, apresentar os comprovantes. No total, 647 formul&aacute;rios foram preenchidos.</p>\r\n\r\n<p>Com o objetivo de auxiliar no importante trabalho desenvolvido pelo Instituto do C&acirc;ncer Infantil, os jovens tamb&eacute;m fizeram parte da campanha de arrecada&ccedil;&atilde;o de tampinhas pet. Al&eacute;m de contribuir com o meio-ambiente, a iniciativa tamb&eacute;m gera renda &agrave; institui&ccedil;&atilde;o.</p>\r\n\r\n<p>Para o diretor de projetos especiais do Simers, Vin&iacute;cius de Souza, os resultados superaram as expectativas. &ldquo;N&oacute;s t&iacute;nhamos algumas d&uacute;vidas sobre a aceita&ccedil;&atilde;o desse novo modelo do Trote Solid&aacute;rio. Foi necess&aacute;rio mudar o formato do evento em fun&ccedil;&atilde;o da pandemia. Mesmo assim, tivemos ampla participa&ccedil;&atilde;o n&atilde;o s&oacute; dos estudantes, mas da sociedade de uma forma geral, o que nos deixa muito felizes e agradecidos&rdquo;, destaca.</p>\r\n\r\n<p>&nbsp;</p>\r\n','Trote Solidário 2021: Estudantes unidos na arrecadação de alimentos e doação de sangue','2021-06-04 08:00:00',1,'2021-08-12 17:08:08','2021-08-13 20:56:53','1_destaque.jpg',NULL,NULL,1,NULL,NULL,NULL),(2,'Ação solidária auxilia idosas na prevenção da Covid-19 ','acao-solidaria-auxilia-idosas-na-prevencao-da-covid-19-','<p><strong>A&ccedil;&atilde;o solid&aacute;ria auxilia idosas na preven&ccedil;&atilde;o da Covid-19 </strong></p>\r\n\r\n<p>As idosas residentes do Lar Maria de Nazar&eacute; ganharam uma forcinha na preven&ccedil;&atilde;o &agrave; Covid-19. M&aacute;scaras de prote&ccedil;&atilde;o foram doadas &agrave; institui&ccedil;&atilde;o, que est&aacute; localizada no bairro Petr&oacute;polis, em Porto Alegre.</p>\r\n\r\n<p>O material foi entregue pela estudante de medicina e integrante do grupo de volunt&aacute;rios, Scarlet Orihuela. As m&aacute;scaras foram confeccionadas por Carla Monteiro, que &eacute; m&atilde;e da jovem e tamb&eacute;m se engajou &agrave; causa solid&aacute;ria.</p>\r\n\r\n<p>A entrega tamb&eacute;m foi acompanhada pelo vice-presidente do Simers, Marcos Rovinski.</p>\r\n\r\n<p><br />\r\n&nbsp;</p>\r\n\r\n<p><strong>Sobre o Lar Maria de Nazar&eacute;:</strong></p>\r\n\r\n<p>O Lar Maria de Nazar&eacute; &eacute; uma Organiza&ccedil;&atilde;o da Sociedade Civil (OSC), vinculada &agrave; Sociedade Esp&iacute;rita Maria de Nazar&eacute;, uma associa&ccedil;&atilde;o religiosa sem fins lucrativos fundada em 1921, que visa &agrave; presta&ccedil;&atilde;o de assist&ecirc;ncia social e destina-se ao abrigo de pessoas idosas do sexo feminino, acolhendo-as para moradia e oferecendo todo o atendimento necess&aacute;rio.</p>\r\n\r\n<p><strong>Endere&ccedil;o: </strong>Av. Cel. Lucas de Oliveira, 2746 - Petr&oacute;polis, Porto Alegre</p>\r\n\r\n<p><a href=\"https://www.google.com/search?q=lar+maria+de+nazar%C3%A9+telefone&amp;ludocid=15106718572164679616&amp;sa=X&amp;ved=2ahUKEwj06brDh6zyAhWnqJUCHSgdBz4Q6BMwFXoECCsQAg\"><strong>Telefone</strong></a><strong>: </strong><a href=\"https://www.google.com/search?q=lar+maria+de+nazar%C3%A9&amp;oq=lar+maria+&amp;aqs=chrome.0.69i59j46i175i199i512l2j69i57j46i175i199i512j69i60l3.3847j0j7&amp;sourceid=chrome&amp;ie=UTF-8\">(51) 3209-3932</a></p>\r\n\r\n<p><strong>E-mail:</strong> contato@larmariadenazare.com.br</p>\r\n','Ação solidária auxilia idosas na prevenção da Covid-19 ','2020-05-06 08:00:00',1,'2021-08-12 17:19:02','2021-08-13 20:57:39','2_destaque.jpg',NULL,NULL,1,NULL,NULL,NULL),(3,'Campanha do agasalho ajuda a aquecer o inverno de quem mais precisa','campanha-do-agasalho-ajuda-a-aquecer-o-inverno-de-quem-mais-precisa','<p>Ao perceber a possibilidade de ajudar a manter o inverno de quem mais precisa aquecido, a estudante de medicina Luiza Costa prontamente entrou em contato com as amigas para arrecadar agasalhos para doa&ccedil;&atilde;o. Acompanhada do pai, o m&eacute;dico Luiz Carlos Costa, Luiza levou presencialmente as doa&ccedil;&otilde;es at&eacute; a sede do Sindicato M&eacute;dico do Rio Grande do Sul (Simers), parceiro do Hub de Solidariedade, onde roupas e acess&oacute;rios de frio estavam sendo recolhidas. &ldquo;A Luiza sempre gostou de participar de a&ccedil;&otilde;es em benef&iacute;cio do pr&oacute;ximo, o que para mim &eacute; motivo de muita alegria&rdquo;, destacou o pai orgulhoso.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>A a&ccedil;&atilde;o teve in&iacute;cio em 22 de maio, data em que m&eacute;dicos e familiares receberam a vacina contra a gripe na sede da entidade e aproveitaram para fazer suas doa&ccedil;&otilde;es. Ao final da campanha, os agasalhos foram entregues ao Hospital Psiqui&aacute;trico S&atilde;o Pedro para serem distribu&iacute;dos aos pacientes e internos na casa de sa&uacute;de.</p>\r\n','Campanha do agasalho ajuda a aquecer o inverno de quem mais precisa','2021-06-09 08:21:00',1,'2021-08-12 17:24:39','2021-08-13 20:53:06','3_destaque.jpg',NULL,NULL,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_news_categories`
--

DROP TABLE IF EXISTS `_news_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_news_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_news_categories_categories_idx` (`categorie_id`),
  KEY `fk_news_categories_news_idx` (`news_id`),
  CONSTRAINT `fk_news_categories_categories` FOREIGN KEY (`categorie_id`) REFERENCES `_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_news_categories_news` FOREIGN KEY (`news_id`) REFERENCES `_news` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_news_categories`
--

LOCK TABLES `_news_categories` WRITE;
/*!40000 ALTER TABLE `_news_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `_news_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_news_tags`
--

DROP TABLE IF EXISTS `_news_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_news_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_news_tags_news_idx` (`news_id`),
  KEY `fk_news_tags_tags_idx` (`tag_id`),
  CONSTRAINT `fk_news_tags_news` FOREIGN KEY (`news_id`) REFERENCES `_news` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_news_tags_tags` FOREIGN KEY (`tag_id`) REFERENCES `_tags` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_news_tags`
--

LOCK TABLES `_news_tags` WRITE;
/*!40000 ALTER TABLE `_news_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `_news_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_project_item`
--

DROP TABLE IF EXISTS `_project_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_project_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imagem_item` text,
  `project_id` int(11) DEFAULT NULL,
  `user_create` int(11) DEFAULT NULL,
  `user_update` int(11) DEFAULT NULL,
  `data_create` datetime DEFAULT NULL,
  `data_update` datetime DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_project_item_user_create_idx` (`user_create`),
  KEY `fk_project_item_user_update_idx` (`user_update`),
  KEY `fk_project_item_project_idx` (`project_id`),
  CONSTRAINT `fk_project_item_project` FOREIGN KEY (`project_id`) REFERENCES `_projects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_project_item_user_create` FOREIGN KEY (`user_create`) REFERENCES `_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_project_item_user_update` FOREIGN KEY (`user_update`) REFERENCES `_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_project_item`
--

LOCK TABLES `_project_item` WRITE;
/*!40000 ALTER TABLE `_project_item` DISABLE KEYS */;
INSERT INTO `_project_item` VALUES (1,'1_projetoitem.jpg',1,1,NULL,'2021-08-16 09:41:18',NULL,1),(2,'2_projetoitem.jpg',1,1,NULL,'2021-08-16 09:41:33',NULL,1),(3,'3_projetoitem.jpg',1,1,NULL,'2021-08-16 09:41:48',NULL,1),(4,'4_projetoitem.jpg',1,1,NULL,'2021-08-16 09:42:03',NULL,1),(5,'5_projetoitem.jpg',1,1,NULL,'2021-08-16 09:42:33',NULL,1),(6,'6_projetoitem.jpg',2,1,NULL,'2021-08-16 09:44:36',NULL,1),(7,'7_projetoitem.jpg',2,1,NULL,'2021-08-16 09:44:52',NULL,1),(8,'8_projetoitem.jpg',2,1,NULL,'2021-08-16 09:45:20',NULL,1),(9,'9_projetoitem.jpg',2,1,NULL,'2021-08-16 09:46:50',NULL,1),(10,'10_projetoitem.jpg',2,1,NULL,'2021-08-16 09:47:08',NULL,1),(11,'11_projetoitem.jpg',2,1,NULL,'2021-08-16 09:47:26',NULL,1),(12,'12_projetoitem.jpg',2,1,NULL,'2021-08-16 09:47:46',NULL,1),(13,'13_projetoitem.jpg',2,1,NULL,'2021-08-16 09:48:09',NULL,1),(14,'14_projetoitem.jpg',2,1,NULL,'2021-08-16 09:48:33',NULL,1),(15,'15_projetoitem.jpg',2,1,NULL,'2021-08-16 09:49:21',NULL,1),(16,'16_projetoitem.jpg',3,1,NULL,'2021-08-16 09:49:48',NULL,1),(17,'17_projetoitem.jpg',4,1,NULL,'2021-08-16 09:50:19',NULL,1),(18,'18_projetoitem.jpg',4,1,NULL,'2021-08-16 09:50:44',NULL,1);
/*!40000 ALTER TABLE `_project_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_projects`
--

DROP TABLE IF EXISTS `_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` text,
  `img_capa` text,
  `resumo_projeto` text,
  `user_create` int(11) DEFAULT NULL,
  `user_update` int(11) DEFAULT NULL,
  `data_create` datetime DEFAULT NULL,
  `data_update` datetime DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_projects_users_user_create_idx` (`user_create`),
  KEY `fk_projects_users_user_update_idx` (`user_update`),
  CONSTRAINT `fk_projects_users_user_create` FOREIGN KEY (`user_create`) REFERENCES `_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_projects_users_user_update` FOREIGN KEY (`user_update`) REFERENCES `_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_projects`
--

LOCK TABLES `_projects` WRITE;
/*!40000 ALTER TABLE `_projects` DISABLE KEYS */;
INSERT INTO `_projects` VALUES (1,'Trote Solidário','1_capa.jpg','Realizado semestralmente pelo Núcleo Acadêmico do Simers, o Trote Solidário reúne estudantes de Medicina, 20 Universidades de Medicina do RS, a Fundação Gaúcha dos Bancos Sociais, os hemocentros do Estado, o Instituto da Criança com Câncer, o curso Fleming em ações sociais. Entre elas arrecadação de alimentos, doação de sangue, de livros, tampinhas, entre outras.',1,1,'2021-08-12 15:49:45','2021-08-16 09:02:39',1),(2,'Lar Maria de Nazaré','2_capa.jpg','No Lar é realizado pelas voluntárias – estudantes de Medicina -, o Projeto “Ouvindo a Vida”. Uma vez ao mês o grupo vai ao local e realiza atividades com as moradoras do Lar.',1,1,'2021-08-11 17:41:38','2021-08-16 09:03:18',1),(3,'Obrigado, Dr!','3_capa.jpg','Criado como forma de homenagear os médicos, o \"Obrigado, Dr!\" transforma a gratidão dos pacientes em ações. O projeto é realizado nos hospitais de Porto Alegre e envolve médicos, estudantes de Medicina, colaboradores voluntários, recreacionistas e administração dos hospitais. A atividade é desenvolvida uma vez ao ano com pacientes, crianças e adolescentes internadas nas casas de saúde.',1,1,'2021-08-11 17:41:38','2021-08-16 09:03:59',1),(4,'Natal solidário','4_capa.jpg','A solidariedade é um valor incentivado durante o ano inteiro, no Natal não poderia ser diferente. A ONG Integração dos Anjos, que acolhe mais de 250 crianças em situação de vulnerabilidade social, por exemplo, já recebeu brinquedos e material escolar. O Natal Solidário acontece todos os anos e você também poderá participar.',1,1,'2021-08-11 17:41:38','2021-08-16 09:04:57',1);
/*!40000 ALTER TABLE `_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_tags`
--

DROP TABLE IF EXISTS `_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_tags`
--

LOCK TABLES `_tags` WRITE;
/*!40000 ALTER TABLE `_tags` DISABLE KEYS */;
INSERT INTO `_tags` VALUES (1,'Teste tag1','testetag1');
/*!40000 ALTER TABLE `_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_users`
--

DROP TABLE IF EXISTS `_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passwordHash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passwordResetToken` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `authKey` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_users`
--

LOCK TABLES `_users` WRITE;
/*!40000 ALTER TABLE `_users` DISABLE KEYS */;
INSERT INTO `_users` VALUES (1,'Nome do admin','$2y$13$8rkASx4I7l9KiwMeV9RrieedzF3muIR5CmGWxQQs5.lvnOLLl5Nce','admin@hubdesolidariedade.com.br',1,NULL,'2021-08-16 10:20:57','usuarioadmin',NULL,NULL);
/*!40000 ALTER TABLE `_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-08-16 14:18:35
