<h1>Související produkty</h1>
<?php 
$back_link = array('controller' => 'products', 'action' => 'index');
echo $this->Html->link('ZPĚT NA SEZNAM PRODUKTŮ', $back_link)?>
<br /><br />
<h2><?php echo $product['Product']['name']?></h2>
<?php if (isset($category)) { ?>
<h4><?php echo $category['Category']['name']?></h4>
<?php } ?>

<?php echo $this->element(REDESIGN_PATH . 'admin/product_menu')?>

<div class='prazdny'></div>

<?php if (!empty($related_products)) { ?> 
<table class="tabulka">
	<tr>
		<th>ID</th>
		<th>Název produktu</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($related_products as $related_product) { ?>
	<tr>
		<td><?php echo $related_product['Product']['id']?></td>
		<td><?php echo $this->Html->link($related_product['Product']['name'], array('/' . $related_product['Product']['url']))?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'related_products', 'action' => 'delete', $related_product['RelatedProduct']['id'], (isset($opened_category_id) ? $opened_category_id : false), (isset($this->data['Category']['id']) ? $this->data['Category']['id'] : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'related_products', 'action' => 'move_up', $related_product['RelatedProduct']['id'], (isset($opened_category_id) ? $opened_category_id : false), (isset($this->data['Category']['id']) ? $this->data['Category']['id'] : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'related_products', 'action' => 'move_down', $related_product['RelatedProduct']['id'], (isset($opened_category_id) ? $opened_category_id : false), (isset($this->data['Category']['id']) ? $this->data['Category']['id'] : null)), array('escape' => false));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } else { ?>
<p><em>Produkt nemá dosud žádné související produkty.</em></p>
<?php } ?>
<br/><br/>
<h2>Přidat související</h2>
<?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'edit_related')))?>
<table class="tabulka">
	<tr>
		<th>Kategorie</th>
		<td><?php echo $this->element(REDESIGN_PATH . 'admin/combobox', array('name' => 'Category.id', 'options' => $categories))?></td>
	</tr>
	<tr>
		<th>Výraz</th>
		<td><?php echo $this->Form->input('Product.fulltext1', array('label' => false, 'type' => 'text', 'div' => false, 'size' => 50)); ?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('Product.id')?>
<?php echo $this->Form->submit('Vyhledat')?>
<?php echo $this->Form->end()?>
<br/><br/>
<?php if (isset($categories_products) && !empty($categories_products)) { ?>
<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Název</th>
	</tr>
	<?php foreach ($categories_products as $categories_product) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			$url = array('controller' => 'related_products', 'action' => 'admin_add', 'product_id' => $product['Product']['id'], 'related_product_id' => $categories_product['Product']['id']);
			if (isset($opened_category_id)) {
				$url['category_id'] = $opened_category_id;
			}
			echo $this->Html->link($icon, $url, array('escape' => false));
		?></td>
		<td><?php echo $categories_product['Product']['id']?></td>
		<td><?php echo $this->Html->link($categories_product['Product']['name'], '/' . $categories_product['Product']['url'], array('target' => '_blank'))?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>
<div class='prazdny'></div>