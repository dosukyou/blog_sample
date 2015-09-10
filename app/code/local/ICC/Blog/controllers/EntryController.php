<?php 

class ICC_Blog_EntryController extends Mage_Core_Controller_Front_Action { 


	public function viewAction() {

		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Blog - Entry'));
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();


	}

}