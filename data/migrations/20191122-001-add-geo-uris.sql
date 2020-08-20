CREATE TABLE `Cities_URIs` (
  `Sequence_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `City_ID` int(11) NOT NULL,
  `Predicate_ID` int(11) NOT NULL,
  `URI` varchar(2048) NOT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`City_ID`) REFERENCES `Cities` (`City_ID`),
  FOREIGN KEY (`Predicate_ID`) REFERENCES `Predicates` (`Predicate_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Countries_URIs` (
  `Sequence_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Country_ID` int(11) NOT NULL,
  `Predicate_ID` int(11) NOT NULL,
  `URI` varchar(2048) NOT NULL,
  PRIMARY KEY (`Sequence_ID`),
  FOREIGN KEY (`Country_ID`) REFERENCES `Countries` (`Country_ID`),
  FOREIGN KEY (`Predicate_ID`) REFERENCES `Predicates` (`Predicate_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
