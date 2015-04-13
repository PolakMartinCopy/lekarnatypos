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

<?php
	$url = array('controller' => 'categories_products', 'action' => 'add');
	if (isset($opened_category_id)) {
		$url['category_id'] = $opened_category_id;
	}
	echo $this->Form->create('CategoriesProduct', array('url' => $url))?>
<p>Nové přiřazení k: <?php echo $this->Form->input('CategoriesProduct.category_id', array('label' => false, 'type' => 'select', 'options' => $categories, 'empty' => true, 'div' => false))?>
<?php echo $this->Form->submit('Přiřadit', array('div' => false))?></p>
<?php echo $this->Form->hidden('CategoriesProduct.product_id', array('value' => $product['Product']['id']))?>
<?php echo $this->Form->end()?>

<div class='prazdny'></div>