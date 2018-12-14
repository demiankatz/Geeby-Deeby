CREATE TABLE `Editions_ISBNs` (
  `Sequence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `ISBN` char(10) DEFAULT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  `ISBN13` char(13) DEFAULT NULL,
  PRIMARY KEY (`Sequence_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Editions_Product_Codes` (
  `Sequence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Product_Code` tinytext NOT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Sequence_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;