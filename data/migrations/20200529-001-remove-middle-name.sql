UPDATE People SET First_Name=CONCAT(TRIM(First_Name), ' ', TRIM(Middle_Name)), Middle_Name='' WHERE Middle_Name != '' AND Middle_Name is not null;
ALTER TABLE People DROP COLUMN `Middle_Name`;