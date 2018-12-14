CREATE TABLE `Editions_Credits` (
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Person_ID` int(11) NOT NULL DEFAULT '0',
  `Role_ID` int(11) NOT NULL DEFAULT '0',
  `Position` int(11) DEFAULT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Edition_ID`,`Person_ID`,`Role_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;