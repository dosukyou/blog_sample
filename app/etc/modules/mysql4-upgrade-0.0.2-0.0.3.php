<?php 

// ICC_Blog to add table and columns

$installer = $this; 
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->run("
	DROP TABLE IF EXISTS `{$this->getTable('blog/ads')}`;
	CREATE TABLE `{$installer->getTable('blog/ads')}` (
		`entry_id` int(11) NOT NULL auto_increment,
		`side_ad_1` varchar(255),
		`side_ad_1_link` varchar(255),
		`side_ad_2` varchar(255),
		`side_ad_2_link` varchar(255),
		`side_ad_3` varchar(255),
		`side_ad_3_link` varchar(255),
		`instagram_banner` varchar(255),
		PRIMARY KEY (`entry_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->endSetup();

?>
