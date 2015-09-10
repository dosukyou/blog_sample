<?php

class ICC_Blog_Model_Resource_Categories extends Mage_Core_Model_Resource_Db_Abstract {

	public function _construct() {   

		$this->_init('blog/categories', 'category_id');

	}


}