ALTER TABLE `tbl_tugas`
	CHANGE COLUMN `working_date_start` `working_date_start` DATETIME NULL AFTER `description`,
	CHANGE COLUMN `working_date_end` `working_date_end` DATETIME NULL AFTER `working_date_start`;

