<h1>Ceník produktu</h1>

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
<?php echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'edit_price_list', (isset($category['Category']['id']) ? $category['Category']['id'] : null))));?>
<table class="tabulkaedit">
	<tr class="nutne" valign="top">
		<td>Běžná cena</td>
		<td><?php echo $this->Form->input('Product.retail_price_with_dph', array('label' => false, 'after' => '&nbsp;Kč'))?></td>
	</tr>
	<?php foreach ($product['CustomerTypeProductPrice'] as $index => $ctpp) { ?>
	<tr valign="top">
		<td>Cena <?php echo $ctpp['CustomerType']['name']?>:</td>
		<td><?php
			echo $this->Form->input('CustomerTypeProductPrice.' . $index . '.price', array('label' => false, 'after' => '&nbsp;Kč'));
			echo $this->Form->hidden('CustomerTypeProductPrice.' . $index . '.id');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php
	echo $this->Form->hidden('Product.id');
	echo $this->Form->submit('VLOŽIT');
	echo $this->Form->end();
?>
<div class='prazdny'></div>