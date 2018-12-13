CREATE TABLE `Editions_Full_Text` (
  `Sequence_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Full_Text_Source_ID` int(11) NOT NULL DEFAULT '0',
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Full_Text_URL` tinytext NOT NULL,
  PRIMARY KEY (`Sequence_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Full_Text_Sources` (
  `Full_Text_Source_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Full_Text_Source_Name` tinytext,
  PRIMARY KEY (`Full_Text_Source_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;