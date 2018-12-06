ALTER TABLE `Items_Files` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_Files` ADD FOREIGN KEY (`File_ID`) REFERENCES `Files` (`File_ID`);