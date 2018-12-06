ALTER TABLE `Items_In_Collections` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_In_Collections` ADD FOREIGN KEY (`Collection_Item_ID`) REFERENCES `Items` (`Item_ID`);