<?php 
	echo $this->element(REDESIGN_PATH . 'admin/combobox-init');
	
	if (!isset($empty)) {
		$empty = false;
	}
	echo $this->element(REDESIGN_PATH . 'admin/combobox-func', array('name' => $name, 'empty' => $empty, 'options' => $options));
?>