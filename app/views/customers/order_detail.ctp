<div class="mainContentWrapper">
	<h2>Objednané položky</h2>
	
	<table id="recapProducts" cellpadding="5" cellspacing="0">
		<tr>
			<th style="width:70%">Název produktu</th>
			<th style="width:10%">Množství</th>
			<th>Cena za kus</th>
			<th>Cena celkem</th>
		</tr>
		<? foreach ( $order['OrderedProduct'] as $product ){ ?>
		<tr>
			<td><?php echo $product['Product']['name'] ?></td>
			<td><?php echo $product['product_quantity'] ?> ks</td>
			<td><?php echo $product['product_price_with_dph'] ?>Kč</td>
			<td align="right"><?php echo ($product['product_price_with_dph'] * $product['product_quantity']) ?> Kč</td>
		</tr>
		<?php } ?>
 		<tr>
			<td align="left" style="padding-top:10px">cena za zboží celkem:</td>
			<td colspan="3" align="right" style="padding-top:10px"><strong><?php echo $order['Order']['subtotal_with_dph'] ?> Kč</strong></td>
		</tr>
		<tr>
			<td>způsob doručení: <strong><?=$order['Shipping']['name']?></strong></td>
			<td>&nbsp;</td>
			<td align="right"><?=number_format($order['Order']['shipping_cost'], 0, ',', ' ')?>&nbsp;Kč</td>
			<td align="right"><?=number_format($order['Order']['shipping_cost'], 0, ',', ' ')?>&nbsp;Kč</td>
		</tr>
		<tr>
			<td colspan="2" align="left">celková cena objednávky:</td>
			<td colspan="2" class="totalPrice" align="right"><?php echo $order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'] ?> Kč</td>
		</tr>
	</table>

	<h2 style="padding-top:10px">Fakturační adresa</h2>
	<?=$order['Order']['customer_name']?><br />
	<?=$order['Order']['customer_street']?><br />
	<?=$order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city']?><br />
	<?=$order['Order']['customer_state']?><br />
	
	<h2 style="padding-top:10px">Doručovací adresa</h2>
	<?=$order['Order']['delivery_name']?><br />
	<?=$order['Order']['delivery_street']?><br />
	<?=$order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city']?><br />
	<?=$order['Order']['delivery_state']?><br />
</div>