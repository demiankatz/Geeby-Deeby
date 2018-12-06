ALTER TABLE `Series_Publishers` ADD FOREIGN KEY (`Series_ID`) REFERENCES `Series`(`Series_ID`);
ALTER TABLE `Series_Publishers` ADD FOREIGN KEY (`Publisher_ID`) REFERENCES `Publishers`(`Publisher_ID`);
ALTER TABLE `Series_Publishers` ADD FOREIGN KEY (`Note_ID`) REFERENCES `Notes`(`Note_ID`);
ALTER TABLE `Series_Publishers` ADD FOREIGN KEY (`Imprint_ID`) REFERENCES `Publishers_Imprints`(`Imprint_ID`);
ALTER TABLE `Series_Publishers` ADD FOREIGN KEY (`Address_ID`) REFERENCES `Publishers_Addresses`(`Address_ID`);