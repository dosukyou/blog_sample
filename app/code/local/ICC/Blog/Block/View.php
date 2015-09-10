<?php 

class ICC_Blog_Block_View extends Mage_Core_Block_Template { 

	public function getEntry() {

		$entries = Mage::getModel('blog/data')->getCollection();
		if ($params = $this->getRequest()->getParams()) {
			$entryUrlKey = array_keys($params);
			if(isset($entryUrlKey[0])) $entries->addFieldToFilter('url_key', array('like' => $entryUrlKey[0]));
		}

		foreach ($entries as $entry) {
			$post = Mage::getModel('blog/data')->load($entry->getEntryId());
			return $post;
		}

		return false;

	}

}