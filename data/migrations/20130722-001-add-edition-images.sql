CREATE TABLE `Editions_Images` (
  `Sequence_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Edition_ID` int(11) NOT NULL DEFAULT '0',
  `Image_Path` tinytext NOT NULL,
  `Thumb_Path` tinytext NOT NULL,
  `Position` int(11) DEFAULT NULL,
  `Note_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Sequence_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;