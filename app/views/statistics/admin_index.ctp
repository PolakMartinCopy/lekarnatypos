<h2>Statistiky</h2>
<fieldset>
	<legend>Filtr</legend>
	<?php echo $this->Form->create('Statistic')?>
	<table>
		<tr>
			<th style="width:25%">Od</th>
			<td style="width:25%"><?php echo $this->Form->input('Statistic.from', array('label' => false, 'type' => 'text'))?></td>
			<th style="width:25%">Do</th>
			<td style="width:25%"><?php echo $this->Form->input('Statistic.to', array('label' => false, 'type' => 'text'))?></td>
		</tr>
		<tr>
			<th>Doprava</th>
			<td><?php echo $this->Form->input('Statistic.shipping_id', array('label' => false, 'options' => $shippings, 'empty' => true))?></td>
			<th>Stav</th>
			<td><?php echo $this->Form->input('Statistic.status_id', array('label' => false, 'options' => $states, 'empty' => true))?></td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $this->Html->link('reset filtru', array('controller' => 'statistics', 'action' => 'index', 'reset' => true))?></td>
		</tr>
	</table>
	<?php echo $this->Form->submit('Odeslat')?>
	<?php echo $this->Form->end()?>
</fieldset>

<h3>Objednávky</h3>
<p>V daném období bylo uskutečněno <strong><?php echo count($orders) ?> objednávek</strong> v celkové hodnotě <strong><?=format_price($orders_income)?></strong>.</p>
<table class="topHeading">
	<tr>
		<th>ID objednávky</th>
		<th>Zákazník</th>
		<th>Datum</th>
		<th>Produktů</th>
		<th>Cena bez dopravy</th>
		<th>Cena bez DPH<br/>a bez dopravy</th>
		<th>Nákupní cena</th>
		<th>Marže</th>
		<th>Celková cena</th>
	</tr>
	<?php if (empty($orders)) { ?>
	<tr>
		<td colspan="9">V daném intervalu nejsou žádné objednávky.</td>
	</tr>
	<?php } else {
		$total_products_count = 0;
		$total_subtotal_wout_dph = 0;
		$total_subtotal_with_dph = 0;
		$total_price = 0;
		$total_wholesale_price = 0;
		$total_margin = 0;
		foreach ($orders as $order) {
			$total_products_count += $order[0]['Order__products_count'];
			$total_subtotal_wout_dph += $order['Order']['subtotal_wout_dph'];
			$total_subtotal_with_dph += $order['Order']['subtotal_with_dph'];
			$total_price += $order[0]['Order__price'];
//			$total_wholesale_price += $order[0]['Order__wholesale_price'];
//			$total_margin += $order[0]['Order__margin'];
	?>
	<tr>
		<td><?php echo $this->Html->link($order['Order']['id'], array('controller' => 'orders', 'action' => 'view', $order['Order']['id']), array('target' => '_blank'))?></td>
		<td><?php echo $this->Html->link($order['Order']['customer_name'], array('controller' => 'customers', 'action' => 'view', $order['Order']['customer_id']), array('target' => '_blank'))?></td>
		<td><?php echo $order[0]['Order__date'] ?></td>
		<td align="right"><?php echo $order[0]['Order__products_count']?></td>
		<td align="right"><?php echo format_price($order['Order']['subtotal_with_dph']) ?></td>
		<td align="right"><?php echo format_price($order['Order']['subtotal_wout_dph'])?></td>
		<td align="right"><?php echo format_price($order[0]['Order__wholesale_price'])?></td>
		<td align="right"><?php echo format_price($order[0]['Order__margin'])?></td>
		<td align="right"><?php echo format_price($order[0]['Order__price']) ?></td>
	</tr>
<?php 	} ?>
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th align="right"><?php echo $total_products_count ?></th>
		<th align="right"><?php echo format_price($total_subtotal_with_dph) ?></th>
		<th align="right"><?php echo format_price($total_subtotal_wout_dph) ?></th>
		<th align="right">&nbsp;<?php //echo format_price($total_wholesale_price) ?></th>
		<th align="right">&nbsp;<?php //echo format_price($total_margin) ?></th>
		<th align="right"><?php echo format_price($total_price) ?></th>
	</tr>
<?php } ?>
</table>


<h3>Prodávané produkty</h3>
<table class="topHeading">
	<tr>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Název produktu', 'Product.name') : 'Název produktu') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Prodané množství', 'Product.ordered_count') : 'Prodané množství') ?></th>
	</tr>
	<?php if (empty($products)) { ?>
	<tr>
		<td colspan="3">V daném intervalu nejsou žádné prodané produkty.</td>
	</tr>
	<?php } else {
		$total_quantity = 0;
		foreach ($products as $product) {
			$total_quantity += $product[0]['Product__ordered_count'];
	?>
	<tr>
		<td><?php echo $this->Html->link($product['Product']['name'], '/' . $product['Product']['url'], array('target' => '_blank'))?></td>
		<td align="right"><?php echo $product[0]['Product__ordered_count']?></td>
	</tr>
<?php 	} ?>
	<tr>
		<th>&nbsp;</th>
		<th align="right"><?php echo $total_quantity ?></th>
	</tr>
<?php } ?>
</table>

<div class='prazdny'></div>

<script>
	$(function() {
		var dateFromId = 'StatisticFrom';
		var dateToId = 'StatisticTo';
		var dates = $('#' + dateFromId + ',#' + dateToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dateFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>