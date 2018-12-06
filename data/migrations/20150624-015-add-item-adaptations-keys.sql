ALTER TABLE `Items_Adaptations` ADD FOREIGN KEY (`Source_Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_Adaptations` ADD FOREIGN KEY (`Adapted_Item_ID`) REFERENCES `Items` (`Item_ID`);
