<h1>Dodavatelé</h1>
<ul>
	<li><?php echo $this->Html->link('Přidat dodavatele', array('controller' => 'suppliers', 'action' => 'add'))?></li>
</ul>
<?php if (empty($suppliers)) { ?>
<p><em>V systému nejsou žádní dodavatelé</em></p>
<?php } else { ?>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th><?php echo $this->Paginator->sort('ID', 'Supplier.id')?></th>
		<th><?php echo $this->Paginator->sort('Název', 'Supplier.name')?></th>
		<th><?php echo $this->Paginator->sort('URL', 'Supplier.url')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($suppliers as $supplier) { ?>
	<tr>
		<td><?php echo $supplier['Supplier']['id']?></td>
		<td><?php echo $supplier['Supplier']['name']?></td>
		<td><?php echo $this->Html->link($supplier['Supplier']['url'], $supplier['Supplier']['url'], array('target' => '_blank'))?></td>
		<td><?php 
			$links = array();
			$links[] = $this->Html->link('Kategorie', array('controller' => 'suppliers', 'action' => 'pair_categories', $supplier['Supplier']['id']));
			$links[] = $this->Html->link('Vynutit upload', array('controller' => 'suppliers', 'action' => 'upload', $supplier['Supplier']['id'], 'admin' => false, 'force' => true), array('target' => '_blank'));
			$links[] = $this->Html->link('Upravit', array('controller' => 'suppliers', 'action' => 'edit', $supplier['Supplier']['id']));
			$links[] = $this->Html->link('Smazat', array('controller' => 'suppliers', 'action' => 'delete', $supplier['Supplier']['id']), array(), 'Opravdu chcete dodavatele odstranit?');
			echo implode('&nbsp| ', $links);
		?></td>
	</tr>
	<?php } ?>
</table>
<?php 
echo $paginator->numbers();
echo $paginator->prev('&lt;&lt; Předchozí ', array('escape' => false), null, array('class' => 'disabled', 'escape' => false));
echo $paginator->next(' Další &gt;&gt;', array('escape' => false), null, array('class' => 'disabled', 'escape' => false));
?>
<?php } ?>