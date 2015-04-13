<h2>Detail zákazníka <?php echo $customer['Customer']['last_name']. ' ' . $customer['Customer']['first_name'] ?></h2>
<?php echo $this->Form->create('Customer')?>
<table>
	<tr>
		<td rowspan="2">
			<table class="tabulkaedit">
				<tr class="nutne">
					<td>Jméno</td>
					<td><?php echo $this->Form->input('Customer.first_name', array('label' => false, 'size' => 60)) ?></td>
				</tr>
				<tr class="nutne">
					<td>Příjmení</td>
					<td><?php echo $this->Form->input('Customer.last_name', array('label' => false, 'size' => 60)) ?></td>
				</tr>
				<tr>
					<td>Registrace</td>
					<td>
						<?php echo $customer['Customer']['created'] ?>
						<br />
						<?php echo $html->link('smazat zákazníka z databáze', array('controller' => 'customers', 'action' => 'delete', $customer['Customer']['id']), array(), 'Opravdu si přejete zákazníka odstranit z databáze?')?>
					</td>
				</tr>
				<tr class="nutne">
					<td>Telefon</td>
					<td><?php echo $this->Form->input('Customer.phone', array('label' => false)) ?></td>
				</tr>
				<tr class="nutne">
					<td>Email</td>
					<td><?php echo $this->Form->input('Customer.email', array('label' => false, 'size' => 60)) ?></td>
				</tr>
				<tr>
				<?php foreach ($customer['CustomerLogin'] as $index => $customer_login) { ?>
					<td>Login <?php echo $index + 1 ?></td>
					<td>
						<?php echo $this->Form->hidden('CustomerLogin.' . $index . '.id')?>
						<?php echo $this->Form->input('CustomerLogin.'. $index . '.login', array('label' => false)) ?>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td>Typ</td>
					<td><?php echo $this->Form->input('Customer.customer_type_id', array('label' => false, 'options' => $customer_types)) ?></td>
				</tr>
				<tr>
					<td>Zasílat novinky</td>
					<td><?php echo $this->Form->input('Customer.newsletter', array('label' => false))?></td>
				</tr>
				<tr>
					<td>Název společnosti</td>
					<td><?php echo $this->Form->input('Customer.company_name', array('label' => false, 'size' => 60)) ?></td>
				</tr>
				<tr>
					<td>IČ</td>
					<td><?php echo $this->Form->input('Customer.company_ico', array('label' => false)) ?></td>
				</tr>
				<tr>
					<td>DIČ</td>
					<td><?php echo $this->Form->input('Customer.company_dic', array('label' => false)) ?></td>
				</tr>
			</table>
		</td>
		<td>
			<strong>fakturační adresa</strong><br />
			<?
			foreach ( $customer['Address'] as $address ){
				if ( $address['type'] == 'f' ){
					echo $address['name'] . '<br />';
					echo $address['street'] . ' ' . $address['street_no'] . '<br />';
					echo $address['zip'] . ' ' . $address['city'] . '<br />';
					echo $html->link('smazat adresu', array('controller' => 'addresses', 'action' => 'delete', $address['id'])) . '<br />';
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<td>
			<strong>doručovací adresa</strong><br />
			<?
			foreach ( $customer['Address'] as $address ){
				if ( $address['type'] == 'd' ){
					echo $address['name'] . '<br />';
					echo $address['street'] . ' ' . $address['street_no'] . '<br />';
					echo $address['zip'] . ' ' . $address['city'] . '<br />';
					echo $html->link('smazat adresu', array('controller' => 'addresses', 'action' => 'delete', $address['id'])) . '<br />';
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<th colspan="2">Objednávky zákazníka:</th>
	</tr>
	<?php if (!empty($customer['Order'])) { ?>
		<tr>
			<th>ID obj.</th>
			<td>hodnota</td>
		</tr>
	<?php foreach ($customer['Order'] as $o) { ?>
		<tr>
			<th><?php echo $html->link($o['id'], array('controller' => 'orders', 'action' => 'view', $o['id']))?></th>
			<td><?php echo $o['orderfinaltotal']?>&nbsp;Kč</td>
		</tr>
	<?php }
		} else { ?>
		<tr>
			<td colspan="2">Zákazník zatím neprovedl žádné objednávky.</td>
		</tr>
	<?php } ?>
</table>
<?php echo $this->Form->hidden('Customer.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>