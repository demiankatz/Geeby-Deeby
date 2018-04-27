ALTER TABLE Editions_Images MODIFY COLUMN `Image_Path` tinytext;
ALTER TABLE Editions_Images MODIFY COLUMN `Thumb_Path` tinytext;
ALTER TABLE Editions_Images ADD COLUMN `IIIF_URI` tinytext;
