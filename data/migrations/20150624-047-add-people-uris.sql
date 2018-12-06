CREATE TABLE `People_URIs` (
  `Sequence_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Person_ID` int(11) NOT NULL,
  `URI` varchar(2048) NOT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;