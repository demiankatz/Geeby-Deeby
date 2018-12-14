ALTER TABLE `Editions` ADD COLUMN `Parent_Edition_ID` int(11) DEFAULT NULL;
ALTER TABLE `Editions` ADD COLUMN `Position_In_Parent` int(11) DEFAULT NULL;
ALTER TABLE `Editions` ADD COLUMN `Extent_In_Parent` text;