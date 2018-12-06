CREATE TABLE `Tags_URIs` (
  `Sequence_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Tag_ID` int(11) NOT NULL,
  `Predicate_ID` int(11) NOT NULL,
  `URI` varchar(2048) NOT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Tag_ID`) REFERENCES `Tags` (`Tag_ID`),
  FOREIGN KEY (`Predicate_ID`) REFERENCES `Predicates` (`Predicate_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;