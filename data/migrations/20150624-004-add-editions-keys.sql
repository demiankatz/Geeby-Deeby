ALTER TABLE `Editions` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items`(`Item_ID`);
ALTER TABLE `Editions` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`);
ALTER TABLE `Editions` ADD FOREIGN KEY (`Preferred_Item_AltName_ID`) REFERENCES `Items_AltTitles` (`Sequence_ID`);
ALTER TABLE `Editions` ADD FOREIGN KEY (`Preferred_Series_AltName_ID`) REFERENCES `Series_AltTitles` (`Sequence_ID`);
ALTER TABLE `Editions` ADD FOREIGN KEY (`Preferred_Series_Publisher_ID`) REFERENCES `Series_Publishers` (`Series_Publisher_ID`);
ALTER TABLE `Editions` ADD FOREIGN KEY (`Parent_Edition_ID`) REFERENCES `Editions` (`Edition_ID`);