<?php 
App::import('Model', 'Setting');
class HomepageBanner extends Setting {
	var $name = 'HomepageBanner';
	
	var $useTable = 'settings';

	var $width = 820;
	var $height = 150;
	
	function isActive() {
		return $this->findValue('HOMEPAGE_BANNER_ACTIVE');
	}
	
	function getImage() {
		return $this->findValue('HOMEPAGE_BANNER_IMAGE');
	}
	
	function getUrl() {
		return $this->findValue('HOMEPAGE_BANNER_URL');
	}
}
?>
