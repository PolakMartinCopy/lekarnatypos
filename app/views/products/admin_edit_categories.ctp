<h1>Přiřazení ke kategoriím</h1>
<?php 
$back_link = array('controller' => 'products', 'action' => 'index');
if (isset($opened_category_id)) {
	$back_link['category_id'] = $opened_category_id;
}
echo $this->Html->link('ZPĚT NA SEZNAM PRODUKTŮ', $back_link)?>
<br /><br />
<h2><?php echo $product['Product']['name']?></h2>
<?php if (isset($category)) { ?>
<h4><?php echo $category['Category']['name']?></h4>
<?php } ?>

<?php echo $this->element(REDESIGN_PATH . 'admin/product_menu')?>

<div class='prazdny'></div>

<?php if (!empty($categories_products)) { ?> 
<table class="tabulka">
	<tr>
		<th>ID</th>
		<th>Kategorie</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($categories_products as $categories_product) { ?>
	<tr>
		<td><?php echo $categories_product['CategoriesProduct']['id']?></td>
		<td><?php echo $categories_product['Category']['name']?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			$url = array('controller' => 'categories_products', 'action' => 'delete', $categories_product['CategoriesProduct']['id']);
			if (isset($opened_category_id)) {
				$url['category_id'] = $opened_category_id;
			}
			echo $this->Html->link($icon, $url, array('escape' => false));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } else { ?>
<p><em>Produkt není přiřazen do žádné kategorie.</em></p>
<?php } ?>

<p>Přidat do kategorie:
<?php
	$url = array('controller' => 'products', 'action' => 'edit_categories', $product['Product']['id']);
	if (isset($opened_category_id)) {
		$url['category_id'] = $opened_category_id;
	}
	echo $this->Form->create('CategoriesProduct', array('url' => $url));
	for ($i = 0; $i < 3; $i++) {
		echo $this->element(REDESIGN_PATH . 'admin/combobox', array('name' => 'CategoriesProduct.' . $i . '.category_id', 'options' => $categories));
		echo $this->Form->hidden('CategoriesProduct.' . $i . '.product_id', array('value' => $product['Product']['id']));
		echo '<br/>';
	}
	echo $this->Form->submit('Přiřadit', array('div' => false));
	echo $this->Form->end()
?>
</p>
<div class='prazdny'></div>