/*********
13th June 
EXCEPTION
**********/

UPDATE `exception` SET `name`='Add Action Taker' WHERE (`id`='1');
UPDATE `exception` SET `name`='Extend deadline of timeline' WHERE (`id`='2');
UPDATE `exception` SET `name`='Re-assign ticket to different action taker' WHERE (`id`='3');
UPDATE `exception` SET `name`='Incorrect Location Tagging' WHERE (`id`='4');
UPDATE `exception` SET `name`='Milestones Project ' WHERE (`id`='5');
UPDATE `exception` SET `name`='Others' WHERE (`id`='6');


/*********
13th June 
project_status
**********/
CREATE TABLE `project_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color` varchar(7) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `order` int(11) DEFAULT NULL,
  `filter_default` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
);	


INSERT INTO `project_status` (`id`, `color`, `name`, `order`, `filter_default`) VALUES (1, '#ff6f00', 'New', 1, 1);
INSERT INTO `project_status` (`id`, `color`, `name`, `order`, `filter_default`) VALUES (2, '#03a9f4', 'In progress', 2, 1);
INSERT INTO `project_status` (`id`, `color`, `name`, `order`, `filter_default`) VALUES (3, '#1aa254', 'Closed', 3, 1);
INSERT INTO `project_status` (`id`, `color`, `name`, `order`, `filter_default`) VALUES (4, '#fc0332', 'Resolved', 4, 1);
INSERT INTO `project_status` (`id`, `color`, `name`, `order`, `filter_default`) VALUES (5, '#fc0332', 'Rejected', 5, 1);
INSERT INTO `project_status` (`id`, `color`, `name`, `order`, `filter_default`) VALUES (6, '#ffbf00', 'Re-Opened', 6, 1);

/*********
15th June 
project
**********/
ALTER TABLE `projects`
ADD COLUMN `area_id`  int(11) NULL DEFAULT NULL AFTER `clientid`,
ADD COLUMN `region_id`  int(11) NULL DEFAULT NULL AFTER `area_id`,
ADD COLUMN `subregion_id`  int(11) NULL DEFAULT NULL AFTER `region_id`,
ADD COLUMN `issue_id`  int(11) NULL DEFAULT NULL AFTER `subregion_id`;

/*********
17th June 
project_Notes for Rejection and other information
**********/
ALTER TABLE `project_notes`
ADD COLUMN `status`  int(4) NULL DEFAULT NULL AFTER `staff_id`,
ADD COLUMN `exception`  int(4) NULL DEFAULT NULL AFTER `status`,
ADD COLUMN `created_at`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `status`;


/*********
18th June 
project_members created time
**********/
ALTER TABLE `project_members`
ADD COLUMN `active`  tinyint(4) NULL DEFAULT 1 AFTER `staff_id`,
ADD COLUMN `created_at`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `active`,
ADD COLUMN `updated_at`  timestamp NULL AFTER `created_at`;

/*********
task_assigned - ADD Ticket assigned time
**********/
ALTER TABLE `task_assigned`
ADD COLUMN `assigned_date`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `assigned_from`;

/*********
add updated_at col to check last updated for ticket
**********/
ALTER TABLE `tasks`
ADD COLUMN `updated_at`  date NULL DEFAULT NULL AFTER `remindertwo_date`;

ALTER TABLE `projects`
ADD COLUMN `updated_at`  date NULL DEFAULT NULL AFTER `date_finished`;

/*********
22nd June 
task_id add by lalit
**********/
ALTER TABLE `project_notes` ADD `task_id` INT(11) NOT NULL AFTER `project_id`;

/*********
Auto update timestamp when cols update
**********/
ALTER TABLE `projects`
MODIFY COLUMN `updated_at`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `date_finished`;

ALTER TABLE `tasks`
MODIFY COLUMN `updated_at`  timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `remindertwo_date`;

/*********
23rd June
Add order for sorting prefrence
**********/
ALTER TABLE `exception`
ADD COLUMN `order`  tinyint(4) NULL AFTER `status`;

/*********
24rd June
Add FLAG for ATA to check new ticket,reopen ticket for sorting prefrence
**********/
ALTER TABLE `task_assigned`
ADD COLUMN `status`  tinyint(1) NULL DEFAULT 1 AFTER `is_assigned_from_contact`;

/***Add Action Date to show data list****/
ALTER TABLE `projects`
ADD COLUMN `action_date`  date NULL DEFAULT NULL AFTER `date_finished`;

/***Update Action Date****/
UPDATE `projects` SET action_date = deadline;

/*********
30th June
Add address column to get geo tagged image address
**********/
ALTER TABLE `project_files`
ADD COLUMN `address`  varchar(200) NULL DEFAULT NULL AFTER `longitude`;

/*********
2nd July
Add rejected column to track no of time rejected
**********/
ALTER TABLE `projects`
ADD COLUMN `rejected`  int(10) NULL DEFAULT 0 AFTER `updated_at`;

/*********
10th July
Add assigned column to track ticket assigned to user
**********/
ALTER TABLE `project_members`
ADD COLUMN `assigned`  tinyint(4) NULL DEFAULT 1 AFTER `active`;

/*********
22nd July
Add assigned column to track ticket assigned to user
**********/
ALTER TABLE `project_status`
ADD COLUMN `bg-color`  varchar(7) NULL DEFAULT NULL AFTER `color`;

/*********
24th July
Add frozen 
**********/
ALTER TABLE `projects`
ADD COLUMN `frozen`  tinyint(1) NULL DEFAULT 0 AFTER `status`;

/*********
27th July
Add reassigned 
**********/
ALTER TABLE `projects`
ADD COLUMN `reassigned`  tinyint(1) NULL DEFAULT 0 AFTER `status`;

/*********
28th July
Add parent_id 
**********/
ALTER TABLE `projects`
ADD COLUMN `parent_id`  int(11) NULL DEFAULT NULL AFTER `subregion_id`;

ALTER TABLE `tasks`
ADD COLUMN `is_staff`  tinyint(1) NULL DEFAULT 0 COMMENT '0=>Clinet, 1=> Staff' AFTER `addedfrom`;

ALTER TABLE `projects`
ADD COLUMN `sub_ticket_id`  varchar(200) NULL DEFAULT NULL AFTER `parent_id`;

ALTER TABLE `projects`
ADD COLUMN `sub_id`  varchar(100) NULL DEFAULT NULL AFTER `parent_id`;

/*********
20th Aug
Update project file table with thumb and optimizes image 
**********/
UPDATE project_files SET thumbnail_link = file_name WHERE thumbnail_link IS NULL
UPDATE project_files SET `subject` = file_name WHERE `subject` IS NULL

/*********
3rd Sep
Update project activity table with updated_at 
**********/
ALTER TABLE `project_activity`
ADD COLUMN `updated_at`  datetime NULL DEFAULT NULL AFTER `dateadded`;

