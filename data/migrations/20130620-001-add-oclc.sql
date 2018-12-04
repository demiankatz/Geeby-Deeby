CREATE TABLE `Editions_OCLC_Numbers` (
  `Sequence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `OCLC_Number` tinytext NOT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Sequence_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;