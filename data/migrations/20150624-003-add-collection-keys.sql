ALTER TABLE `Collections` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`);
ALTER TABLE `Collections` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items`(`Item_ID`);
ALTER TABLE `Collections` ADD FOREIGN KEY (`User_ID`) REFERENCES `Users`(`User_ID`);