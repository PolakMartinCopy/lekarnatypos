<h1>Párování kategorií</h1>
<h2><?php echo $supplier['Supplier']['name']?></h2>
<ul>
	<li><?php echo $this->Html->link('Zpět na seznam dodavatelů', array('controller' => 'suppliers', 'action' => 'index'))?></li>
</ul>


<?php if (empty($this->data['SupplierCategory'])) { ?>
<p><em>Nejsou žádné kategorie ke spárování</em></p>
<?php } else { ?>
<?php echo $this->Form->create('Supplier', array('url' => array('controller' => 'suppliers', 'action' => 'pair_categories', $supplier['Supplier']['id'])))?>
<table class="topHeading" cellspacing="3" cellpadding="5">
	<tr>
		<th>Kategorie dodavatele</th>
		<th>Kategorie u nás</th>
	</tr>
	<?php foreach ($this->data['SupplierCategory'] as $index => $supplier_category) { ?>
	<tr>
		<td><?php
			echo $this->Form->hidden('SupplierCategory.' . $index . '.id');
			echo $this->Form->hidden('SupplierCategory.' . $index . '.name');
			echo $supplier_category['name'];
		?></td>
		<td><?php echo $this->Form->input('SupplierCategory.' . $index . '.category_id', array('label' => false, 'options' => $categories, 'empty' => true))?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>
<?php }?>