<h1>Způsoby dopravy</h1>
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
			echo $this->Html->link($icon, array('controller' => 'shippings', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat způsob dopravy'));
		?></td>
		<td colspan="4">&nbsp;</td>
	</tr>
	<?php if (!empty($shippings)) {
		foreach ($shippings as $shipping) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'shippings', 'action' => 'edit', $shipping['Shipping']['id']), array('escape' => false, 'title' => 'Upravit způsob dopravy'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'shippings', 'action' => 'delete', $shipping['Shipping']['id']), array('escape' => false, 'title' => 'Smazat způsob dopravy'), 'Opravdu chcete způsob dopravy odstranit?');
		?></td>
		<td><?php echo $this->Html->link($shipping['Shipping']['id'], array('controller' => 'shippings', 'action' => 'edit', $shipping['Shipping']['id']))?></td>
		<td><?php echo $this->Html->link($shipping['Shipping']['name'], array('controller' => 'shippings', 'action' => 'edit', $shipping['Shipping']['id']))?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'shippings', 'action' => 'move_up', $shipping['Shipping']['id']), array('escape' => false, 'title' => 'Posunout nahoru'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'shippings', 'action' => 'move_down', $shipping['Shipping']['id']), array('escape' => false, 'title' => 'Posunout dolu'));
		?></td>
	</tr>
	<?php } 
	} ?>
</table>
<div class="prazdny"></div>