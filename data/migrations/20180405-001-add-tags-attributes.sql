CREATE TABLE `Tags_Attributes` (
  `Tags_Attribute_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Tags_Attribute_Name` varchar(255) NOT NULL,
  `Tags_Attribute_RDF_Property` varchar(255),
  `Allow_HTML` smallint(1) NOT NULL DEFAULT '0',
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Tags_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Tags_Attributes_Values` (
  `Tag_ID` int(11) NOT NULL DEFAULT '0',
  `Tags_Attribute_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Tags_Attribute_Value` mediumtext NOT NULL,
  PRIMARY KEY (`Tag_ID`, `Tags_Attribute_ID`),
  FOREIGN KEY (`Tag_ID`) REFERENCES `Tags` (`Tag_ID`),
  FOREIGN KEY (`Tags_Attribute_ID`) REFERENCES `Tags_Attributes` (`Tags_Attribute_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
