<h2>Doprava a způsob platby</h2>
<div class="mainContentWrapper">
	<a id="orderAndPay" href="/customers/order_personal_info"><< Krok 1/4: Vložení osobních údajů</a>
	
	<?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'set_payment_and_shipping')))?>
	<fieldset>
		<legend>Doprava</legend>
		<?php if (!empty($shippings)) { ?>
		<table style="width:100%">
		<?php
			$first = true;
			foreach ($shippings as $shipping) { 
				$checked = '';
				if (isset($this->data['Order']['shipping_id']) && $this->data['Order']['shipping_id'] == $shipping['Shipping']['id']) {
					$checked = ' checked="checked"';
				}
				if (!isset($this->data['Order']['shipping_id']) && $first) {
					$checked = ' checked="checked"';
				}
		?>
			<tr>
				<td style="width:10%;padding:3px"><input name="data[Order][shipping_id]" type="radio" value="<?php echo $shipping['Shipping']['id']?>" id="OrderShippingId<?php echo $shipping['Shipping']['id']?>"<?php echo $checked?>/></td>
				<td style="width:30%;padding:3px"><?php echo $shipping['Shipping']['name']?></td>
				<td style="width:50%;padding:3px"><small><?php echo $shipping['Shipping']['description']?></small></td>
				<td style="width:10%;padding:3px"><?php echo round($shipping['Shipping']['price'])?>&nbsp;Kč</td> 
			</tr>
		<?php	$first = false; 
			} ?>
		</table>
		<?php } ?>
	</fieldset>
	
	<fieldset>
		<legend>Poznámka k objednávce</legend>
		<?php echo $this->Form->input('Order.comments', array('label' => false, 'cols' => 40, 'rows' => 5))?>
	</fieldset>
	
	<?php echo $this->Form->submit('>> Krok 3/4: rekapitulace objednávky', array('class' => 'btn btn-success'))?>
	<?php echo $this->Form->end()?>
</div>