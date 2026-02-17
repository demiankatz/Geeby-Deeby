ALTER TABLE `Material_Types` RENAME COLUMN `Default` to `Is_Default`;
ALTER TABLE `Material_Types` MODIFY `Is_Default` tinyint DEFAULT 0 NOT NULL;
