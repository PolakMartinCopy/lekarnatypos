<h1>Vlastnosti produktů</h1>
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
			echo $this->Html->link($icon, array('controller' => 'product_properties', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat vlastnost produktu'));
		?></td>
		<td colspan="4">&nbsp;</td>
	</tr>
	<?php foreach ($properties as $property) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_properties', 'action' => 'edit', $property['ProductProperty']['id']), array('escape' => false, 'title' => 'Upravit vlastnost produktu'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_properties', 'action' => 'delete', $property['ProductProperty']['id']), array('escape' => false, 'title' => 'Smazat vlastnost produktu'), 'Opravdu chcete vlastnost produktu odstranit?');		
		?></td>
		<td><?php echo $this->Html->link($property['ProductProperty']['id'], array('controller' => 'product_properties', 'action' => 'edit', $property['ProductProperty']['id']), array('title' => 'Upravit vlastnost produktu'))?></td>
		<td><?php echo $this->Html->link($property['ProductProperty']['name'], array('controller' => 'product_properties', 'action' => 'edit', $property['ProductProperty']['id']), array('title' => 'Upravit vlastnost produktu'))?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_properties', 'action' => 'move_up', $property['ProductProperty']['id']), array('escape' => false, 'title' => 'Posunout nahoru'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'product_properties', 'action' => 'move_down', $property['ProductProperty']['id']), array('escape' => false, 'title' => 'Posunout dolu'));
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
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/add.png' width='16' height='16' /> ... přidat vlastnost produktu<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/pencil.png' width='16' height='16' /> ... upravit vlastnost produktu<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/delete.png' width='16' height='16' /> ... smazat vlastnost produktu<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/up.png' width='16' height='16' /> ... změnit pořadí nahoru<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/down.png' width='16' height='16' /> ... změnit pořadí dolů
		</td>
	</tr>
</table><div class='prazdny'></div>