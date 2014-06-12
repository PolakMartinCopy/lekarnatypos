<h1>Dobropisy</h1>
<?php
	echo $this->element('search_forms/c_s_credit_notes');

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_credit_notes', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($credit_notes)) { ?>
<p><em>V systému nejsou žádné dobropisy.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Číslo', 'CSCreditNote.code')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Datum vystavení', 'CSCreditNote.date_of_issue')?></th>
		<th><?php echo $this->Paginator->sort('Datum splatnosti', 'CSCreditNote.due_date')?></th>
		<th><?php echo $this->Paginator->sort('Celková částka', 'CSCreditNote.amount')?></th>
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
	foreach ($credit_notes as $credit_note) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $this->Html->link($credit_note['CSCreditNote']['code'], array('user' => false, 'action' => 'view_pdf', $credit_note['CSCreditNote']['id']), array('target' => '_blank'))?></td>
		<td><?php echo $this->Html->link($credit_note['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $credit_note['BusinessPartner']['id'], 'tab' => 15))?></td>
		<td><?php echo $credit_note['CSCreditNote']['date_of_issue']?></td>
		<td><?php echo czech_date($credit_note['CSCreditNote']['due_date'])?></td>
		<td><?php echo $credit_note['CSCreditNote']['amount']?></td>
		<td><?php echo $credit_note['CSTransactionItem']['c_s_product_name']?></td>
		<td><?php echo $credit_note['CSTransactionItem']['quantity']?></td>
		<td><?php echo $credit_note['Unit']['shortcut']?></td>
		<td><?php echo $credit_note['CSTransactionItem']['price']?></td>
		<td><?php echo $credit_note['CSProduct']['vzp_code']?></td>
		<td><?php echo $credit_note['CSProduct']['group_code']?></td>
		<td><?php echo $credit_note['User']['last_name']?></td>
		<td><?php 
			echo $this->Html->link('Upravit', array('action' => 'edit', $credit_note['CSCreditNote']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $credit_note['CSCreditNote']['id']), array(), 'Opravdu chcete dobropis smazat?') . ' | ';
			echo $this->Html->link('Smazat položku', array('controller' => 'c_s_transaction_items', 'action' => 'delete', $credit_note['CSTransactionItem']['id']), array(), 'Opravdy chcete položku vymazat?');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>

<?php } ?>