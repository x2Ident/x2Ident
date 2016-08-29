-- MySQL dump 10.13  Distrib 5.5.49, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: x2Ident_demo
-- ------------------------------------------------------
-- Server version	5.5.49-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text,
  `secret` text NOT NULL,
  `not_show` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conf_key` text NOT NULL,
  `conf_value` text NOT NULL,
  `conf_default` text NOT NULL,
  `conf_info` text NOT NULL,
  `only_admin` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pwid` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=327 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history`
--

LOCK TABLES `history` WRITE;
/*!40000 ALTER TABLE `history` DISABLE KEYS */;
/*!40000 ALTER TABLE `history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text NOT NULL,
  `de` text NOT NULL,
  `en` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language`
--

LOCK TABLES `language` WRITE;
/*!40000 ALTER TABLE `language` DISABLE KEYS */;
INSERT INTO `language` VALUES (1,'proxy_inaktiv','du bist nicht auf dem Proxy!','you\'re not on the proxy!'),(2,'proxy_aktiv','du bist auf dem Proxy!','you are on the proxy!'),(3,'hallo','Hallo','hello'),(4,'admin_text','Verwalte deine Passwörter. Verwende diesen Modus nur in einer sicheren Umgebung.','Administrate your passwords. Use only in a secure environment.'),(5,'keygen_text','Logge dich mit Einmalkeys sicher in einer möglicherweise unsicheren Umgebung ein.','Log in with one-time-keys in a potentially unsecure environment.'),(6,'nicht_angemeldet','nicht angemeldet','not logged in'),(7,'admin_title','Admin','Admin'),(8,'keygen_title','Keygen','Keygen'),(9,'otk_create_title','Einmal-Key erstellen','Create a one-time-key'),(10,'angemeldet_als','Angemeldet als','Logged in as'),(11,'logout','Abmelden','Logout'),(12,'settings','Einstellungen','settings'),(13,'loginfirst_link','Bitte zuerst <a href=\"login\">einloggen</a>','Please <a href=\"login\">login</a> first'),(14,'key','Schlüssel','key'),(15,'value','Wert','value'),(16,'default','Standard','default'),(17,'info','Info','info'),(18,'save','Speichern','save'),(19,'einstellungen_erst_nach_login','Einige Einstellungen werden unter Umständen erst nach erneutem Login übernommen.','You need to login again to change some settings.'),(20,'title','Titel','title'),(21,'website','Webseite','website'),(22,'user','Benutzername','user'),(23,'otk','Einmal-Key','one time key'),(24,'global','Global','global'),(25,'expires_in','Läuft ab in','expires in'),(26,'last_login','Letzte Anmeldung','last login'),(27,'create_otk_button','Key erstellen','create key'),(28,'delete_otk_button','Löschen','delete key'),(29,'vor_zeit_1','vor',''),(30,'noch_nie','noch nie','never'),(31,'sekunden','Sekunden','seconds'),(32,'session_noch_aktiv_1','Session noch','session expires in'),(33,'time_sekunden','Sekunde(n)','second(s)'),(34,'time_minuten','Minute(n)','minute(s)'),(35,'time_stunden','Stunde(n)','hour(s)'),(36,'time_tage','Tag(e)','day(s)'),(37,'time_monate','Monat(e)','month(s)'),(38,'session_noch_aktiv_2','aktiv',''),(39,'vor_zeit_2','','ago'),(40,'api_conn_failed','Verbindung zur API fehlgeschlagen. Überprüfen Sie den API-Key.','API connection failed. Check the API key.');
/*!40000 ALTER TABLE `language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `onetimekeys`
--

DROP TABLE IF EXISTS `onetimekeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `onetimekeys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pwid` int(11) NOT NULL,
  `onetime` text NOT NULL,
  `real_pw` text NOT NULL,
  `pw_active` int(11) NOT NULL,
  `url` text NOT NULL,
  `pw_global` int(11) NOT NULL,
  `user` text NOT NULL,
  `sess_id` text NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=351 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `onetimekeys`
--

LOCK TABLES `onetimekeys` WRITE;
/*!40000 ALTER TABLE `onetimekeys` DISABLE KEYS */;
/*!40000 ALTER TABLE `onetimekeys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_proxy`
--

DROP TABLE IF EXISTS `session_proxy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_proxy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_proxy`
--

LOCK TABLES `session_proxy` WRITE;
/*!40000 ALTER TABLE `session_proxy` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_proxy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_user`
--

DROP TABLE IF EXISTS `session_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text NOT NULL,
  `ip` text NOT NULL,
  `user_agent` text NOT NULL,
  `sess_id` text NOT NULL,
  `js_id` text NOT NULL,
  `expires` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_user`
--

LOCK TABLES `session_user` WRITE;
/*!40000 ALTER TABLE `session_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `session_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_conf`
--

DROP TABLE IF EXISTS `user_conf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text NOT NULL,
  `conf_key` text NOT NULL,
  `conf_value` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_conf`
--

LOCK TABLES `user_conf` WRITE;
/*!40000 ALTER TABLE `user_conf` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_conf` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-29 15:21:46
