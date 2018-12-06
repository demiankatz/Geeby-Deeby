ALTER TABLE `Editions_Credits` ADD FOREIGN KEY (`Edition_ID`) REFERENCES `Editions` (`Edition_ID`);
ALTER TABLE `Editions_Credits` ADD FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`);
ALTER TABLE `Editions_Credits` ADD FOREIGN KEY (`Role_ID`) REFERENCES `Roles` (`Role_ID`);
ALTER TABLE `Editions_Credits` ADD FOREIGN KEY (`Note_ID`) REFERENCES `Notes` (`Note_ID`);