ALTER TABLE `projects` ADD `is_assigned` INT NOT NULL AFTER `status`;

--Date 14-aug-2020

ALTER TABLE `project_activity` CHANGE `description_key` `description_key` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Language file key'; 

-- Date 20-aug-2020

ALTER TABLE `issue_milestones` ADD `is_closure` BOOLEAN NOT NULL DEFAULT FALSE AFTER `milestone_name`;