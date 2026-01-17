ALTER TABLE Items_Reviews ADD COLUMN `Added` date NOT NULL DEFAULT '2004-09-23';
ALTER TABLE Series_Reviews ADD COLUMN `Added` date NOT NULL DEFAULT '2004-09-23';
UPDATE Items_Reviews ir JOIN Recent_Reviews rr ON ir.User_ID=rr.User_ID AND ir.Item_ID=rr.Item_ID AND rr.Type='item' SET ir.Added = rr.Added;
UPDATE Series_Reviews sr JOIN Recent_Reviews rr ON sr.User_ID=rr.User_ID AND sr.Series_ID=rr.Item_ID AND rr.Type='series' SET sr.Added = rr.Added;
DROP TABLE Recent_Reviews;
