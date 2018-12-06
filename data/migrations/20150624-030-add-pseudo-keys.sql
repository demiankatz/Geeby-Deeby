ALTER TABLE `Pseudonyms` ADD FOREIGN KEY (`Real_Person_ID`) REFERENCES `People` (`Person_ID`);
ALTER TABLE `Pseudonyms` ADD FOREIGN KEY (`Pseudo_Person_ID`) REFERENCES `People` (`Person_ID`);