<div class="mainContentWrapper">
	<span style="font-size:24px">Informace o zákazníkovi</span> - <?=$html->link('editovat', array('controller' => 'customers', 'action' => 'edit'))?>
	<table class="leftHeading" style="width:100%">
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
		<tr>
			<th>login:</th>
			<td><?=$customer['Customer']['login']?></td>
		</tr>
		<tr>
			<th>heslo:</th>
			<td>********</td>
		</tr>
	</table>
	
	<h2>Zákazníkovy objednávky</h2>
<?	$count = count($customer['Order']);
	if (empty($customer['Order'])){ ?>
	<p><em>Nevytvořil(a) jste zatím žádnou objednávku.</em></p>
<?php } else { ?>
	<table class="topHeading" style="width:100%">
		<tr>
			<th>číslo</th>
			<th>vytvořena</th>
			<th>cena</th>
			<th>stav</th>
			<th>&nbsp;</th>
		</tr>
<? 		for ( $i = 0; ( $i < 3 && $i < $count ); $i++ ){ ?>
		<tr>
			<td><?=$customer['Order'][$i]['id']?></td>
			<td><?=$customer['Order'][$i]['created']?></td>
			<td><?=($customer['Order'][$i]['subtotal_with_dph'] + $customer['Order'][$i]['shipping_cost']) . '&nbsp;Kč' ?></td>
			<td><?
					$color = '';
					if ( !empty($customer['Order'][$i]['Status']['color']) ){
						$color = ' style="color:#' . $customer['Order'][$i]['Status']['color'] . '"';
					}
					echo '<span' . $color . '>' . $customer['Order'][$i]['Status']['name'] . '</span>';
			?></td>
			<td><?=$html->link('detaily', array('controller' => 'customers', 'action' => 'order_detail', $customer['Order'][$i]['id']));?></td>
		</tr>
<?		} ?>
		<tr>
			<td colspan="5"><?
				if ( $count > 3 ){
					echo 'Zobrazeny jsou poslední tři objednávky z ' . $count . ' celkem.<br />';
				}
				echo $html->link('zobrazit seznam objednávek', array('controller' => 'customers', 'action' => 'orders_list'));
			?></td>
		</tr>
	</table>
<? } ?>

	<h2 style="padding-top:10px">Adresář zákazníka</h2>
	<table class="topHeading" width="100%">
		<tr>
			<th>Fakturační adresa</th>
			<th>Doručovací adresa</th>
		</tr>
		<tr>
			<td><?
				foreach ( $customer['Address'] as $address ){
					if ( $address['type'] == 'f' ){
						echo $address['name'] . '<br />' . $address['street'] . ' ' . $address['street_no'] . '<br />' . $address['zip'] . ' ' . $address['city'] . '<br />' . $address['state'];
					}
				}
			?></td>
			<td><?
				foreach ( $customer['Address'] as $address ){
					if ( $address['type'] == 'd' ){
						echo $address['name'] . '<br />' . $address['street'] . ' ' . $address['street_no'] . '<br />' . $address['zip'] . ' ' . $address['city'] . '<br />' . $address['state'];
					}
				}
			?></td>
		</tr>
		<tr>
			<td><?=$html->link('upravit', array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'f')) ?></td>
			<td><?=$html->link('upravit', array('controller' => 'customers', 'action' => 'address_edit', 'type' => 'd')) ?></td>
		</tr>
	</table>
</div>