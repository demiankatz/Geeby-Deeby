CREATE TABLE `Editions_Full_Text_Attributes` (
  `Editions_Full_Text_Attribute_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Editions_Full_Text_Attribute_Name` varchar(255) NOT NULL,
  `Editions_Full_Text_Attribute_RDF_Property` varchar(255),
  `Allow_HTML` smallint(1) NOT NULL DEFAULT '0',
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Editions_Full_Text_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Editions_Full_Text_Attributes_Values` (
  `Editions_Full_Text_ID` int(11) NOT NULL DEFAULT '0',
  `Editions_Full_Text_Attribute_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Editions_Full_Text_Attribute_Value` mediumtext NOT NULL,
  PRIMARY KEY (`Editions_Full_Text_ID`, `Editions_Full_Text_Attribute_ID`),
  FOREIGN KEY (`Editions_Full_Text_ID`) REFERENCES `Editions_Full_Text` (`Sequence_ID`),
  FOREIGN KEY (`Editions_Full_Text_Attribute_ID`) REFERENCES `Editions_Full_Text_Attributes` (`Editions_Full_Text_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
