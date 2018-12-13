ALTER TABLE `Series_Links` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`);
ALTER TABLE `Series_Links` ADD FOREIGN KEY (`Link_ID`) REFERENCES `Links`(`Link_ID`);