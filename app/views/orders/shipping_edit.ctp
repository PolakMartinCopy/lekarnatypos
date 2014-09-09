<div class="mainContentWrapper">
<?=$form->Create('Order', array('url' => '/orders/shipping_edit'))?>
	<fieldset>
		<legend>Detaily objednávky</legend>
		<div class="form-group">
			<label>Způsob doručení<sup>*</sup></label>
<?php		if ( !isset($this->data['Order']['shipping_id']) ){
				$this->data['Order']['shipping_id'] = null;
			}
			echo $form->select('Order.shipping_id', $shipping_choices, $this->data['Order']['shipping_id'], array('empty' => false, 'class' => 'form-control'));
?>
		</div>
		<div class="form-group">
			<label>Váš komentář k objednávce</label>
			<?=$form->textarea('Order.comments', array('cols' => 40, 'rows' => 5, 'class' => 'form-control'))?>
		</div>
	</fieldset>
<?php
	echo $form->Submit('Rekapitulace objednávky', array('class' => 'btn btn-success'));
	echo $form->end();
?>
</div>