-- MySQL dump 10.13  Distrib 5.1.41, for Win32 (ia32)
--
-- Host: .    Database: gamebooks
-- ------------------------------------------------------
-- Server version	5.1.41

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
-- Table structure for table `Authorities`
--

DROP TABLE IF EXISTS `Authorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Authorities` (
  `Authority_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Authority_Name` tinytext NOT NULL,
  PRIMARY KEY (`Authority_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Categories`
--

DROP TABLE IF EXISTS `Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Categories` (
  `Category_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Category` tinytext NOT NULL,
  `Description` text,
  PRIMARY KEY (`Category_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cities`
--

DROP TABLE IF EXISTS `Cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cities` (
  `City_ID` int(11) NOT NULL AUTO_INCREMENT,
  `City_Name` tinytext NOT NULL,
  PRIMARY KEY (`City_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Collections`
--

DROP TABLE IF EXISTS `Collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Collections` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `User_ID` int(11) NOT NULL DEFAULT '0',
  `Collection_Status` enum('have','want','extra') NOT NULL DEFAULT 'have',
  `Collection_Note` tinytext,
  PRIMARY KEY (`Series_ID`,`Item_ID`,`User_ID`,`Collection_Status`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items`(`Item_ID`),
  FOREIGN KEY (`User_ID`) REFERENCES `Users`(`User_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Countries`
--

DROP TABLE IF EXISTS `Countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Countries` (
  `Country_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Country_Name` tinytext NOT NULL,
  PRIMARY KEY (`Country_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions`
--

DROP TABLE IF EXISTS `Editions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions` (
  `Edition_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Edition_Name` tinytext NOT NULL,
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Series_ID` int(11) DEFAULT NULL,
  `Position` int(11) NOT NULL DEFAULT '0',
  `Preferred_Item_AltName_ID` bigint(20) DEFAULT NULL,
  `Preferred_Series_AltName_ID` bigint(20) unsigned DEFAULT NULL,
  `Edition_Length` tinytext,
  `Edition_Endings` tinytext,
  `Edition_Description` text,
  `Preferred_Series_Publisher_ID` int(11) DEFAULT NULL,
  `Parent_Edition_ID` int(11) DEFAULT NULL,
  `Position_In_Parent` int(11) DEFAULT NULL,
  `Extent_In_Parent` text,
  PRIMARY KEY (`Edition_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items`(`Item_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`Preferred_Item_AltName_ID`) REFERENCES `Items_AltTitles` (`Sequence_ID`),
  FOREIGN KEY (`Preferred_Series_AltName_ID`) REFERENCES `Series_AltTitles` (`Sequence_ID`),
  FOREIGN KEY (`Preferred_Series_Publisher_ID`) REFERENCES `Series_Publishers` (`Series_Publisher_ID`),
  FOREIGN KEY (`Parent_Edition_ID`) REFERENCES `Editions` (`Edition_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions_Credits`
--

DROP TABLE IF EXISTS `Editions_Credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions_Credits` (
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Person_ID` int(11) NOT NULL DEFAULT '0',
  `Role_ID` int(11) NOT NULL DEFAULT '0',
  `Position` int(11) DEFAULT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Edition_ID`,`Person_ID`,`Role_ID`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`),
  FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`),
  FOREIGN KEY (`Role_ID`) REFERENCES `Roles` (`Role_ID`),
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions_Full_Text`
--

DROP TABLE IF EXISTS `Editions_Full_Text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions_Full_Text` (
  `Sequence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Full_Text_Source_ID` int(11) NOT NULL DEFAULT '0',
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Full_Text_URL` tinytext NOT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Full_Text_Source_ID`) REFERENCES `Full_Text_Sources` (`Full_Text_Source_ID`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions_Images`
--

DROP TABLE IF EXISTS `Editions_Images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions_Images` (
  `Sequence_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Image_Path` tinytext NOT NULL,
  `Thumb_Path` tinytext NOT NULL,
  `Position` int(11) DEFAULT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`),
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions_ISBNs`
--

DROP TABLE IF EXISTS `Editions_ISBNs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions_ISBNs` (
  `Sequence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `ISBN` char(10) DEFAULT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  `ISBN13` char(13) DEFAULT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`),
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions_OCLC_Numbers`
--

DROP TABLE IF EXISTS `Editions_OCLC_Numbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions_OCLC_Numbers` (
  `Sequence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `OCLC_Number` tinytext NOT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`),
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions_Platforms`
--

DROP TABLE IF EXISTS `Editions_Platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions_Platforms` (
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Platform_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Edition_ID`,`Platform_ID`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`),
  FOREIGN KEY (`Platform_ID`) REFERENCES `Platforms` (`Platform_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions_Product_Codes`
--

DROP TABLE IF EXISTS `Editions_Product_Codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions_Product_Codes` (
  `Sequence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Product_Code` tinytext NOT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`),
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Editions_Release_Dates`
--

DROP TABLE IF EXISTS `Editions_Release_Dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Editions_Release_Dates` (
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Year` int(11) NOT NULL DEFAULT '0',
  `Month` int(11) NOT NULL DEFAULT '0',
  `Day` int(11) NOT NULL DEFAULT '0',
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Edition_ID`,`Month`,`Day`,`Year`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`),
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQ_Categories`
--

DROP TABLE IF EXISTS `FAQ_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FAQ_Categories` (
  `FAQ_Category_ID` int(11) NOT NULL DEFAULT '0',
  `FAQ_Category_Name` tinytext,
  PRIMARY KEY (`FAQ_Category_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FAQs`
--

DROP TABLE IF EXISTS `FAQs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FAQs` (
  `FAQ_Category_ID` int(11) NOT NULL DEFAULT '0',
  `FAQ_ID` int(11) NOT NULL DEFAULT '0',
  `FAQ_Name` tinytext,
  `FAQ_Body` text,
  PRIMARY KEY (`FAQ_Category_ID`,`FAQ_ID`),
  FOREIGN KEY (`FAQ_Category_ID`) REFERENCES `FAQ_Categories` (`FAQ_Category_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `File_Types`
--

DROP TABLE IF EXISTS `File_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `File_Types` (
  `File_Type_ID` int(11) NOT NULL AUTO_INCREMENT,
  `File_Type` tinytext NOT NULL,
  PRIMARY KEY (`File_Type_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Files`
--

DROP TABLE IF EXISTS `Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Files` (
  `File_ID` int(11) NOT NULL AUTO_INCREMENT,
  `File_Name` tinytext,
  `File_Path` tinytext,
  `Description` text,
  `File_Type_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`File_ID`),
  FOREIGN KEY (`File_Type_ID`) REFERENCES `File_Types` (`File_Type_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Full_Text_Sources`
--

DROP TABLE IF EXISTS `Full_Text_Sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Full_Text_Sources` (
  `Full_Text_Source_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Full_Text_Source_Name` tinytext,
  PRIMARY KEY (`Full_Text_Source_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items`
--

DROP TABLE IF EXISTS `Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items` (
  `Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Item_Name` tinytext NOT NULL,
  `Item_Errata` text,
  `Item_Thanks` tinytext,
  `Material_Type_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_Adaptations`
--

DROP TABLE IF EXISTS `Items_Adaptations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_Adaptations` (
  `Source_Item_ID` int(11) NOT NULL DEFAULT '0',
  `Adapted_Item_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Source_Item_ID`,`Adapted_Item_ID`),
  FOREIGN KEY (`Source_Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Adapted_Item_ID`) REFERENCES `Items` (`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_AltTitles`
--

DROP TABLE IF EXISTS `Items_AltTitles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_AltTitles` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Item_AltName` tinytext NOT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  `Sequence_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_Bibliography`
--

DROP TABLE IF EXISTS `Items_Bibliography`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_Bibliography` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Bib_Item_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Item_ID`,`Bib_Item_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Bib_Item_ID`) REFERENCES `Items` (`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_Descriptions`
--

DROP TABLE IF EXISTS `Items_Descriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_Descriptions` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Source` enum('LC','Cover','User','Ad') NOT NULL DEFAULT 'User',
  `Description` text NOT NULL,
  PRIMARY KEY (`Item_ID`,`Source`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_Files`
--

DROP TABLE IF EXISTS `Items_Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_Files` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `File_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Item_ID`,`File_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`File_ID`) REFERENCES `Files` (`File_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_In_Collections`
--

DROP TABLE IF EXISTS `Items_In_Collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_In_Collections` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Collection_Item_ID` int(11) NOT NULL DEFAULT '0',
  `Position` int(11) NOT NULL DEFAULT '0',
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Item_ID`,`Collection_Item_ID`,`Position`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Collection_Item_ID`) REFERENCES `Items` (`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_Links`
--

DROP TABLE IF EXISTS `Items_Links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_Links` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Link_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Item_ID`,`Link_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Link_ID`) REFERENCES `Links` (`Link_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_Reviews`
--

DROP TABLE IF EXISTS `Items_Reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_Reviews` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `User_ID` int(11) NOT NULL DEFAULT '0',
  `Review` text NOT NULL,
  `Approved` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`Item_ID`,`User_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`),
  KEY `Approved` (`Approved`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_Tags`
--

DROP TABLE IF EXISTS `Items_Tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_Tags` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Tag_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Item_ID`,`Tag_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Tag_ID`) REFERENCES `Tags` (`Tag_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Items_Translations`
--

DROP TABLE IF EXISTS `Items_Translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Items_Translations` (
  `Source_Item_ID` int(11) NOT NULL DEFAULT '0',
  `Trans_Item_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Source_Item_ID`,`Trans_Item_ID`),
  FOREIGN KEY (`Source_Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Trans_Item_ID`) REFERENCES `Items` (`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Languages`
--

DROP TABLE IF EXISTS `Languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Languages` (
  `Language_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Language_Name` tinytext NOT NULL,
  PRIMARY KEY (`Language_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Link_Types`
--

DROP TABLE IF EXISTS `Link_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Link_Types` (
  `Link_Type_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Link_Type` tinytext NOT NULL,
  PRIMARY KEY (`Link_Type_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Links`
--

DROP TABLE IF EXISTS `Links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Links` (
  `Link_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Link_Name` tinytext NOT NULL,
  `URL` tinytext NOT NULL,
  `Description` tinytext,
  `Date_Checked` date NOT NULL DEFAULT '0000-00-00',
  `Link_Type_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Link_ID`),
  FOREIGN KEY  (`Link_Type_ID`) REFERENCES `Link_Types` (`Link_Type_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Material_Types`
--

DROP TABLE IF EXISTS `Material_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Material_Types` (
  `Material_Type_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Material_Type_Name` tinytext NOT NULL,
  `Material_Type_Plural_Name` tinytext NOT NULL,
  `Material_Type_RDF_Class` tinytext,
  `Default` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Material_Type_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Notes`
--

DROP TABLE IF EXISTS `Notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Notes` (
  `Note_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Note` tinytext NOT NULL,
  PRIMARY KEY (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `People`
--

DROP TABLE IF EXISTS `People`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `People` (
  `Person_ID` int(11) NOT NULL AUTO_INCREMENT,
  `First_Name` tinytext,
  `Middle_Name` tinytext,
  `Last_Name` tinytext,
  `Extra_Details` tinytext,
  `Biography` text,
  `Authority_ID` int,
  PRIMARY KEY (`Person_ID`),
  FOREIGN KEY (`Authority_ID`) REFERENCES `Authorities` (`Authority_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `People_Bibliography`
--

DROP TABLE IF EXISTS `People_Bibliography`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `People_Bibliography` (
  `Person_ID` int(11) NOT NULL DEFAULT '0',
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Person_ID`,`Item_ID`),
  FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `People_Files`
--

DROP TABLE IF EXISTS `People_Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `People_Files` (
  `Person_ID` int(11) NOT NULL DEFAULT '0',
  `File_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Person_ID`,`File_ID`),
  FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`),
  FOREIGN KEY (`File_ID`) REFERENCES `Files` (`File_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `People_Links`
--

DROP TABLE IF EXISTS `People_Links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `People_Links` (
  `Person_ID` int(11) NOT NULL DEFAULT '0',
  `Link_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Person_ID`,`Link_ID`),
  FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`),
  FOREIGN KEY (`Link_ID`) REFERENCES `Links` (`Link_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Platforms`
--

DROP TABLE IF EXISTS `Platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Platforms` (
  `Platform_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Platform` tinytext NOT NULL,
  PRIMARY KEY (`Platform_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Pseudonyms`
--

DROP TABLE IF EXISTS `Pseudonyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Pseudonyms` (
  `Real_Person_ID` int(11) NOT NULL DEFAULT '0',
  `Pseudo_Person_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Real_Person_ID`,`Pseudo_Person_ID`),
  FOREIGN KEY (`Real_Person_ID`) REFERENCES `People` (`Person_ID`),
  FOREIGN KEY (`Pseudo_Person_ID`) REFERENCES `People` (`Person_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Publishers`
--

DROP TABLE IF EXISTS `Publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publishers` (
  `Publisher_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Publisher_Name` tinytext NOT NULL,
  PRIMARY KEY (`Publisher_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Publishers_Addresses`
--

DROP TABLE IF EXISTS `Publishers_Addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publishers_Addresses` (
  `Address_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Publisher_ID` int(11) NOT NULL,
  `Country_ID` int(11) NOT NULL,
  `City_ID` int(11) DEFAULT NULL,
  `Street` tinytext DEFAULT '',
  PRIMARY KEY (`Address_ID`),
  FOREIGN KEY (`Publisher_ID`) REFERENCES `Publishers` (`Publisher_ID`),
  FOREIGN KEY (`Country_ID`) REFERENCES `Countries` (`Country_ID`),
  FOREIGN KEY (`City_ID`) REFERENCES `Cities` (`City_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Publishers_Imprints`
--

DROP TABLE IF EXISTS `Publishers_Imprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publishers_Imprints` (
  `Imprint_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Publisher_ID` int(11) NOT NULL,
  `Imprint_Name` tinytext NOT NULL,
  PRIMARY KEY (`Imprint_ID`),
  FOREIGN KEY (`Publisher_ID`) REFERENCES `Publishers` (`Publisher_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Recent_Reviews`
--

DROP TABLE IF EXISTS `Recent_Reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recent_Reviews` (
  `Added` date NOT NULL DEFAULT '0000-00-00',
  `User_ID` int(11) NOT NULL DEFAULT '0',
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Type` enum('item','series') NOT NULL DEFAULT 'item',
  PRIMARY KEY (`User_ID`,`Item_ID`),
  FOREIGN KEY (`User_ID`) REFERENCES `Users`(`User_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Roles`
--

DROP TABLE IF EXISTS `Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Roles` (
  `Role_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Role_Name` tinytext NOT NULL,
  PRIMARY KEY (`Role_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series`
--

DROP TABLE IF EXISTS `Series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series` (
  `Series_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Series_Name` tinytext NOT NULL,
  `Series_Description` text,
  `Language_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_ID`),
  FOREIGN KEY (`Language_ID`) REFERENCES `Languages` (`Language_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_AltTitles`
--

DROP TABLE IF EXISTS `Series_AltTitles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_AltTitles` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Series_AltName` tinytext NOT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  `Sequence_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series` (`Series_ID`)
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Attributes`
--

DROP TABLE IF EXISTS `Series_Attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Attributes` (
  `Series_Attribute_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Series_Attribute_Name` varchar(255) NOT NULL,
  `Series_Attribute_RDF_Property` varchar(255),
  `Allow_HTML` smallint(1) NOT NULL DEFAULT '0',
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Attributes_Values`
--

DROP TABLE IF EXISTS `Series_Attributes_Values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Attributes_Values` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Series_Attribute_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Series_Attribute_Value` varchar(32768) NOT NULL,
  PRIMARY KEY (`Series_ID`, `Series_Attribute_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series` (`Series_ID`),
  FOREIGN KEY (`Series_Attribute_ID`) REFERENCES `Series_Attributes` (`Series_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Bibliography`
--

DROP TABLE IF EXISTS `Series_Bibliography`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Bibliography` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_ID`,`Item_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items`(`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Categories`
--

DROP TABLE IF EXISTS `Series_Categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Categories` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Category_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_ID`,`Category_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`Category_ID`) REFERENCES `Categories`(`Category_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Files`
--

DROP TABLE IF EXISTS `Series_Files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Files` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `File_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_ID`,`File_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`File_ID`) REFERENCES `Files`(`File_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Links`
--

DROP TABLE IF EXISTS `Series_Links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Links` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Link_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_ID`,`Link_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`Link_ID`) REFERENCES `Links`(`Link_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Material_Types`
--

DROP TABLE IF EXISTS `Series_Material_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Material_Types` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Material_Type_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_ID`,`Material_Type_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`Material_Type_ID`) REFERENCES `Material_Types`(`Material_Type_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Publishers`
--

DROP TABLE IF EXISTS `Series_Publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Publishers` (
  `Series_Publisher_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Publisher_ID` int(11) NOT NULL DEFAULT '0',
  `Note_ID` int(11) DEFAULT NULL,
  `Imprint_ID` int(11) DEFAULT NULL,
  `Address_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Series_Publisher_ID`),
  KEY `SERIES` (`Series_ID`),
  KEY `PUBLISHER` (`Publisher_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`Publisher_ID`) REFERENCES `Publishers`(`Publisher_ID`),
  FOREIGN KEY (`Note_ID`) REFERENCES `Notes`(`Note_ID`),
  FOREIGN KEY (`Imprint_ID`) REFERENCES `Publishers_Imprints`(`Imprint_ID`),
  FOREIGN KEY (`Address_ID`) REFERENCES `Publishers_Addresses`(`Address_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Reviews`
--

DROP TABLE IF EXISTS `Series_Reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Reviews` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `User_ID` int(11) NOT NULL DEFAULT '0',
  `Review` text NOT NULL,
  `Approved` enum('y','n') NOT NULL DEFAULT 'y',
  PRIMARY KEY (`Series_ID`,`User_ID`),
  FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`User_ID`) REFERENCES `Users`(`User_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Series_Translations`
--

DROP TABLE IF EXISTS `Series_Translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Series_Translations` (
  `Source_Series_ID` int(11) NOT NULL DEFAULT '0',
  `Trans_Series_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Source_Series_ID`,`Trans_Series_ID`),
  FOREIGN KEY (`Source_Series_ID`) REFERENCES `Series`(`Series_ID`),
  FOREIGN KEY (`Trans_Series_ID`) REFERENCES `Series`(`Series_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tag_Types`
--

DROP TABLE IF EXISTS `Tag_Types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tag_Types` (
  `Tag_Type_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Tag_Type` tinytext NOT NULL,
  PRIMARY KEY (`Tag_Type_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Tags`
--

DROP TABLE IF EXISTS `Tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Tags` (
  `Tag_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Tag` tinytext NOT NULL,
  `Tag_Type_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Tag_ID`),
  FOREIGN KEY (`Tag_Type_ID`) REFERENCES `Tag_Types` (`Tag_Type_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User_Groups`
--

DROP TABLE IF EXISTS `User_Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User_Groups` (
  `User_Group_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Group_Name` tinytext NOT NULL,
  `Content_Editor` tinyint NOT NULL DEFAULT 0,
  `User_Editor` tinyint NOT NULL DEFAULT 0,
  `Approver` tinyint NOT NULL DEFAULT 0,
  `Data_Manager` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`User_Group_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` tinytext NOT NULL,
  `Password_Hash` tinytext NOT NULL,
  `Name` tinytext NOT NULL,
  `Address` tinytext,
  `Person_ID` int(11) DEFAULT NULL,
  `User_Group_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`User_ID`),
  FOREIGN KEY (`User_Group_ID`) REFERENCES `User_Groups` (`User_Group_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-10-26 16:36:07
