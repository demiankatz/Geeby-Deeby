CREATE TABLE `Items_Attributes` (
  `Items_Attribute_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Items_Attribute_Name` varchar(255) NOT NULL,
  `Items_Attribute_RDF_Property` varchar(255),
  `Allow_HTML` smallint(1) NOT NULL DEFAULT '0',
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Items_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Items_Attributes_Values` (
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Items_Attribute_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Items_Attribute_Value` mediumtext NOT NULL,
  PRIMARY KEY (`Item_ID`, `Items_Attribute_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Items_Attribute_ID`) REFERENCES `Items_Attributes` (`Items_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
