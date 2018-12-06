ALTER TABLE `Series_Reviews` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`);
ALTER TABLE `Series_Reviews` ADD FOREIGN KEY (`User_ID`) REFERENCES `Users`(`User_ID`);