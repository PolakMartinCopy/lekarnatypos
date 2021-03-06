<h1>Uživatelé</h1>
<a href='/administrace/help.php?width=500&id=20' class='jTip' id='20' name='Uživatelé (20)'><img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' /></a>
<?php echo $this->Form->create('Customer')?>
<table class="tabulkaedit">
	<tr>
		<td>Fráze</td>
		<td><?php echo $this->Form->input('Customer.query', array('label' => false, 'type' => 'text', 'size' => 60))?></td>
	</tr>
	<tr>
		<td>nebo celková částka objednávek nad:</td>
		<td><?php echo $this->Form->input('Customer.orders_amount', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Z popup okna?</td>
		<td><?php echo $this->Form->input('Customer.is_popup', array('label' => false, 'type' => 'checkbox'))?></td>
	</tr>
	<tr>
		<td>CSV</td>
		<td><?php echo $this->Form->input('Customer.csv', array('label' => false, 'type' => 'checkbox'))?></td>
	</tr>
</table>
<br/>
<?php echo $this->Form->submit('Zobrazit')?>
<?php echo $this->Form->end()?>
<div class="prazdny"></div>

<?php
if (isset($this->data) && isset($this->Paginator)) {
	$this->Paginator->options(array('url' => $this->data['Customer']));
} ?>

<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('ID', 'Customer.id') : 'ID')?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Firma', 'Customer.company_name') : 'Firma') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Jméno / E-mail', 'Customer.name') : 'Jméno') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Cena', 'Customer.customer_type_name') : 'Cena') ?></th>
		<th>Město</th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Přihlášen', 'Customer.login_count') : 'Přihlášen') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Naposledy', 'Customer.login_date') : 'Naposledy') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Objednávek', 'Customer.orders_amount') : 'Objednávek') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Z popup?', 'Customer.is_popup') : 'Z popup?')?></th>
	</tr>
	<tr>
		<td colspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt=""/>';
			echo $this->Html->link($icon, array('controller' => 'customers', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat zákazníka'));
		?></td>
		<td colspan="8">&nbsp;</td>
	</tr>
	<?php if (isset($customers)) {
		foreach ($customers as $customer) { ?>
	<tr>
		<td><?php
			if (!$customer['Customer']['is_popup']) {
				$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
				echo $this->Html->link($icon, array('controller' => 'customers', 'action' => 'view', $customer['Customer']['id']), array('escape' => false, 'title' => 'Upravit zákazníka'));
			}
		?></td>
		<td><?php
			if (!$customer['Customer']['is_popup']) {
				$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/email.png" alt="" />';
				echo $this->Html->link($icon, array('controller' => 'customers', 'action' => 'send_login', $customer['Customer']['id']), array('escape' => false, 'title' => 'Poslat přístupové údaje'), 'Opravdu chcete uživateli poslat emailovou adresu s přístupovými údaji?');
			}
		?></td>
		<td><?php 
			if (!$customer['Customer']['is_popup']) {
				echo $customer['Customer']['id'];
			}
		 ?></td>
		<td><?php echo $customer['Customer']['company_name']?></td>
		<td>
			<?php echo $this->Html->link($customer['Customer']['name'], array('controller' => 'customers', 'action' => 'view', $customer['Customer']['id']))?><br/>
			<?php echo $customer['Customer']['email']?><br/>
			<?php echo $customer['Customer']['phone']?>
		</td>
		<td><?php echo $customer['Customer']['customer_type_name']?></td>
		<td><?php 
			$address = '';
			if (!empty($customer['Customer']['address_street']) || !empty($customer['Customer']['address_street_no']) || !empty($customer['Customer']['address_city']) || !empty($customer['Customer']['address_zip'])) {
				$address = $customer['Customer']['address_street'];
				if (!empty($customer['Customer']['address_street_no'])) {
					$address .= ' ' . $customer['Customer']['address_street_no'];
				}
				if (!empty($address)) {
					$address .= '<br/>';
				}
				$address .= '<strong>' . $customer['Customer']['address_city'] . '</strong><br/>';
				$address .= $customer['Customer']['address_zip'];
			}
			echo $address;
		?></td>
		<td><?php
			if (!$customer['Customer']['is_popup']) {
				echo $customer['Customer']['login_count'];
			}
		?></td>
		<td><?php
			if (!$customer['Customer']['is_popup']) {
				echo $customer['Customer']['login_date'];
			}
		?></td>
		<td><?php
			if (!$customer['Customer']['is_popup']) {
				echo $customer['Customer']['orders_count']?><br/><?php echo format_price($customer['Customer']['orders_amount']);
			}
		?></td>
		<td><?php echo ($customer['Customer']['is_popup'] ? 'ano' : 'ne')?></td>
	</tr>
	<?php }
	} ?>
</table>

<div>
<?
if (isset($this->Paginator)) {
	echo $this->Paginator->prev('<< Předchozí', array(), '<< Předchozí');
	echo '&nbsp;&nbsp;' . $this->Paginator->numbers() . '&nbsp;&nbsp;';
	echo $this->Paginator->next('Další >>', array(), 'Další >>');
}
?>
</div>

<br/>
<table class='legenda'>
	<tr>
		<th align='left'><strong>LEGENDA:</strong></th>
	</tr>
	<tr>
		<td>
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/add.png' width='16' height='16' /> ... přidat uživatele<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/pencil.png' width='16' height='16' /> ... upravit uživatele<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/email.png' width='16' height='16' /> ... poslat přístup <a href='/administrace/help.php?width=500&id=103' class='jTip' id='103' name='poslat přístup (103)'><img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' /></a><br />
		</td>
	</tr>
</table>
<div class='prazdny'></div>