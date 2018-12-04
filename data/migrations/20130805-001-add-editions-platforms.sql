CREATE TABLE `Editions_Platforms` (
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Platform_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Edition_ID`,`Platform_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;