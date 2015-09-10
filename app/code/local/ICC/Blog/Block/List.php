<?php 

class ICC_Blog_Block_List extends Mage_Core_Block_Template { 

	public function getEntries() {

		$collection = Mage::getModel('blog/data')->getCollection();

		if ($params = $this->getRequest()->getParams()) {
			$catUrlKey = array_keys($params);
			$catId = $this->_getCategoryId($catUrlKey[0]);
			$collection->addFieldToFilter('category_id', array('like' => '%'.$catId.'%'));
		}

		if ($search = Mage::registry('blog_search')) {

			$collection->addFieldToFilter('title', array('like' => '%'.$search.'%'));

		}


		if (Mage::getSingleton('customer/session')->isLoggedIn() && Mage::getSingleton('customer/session')->getCustomer()->getEmail() == 'user@domain.com') {

		} else { 

			$collection->addFieldToFilter('save_as_draft', array('eq' => 0));

		}


		return $collection; 

	}

	public function _getCategoryId($urlKey) {

		$collection = Mage::getModel('blog/categories')->getCollection(); 
		$collection->addFieldToFilter('url_key', array('like' => $urlKey)); 

		foreach ($collection as $category) {
			return $category->getData('category_id');
		}

	}



}