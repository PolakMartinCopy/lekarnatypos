<?php 
App::import('Model', 'Setting');
class HomepageBanner extends Setting {
	var $name = 'HomepageBanner';
	
	var $useTable = 'settings';

	var $width = 668;
	var $height = 150;
}
?>