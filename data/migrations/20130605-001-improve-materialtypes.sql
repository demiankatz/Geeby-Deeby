ALTER TABLE `Material_Types` ADD COLUMN `Material_Type_Plural_Name` tinytext NOT NULL;
ALTER TABLE `Material_Types` ADD COLUMN `Default` smallint(1) NOT NULL DEFAULT '0';