ALTER TABLE `Items_Bibliography` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_Bibliography` ADD FOREIGN KEY (`Bib_Item_ID`) REFERENCES `Items` (`Item_ID`);