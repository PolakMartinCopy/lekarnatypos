<script type="text/javascript">
	$(function() {
		$('#CategoryId').change(function() {
			$('#ProductAdminEditRelatedForm').submit();
		});
	});
</script>

<h1>Související produkty</h1>
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

<?php if (!empty($related_products)) { ?> 
<table class="tabulka">
	<tr>
		<th>ID</th>
		<th>Název produktu</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($related_products as $related_product) { ?>
	<tr>
		<td><?php echo $related_product['Product']['id']?></td>
		<td><?php echo $this->Html->link($related_product['Product']['name'], array('/' . $related_product['Product']['url']))?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'related_products', 'action' => 'delete', $related_product['RelatedProduct']['id'], (isset($opened_category_id) ? $opened_category_id : false), (isset($this->data['Category']['id']) ? $this->data['Category']['id'] : null)), array('escape' => false));
		?>&nbsp;&nbsp;<?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'related_products', 'action' => 'move_up', $related_product['RelatedProduct']['id'], (isset($opened_category_id) ? $opened_category_id : false), (isset($this->data['Category']['id']) ? $this->data['Category']['id'] : null)), array('escape' => false));
		?>&nbsp;&nbsp;<?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'related_products', 'action' => 'move_down', $related_product['RelatedProduct']['id'], (isset($opened_category_id) ? $opened_category_id : false), (isset($this->data['Category']['id']) ? $this->data['Category']['id'] : null)), array('escape' => false));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } else { ?>
<p><em>Produkt nemá dosud žádné související produkty.</em></p>
<?php } ?>

<?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'edit_related', (isset($opened_category_id) ? $opened_category_id : null))))?>
<p>Vyberte rubriku: <?php echo $this->Form->input('Category.id', array('label' => false, 'type' => 'select', 'options' => $categories, 'empty' => true, 'div' => false))?></p>
<?php echo $this->Form->hidden('Product.id')?>
<?php echo $this->Form->end()?>

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
			$url = array('controller' => 'related_products', 'action' => 'admin_add', 'product_id' => $product['Product']['id'], 'related_product_id' => $categories_product['Product']['id'], 'related_category_id' => $this->data['Category']['id']);
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