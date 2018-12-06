ALTER TABLE `Series_Categories` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`);
ALTER TABLE `Series_Categories` ADD FOREIGN KEY (`Category_ID`) REFERENCES `Categories`(`Category_ID`);