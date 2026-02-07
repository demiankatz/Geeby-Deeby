ALTER TABLE `Users` ADD COLUMN `Approved` enum('y','n') NOT NULL DEFAULT 'n';
UPDATE `Users` SET `Approved`='y' WHERE `Person_ID` != '0' OR `Person_ID` IS NULL;
UPDATE `Users` SET `Person_ID`=null WHERE `Person_ID` IN ('0', '-1');
ALTER TABLE `Users` ADD FOREIGN KEY (`Person_ID`) REFERENCES `People` (`Person_ID`);
