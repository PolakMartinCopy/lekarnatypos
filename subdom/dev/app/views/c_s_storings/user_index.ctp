<h1>Naskladnění</h1>
<?php
	echo $this->element('search_forms/c_s_storings');

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_storings', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($storings)) { ?>
<p><em>V systému nejsou žádné naskladnění.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum nas.', 'CSStoring.date')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSTransactionItem.c_s_product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSTransactionItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'CSTransactionItem.price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'CSProduct.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'CSProduct.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Naskladnil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($storings as $storing) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($storing['CSStoring']['date'])?></td>
		<td><?php echo $storing['CSTransactionItem']['c_s_product_name']?></td>
		<td><?php echo $storing['CSTransactionItem']['quantity']?></td>
		<td><?php echo $storing['Unit']['shortcut']?></td>
		<td><?php echo $storing['CSTransactionItem']['price']?></td>
		<td><?php echo $storing['CSProduct']['vzp_code']?></td>
		<td><?php echo $storing['CSProduct']['group_code']?></td>
		<td><?php echo $storing['User']['last_name']?></td>
		<td><?php 
			echo $this->Html->link('Upravit', array('action' => 'edit', $storing['CSStoring']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $storing['CSStoring']['id']), array(), 'Opravdu chcete naskladnění smazat?') . ' | ';
			echo $this->Html->link('Smazat položku', array('controller' => 'c_s_transaction_items', 'action' => 'delete', $storing['CSTransactionItem']['id']));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>

<?php } ?>