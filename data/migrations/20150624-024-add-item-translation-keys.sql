ALTER TABLE `Items_Translations` ADD FOREIGN KEY (`Source_Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_Translations` ADD FOREIGN KEY (`Trans_Item_ID`) REFERENCES `Items` (`Item_ID`);