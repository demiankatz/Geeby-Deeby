CREATE TABLE `Editions_Attributes` (
  `Editions_Attribute_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Editions_Attribute_Name` varchar(255) NOT NULL,
  `Editions_Attribute_RDF_Property` varchar(255),
  `Allow_HTML` smallint(1) NOT NULL DEFAULT '0',
  `Copy_To_Clone` smallint(1) NOT NULL DEFAULT '0',
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Editions_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Editions_Attributes_Values` (
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Editions_Attribute_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Editions_Attribute_Value` varchar(32768) NOT NULL,
  PRIMARY KEY (`Edition_ID`, `Editions_Attribute_ID`),
  FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`),
  FOREIGN KEY (`Editions_Attribute_ID`) REFERENCES `Editions_Attributes` (`Editions_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;