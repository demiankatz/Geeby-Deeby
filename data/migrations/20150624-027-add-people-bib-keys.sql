ALTER TABLE `People_Bibliography` ADD FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`);
ALTER TABLE `People_Bibliography` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`);