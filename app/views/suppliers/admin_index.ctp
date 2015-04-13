<h1>Dodavatelé</h1>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th><?php echo $this->Paginator->sort('ID', 'Supplier.id')?></th>
		<th><?php echo $this->Paginator->sort('Název', 'Supplier.name')?></th>
		<th><?php echo $this->Paginator->sort('URL', 'Supplier.url')?></th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>				
	</tr>
	<tr>
		<td colspan="7"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'suppliers', 'action' => 'add'), array('escape' => false));
		?></td>
	</tr>
	<?php foreach ($suppliers as $supplier) { ?>
	<tr>
		<td><?php echo $supplier['Supplier']['id']?></td>
		<td><?php echo $this->Html->link($supplier['Supplier']['name'], array('controller' => 'suppliers', 'action' => 'edit', $supplier['Supplier']['id']))?></td>
		<td><?php echo $this->Html->link($supplier['Supplier']['url'], $supplier['Supplier']['url'], array('target' => '_blank'))?></td>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'suppliers', 'action' => 'edit', $supplier['Supplier']['id']), array('escape' => false));
		?></td>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'suppliers', 'action' => 'delete', $supplier['Supplier']['id']), array('escape' => false), 'Opravdu chcete dodavatele odstranit?');
		?></td>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/book.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'suppliers', 'action' => 'pair_categories', $supplier['Supplier']['id']), array('escape' => false));
		?></td>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/link_external.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'suppliers', 'action' => 'upload', $supplier['Supplier']['id'], 'admin' => false, 'force' => true), array('target' => '_blank', 'escape' => false));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php 
echo $paginator->numbers();
echo $paginator->prev('&lt;&lt; Předchozí ', array('escape' => false), null, array('class' => 'disabled', 'escape' => false));
echo $paginator->next(' Další &gt;&gt;', array('escape' => false), null, array('class' => 'disabled', 'escape' => false));
?>

<br/>
<table class="legenda">
	<tr>
		<th align="left"><strong>LEGENDA:</strong></th>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/add.png" width='16' height='16' /> ... přidat dodavatele</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/delete.png" width='16' height='16' /> ... smazat dodavatele</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/pencil.png" width='16' height='16' /> ... upravit dodavatele</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/link_external.png" width='16' height='16' /> ... vynutit upload</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/book.png" width='16' height='16' /> ... kategorie dodavatele</td>
	</tr>
</table>
<div class="prazdny"></div>