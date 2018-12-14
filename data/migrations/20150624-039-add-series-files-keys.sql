ALTER TABLE `Series_Files` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`);
ALTER TABLE `Series_Files` ADD FOREIGN KEY (`File_ID`) REFERENCES `Files`(`File_ID`);