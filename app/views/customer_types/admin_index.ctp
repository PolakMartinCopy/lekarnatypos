<h1>Cenové kategorie</h1>
<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Název</th>
		<th>Nahrazuje</th>
	</tr>
	<?php foreach ($customer_types as $customer_type) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'customer_types', 'action' => 'edit', $customer_type['CustomerType']['id']), array('escape' => false, 'title' => 'Upravit'));
		?></td>
		<td><?php echo $this->Html->link($customer_type['CustomerType']['id'], array('controller' => 'customer_types', 'action' => 'edit', $customer_type['CustomerType']['id']), array('title' => 'Upravit'))?></td>
		<td><?php echo $this->Html->link($customer_type['CustomerType']['name'], array('controller' => 'customer_types', 'action' => 'edit', $customer_type['CustomerType']['id']), array('title' => 'Upravit'))?></td>
		<td><?php echo $customer_type['CustomerTypeSubstitution']['name']?></td>
	</tr>
	<?php } ?>
</table>
<div class="prazdny"></div>