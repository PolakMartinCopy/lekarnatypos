<div id="search_form_orders">
<?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'index')))?>
<table class="tabulka">
	<tr>
		<th style="width:25%">Od</th>
		<td style="width:25%"><?php echo $this->Form->input('AdminOrderForm.Order.from', array('label' => false, 'type' => 'text'))?></td>
		<th style="width:25%">Do</th>
		<td style="width:25%"><?php echo $this->Form->input('AdminOrderForm.Order.to', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th rowspan="2">Výraz</th>
		<td colspan="3"><?php echo $this->Form->input('AdminOrderForm.Order.fulltext1', array('label' => false, 'type' => 'text', 'div' => false, 'size' => 50)); ?></td>
	</tr>
	<tr>
		<td colspan="3"><?php echo $this->Form->input('AdminOrderForm.Order.fulltext2', array('label' => false, 'type' => 'text', 'div' => false, 'size' => 50)); ?></td>
	</tr>
	<tr>
		<td colspan="4"><?php echo $this->Html->link('reset filtru a řazení', array('reset' => 'orders') + $this->passedArgs) ?></td>
	</tr>
</table>
<?php
	echo $this->Form->submit('Vyhledat', array('div' => false));
	echo $form->hidden('AdminOrderForm.Order.search_form', array('value' => true));
	echo $form->end();
?>
</div>

<script>
	$(function() {
		var dateFromId = 'AdminOrderFormOrderFrom';
		var dateToId = 'AdminOrderFormOrderTo';
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