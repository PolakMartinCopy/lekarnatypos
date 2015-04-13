<h3>Zvolte si variantu</h3>
<?php echo $this->Form->create('Subproduct', array('url' => '/' . $product['Product']['url'])); ?>
<table>
	<tr>
		<th class="left">Varianta</th>
		<th>Cena</th>
		<th>Množství</th>
		<th>&nbsp;</th>
	</tr>
<?php 
foreach ($subproducts as $subproduct) {
	$information = '';
	foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
		$information .= $attributes_subproduct['Attribute']['Option']['name'] . ': ' . $attributes_subproduct['Attribute']['value'] . '<br/>';
	}
	
	if ($subproduct['Subproduct']['price_with_dph'] != 0) {
		$product['Product']['price'] += $subproduct['Subproduct']['price_with_dph'];
	}
?>
	<tr>
		<td><?php echo $information ?></td>
		<td class="center"><?php echo $product['Product']['price']?>&nbsp;Kč</td>
		<td class="center"><input type="number" value="1"/>&nbsp;Ks</td>
		<td class="center"><button name="add" value="1">Do košíku</button></td>
	</tr>
<?php } ?>
</table>
<?php echo $this->Form->end()?>