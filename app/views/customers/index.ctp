<h2><?php echo $page_heading?></h2>
<h3>Informace o zákazníkovi</h3>
<table class="topHeading" style="width:100%">
	<tr>
		<th>Jméno a příjmení:</th>
		<td><?=$customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name']?></td>
	</tr>
	<tr>
		<th>telefon:</th>
		<td><?=$customer['Customer']['phone']?></td>
	</tr>
	<tr>
		<th>email:</th>
		<td><?=ife( $customer['Customer']['email'], $customer['Customer']['email'], 'neuveden' )?></td>
	</tr>
	<?php foreach ($customer['CustomerLogin'] as $customer_login) { ?>
	<tr>
		<th>login:</th>
		<td><?=$customer_login['login']?></td>
	</tr>
	<tr>
		<th>heslo:</th>
		<td>********</td>
	</tr>
	<?php } ?>
</table>
<?=$html->link('editovat', array('controller' => 'customers', 'action' => 'edit'))?><br/><br/>

<h3>Zákazníkovy objednávky</h3>
<?
	if (empty($customer['Order'])){
		echo 'Nevytvořil(a) jste zatím žádnou objednávku.';
	} else {
		$count = count($customer['Order']);
?>
<table class="topHeading" style="width:100%">
	<tr>
		<th>číslo</th>
		<th>vytvořena</th>
		<th>cena</th>
		<th>stav</th>
		<th>&nbsp;</th>
	</tr>
	<?
		for ($i = 0; ( $i < 3 && $i < $count ); $i++ ){
	?>

	<tr>
		<td><?=$customer['Order'][$i]['id']?></td>
		<td><?=cz_date_time($customer['Order'][$i]['created'])?></td>
		<td><?=front_end_display_price($customer['Order'][$i]['subtotal_with_dph'] + $customer['Order'][$i]['shipping_cost']) . '&nbsp;Kč' ?></td>
		<td><?
				$color = '';
				if ( !empty($customer['Order'][$i]['Status']['color']) ){
					$color = ' style="color:#' . $customer['Order'][$i]['Status']['color'] . '"';
				}
				echo '<span' . $color . '>' . $customer['Order'][$i]['Status']['name'] . '</span>';
			?>
		</td>
		<td>
			<?=$html->link('detaily', array('controller' => 'customers', 'action' => 'order_detail', $customer['Order'][$i]['id']));?>
		</td>
	</tr>
	<? } ?>
</table>
<?
	if ( $count > 3 ){
		echo 'Zobrazeny jsou poslední tři objednávky z ' . $count . ' celkem.<br />';
	}
	echo $html->link('zobrazit seznam objednávek', array('controller' => 'customers', 'action' => 'orders_list'));
?>
<? } ?>
<br/><br/>
<h3>Adresář zákazníka</h3>
<table class="topHeading" width="100%">
	<tr>
		<th>Fakturační adresa</th>
		<th>Doručovací adresa</th>
	</tr>
	<tr>
		<td>
			<?
			foreach ( $customer['Address'] as $address ){
				if ( $address['type'] == 'f' ){
					echo $address['name'] . '<br />' . $address['street'] . ' ' . $address['street_no'] . '<br />' . $address['zip'] . ' ' . $address['city'] . '<br />' . $address['state'];
				}
			}
			?>
		</td>
		<td>
			<?
			foreach ( $customer['Address'] as $address ){
				if ( $address['type'] == 'd' ){
					echo $address['name'] . '<br />' . $address['street'] . ' ' . $address['street_no'] . '<br />' . $address['zip'] . ' ' . $address['city'] . '<br />' . $address['state'];
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<td><?=$html->link('upravit', array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'f')) ?></td>
		<td><?=$html->link('upravit', array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'd')) ?></td>
	</tr>
</table>