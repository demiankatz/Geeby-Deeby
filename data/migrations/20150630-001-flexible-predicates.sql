CREATE TABLE `Predicates` (
  `Predicate_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Predicate` varchar(2048) NOT NULL,
  `Predicate_Abbrev` varchar(256) NOT NULL,
  PRIMARY KEY (`Predicate_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

ALTER TABLE `People_URIs` ADD COLUMN `Predicate_ID` int(11) NOT NULL;
ALTER TABLE `People_URIs` ADD FOREIGN KEY (`Predicate_ID`) REFERENCES `Predicates` (`Predicate_ID`);