<h2>Doprava a způsob platby</h2>
<div class="mainContentWrapper">
	<a id="orderAndPay" href="/customers/order_personal_info"><< Krok 1/4: Vložení osobních údajů</a>
	
	<?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'set_payment_and_shipping')))?>
	<fieldset>
		<legend>Doprava</legend>
		<?php if (!empty($providers)) { ?>
		<table style="width:100%">
		<?php
			$first = true;
			foreach ($providers as $provider) {
				$show_provider_row = true;
				foreach ($provider['shippings'] as $shipping) { 
					$checked = '';
					if (isset($this->data['Order']['shipping_id']) && $this->data['Order']['shipping_id'] == $shipping['Shipping']['id']) {
						$checked = ' checked="checked"';
					}
					if (!isset($this->data['Order']['shipping_id']) && $first) {
						$checked = ' checked="checked"';
					}
		?>
			<tr>
				<?php
					$border_top = '';  
					if ($show_provider_row) {
						if (!$first) {
							$border_top = ';border-top:1px solid #c0c0c0';
						}
				 ?>
				<td style="width:10%;padding:3px<?php echo $border_top?>" nowrap rowspan="<?php echo count($provider['shippings'])?>" valign="top" ><?php echo $provider['Shipping']['provider_name']?></td>
				<?php
					$show_provider_row = false; 
				} ?>
				<td style="width:5%;padding:3px<?php echo $border_top?>"><input name="data[Order][shipping_id]" type="radio" value="<?php echo $shipping['Shipping']['id']?>" id="OrderShippingId<?php echo $shipping['Shipping']['id']?>"<?php echo $checked?>/></td>
				<td style="width:35%;padding:3px<?php echo $border_top?>"><?php echo $shipping['Shipping']['name']?></td>
				<td style="width:40%;padding:3px<?php echo $border_top?>"><small><?php echo $shipping['Shipping']['description']?></small></td>
				<td style="width:10%;padding:3px<?php echo $border_top?>"><?php echo round($shipping['Shipping']['price'])?>&nbsp;Kč</td> 
			</tr>
		<?php		$first = false;
				}
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