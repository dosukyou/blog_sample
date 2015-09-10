<?php

class ICC_Blog_Model_Resource_Categories_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

	public function _construct() {
		$this->_init('blog/categories');
	}

}
