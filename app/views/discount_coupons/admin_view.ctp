<h1>Slevový kupón</h1>
<?php echo $this->Html->link('ZPĚT NA SEZNAM KUPONŮ', array('controller' => 'discount_coupons', 'action' => 'index'))?>
<table class="tabulka">
	<tr>
		<th>ID</th>
		<th>Vytvořen</th>
		<th>Kód</th>
		<th>Hodnota</th>
		<th>Platnost</th>
		<th>Min. hodnota obj.</th>
		<th>Zákazník</th>
		<th>Objednávka</th>
	</tr>
	<tr>
		<td><?php echo $coupon['DiscountCoupon']['id']?></td>
		<td><?php echo cz_date_time($coupon['DiscountCoupon']['created'], '.')?></td>
		<td><?php echo $coupon['DiscountCoupon']['name']?></td>
		<td align="right"><?php echo format_price($coupon['DiscountCoupon']['value'])?></td>
		<td><?php echo ($coupon['DiscountCoupon']['valid_until'] ? cz_date($coupon['DiscountCoupon']['valid_until'], '.') : '')?></td>
		<td align="right"><?php echo ($coupon['DiscountCoupon']['min_amount'] ? format_price($coupon['DiscountCoupon']['min_amount']) : '')?></td>
		<td><?php echo ($coupon['DiscountCoupon']['customer_id'] ? $this->Html->link($coupon['Customer']['name'], array('controller' => 'customers', 'action' => 'view', $coupon['DiscountCoupon']['customer_id'])) : '')?></td>
		<td><?php echo ($coupon['DiscountCoupon']['order_id'] ? $this->Html->link($coupon['DiscountCoupon']['order_id'], array('controller' => 'orders', 'action' => 'view', $coupon['DiscountCoupon']['order_id'])) : '')?></td>
	</tr>
</table>
<?php if (!empty($couponProducts)) { ?>
<div class="prazdny"></div>
<h2>Produkty</h2>
<table class="tabulka">
	<tr>
		<th style="width:10%">ID</th>
		<th style="width:10%">Obrázek</th>
		<th>Název</th>
	</tr>
	<?php foreach ($couponProducts as $product) { ?>
	<tr>
		<td><?php echo $product['Product']['id']?></td>
		<td style="text-align:center"><?php echo (!empty($product['Product']['Image']) ? '<img src="/product-images/medium/' . $product['Product']['Image'][0]['name'] . '" height="80px" alt="' . $product['Product']['name'] . '" />' : '')?></td>
		<td><?php echo $this->Html->link($product['Product']['name'], '/' . $product['Product']['url'])?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<?php if (!empty($couponCategories)) { ?>
<div class="prazdny"></div>
<h2>Kategorie</h2>
<table class="tabulka">
	<tr>
		<th style="width:10%">ID</th>
		<th style="width:10%">Obrázek</th>
		<th>Název</th>
	</tr>
	<?php foreach ($couponCategories as $category) { ?>
	<tr>
		<td><?php echo $category['Category']['id']?></td>
		<td style="text-align:center"><?php echo (!empty($category['Category']['image']) ? '<img src="/images/categories/' . $category['Category']['image'] . '" height="80px" alt="' . $category['Category']['name'] . '" />' : '')?></td>
		<td><?php echo $this->Html->link($category['Category']['name'], '/' . $category['Category']['url'])?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>