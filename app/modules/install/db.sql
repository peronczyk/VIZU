--
-- `fields` table structure
--

CREATE TABLE IF NOT EXISTS `fields` (
	`num` INTEGER PRIMARY KEY AUTO_INCREMENT,
	`template` varchar(50) NOT NULL,
	`language` varchar(2) NOT NULL,
	`id` varchar(50) NOT NULL,
	`content` text,
	`subcontent` text,
	`created` timestamp NOT NULL,
	`modified` timestamp NOT NULL,
	`version` INTEGER NOT NULL
);


-- --------------------------------------------------------


--
-- `config` table structure
--

CREATE TABLE IF NOT EXISTS `users` (
	`id` INTEGER PRIMARY KEY AUTO_INCREMENT,
	`email` varchar(250) NOT NULL,
	`password` text
);


-- --------------------------------------------------------


--
-- `languages` table structure
--

CREATE TABLE IF NOT EXISTS `languages` (
	`code` varchar(2) PRIMARY KEY NOT NULL,
	`short_name` varchar(3) NOT NULL,
	`name` varchar(30),
	`country` varchar(30),
	`active` BOOLEAN NOT NULL DEFAULT TRUE
);

--
-- `languages` default data
--

INSERT INTO `languages` VALUES
	('pl', 'PL', 'Polski', 'Polska', '1'),
	('en', 'EN', 'English', 'Great Britain', '1');