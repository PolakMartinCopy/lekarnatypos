<script type="text/javascript">
	$(function() {
		var rowCount = $('.product_row').length; 
		
		$("#CSCreditNoteDueDate").datepicker({
			changeMonth: false,
			numberOfMonths: 1
		});

		$('#CSCreditNoteBusinessPartnerName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				$('#CSCreditNoteBusinessPartnerName').val(ui.item.label);
				$('#CSCreditNoteBusinessPartnerId').val(ui.item.value);
				return false;
			}
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

		$('table').delegate('.deleteRowButton', 'click', function(e) {
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

<h1>Upravit dobropis</h1>
<?php
$form_options = array();
if (isset($business_partner)) {
?>
<ul>
	<li><?php echo $this->Html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']))?></li>
</ul>
<?php 
	$form_options = array('url' => array('business_partner_id' => $business_partner['BusinessPartner']['id']));
}
echo $this->Form->create('CSCreditNote', $form_options);
?>
<table class="left_heading">
	<tr>
		<th>Komu:</th>
		<td colspan="4"><?php 
			if (isset($business_partner)) {
				echo $this->Form->input('CSCreditNote.business_partner_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Form->input('CSCreditNote.business_partner_name', array('label' => false, 'size' => 50));
				echo $this->Form->error('CSCreditNote.business_partner_id');
			}
			echo $this->Form->hidden('CSCreditNote.business_partner_id')
		?></td>
	</tr>
	<tr>
		<th>Datum splatnosti</th>
		<td colspan="4">
			<?php echo $this->Form->input('CSCreditNote.due_date', array('label' => false, 'type' => 'text', 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $this->Form->input('CSCreditNote.note', array('label' => false, 'cols' => 60, 'rows' => 5))?></td>
	</tr>
</table>
<h2>Položky</h2>
<table class="top_heading">
	<tr>
		<th>Zboží</th>
		<th>Popis</th>
		<th>Množství</th>
		<th>Prodejní cena</th>
		<th>&nbsp;</th>
	</tr>
	<?php if (empty($this->data['CSTransactionItem'])) { ?>
	<tr rel="0">
		<td>
			<?php echo $this->Form->input('CSTransactionItem.0.c_s_product_name', array('label' => false, 'size' => 50, 'class' => 'CSTransactionItemCSProductName'))?>
			<?php echo $this->Form->error('ProductsTransaction.0.product_id')?>
			<?php echo $this->Form->hidden('CSTransactionItem.0.c_s_product_id')?>
		</td>
		<td><?php echo $this->Form->input('CSTransactionItem.0.description', array('label' => false, 'size' => 50))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.0.quantity', array('label' => false, 'size' => 2, 'after' => 'ks'))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.0.price', array('label' => false, 'size' => 5, 'after' => 'Kč'))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="deleteRowButton" href="#">-</a>
		</td>
	</tr>
	<?php } else { ?>
	<?php foreach ($this->data['CSTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td>
			<?php echo $this->Form->input('CSTransactionItem.' . $index . '.c_s_product_name', array('label' => false, 'size' => 50, 'class' => 'CSTransactionItemCSProductName'))?>
			<?php echo $this->Form->error('ProductsTransaction.' . $index . '.product_id')?>
			<?php echo $this->Form->hidden('CSTransactionItem.' . $index . '.c_s_product_id')?>
		</td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.description', array('label' => false, 'size' => 50))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 2, 'after' => 'ks'))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.price', array('label' => false, 'size' => 5, 'after' => 'Kč'))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="deleteRowButton" href="#">-</a>
		</td>
	</tr>
	<?php } ?>
	<?php } ?>
</table>
<?php
	echo $this->Form->hidden('CSCreditNote.id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>