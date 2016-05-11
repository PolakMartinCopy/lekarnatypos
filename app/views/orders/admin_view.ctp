<table id="orderDisplay">
	<tr>
		<td valign="top">
		
		<? if ( !empty( $order['Order']['comments'] ) ){ ?>
			<h3>Komentář od zákazníka</h3>
				<p style="font-size:11px;color:blue;"><?=$order['Order']['comments']?></p>
		<? } ?>
	
		<h3>Objednávka č. <?=$order['Order']['id']?> (<?=strftime("%d.%m.%Y %H:%M", strtotime($order['Order']['created']))?>)</h3>
		<ul>
			<?php if (!$adminIsRestricted) { ?>
			<li><?=$html->link('Editovat objednávku', array('controller' => 'ordered_products', 'action' => 'edit', $order['Order']['id'])) ?></li>
			<?php } ?>
			<li><?php echo $this->Html->link('Zpět na seznam objednávek', array('controller' => 'orders', 'action' => 'index'))?></li>
		</ul>

		<table id="productList"  class="tabulka">
			<tr>
				<th style="width:70%">Název produktu</th>
				<th style="width:10%">Množství</th>
				<th style="width:10%">Cena<br />za kus</th>
				<th style="width:10%">Cena<br />celkem</th>
			</tr>
			<? foreach ($order['OrderedProduct'] as $product) {
					if ($product['show']) {
						// celkova cena za pocet kusu krat jednotkova cena
						$total_products_price = $product['product_quantity'] * $product['product_price_with_dph']; ?>
			<tr>
				<td><?php 
					if (!empty($product['Product'])) { 
						echo $html->link($product['Product']['name'] . '&nbsp;(' . $product['Product']['Manufacturer']['name'] . ')', '/' . $product['Product']['url'], array('escape' => false), false);
					} else {
						echo $product['product_name'];
					}
					// musim vyhodit atributy, pokud nejake produkt ma
					if (!empty( $product['OrderedProductsAttribute'])) {
						echo '<div class="orderedProductsAttributes">';
						foreach( $product['OrderedProductsAttribute'] as $attribute ) {
							echo '<span>- <strong>' . $attribute['Attribute']['Option']['name'] . '</strong>: ' . $attribute['Attribute']['value'] . '</span><br />';
						}
						echo '</div>';
					} ?></td>
				<td><?php echo $product['product_quantity'] ?>&nbsp;ks</td>
				<td align="right"><?php echo $product['product_price_with_dph'] ?>&nbsp;Kč</td>
				<td align="right"><?php echo $total_products_price ?>&nbsp;Kč</td>
			</tr>
			<?php }
			} ?>
		</table>
		<br/>
		<?php if (!empty($order['DiscountCoupon']['id'])) { ?>
		<table id="discountCoupon"  class="tabulka">
			<tr>
				<th style="width:80%">Slevový kupón</th>
				<th style="width:10%">ID</th>
				<th style="width:10%">hodnota</th>
			</tr>
			<tr>
				<td><?php echo $order['DiscountCoupon']['name']?></td>
				<td><?php echo $order['DiscountCoupon']['id']?></td>
				<td align="right"><?php echo $order['DiscountCoupon']['value']?>&nbsp;Kč</td>
			</tr>
		</table>
		<br/>
		<?php } ?>
		<table id="orderParameters"  class="tabulka">
			<?php if (!$adminIsRestricted) { ?>
			<tr>
				<th style="width:80%" align="right">cena za zboží celkem:</th>
				<td style="width:20%" align="right"><?=$order['Order']['subtotal_with_dph']?>&nbsp;Kč</td>
			</tr>
			<?php } ?>
			<tr>
				<td align="right">způsob doručení: <?=$order['Shipping']['name']?></td>
				<td align="right"><?=$order['Order']['shipping_cost']?>&nbsp;Kč</td>
			</tr>
			<?php if (!$adminIsRestricted) { ?>
			<tr>
				<th align="right">celková cena objednávky:</th>
				<td align="right"><?=( $order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'])?>&nbsp;Kč</td>
			</tr>
			<?php } ?>
		</table>
<?
			$color = '';
			if ( !empty($order['Status']['color']) ){
				$color = ' style="color:#' . $order['Status']['color'] . '"';
			}
?>
		<h3>Stav objednávky - <?='<span' . $color . '>' . $order['Status']['name'] . '</span>'?></h3>
<?
	if ( !empty ( $order['Ordernote'] ) ){
?>
		<h3>Poznámky</h3>
		<table class="topHeading" style="width:80%">
			<tr>
				<th>datum</th>
				<th>status</th>
				<th>kdo</th>
				<th>poznámka</th>
			</tr>
<?
		foreach ( $order['Ordernote'] as $note ){
			echo '
				<tr>
					<td>' . $note['created'] . '</td>
					<td>' . $note['Status']['name'] . '</td>
					<td>' . $note['Administrator']['first_name'] . ' ' . $note['Administrator']['last_name'] . '</td>
					<td>' . $note['note'] . '</td>
				</tr>
			';
		} ?>
		</table>
<?php 	} ?>
		<h3>Změna stavu / poznámka</h3>
		<?=$form->Create('Order', array('url' => array('action' => 'edit')))?>
		<fieldset  style="width:70%">
		<table class="leftHeading">
			<tr>
				<th>status:</th>
				<td>
					<?=$form->select('Order.status_id', $statuses, $order['Order']['status_id'], array('empty' => false))?>
				</td>
			</tr>
			<tr>
				<th>poznámka:</th>
				<td><?=$form->textarea('Ordernote.note', array('cols' => 70, 'rows' => 3))?></td>
			</tr>
			<tr>
				<th>číslo balíku:</th>
				<td><?=$form->text('Order.shipping_number')?></td>
			</tr>
			<tr>
				<th>variabilní symbol:</th>
				<td><?=$form->text('Order.variable_symbol')?></td>
			</tr>
		</table>
		<?=$form->hidden('Order.id', array('value' => $order['Order']['id']))?>
		</fieldset>
		<?=$form->end('změnit')?>
	</td>
	<td valign="top">
		<h3>Kontaktní údaje</h3>
		<span class="smallText"><?=$html->link('zobrazit profil', array('controller' => 'customers', 'action' => 'view', $order['Order']['customer_id'])) ?></span><br />
		<?
			//print_r ($order);
			echo 'jméno:&nbsp;' . $order['Customer']['first_name'] . '<br />';
			echo 'příjmení:&nbsp;' . $order['Customer']['last_name'] . '<br />';
			echo 'telefon:&nbsp;' . $order['Order']['customer_phone'] . '<br />';
			echo 'email:&nbsp;' . ife($order['Order']['customer_email'], $order['Order']['customer_email'], 'neuveden');
		?>
		<h3>Fakturační adresa</h3>
		<?
			$full_name =  full_name($order['Order']['customer_first_name'], $order['Order']['customer_last_name']);
			echo $order['Order']['customer_name'] . '<br />'
			. ife ($full_name && $full_name != $order['Order']['customer_name'], $full_name . '<br/>', '')
			. ife( $order['Order']['customer_ico'], 'IČO: ' . $order['Order']['customer_ico'] . '<br />', '' )
			. ife( $order['Order']['customer_dic'], 'DIČ: ' . $order['Order']['customer_dic'] . '<br />', '' )
			. $order['Order']['customer_street'] . '<br />'
			. $order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city'] . '<br />'
			. $order['Order']['customer_state'] . '<br />
			platba: <strong>' . $order['Payment']['name'] . '</strong>';
		?>
		<h3>Doručovací adresa</h3>
		<?
			$full_name =  full_name($order['Order']['delivery_first_name'], $order['Order']['delivery_last_name']);
			echo $order['Order']['delivery_name'] . '<br />'
			. ife ($full_name && $full_name != $order['Order']['delivery_name'], $full_name . '<br/>', '')
			. $order['Order']['delivery_street'] . '<br />'
			. $order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city'] . '<br />'
			. $order['Order']['delivery_state'] . '<br />
			doručení: <strong>' . $order['Shipping']['name'] . '</strong>';

			echo '<br />číslo balíku: ' . $html->link($order['Order']['shipping_number'], $order['Shipping']['tracker_prefix'] . trim($order['Order']['shipping_number']) . $order['Shipping']['tracker_postfix']);
			echo '<br />variabilní symbol: ' . $order['Order']['variable_symbol'];
		?>
	</td>
</tr>
</table>