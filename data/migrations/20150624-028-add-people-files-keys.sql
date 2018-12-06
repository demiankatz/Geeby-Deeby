ALTER TABLE `People_Files` ADD FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`);
ALTER TABLE `People_Files` ADD FOREIGN KEY (`File_ID`) REFERENCES `Files` (`File_ID`);