<?php if (!empty($subproducts)) { ?>
<h2>Varianty produktu <?=$product['Product']['name'] ?></h2>
<? echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'add_subproducts', $product['Product']['id']))); ?>
<table>
	<tr>
		<th>Atributy</th>
		<th>Přírůstková cena s&nbsp;DPH</th>
		<th>Povoleno</th>
	</tr>
	<? foreach ($subproducts as $subproduct) { ?>
	<tr>
		<td>
			<? foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) { ?>
					-&nbsp;<?=$attributes_subproduct['Attribute']['Option']['name'] ?>: <?=$attributes_subproduct['Attribute']['value'] ?><br/>
			<? } ?>
		</td>
		<td><?=$form->input('Product.' . $subproduct['Subproduct']['id'] . '.price_with_dph', array('label' => false, 'size' => 10, 'value' => $data['Product'][$subproduct['Subproduct']['id']]['price_with_dph'], 'after' => '&nbsp;Kč')) ?></td>
		<td>
			<?=$form->checkbox('Product.' . $subproduct['Subproduct']['id'] . '.active', array('label' => false, 'checked' => ($data['Product'][$subproduct['Subproduct']['id']]['active'] == 1))); ?>
			<?=$form->hidden('Product.' . $subproduct['Subproduct']['id'] . '.product_id', array('value' => $product['Product']['id'])); ?>
		</td>
	</tr>
	<? } ?>
</table>
<?
	echo $form->submit('Odeslat');
	echo $form->end();
}
?>