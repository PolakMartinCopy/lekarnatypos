<h1>Pohyby na centrálním skladě</h1>
<?php
	echo $this->element('search_forms/c_s_transactions');

	echo $form->create('CSV', array('url' => array('controller' => 'c_s_transactions', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($transactions)) { ?>
<p><em>V systému nejsou žádné pohyby.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'CSTransaction.date_of_issue')?></th>
		<th><?php echo $this->Paginator->sort('Č. dokladu', 'CSTransaction.code')?></th>
		<th><?php echo $this->Paginator->sort('Typ', 'CSTransaction.type')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'CSTransaction.business_partner_name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSTransaction.c_s_product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSTransaction.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'CSTransaction.unit_shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'CSTransaction.price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'CSTransaction.c_s_product_vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'CSTransaction.c_s_product_group_code')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($transactions as $transaction) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		$controller = 'c_s_invoices';
		if ($transaction['CSTransaction']['type'] == 'dobropis') {
			$controller = 'c_s_credit_notes';
		} elseif ($transaction['CSTransaction']['type'] == 'naskladění') {
			$controller = 'c_s_storings';
		}
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $transaction['CSTransaction']['date_of_issue']?></td>
		<td><?php echo $this->Html->link($transaction['CSTransaction']['code'], array('user' => false, 'controller' => $controller, 'action' => 'view_pdf', $transaction['CSTransaction']['id']), array('target' => '_blank')) ?></td>
		<td><?php echo $transaction['CSTransaction']['type']?></td>
		<td><?php echo $this->Html->link($transaction['CSTransaction']['business_partner_name'], array('controller' => 'business_partners', 'action' => 'view', $transaction['CSTransaction']['business_partner_id'], 'tab' => 16))?></td>
		<td><?php echo $transaction['CSTransaction']['c_s_product_name']?></td>
		<td><?php echo $transaction['CSTransaction']['quantity']?></td>
		<td><?php echo $transaction['CSTransaction']['unit_shortcut']?></td>
		<td><?php echo $transaction['CSTransaction']['price']?></td>
		<td><?php echo $transaction['CSTransaction']['c_s_product_vzp_code']?></td>
		<td><?php echo $transaction['CSTransaction']['c_s_product_group_code']?></td>
		<td><?php 
			echo $this->Html->link('Upravit', array('controller' => $controller, 'action' => 'edit', $transaction['CSTransaction']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('controller' => $controller, 'action' => 'delete', $transaction['CSTransaction']['id']), array(), 'Opravdu chcete transakci smazat?') . ' | ';
			echo $this->Html->link('Smazat položku', array('controller' => 'c_s_transaction_items', 'action' => 'delete', $transaction['CSTransaction']['item_id']), array(), 'Opravdu chcete položku vymazat?');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>

<?php } ?>