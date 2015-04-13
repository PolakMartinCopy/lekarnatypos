<h1>Ceny za proklik produktu</h1>

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
<?php echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'comparator_click_prices', $id, (isset($category['Category']['id']) ? $category['Category']['id'] : null))));?>
<table class="tabulkaedit">
<?php 
	foreach ($comparators as $comparator) {
		$price = 1;
		foreach ($comparator_product_click_prices as $cpcp) {
			if ($cpcp['ComparatorProductClickPrice']['comparator_id'] == $comparator['Comparator']['id']) {
				$price = $cpcp['ComparatorProductClickPrice']['click_price'];
			}
		}
	?>
	<tr valign="top">
		<td>Srovnávač:</td>
		<td><?php echo $comparator['Comparator']['name']?></td>
		<td>CPC:</td>
		<td><?php
			echo $this->Form->input('ComparatorProductClickPrice.' . $comparator['Comparator']['id'] . '.click_price', array('label' => false, 'after' => '&nbsp;Kč', 'value' => $price));
			echo $this->Form->hidden('ComparatorProductClickPrice.' . $comparator['Comparator']['id'] . '.product_id', array('value' => $product['Product']['id']));
			echo $this->Form->hidden('ComparatorProductClickPrice.' . $comparator['Comparator']['id'] . '.comparator_id', array('value' => $comparator['Comparator']['id']));
		?></td>
	</tr>
<?php } ?>
</table>
<?php
	echo $this->Form->submit('VLOŽIT');
	echo $this->Form->end();
?>
<div class='prazdny'></div>