ALTER TABLE `Editions_Images` ADD FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`);
ALTER TABLE `Editions_Images` ADD FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`);