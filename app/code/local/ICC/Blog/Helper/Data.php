<?php 

class ICC_Blog_Helper_Data extends Mage_Core_Helper_Abstract { 


	public function convertNameToUrlKey($name) {

		$urlKey = str_replace(' ', '-', strtolower($name));

		return $urlKey;
	}


	public function truncateCopy($text, $limit, $link) {
	    if (str_word_count($text, 0) > $limit) {
	        $words = str_word_count($text, 2);
	        $pos = array_keys($words);
	        $text = substr($text, 0, $pos[$limit]) . '... <a href="'.$link.'">READ MORE</a>';
	    }
	    return $text;
	}

	public function truncateCharacters($text, $limit, $link) {
		$text = strip_tags($text);
	    $text = (strlen($text) > $limit) ? substr($text, 0, $limit).'<a href="'.$link.'">...READ MORE</a>': $text;
	    return $text;
	}



}