ALTER TABLE `People_Links` ADD FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`);
ALTER TABLE `People_Links` ADD FOREIGN KEY (`Link_ID`) REFERENCES `Links` (`Link_ID`);