CREATE TABLE `Tags_Relationships` (
  `Tags_Relationship_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Tags_Relationship_Name` varchar(255) NOT NULL,
  `Tags_Relationship_RDF_Property` varchar(255),
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  `Tags_Inverse_Relationship_Name` varchar(255),
  `Tags_Inverse_Relationship_RDF_Property` varchar(255),
  `Inverse_Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Tags_Relationship_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Tags_Relationships_Values` (
  `Subject_Tag_ID` int(11) NOT NULL DEFAULT '0',
  `Tags_Relationship_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Object_Tag_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Subject_Tag_ID`, `Tags_Relationship_ID`, `Object_Tag_ID`),
  FOREIGN KEY (`Subject_Tag_ID`) REFERENCES `Tags` (`Tag_ID`),
  FOREIGN KEY (`Tags_Relationship_ID`) REFERENCES `Tags_Relationships` (`Tags_Relationship_ID`),
  FOREIGN KEY (`Object_Tag_ID`) REFERENCES `Tags` (`Tag_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
