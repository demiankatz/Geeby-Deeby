CREATE TABLE `Publishers_URIs` (
  `Sequence_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Publisher_ID` int(11) NOT NULL,
  `Predicate_ID` int(11) NOT NULL,
  `URI` varchar(2048) NOT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Publisher_ID`) REFERENCES `Publishers` (`Publisher_ID`),
  FOREIGN KEY (`Predicate_ID`) REFERENCES `Predicates` (`Predicate_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
