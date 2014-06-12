<script type="text/javascript">
	$(function() {
		var rowCount = $('.product_row').length; 

		$("#CSStoringDate").datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});

		$('table').delegate('.CSTransactionItemCSProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/c_s_products/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#CSTransactionItem' + count + 'CSProductId').val(ui.item.value);
					$('#CSTransactionItem' + count + 'CSProductName').val(ui.item.name);
					return false;
				}
			});
		});
		
		$('table').delegate('.addRowButton', 'click', function(e) {
			e.preventDefault();
			// pridat radek s odpovidajicim indexem na konec tabulky s addRowButton
			var tableRow = $(this).closest('tr');
			tableRow.after(productRow(rowCount));
			// zvysim pocitadlo radku
			rowCount++;
		});

		$('table').delegate('.removeRowButton', 'click', function(e) {
			e.preventDefault();
			var tableRow = $(this).closest('tr');
			tableRow.remove();
		});
	});

	function productRow(count) {
		count++;
		var rowData = '<tr rel="' + count + '">';
		rowData += '<td>';
		rowData += '<input name="data[CSTransactionItem][' + count + '][c_s_product_name]" type="text" class="CSTransactionItemCSProductName" size="50" id="CSTransactionItem' + count + 'CSProductName" />';
		rowData += '<input type="hidden" name="data[CSTransactionItem][' + count + '][c_s_product_id]" id="CSTransactionItem' + count + 'CSProductId" />';
		rowData += '</td>';
		rowData += '<td><input name="data[CSTransactionItem][' + count + '][description]" type="text" size="50" id="CSTransactionItem' + count + 'Description"></td>';
		rowData += '<td><input name="data[CSTransactionItem][' + count + '][quantity]" type="text" size="2" maxlength="11" id="CSTransactionItem' + count + 'Quantity" />ks</td>';
		rowData += '<td><input name="data[CSTransactionItem][' + count + '][price]" type="text" size="5" maxlength="11" id="CSTransactionItem' + count + 'Price" />Kč</td>';
		rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
		rowData += '</tr>';
		return rowData;
	}
</script>

<h1>Upravit naskladnění</h1>
<?php echo $this->Form->create('CSStoring', array('url' => array('controller' => 'c_s_storings', 'action' => 'edit', $storing['CSStoring']['id'])))?>
<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td>
			<?php echo $this->Form->input('CSStoring.date', array('label' => false, 'type' => 'text', 'div' => false))?>
			<?php echo $this->Form->input('CSStoring.time', array('label' => false, 'timeFormat' => '24', 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $this->Form->input('CSStoring.note', array('label' => false, 'cols' => 60, 'rows' => 5))?></td>
	</tr>
</table>
<h2>Položky</h2>
<table class="top_heading">
	<tr>
		<th>Zboží</th>
		<th>Popis</th>
		<th>Množství</th>
		<th>Nákupní cena</th>
		<th>&nbsp;</th>
	</tr>
<?php if (empty($this->data['CSTransactionItem'])) { ?>
	<tr rel="1">
		<td>
			<?php echo $this->Form->input('CSTransactionItem.1.c_s_product_name', array('label' => false, 'size' => 50, 'class' => 'CSTransactionItemCSProductName'))?>
			<?php echo $this->Form->error('CSTransactionItem.1.c_s_product_id')?>
			<?php echo $this->Form->hidden('CSTransactionItem.1.c_s_product_id')?>
		</td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.description', array('label' => false, 'size' => 50))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.quantity', array('label' => false, 'size' => 2, 'after' => 'ks'))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.price', array('label' => false, 'size' => 5, 'after' => 'Kč'))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php } else { ?>
<?php 	foreach ($this->data['CSTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td>
			<?php echo $this->Form->input('CSTransactionItem.' . $index . '.c_s_product_name', array('label' => false, 'size' => 50, 'class' => 'CSTransactionItemCSProductName'))?>
			<?php echo $this->Form->error('CSTransactionItem.' . $index . '.c_s_product_id')?>
			<?php echo $this->Form->hidden('CSTransactionItem.' . $index . '.c_s_product_id')?>
		</td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.description', array('label' => false, 'size' => 50))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 2, 'after' => 'ks'))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.price', array('label' => false, 'size' => 5, 'after' => 'Kč'))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php 	}?>
<?php }?>
</table>
<?php echo $this->Form->hidden('CSStoring.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>