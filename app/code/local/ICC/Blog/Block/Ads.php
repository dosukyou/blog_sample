<?php 

class ICC_Blog_Block_Ads extends Mage_Core_Block_Template { 

	public function getAds() {

		$ads = Mage::getModel('blog/ads')->load(1); 

		return $ads; 
	}

}