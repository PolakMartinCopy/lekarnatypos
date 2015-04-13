<h2>Stavy objednávek</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		
	</tr>
	<?php foreach ($statuses as $status) {?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'statuses', 'action' => 'edit', $status['Status']['id']), array('escape' => false));
		?></td>
		<td><?php echo $status['Status']['id'] ?></td>
		<td><?php echo $status['Status']['name'] ?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'statuses', 'action' => 'move_up', $status['Status']['id']), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'statuses', 'action' => 'move_down', $status['Status']['id']), array('escape' => false));
		?></td>
	</tr>
	<?php } ?>
</table>