<h1><?php echo $page_heading?></h1>

<table id="cartContents" cellpadding="0" cellspacing="0" class="table basket-table">
	<thead>
		<tr>
			<th style="width:60%" colspan="2">Produkt</th>
			<th style="width:16%;text-align:right">Množství</th>
			<th style="width:9%;white-space:nowrap;text-align:right" class="hidden-xs hidden-sm">Cena za kus</th>
			<th style="width:9%;white-space:nowrap;text-align:right" class="hidden-xs hidden-sm">Cena celkem</th>
		</tr>
	</thead>
	<tbody>
<?		foreach ($order['OrderedProduct'] as $product) {
			$image = '/img/na_small.jpg';
			if (isset($product['Product']['Image']) && !empty($product['Product']['Image'])) {
				$path = 'product-images/small/' . $product['Product']['Image'][0]['name'];
				if (file_exists($path) && is_file($path) && getimagesize($path)) {
					$image = '/' . $path;
				}
			}
?>
		<tr>
			<td class="product" style="vertical-align:middle">
				<div class="image_holder" style="float:left">
					<a href="/<?php echo $product['Product']['url']?>">
						<img src="<?php echo $image?>" alt="Obrázek <?php echo $product['Product']['name']?>" width="45" />
					</a>
				</div>
			</td>
			<td  style="vertical-align:middle">
				<a href="/<?php echo $product['Product']['url'] ?>"><?php echo $product['Product']['name'] ?></a>
<?php			// musim vyhodit atributy, pokud nejake produkt ma
				if (!empty($product['OrderedProductsAttribute'])) {
					echo '<br /><div style="font-size:11px;padding-left:20px;">';
					foreach ($product['OrderedProductsAttribute'] as $attribute) {
					echo '<span>- <strong>' . $attribute['Attribute']['Option']['name'] . '</strong>: ' . $attribute['Attribute']['value'] . '</span><br />';
					}
					echo '</div>';
				} ?>
			</td>
			<td style="vertical-align:middle" align="right"><?php echo $product['product_quantity']?></td>
			<td class="price-per-unit hidden-xs hidden-sm" align="right" style="vertical-align:middle"><span class="price"><?php echo front_end_display_price($product['product_price_with_dph']) ?></span>&nbsp;Kč</td>
			<td class="price hidden-xs hidden-sm" align="right" style="vertical-align:middle"><span class="price"><?php echo front_end_display_price($product['product_price_with_dph'] * $product['product_quantity']) ?></span>&nbsp;Kč</td>
		</tr>
<?php	} ?>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="2" style="text-align:right;font-size:18px">objednané zboží celkem:</th>
			<td colspan="3" align="right" style="vertical-align:middle"><?=front_end_display_price($order['Order']['subtotal_with_dph'])?> Kč</td>
		</tr>
		<tr>
			<th colspan="2" style="text-align:right;font-size:18px">způsob dopravy:</th>
			<td colspan="2" align="right" style="vertical-align:middle"><?=$order['Shipping']['name']?></td>
			<td align="right" style="vertical-align:middle"><?=front_end_display_price($order['Order']['shipping_cost'])?>&nbsp;Kč</td>
		</tr>
		<tr>
			<th colspan="2" style="text-align:right;font-size:18px">celková cena objednávky:</th>
			<td colspan="3" align="right" style="vertical-align:middle;font-size:18px"><strong><?=front_end_display_price($order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'])?>&nbsp;Kč</strong></td>
		</tr>
		<?php if (isset($order['Order']['comments']) && !empty($order['Order']['comments'])) { ?>
		<tr>
			<th colspan="2" style="text-align:right;font-size:18px">váš komentář</th>
			<td colspan="3" align="right" style="vertical-align:middle"><?php echo $order['Order']['comments']?></td>
		</tr>
		<?php } ?>
	</tfoot>
</table>
<br/><br/>
<?php if ($order['Order']['shipping_id'] != PERSONAL_PURCHASE_SHIPPING_ID) { ?>
<table class="table">
	<tr>
		<th style="width:50%">Fakturační adresa</th>
		<th style="width:50%">Doručovací adresa</th>
	</tr>
	<tr>
		<td><?php
			echo $order['Order']['customer_name'] . '<br />';
			$full_name = full_name($order['Order']['customer_first_name'], $order['Order']['customer_last_name']);
			if ($full_name) {
				echo $full_name . '<br/>';
			}
			echo $order['Order']['customer_street']  . '<br />';
			echo $order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city'] . '<br />';
			echo $order['Order']['customer_state'];
		?></td>
		<td><?php 
			echo $order['Order']['delivery_name'] . '<br />';
			$full_name = full_name($order['Order']['delivery_first_name'], $order['Order']['delivery_last_name']);
			if ($full_name) {
				echo $full_name . '<br/>';
			}
			echo $order['Order']['delivery_street'] . '<br />';
			echo $order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city'] . '<br />';
			echo $order['Order']['delivery_state'];
		?></td>
	</tr>
</table>
<?php } ?>