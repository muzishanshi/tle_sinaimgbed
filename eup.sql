ALTER TABLE `{db_prefix}block` ADD COLUMN `attack_num` int(10) NOT NULL default '0' AFTER `serverip`;

DELETE FROM `{db_prefix}options` WHERE option_name= 'webscan_seconds';
DELETE FROM `{db_prefix}options` WHERE option_name= 'webscan_refresh';
DELETE FROM `{db_prefix}options` WHERE option_name= 'cc_switch';


INSERT INTO `{db_prefix}options` (option_name, option_value) VALUES ('attacks','10');
