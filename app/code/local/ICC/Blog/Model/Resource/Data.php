<?php

class ICC_Blog_Model_Resource_Data extends Mage_Core_Model_Resource_Db_Abstract {

	public function _construct() {   

		$this->_init('blog/data', 'entry_id');

	}


}