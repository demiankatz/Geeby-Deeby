CREATE TABLE `Publishers_Addresses` (
  `Address_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Publisher_ID` int(11) NOT NULL,
  `Country_ID` int(11) NOT NULL,
  `City_ID` int(11) DEFAULT NULL,
  `Street` tinytext DEFAULT '',
  PRIMARY KEY (`Address_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `Series_Publishers` ADD COLUMN `Address_ID` int(11) DEFAULT NULL;