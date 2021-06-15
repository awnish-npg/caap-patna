ALTER TABLE `tasks` ADD `reminderone_notified` DATE NULL DEFAULT NULL AFTER `deadline_notified`, ADD `remindertwo_notified` DATE NULL DEFAULT NULL AFTER `reminderone_notified`;
