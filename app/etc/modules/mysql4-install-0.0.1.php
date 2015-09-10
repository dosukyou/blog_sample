<?php 

// ICC_Blog to add table and columns

$installer = $this; 
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->run("
	DROP TABLE IF EXISTS `{$this->getTable('blog/data')}`;
	CREATE TABLE `{$installer->getTable('blog/data')}` (
		`entry_id` int(11) NOT NULL auto_increment,
		`title` varchar(255),
		`subtitle` varchar(255),
		`thumbnail` varchar(255),
		`image` varchar(255),
		`content` text,
		`created_at` timestamp,
		`category_id` varchar(255),
		`url_key` varchar(255),
		PRIMARY KEY (`entry_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;


	DROP TABLE IF EXISTS `{$this->getTable('blog/categories')}`;
	CREATE TABLE `{$this->getTable('blog/categories')}` (
		`category_id` int(11) NOT NULL auto_increment,
		`name` varchar(255),
		`url_key` varchar(255),
		PRIMARY KEY (`category_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");


$installer->endSetup();

?>
