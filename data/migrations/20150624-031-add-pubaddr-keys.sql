ALTER TABLE `Publishers_Addresses` ADD FOREIGN KEY (`Publisher_ID`) REFERENCES `Publishers` (`Publisher_ID`);
ALTER TABLE `Publishers_Addresses` ADD FOREIGN KEY (`Country_ID`) REFERENCES `Countries` (`Country_ID`);
ALTER TABLE `Publishers_Addresses` ADD FOREIGN KEY (`City_ID`) REFERENCES `Cities` (`City_ID`);