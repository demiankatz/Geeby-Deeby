ALTER TABLE `Series_Bibliography` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`);
ALTER TABLE `Series_Bibliography` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items`(`Item_ID`);