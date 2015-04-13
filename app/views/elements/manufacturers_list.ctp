<?=$form->create('Manufacturer', array('action' => 'show')) ?>
<?
	$selected = null;
	if ( isset($this->params['manufacturer_id']) ){
		$selected = $this->params['manufacturer_id'];
	}

	if ( isset($product['Manufacturer']['id']) ){
		$selected = $product['Manufacturer']['id'];
	}
	
	echo '<div>' . $form->select('Manufacturer.id', $manufacturers, $selected, array('empty' => false)) . '</div>';
?>
<?=$form->submit('zobrazit produkty') ?>
<?=$form->end() ?>