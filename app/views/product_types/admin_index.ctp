<h1>Typy produktů</h1>

<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
	<tr>
		<td colspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_types', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat typ produktu'));
		?></td>
		<td colspan="4">&nbsp;</td>
	</tr>
	<?php foreach ($product_types as $product_type) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_types', 'action' => 'edit', $product_type['ProductType']['id']), array('escape' => false, 'title' => 'Upravit typ produktu'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_types', 'action' => 'delete', $product_type['ProductType']['id']), array('escape' => false, 'title' => 'Smazat typ produktu'), 'Opravdu chcete typ produktu smazat?');
		?></td>
		<td><?php echo $this->Html->link($product_type['ProductType']['id'], array('controller' => 'product_types', 'action' => 'edit', $product_type['ProductType']['id']))?></td>
		<td><?php echo $this->Html->link($product_type['ProductType']['name'], array('controller' => 'product_types', 'action' => 'edit', $product_type['ProductType']['id']))?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_types', 'action' => 'move_up', $product_type['ProductType']['id']), array('escape' => false, 'title' => 'Posunout nahoru'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_types', 'action' => 'move_down', $product_type['ProductType']['id']), array('escape' => false, 'title' => 'Posunout dolu'));
		?></td>
	</tr>
	<?php } ?>
	</tr>
</table>