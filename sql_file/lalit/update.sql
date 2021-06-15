/*********
14th July
Add frozen flag in projects table
**********/
ALTER TABLE `projects` ADD `frozen` BOOLEAN NOT NULL DEFAULT FALSE AFTER `status`;