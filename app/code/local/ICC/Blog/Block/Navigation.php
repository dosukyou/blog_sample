<?php 

class ICC_Blog_Block_Navigation extends Mage_Core_Block_Template { 

	public function getCategories() {

		return Mage::getModel('blog/categories')->getCollection()->addFieldToFilter('category_id', array('neq' => 6));

	}


	public function getPopularPost() {

		$collection = Mage::getModel('blog/data')->getCollection();
		$collection->addFieldToFilter('category_id', array('like' => '%6%'))
			->setPageSize(5);

		return $collection;

	}

	public function getActiveCategory() {

		$catUrlKey = false; 

		if ($params = $this->getRequest()->getParams()) {
			$keyes = array_keys($params); 
			$catUrlKey = $keyes[0];
		} 

		return $catUrlKey;
	}


}