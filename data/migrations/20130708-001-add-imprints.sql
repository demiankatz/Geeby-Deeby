CREATE TABLE `Publishers_Imprints` (
  `Imprint_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Publisher_ID` int(11) NOT NULL,
  `Imprint_Name` tinytext NOT NULL,
  PRIMARY KEY (`Imprint_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `Series_Publishers` ADD COLUMN `Imprint_ID` int(11) DEFAULT NULL;