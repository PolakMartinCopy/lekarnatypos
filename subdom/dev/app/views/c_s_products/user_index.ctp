<h1>Číselník zboží - centrální sklad</h1>

<button id="search_form_show_c_s_products">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSProductSearch2']) ){
		$hide = '';
	}
?>
<div id="search_form_c_s_products"<?php echo $hide?>>
	<?php echo $form->create('CSProducts', array('url' => array('controller' => 'c_s_products', 'action' => 'index'))); ?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('CSProductSearch2.CSProduct.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $form->input('CSProductSearch2.CSProduct.vzp_code', array('label' => false))?></td>
			<th>Kód skupiny</th>
			<td><?php echo $form->input('CSProductSearch2.CSProduct.group_code', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => 'c_s_products', 'action' => 'index', 'reset' => 'c_s_products')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('CSProductSearch2.CSProduct.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_c_s_products").click(function () {
		if ($('#search_form_c_s_products').css('display') == "none"){
			$("#search_form_c_s_products").show("slow");
		} else {
			$("#search_form_c_s_products").hide("slow");
		}
	});
</script>

<?php
echo $form->create('CSV', array('url' => array('controller' => 'c_s_products', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($products)) { ?>
<p><em>Číselník zboží je prázdný.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Kód VZP', 'CSProduct.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'CSProduct.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Název', 'CSProduct.name')?></th>
		<th><?php echo $this->Paginator->sort('Jednotka', 'Unit.name')?></th>
		<th><?php echo $this->Paginator->sort('Skladová cena', 'CSProduct.store_price')?></th>
		<th><?php echo $this->Paginator->sort('Množství', 'CSProduct.quantity')?></th>
		<th><?php echo $this->Paginator->sort('DPH', 'TaxClass.value')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($products as $product) { ?>
	<tr>
		<td><?php echo $product['CSProduct']['vzp_code']?></td>
		<td><?php echo $product['CSProduct']['group_code']?></td>
		<td><?php echo $product['CSProduct']['name']?></td>
		<td><?php echo $product['Unit']['name']?></td>
		<td><?php echo round($product['CSProduct']['store_price'], 2)?></td>
		<td><?php echo $product['CSProduct']['quantity']?></td>
		<td><?php echo $product['TaxClass']['value']?></td>
		<td><?php
			echo $this->Html->link('Upravit', array('action' => 'edit', $product['CSProduct']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $product['CSProduct']['id']));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<?php } ?>