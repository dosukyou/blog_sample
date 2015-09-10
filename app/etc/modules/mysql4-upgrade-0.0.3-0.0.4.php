<?php 

// ICC_Blog to add table and columns

$installer = $this; 
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$installer->run("
	ALTER TABLE `{$installer->getTable('blog/data')}`
	ADD `save_as_draft` int(2) DEFAULT 0;
");


$installer->endSetup();

?>
