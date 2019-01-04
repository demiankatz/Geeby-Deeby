CREATE TABLE `Citations` (
  `Citation_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Citation` text NOT NULL,
  PRIMARY KEY (`Citation_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Items_Creators` (
  `Item_Creator_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Item_ID` int(11) NOT NULL DEFAULT '0',
  `Person_ID` int(11) NOT NULL DEFAULT '0',
  `Role_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Item_Creator_ID`),
  UNIQUE KEY (`Item_ID`, `Person_ID`, `Role_ID`),
  FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`),
  FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`),
  FOREIGN KEY (`Role_ID`) REFERENCES `Roles` (`Role_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE `Items_Creators_Citations` (
  `Item_Creator_ID` int(11) NOT NULL DEFAULT '0',
  `Citation_ID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Item_Creator_ID`, `Citation_ID`),
  FOREIGN KEY (`Item_Creator_ID`) REFERENCES `Items_Creators` (`Item_Creator_ID`),
  FOREIGN KEY (`Citation_ID`) REFERENCES `Citations` (`Citation_ID`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
