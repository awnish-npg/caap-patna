--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text,
  `landmark` varchar(200) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `clientid` int(11) NOT NULL,
  `billing_type` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `deadline` date DEFAULT NULL,
  `project_created` date NOT NULL,
  `date_finished` datetime DEFAULT NULL,
  `progress` int(11) DEFAULT '0',
  `progress_from_tasks` int(11) NOT NULL DEFAULT '1',
  `project_cost` decimal(15,2) DEFAULT NULL,
  `project_rate_per_hour` decimal(15,2) DEFAULT NULL,
  `estimated_hours` decimal(15,2) DEFAULT NULL,
  `addedfrom` int(11) NOT NULL
);

INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (1, 'Garbage - no dustbin 1', '<p>For categories with SLA &gt; 1 month, the facility to add &#8220;milestone 1, milestone 2...&#8221; would be available to the Action Taker and Reviewer. This would be a pre-fixed drop down list (as below) with timelines for completion of each that can be input by Action Reviewer:</p>', 'landmark 1', 6, 1, 1, '2020-5-3', '2020-6-8', '2020-5-3', NULL, 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (2, 'Road - repairing 2', 'Description 2', 'landmark 2', 1, 1, 1, '2020-5-3', '2020-6-12', '2020-5-3', NULL, 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (3, 'Garbage - no dustbin 3', 'Description 3', 'landmark 3', 1, 1, 1, '2020-5-3', '2020-6-30', '2020-5-3', '0000-0-0 00:00:00', 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (4, 'Road - repairing 4', 'Description 4', 'landmark 4', 2, 1, 1, '2020-5-3', '2020-6-16', '2020-5-3', '0000-0-0 00:00:00', 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (5, 'Garbage - no dustbin 5', 'Description 5', 'landmark 5', 2, 1, 1, '2020-5-3', '2020-6-30', '2020-5-3', '0000-0-0 00:00:00', 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (6, 'Road - repairing 6', 'Description 6', 'landmark 6', 4, 1, 1, '2020-5-3', '2020-6-12', '2020-5-3', '0000-0-0 00:00:00', 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (7, 'Garbage - no dustbin 7', 'Description 7', 'landmark 7', 3, 1, 1, '2020-5-3', '2020-6-30', '2020-5-3', '0000-0-0 00:00:00', 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (8, 'Road - repairing 8', 'Description 8', 'landmark 8', 2, 1, 1, '2020-5-3', '2020-6-30', '2020-5-3', '0000-0-0 00:00:00', 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (9, 'Garbage - no dustbin 9', 'Description 9', 'landmark 9', 5, 1, 1, '2020-5-3', '2020-6-9', '2020-5-3', '0000-0-0 00:00:00', 0, 1, 0.00, 0.00, 0.00, 1);
INSERT INTO `projects` (`id`, `name`, `description`, `landmark`, `status`, `clientid`, `billing_type`, `start_date`, `deadline`, `project_created`, `date_finished`, `progress`, `progress_from_tasks`, `project_cost`, `project_rate_per_hour`, `estimated_hours`, `addedfrom`) VALUES (10, 'Road - repairing 10', 'Description 10', 'landmark 10', 5, 1, 1, '2020-5-3', '2020-6-30', '2020-5-3', '0000-0-0 00:00:00', 0, 1, 0.00, 0.00, 0.00, 1);
