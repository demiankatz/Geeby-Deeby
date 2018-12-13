CREATE TABLE `Editions` (
  `Edition_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Edition_Name` tinytext NOT NULL,
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Series_ID` int(11) DEFAULT NULL,
  `Position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Edition_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;