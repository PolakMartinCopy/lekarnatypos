<h1><?php echo $page_heading?></h1>
<h2>Informace</h2>
<table class="table">
	<tr>
		<th>Jméno a příjmení:</th>
		<td><?=$customer['Customer']['first_name'] . ' ' . $customer['Customer']['last_name']?></td>
	</tr>
	<tr>
		<th>Telefon:</th>
		<td><?=$customer['Customer']['phone']?></td>
	</tr>
	<tr>
		<th>Email:</th>
		<td><?=ife( $customer['Customer']['email'], $customer['Customer']['email'], 'neuveden' )?></td>
	</tr>
<?php if (isset($customer['Customer']['company_name']) && !empty($customer['Customer']['company_name'])) { ?>
	<tr>
		<th>Název firmy:</th>
		<td><?php echo $customer['Customer']['company_name']?></td>
	</tr>
<?php } ?>
<?php if (isset($customer['Customer']['company_ico']) && !empty($customer['Customer']['company_ico'])) { ?>
	<tr>
		<th>IČ:</th>
		<td><?php echo $customer['Customer']['company_ico']?></td>
	</tr>
<?php } ?>
<?php if (isset($customer['Customer']['company_dic']) && !empty($customer['Customer']['company_dic'])) { ?>
	<tr>
		<th>IČ:</th>
		<td><?php echo $customer['Customer']['company_dic']?></td>
	</tr>
<?php } ?>
	<?php foreach ($customer['CustomerLogin'] as $customer_login) { ?>
	<tr>
		<th>Login:</th>
		<td><?=$customer_login['login']?></td>
	</tr>
	<tr>
		<th>Heslo:</th>
		<td>********</td>
	</tr>
	<?php } ?>
</table>
<?=$html->link('editovat', array('controller' => 'customers', 'action' => 'edit'))?><br/><br/>

<h2>Objednávky</h2>
<?
	if (empty($customer['Order'])){
		echo 'Nevytvořil(a) jste zatím žádnou objednávku.';
	} else {
		$count = count($customer['Order']);
?>
<table class="table">
	<tr>
		<th>Číslo</th>
		<th>Vytvořena</th>
		<th>Cena</th>
		<th>Stav</th>
		<th>&nbsp;</th>
	</tr>
	<?
		for ($i = 0; ($i < 3 && $i < $count); $i++ ){
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
	if ($customer_orders_count > 3){
		echo 'Zobrazeny jsou poslední tři objednávky z ' . $customer_orders_count . ' celkem.<br />';
	}
	echo $html->link('zobrazit seznam objednávek', array('controller' => 'customers', 'action' => 'orders_list'));
?>
<? } ?>
<br/><br/>
<h2>Adresář</h2>
<table class="table">
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