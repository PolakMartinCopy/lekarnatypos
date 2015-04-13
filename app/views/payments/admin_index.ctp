<h1>Nastavení plateb</h1>

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
			echo $this->Html->link($icon, array('controller' => 'payments', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat způsob platby'));
		?></td>
		<td colspan="4">&nbsp;</td>
	</tr>
	<?php foreach ($payments as $payment) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'payments', 'action' => 'edit', $payment['Payment']['id']), array('escape' => false, 'title' => 'Upravit způsob platby'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'payments', 'action' => 'delete', $payment['Payment']['id']), array('escape' => false, 'title' => 'Smazat způsob platby'), 'Opravdu chcete způsob platby odstranit?');		
		?></td>
		<td><?php echo $this->Html->link($payment['Payment']['id'], array('controller' => 'payments', 'action' => 'edit', $payment['Payment']['id']), array('title' => 'Upravit způsob platby'))?></td>
		<td><?php echo $this->Html->link($payment['Payment']['name'], array('controller' => 'payments', 'action' => 'edit', $payment['Payment']['id']), array('title' => 'Upravit způsob platby'))?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'payments', 'action' => 'move_up', $payment['Payment']['id']), array('escape' => false, 'title' => 'Posunout nahoru'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'payments', 'action' => 'move_down', $payment['Payment']['id']), array('escape' => false, 'title' => 'Posunout dolu'));
		?></td>
	<?php } ?>
</table>
<br/>
<table class='legenda'>
	<tr>
		<th align='left'><strong>LEGENDA:</strong></th>
	</tr>
	<tr>
		<td>
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/add.png' width='16' height='16' /> ... přidat platbu<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/pencil.png' width='16' height='16' /> ... upravit platbu<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/delete.png' width='16' height='16' /> ... smazat platbu<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/up.png' width='16' height='16' /> ... změnit pořadí nahoru<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/down.png' width='16' height='16' /> ... změnit pořadí dolů
		</td>
	</tr>
</table><div class='prazdny'></div>