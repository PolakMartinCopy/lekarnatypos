<h3 class="star">Výrobci</h3>
<?php
	$selected = null;
	if ( isset($this->params['manufacturer_id']) ){
		$selected = $this->params['manufacturer_id'];
	}

	if ( isset($product['Manufacturer']['id']) ){
		$selected = $product['Manufacturer']['id'];
	}
	
	echo $this->Form->select('Manufacturer.id', $manufacturers_list, $selected, array('empty' => 'Vyberte výrobce', 'id' => 'ManufacturerSelect'));
?>