CREATE TABLE `Items_Relationships` (
  `Items_Relationship_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Items_Relationship_Name` varchar(255) NOT NULL,
  `Items_Relationship_RDF_Property` varchar(255),
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  `Items_Inverse_Relationship_Name` varchar(255),
  `Items_Inverse_Relationship_RDF_Property` varchar(255),
  `Inverse_Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Items_Relationship_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Items_Relationships_Values` (
  `Subject_Item_ID` int(11) NOT NULL DEFAULT '0',
  `Items_Relationship_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Object_Item_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Subject_Item_ID`, `Items_Relationship_ID`, `Object_Item_ID`),
  FOREIGN KEY (`Subject_Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Items_Relationship_ID`) REFERENCES `Items_Relationships` (`Items_Relationship_ID`),
  FOREIGN KEY (`Object_Item_ID`) REFERENCES `Items` (`Item_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Series_Relationships` (
  `Series_Relationship_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Series_Relationship_Name` varchar(255) NOT NULL,
  `Series_Relationship_RDF_Property` varchar(255),
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  `Series_Inverse_Relationship_Name` varchar(255),
  `Series_Inverse_Relationship_RDF_Property` varchar(255),
  `Inverse_Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_Relationship_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Series_Relationships_Values` (
  `Subject_Series_ID` int(11) NOT NULL DEFAULT '0',
  `Series_Relationship_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Object_Series_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Subject_Series_ID`, `Series_Relationship_ID`, `Object_Series_ID`),
  FOREIGN KEY (`Subject_Series_ID`) REFERENCES `Series` (`Series_ID`),
  FOREIGN KEY (`Series_Relationship_ID`) REFERENCES `Series_Relationships` (`Series_Relationship_ID`),
  FOREIGN KEY (`Object_Series_ID`) REFERENCES `Series` (`Series_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;