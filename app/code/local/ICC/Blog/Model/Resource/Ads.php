<?php

class ICC_Blog_Model_Resource_Ads extends Mage_Core_Model_Resource_Db_Abstract {

	public function _construct() {   

		$this->_init('blog/ads', 'entry_id');

	}


}