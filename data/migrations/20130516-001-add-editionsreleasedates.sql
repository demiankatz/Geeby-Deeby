CREATE TABLE `Editions_Release_Dates` (
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Year` int(11) NOT NULL DEFAULT '0',
  `Month` int(11) NOT NULL DEFAULT '0',
  `Day` int(11) NOT NULL DEFAULT '0',
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Edition_ID`,`Month`,`Day`,`Year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;