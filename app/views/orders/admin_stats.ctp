<h1>Statistiky</h1>

<h2>Všechny nezrušené objednávky</h2>
<table class="tabulka">
	<tr>
		<th>Měsíc</th>
		<th>Počet objednávek</th>
		<th>Cena objednávek</th>
	</tr>
	<?php foreach ($no_storno_orders as $ns_order) { ?>
	<tr>
		<td><?php echo $ns_order['Order']['date']?></td>
		<td><?php echo $ns_order['Order']['count']?></td>
		<td><?php echo format_price($ns_order['Order']['income'])?></td>
	</tr>
	<?php } ?>
	<?php if (!empty($no_storno_orders_sum)) { ?>
	<tr>
		<td>&nbsp;</td>
		<td><strong><?php echo $no_storno_orders_sum['Order']['count']?></strong></td>
		<td><strong><?php echo format_price($no_storno_orders_sum['Order']['income'])?></strong></td>
	</tr>
	<?php } ?>
</table>

<h2>Vyřízené objednávky</h2>
<table class="tabulka">
	<tr>
		<th>Měsíc</th>
		<th>Počet objednávek</th>
		<th>Cena objednávek</th>
	</tr>
	<?php foreach ($finished_orders as $f_order) {?>
	<tr>
		<td><?php echo $f_order['Order']['date']?></td>
		<td><?php echo $f_order['Order']['count']?></td>
		<td><?php echo format_price($f_order['Order']['income'])?></td>
	</tr>
	<?php } ?>
		<?php if (!empty($finished_orders_sum)) { ?>
	<tr>
		<td>&nbsp;</td>
		<td><strong><?php echo $finished_orders_sum['Order']['count']?></strong></td>
		<td><strong><?php echo format_price($finished_orders_sum['Order']['income'])?></strong></td>
	</tr>
	<?php } ?>
</table>