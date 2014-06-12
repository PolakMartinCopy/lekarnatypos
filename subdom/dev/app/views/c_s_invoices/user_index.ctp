<h1>Faktury</h1>
<?php
	echo $this->element('search_forms/c_s_invoices');

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_invoices', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($invoices)) { ?>
<p><em>V systému nejsou žádné faktury.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Číslo', 'CSInvoice.code')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Datum vystavení', 'CSInvoice.date_of_issue')?></th>
		<th><?php echo $this->Paginator->sort('Datum splatnosti', 'CSInvoice.due_date')?></th>
		<th><?php echo $this->Paginator->sort('Celková částka', 'CSInvoice.amount')?></th>
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
	foreach ($invoices as $invoice) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $this->Html->link($invoice['CSInvoice']['code'], array('user' => false, 'action' => 'view_pdf', $invoice['CSInvoice']['id']), array('target' => '_blank'))?></td>
		<td><?php echo $this->Html->link($invoice['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $invoice['BusinessPartner']['id'], 'tab' => 14))?></td>
		<td><?php echo $invoice['CSInvoice']['date_of_issue']?></td>
		<td><?php echo czech_date($invoice['CSInvoice']['due_date'])?></td>
		<td><?php echo $invoice['CSInvoice']['amount']?></td>
		<td><?php echo $invoice['CSTransactionItem']['c_s_product_name']?></td>
		<td><?php echo $invoice['CSTransactionItem']['quantity']?></td>
		<td><?php echo $invoice['Unit']['shortcut']?></td>
		<td><?php echo $invoice['CSTransactionItem']['price']?></td>
		<td><?php echo $invoice['CSProduct']['vzp_code']?></td>
		<td><?php echo $invoice['CSProduct']['group_code']?></td>
		<td><?php echo $invoice['User']['last_name']?></td>
		<td><?php 
			echo $this->Html->link('Upravit', array('action' => 'edit', $invoice['CSInvoice']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $invoice['CSInvoice']['id']), array(), 'Opravdu chcete fakturu smazat?') . ' | ';
			echo $this->Html->link('Smazat položku', array('controller' => 'c_s_transaction_items', 'action' => 'delete', $invoice['CSTransactionItem']['id']), array(), 'Opravdu chcete položku vymazat?');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>

<?php } ?>