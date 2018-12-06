ALTER TABLE `Editions_ISBNs` ADD FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`);
ALTER TABLE `Editions_ISBNs` ADD FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`);