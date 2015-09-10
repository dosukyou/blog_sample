<?php

class ICC_Blog_Model_Resource_Data_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

	public function _construct() {
		$this->_init('blog/data');
	}

}
