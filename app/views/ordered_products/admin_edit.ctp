<h2>Úprava objednávky č. <?=$id ?></h2>
<p><?=$html->link('zpět na objednávku', array('controller' => 'orders', 'action' => 'view', $order['Order']['id'])) ?></p>
<table id="productList" class="tabulka">
	<tr>
		<th style="width:35%">Objednaný produkt</th>
		<th style="width:15%">Změna atributů</th>
		<th style="width:15%">Množství</th>
		<th style="width:25%">Cena<br />za kus</th>
		<th style="width:10%">&nbsp;</th>
	</tr>
	<?
	foreach ( $products as $product ){
		// celkova cena za pocet kusu krat jednotkova cena
		$total_products_price = $product['OrderedProduct']['product_quantity'] * $product['OrderedProduct']['product_price_with_dph'];
	?>
	<tr>
		<td><?php 
			echo $product['Product']['name'];
			// musim vyhodit atributy, pokud nejake produkt ma
			if (!empty( $product['OrderedProductsAttribute'])) { ?>
			<div class="orderedProductAttributes">
			<? 	foreach( $product['OrderedProductsAttribute'] as $attribute ){ ?>
				<span>- <strong> <?=$attribute['Attribute']['Option']['name'] ?></strong>: <?=$attribute['Attribute']['value'] ?></span><br /> 
			<? 	} ?>
			</div>
			<? } ?>
			<br /><span style="font-size:11px">cena za kus: <strong><?php echo $product['OrderedProduct']['product_price_with_dph'] ?> Kč</strong></span>
		</td>
		<td><?php
			if ( !empty($product['Subs']) ){
				echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id']))); ?>
			<table style="font-size:10px"><?
				foreach ( $product['Subs'] as $sub ){
					if ( !empty($sub['Value']) ){ ?>
				<tr>
					<th align="right"><?php  $sub['Option']['name'] ?></th>
					<td>
						<select name="data[OrderedProduct][Option][<?php echo $sub['Option']['id'] ?>]" style="font-size:10px;">
							<?php
							foreach ( $sub['Value'] as $value ){
								$selected = '';
								foreach ( $product['OrderedProductsAttribute'] as $attr ){
									if ( $attr['attribute_id'] == $value['id']){
										$selected = ' selected="selected"';
									}
								} ?>
							<option value="' . $value['id'] . '"<?php echo  $selected ?>><?php echo $value['value'] ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<?php }
				} ?>
				<tr>
					<th>&nbsp;</th>
					<td><?=$form->submit('změnit atributy', array('div' => false, 'style' => 'margin-left:5px')) ?></td>
				</tr>
			</table>
			<?
				echo $form->hidden('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
				echo $form->hidden('OrderedProduct.change_switch', array('value' => 'attributes_change'));
				echo $form->end();
			} else {
				echo '&nbsp;';
			} ?>
		</td>
		<td><?
			echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
			echo $form->input('OrderedProduct.product_quantity', array('value' => $product['OrderedProduct']['product_quantity'], 'label' => false, 'div' => false, 'size' => 3)) . ' ks';
			echo $form->input('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
			echo $form->hidden('OrderedProduct.change_switch', array('value' => 'quantity_change'));
			echo $form->submit('změnit počet', array('div' => false, 'style' => 'margin-left:5px'));
			echo $form->end();
		?>
		</td>
		<td><?
			echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
		?>
			<select name="data[OrderedProduct][product_price_with_dph]" style="margin-bottom:5px">
				<option value="<?=$product['Product']['retail_price_with_dph'] ?>"<?=( $product['Product']['retail_price_with_dph'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "" ) ?>>
					základní cena: <?=$product['Product']['retail_price_with_dph'] ?> Kč
				</option>
			<?php if ($product['Product']['discount_common'] > 0) { ?>
				<option value="<?php echo $product['Product']['discount_common'] ?>"<?php echo ($product['Product']['discount_common'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "") ?>>
					běžná sleva: <?php echo $product['Product']['discount_common']?> Kč
				</option>
			<?php } ?>
			<?php if (isset($product['Product']['discount_member']) && $product['Product']['discount_member'] > 0) { ?>
				<option value="<?php echo $product['Product']['discount_member'] ?>"<?php echo ($product['Product']['discount_member'] == $product['OrderedProduct']['product_price_with_dph'] ? ' selected="selected"' : "") ?>>
					členská sleva: <?php echo $product['Product']['discount_member']?> Kč
				</option>
			<?php } ?>
			</select><br/>
			<span style="font-size:10px">ručně:</span> <?=$form->input('OrderedProduct.custom_price', array('label' => false, 'size' => 5, 'div' => false)); ?>
			<?
				echo $form->input('OrderedProduct.id', array('value' => $product['OrderedProduct']['id']));
				echo $form->hidden('OrderedProduct.change_switch', array('value' => 'price_change'));
				echo $form->submit('změnit cenu', array('div' => false, 'style' => 'margin-left:5px'));
				echo $form->end();
		?></td>
		<td>
			<?=$html->link('smazat produkt', array('controller' => 'ordered_products', 'action' => 'delete', $product['OrderedProduct']['id'])) ?>
		</td>
	</tr>
<? } ?>
</table>
<br/>


<table id="discountCoupon"  class="tabulka">
	<tr>
		<th style="width:65%">Slevový kupón</th>
		<th style="width:10%">hodnota</th>
	</tr>
	<tr>
		<td><?php
		echo $this->Form->create('DiscountCoupon', array('controller' => 'orders', 'action' => 'edit_order'));
		echo $this->Form->input('DiscountCoupon.name', array('label' => false, 'div' => false, 'before' => 'Kód:&nbsp;', 'value' => $order['DiscountCoupon']['name']));
		echo $this->Form->hidden('DiscountCoupon.order_id', array('value' => $order['Order']['id']));
		echo $this->Form->submit('Upravit', array('div' => false));
		echo $this->Form->end();
		?></td>
		<td align="right"><?php echo (!empty($order['DiscountCoupon']['value']) ? $order['DiscountCoupon']['value'] . '&nbsp;Kč' : '')?></td>
	</tr>
</table>
<br/>

<table id="orderParameters"  class="tabulka">
	<tr>
		<th style="width:65%" align="right">cena za zboží celkem:</th>
		<td style="width:35%" align="right"><?=$order['Order']['subtotal_with_dph']?> Kč</td>
	</tr>
	<tr>
		<td align="right">způsob doručení:</td>
		<td align="right">
			<?=$form->create('Order', array('url' => array('action' => 'edit_shipping', $order['Order']['id'])));?>
			<?=$form->select('Order.shipping_id', $shipping_choices, $order['Order']['shipping_id'], array('empty' => false));?>
			<?=$form->submit('změnit');?>
			<?=$form->end();?>
		</td>
	</tr>
	<tr>
		<td align="right">způsob platby:</td>
		<td align="right">
			<?=$form->create('Order', array('url' => array('action' => 'edit_payment', $order['Order']['id'])));?>
			<?=$form->select('Order.payment_id', $payment_choices, $order['Order']['payment_id'], array('empty' => false));?>
			<?=$form->submit('změnit');?>
			<?=$form->end();?>
		</td>
	</tr>
	<tr>
		<th align="right">celková cena objednávky:</th>
		<td align="right"><?=( $order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'])?> Kč</td>
	</tr>
</table>

<h3>Přidat nový produkt</h3>
<?=$form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id']))); ?>
<table>
	<tr>
		<td colspan="2">
			<p style="font-size:12px">Napište jakoukoliv část názvu produktu. ("metr" vyhledá např. gluko<strong>metr</strong> i tono<strong>metr</strong> ).</p>
		</td>
	</tr>
	<tr>
		<th>vyhledat produkt</th>
		<th><?=$form->input('OrderedProduct.query', array('label' => false)) ?></th>
	</tr>
</table>
<?=$form->submit('vyhledat') ?>
<?=$form->hidden('OrderedProduct.change_switch', array('value' => 'product_query')); ?>
<?=$form->end(); ?>
<br/>
<?
if ( isset($query_products) ){
	echo $form->create('OrderedProduct', array('url' => array('action' => 'edit', $order['Order']['id'])));
	echo $form->hidden('OrderedProduct.change_switch', array('value' => 'add_product'));
?>
<table class="tabulka">
<? foreach ( $query_products as $product ){ ?>
	<tr>
		<td><?php echo $this->Html->link($product['Product']['name'], '/' . $product['Product']['url'], array('target' => 'blank')) ?></td>
		<td><?
			if ( !empty($product['Subs']) ){ ?>
			<table style="font-size:10px"><?
				foreach ( $product['Subs'] as $sub ){
					if ( !empty($sub['Value']) ){ ?>
				<tr>
					<th align="right"><?=$sub['Option']['name']?></th>
					<td>
						<select name="data[OrderedProduct][<?=$product['Product']['id']?>][Option][<?=$sub['Option']['id']?>]" style="font-size:10px;">
						<? foreach ( $sub['Value'] as $value ){ ?>
							<option value="<?=$value['id']?>"><?=$value['value']?></option>
						<? } ?>
						</select>
					</td>
				</tr>
			<?		}
				} ?>
			</table><?
			} else {
				echo '&nbsp;';
			} ?>
		</td>
		<td><? echo $form->input('OrderedProduct.' . $product['Product']['id'] . '.product_quantity', array('value' => '1', 'label' => false, 'div' => false, 'size' => 3)) . ' ks';?></td>
		<td>
			<select name="data[OrderedProduct][<?=$product['Product']['id'] ?>][product_price_with_dph]">
				<option value="<?=$product['Product']['retail_price_with_dph'] ?>">
					základní cena: <?=$product['Product']['retail_price_with_dph'] ?> Kč
				</option>
				<?php if ($product['Product']['discount_common'] > 0) { ?>
				<option value="<?php echo $product['Product']['discount_common'] ?>">
					běžná sleva: <?php echo $product['Product']['discount_common']?> Kč
				</option>
				<?php } ?>
				<?php if (isset($product['Product']['discount_member']) && $product['Product']['discount_member'] > 0) { ?>
				<option value="<?php echo $product['Product']['discount_member'] ?>">
					členská sleva: <?php echo $product['Product']['discount_member']?> Kč
				</option>
				<?php } ?>
			</select>
			<br />
			<span style="font-size:10px">ručně:</span> <?=$form->input('OrderedProduct.' . $product['Product']['id'] . '.custom_price', array('label' => false, 'size' => 5)); ?>
		</td>
		<td>
			<?=$form->hidden('OrderedProduct.' . $product['Product']['id'] . '.product_id', array('value' => $product['Product']['id'])) ?>
			<?=$form->submit('přidat', array('name' => 'data[OrderedProduct][' . $product['Product']['id'] . '][add_it]', 'value' => $product['Product']['id'])) ?>
		</td>
	</tr>
	<? } ?>
</table>
<? echo $form->end();
}
?>