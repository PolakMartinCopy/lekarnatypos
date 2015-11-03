<div id="search_form_products">
<?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'index')))?>
<table class="tabulka">
	<tr>
		<th>Kategorie</th>
		<td><?php echo $this->element(REDESIGN_PATH . 'admin/combobox', array('name' => 'AdminProductForm.Category.id', 'options' => $categories))?></td>
	</tr>
	<tr>
		<th rowspan="2">Výraz</th>
		<td><?php echo $this->Form->input('AdminProductForm.Product.fulltext1', array('label' => false, 'type' => 'text', 'div' => false, 'size' => 50)); ?></td>
	</tr>
	<tr>
		<td><?php echo $this->Form->input('AdminProductForm.Product.fulltext2', array('label' => false, 'type' => 'text', 'div' => false, 'size' => 50)); ?></td>
	</tr>
	<tr>
		<th>Vlastnost</th>
		<td><?php echo $this->Form->input('AdminProductForm.Product.search_property_id', array('label' => false, 'type' => 'select', 'options' => $search_properties, 'empty' => false))?></td>
	</tr>
	<tr>
		<th>Alliance?</th>
		<td><?php echo $this->Form->input('AdminProductForm.Product.is_alliance', array('label' => false, 'type' => 'select', 'options' => $is_alliance, 'empty' => false))?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo $this->Html->link('reset filtru a řazení', array('reset' => 'products')) ?></td>
	</tr>
</table>
<?php
	echo $this->Form->submit('Vyhledat', array('div' => false));
	echo $form->hidden('AdminProductForm.Product.search_form', array('value' => true));
	echo $form->end();
?>
</div>