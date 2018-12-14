ALTER TABLE `Items_Reviews` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_Reviews` ADD FOREIGN KEY (`User_ID`) REFERENCES `Users` (`User_ID`);