<h1>Atributy produktu</h1>
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

<?php echo $this->element('admin_subproducts_control', $this->requestAction('admin/subproducts/control/' . $product['Product']['id']));?>

<h2>Atributy produktu <?=$product['Product']['name']?></h2>
<?php
if (!empty($options)) {
	echo $form->create('Product', array('url' => '/admin/products/attributes_list/' . $product['Product']['id']));
?>
<table class="tabulka">
	<tr>
		<th>Skupina</th>
		<th>Hodnoty</th>
	</tr>
	<?php foreach ($options as $option) { ?>
	<tr>
		<td><?php echo $option['Option']['name'] . ':&nbsp;'; ?></td>
		<td><?php echo $this->Form->input('Attributes.' . $option['Option']['id'], array('label' => false, 'type' => 'text', 'div' => false, 'size' => 100)); ?></td>
	</tr>
	<?php } ?>
</table>
<?php 
	echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
	echo $this->Form->submit('Odeslat');
	echo $this->Form->end();
}
?>
<div class='prazdny'></div>