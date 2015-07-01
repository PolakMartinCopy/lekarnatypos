<h2>Třídy atributů</h2>
<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th><?php echo $paginator->sort('ID', 'id');?></th>
		<th><?php echo $paginator->sort('Název atr.', 'name');?></th>
	</tr>
	<tr>
		<td colspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'options', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat třídu atributů'));
		?></td>
		<td colspan="2">&nbsp;</td>
	</tr>
<?php foreach ($options as $option) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'options', 'action' => 'edit', $option['Option']['id']), array('escape' => false, 'title' => 'Upravit třídu atributů'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'options', 'action' => 'delete', $option['Option']['id']), array('escape' => false, 'title' => 'Smazat třídu atributů'), 'Opravdu chcete třídu atributů odstranit?');		
		?></td>
		<td><?php echo $this->Html->link($option['Option']['id'], array('controller' => 'options', 'action' => 'edit', $option['Option']['id']))?></td>
		<td><?php echo $this->Html->link($option['Option']['name'], array('controller' => 'options', 'action' => 'edit', $option['Option']['id'])) ?></td>
	</tr>
<?php } ?>
</table>
<div class='prazdny'></div>