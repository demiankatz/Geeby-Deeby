ALTER TABLE `Items_Links` ADD FOREIGN KEY (`Item_ID`) REFERENCES `Items` (`Item_ID`);
ALTER TABLE `Items_Links` ADD FOREIGN KEY (`Link_ID`) REFERENCES `Links` (`Link_ID`);