ALTER TABLE `Items_Tags` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_Tags` ADD FOREIGN KEY (`Tag_ID`) REFERENCES `Tags` (`Tag_ID`);