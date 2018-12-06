ALTER TABLE `Items_AltTitles` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_AltTitles` ADD FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`);