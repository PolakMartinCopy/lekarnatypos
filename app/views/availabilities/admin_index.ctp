<h2>Dostupnosti produktů</h2>
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
			echo $this->Html->link($icon, array('controller' => 'availabilities', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat dostupnost'));
		?></td>
		<td colspan="4">&nbsp;</td>
	</tr>
	<? foreach ($availabilities as $availability) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'availabilities', 'action' => 'edit', $availability['Availability']['id']), array('escape' => false, 'title' => 'Upravit dostupnost'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'availabilities', 'action' => 'delete', $availability['Availability']['id']), array('escape' => false, 'title' => 'Smazat dostupnost'), 'Opravdu chcete dostupnost produktu odstranit?');		
		?></td>
		<td><?php echo $availability['Availability']['id'] ?></td>
		<td><?php echo $availability['Availability']['name'] ?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'availabilities', 'action' => 'move_up', $availability['Availability']['id']), array('escape' => false, 'title' => 'Posunout nahoru'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'availabilities', 'action' => 'move_down', $availability['Availability']['id']), array('escape' => false, 'title' => 'Posunout dolu'));
		?></td>
	</tr>
	<?php } ?>
</table>