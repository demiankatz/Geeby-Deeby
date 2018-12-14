ALTER TABLE `Series_AltTitles` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series` (`Series_ID`);
ALTER TABLE `Series_AltTitles` ADD FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`);