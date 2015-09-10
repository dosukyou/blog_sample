<?php 

class ICC_Blog_IndexController extends Mage_Core_Controller_Front_Action { 

	public function indexAction() { 

		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Nail Trends, Nail Care Tips, Nail Art Inspiration Blog'));
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();

	}

	public function searchAction() { 

		$params = $this->getRequest()->getParams();
		if (isset($params['search'])) {

			Mage::register('blog_search', $params['search']); 

		}


		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Nail Trends, Nail Care Tips, Nail Art Inspiration Blog'));
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();

	}

}