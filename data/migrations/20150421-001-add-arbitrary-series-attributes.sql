CREATE TABLE `Series_Attributes` (
  `Series_Attribute_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Series_Attribute_Name` varchar(255) NOT NULL,
  `Series_Attribute_RDF_Property` varchar(255),
  `Allow_HTML` smallint(1) NOT NULL DEFAULT '0',
  `Display_Priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Series_Attribute_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Series_Attributes_Values` (
  `Series_ID` int(11) NOT NULL DEFAULT '0',
  `Series_Attribute_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Series_Attribute_Value` varchar(32768) NOT NULL,
  PRIMARY KEY (`Series_ID`, `Series_Attribute_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;