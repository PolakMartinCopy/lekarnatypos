<h1>Daňové třídy</h1>
<table class="tabulka" cellpadding="5" cellspacing="3">
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Název</th>
		<th>Hodnota daně</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
	<tr>
		<td colspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'tax_classes', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat daňovou třídu'));
		?></td>
		<td colspan="5">&nbsp;</td>
	</tr>
	<?php foreach ($tax_classes as $tax_class) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'tax_classes', 'action' => 'edit', $tax_class['TaxClass']['id']), array('escape' => false, 'title' => 'Upravit daňovou třídu'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'tax_classes', 'action' => 'delete', $tax_class['TaxClass']['id']), array('escape' => false, 'title' => 'Smazat daňovou třídu'), 'Opravdu chcete daňovou třídu smazat?');
		?></td>
		<td><?php echo $tax_class['TaxClass']['id'] ?></td>
		<td><?php echo $tax_class['TaxClass']['name'] ?></td>
		<td><?php echo $tax_class['TaxClass']['value'] ?>%</td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'tax_classes', 'action' => 'move_up', $tax_class['TaxClass']['id']), array('escape' => false, 'title' => 'Posunout nahoru'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'tax_classes', 'action' => 'move_down', $tax_class['TaxClass']['id']), array('escape' => false, 'title' => 'Posunout dolu'));
		?></td>
	</tr>
	<?php } ?>
</table>
<div class="prazdny"></div>