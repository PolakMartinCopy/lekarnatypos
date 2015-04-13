<h1>Dokumenty k produktu</h1>
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

<h3>Vložit nový dokument</h3>
<?
	echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'edit_documents', $product['Product']['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null))));
	echo $form->submit('Zobrazit');
	echo $form->text('Product.document_fields', array('size' => '1')) . ' polí';
	echo $form->end();

	echo $form->Create('ProductDocument', array('url' => '/admin/product_documents/add', 'type' => 'file')); ?>
	<fieldset>
		<legend>Nový dokument</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<td>
					<?
						if ( !isset($this->data['Product']['document_fields']) OR $this->data['Product']['document_fields'] > 10 OR $this->data['Product']['document_fields'] < 1 ) {
							$this->data['Product']['document_fields'] = 1;
						}
						for ( $i = 0; $i < $this->data['Product']['document_fields']; $i++ ){
							echo '<input type="file" name="data[ProductDocument][document' . $i . ']" /><br />';
						}
					?>
				</td>
			</tr>
		</table>
<?
	echo $form->hidden('ProductDocument.document_fields', array('value' => $this->data['Product']['document_fields']));
	echo $form->hidden('ProductDocument.product_id', array('value' => $product['Product']['id']));
	echo $this->Form->hidden('ProductDocument.category_id', array('value' => isset($category['Category']['id']) ? $category['Category']['id'] : null))
?>
	</fieldset>
<?
	echo $form->submit('Vložit dokument');
	echo $form->end();
?>
<br/><br/>

<? if (count($product['ProductDocument']) > 0) { ?>
	<table class="topHeading" cellpadding="5" cellspacing="3">
		<tr>
			<th>ID</th>
			<th>Název</th>
			<th>&nbsp;</th>
		</tr>
<?
	foreach ($product['ProductDocument'] as $document){
?>
		<tr>
			<td><?php echo $document['id']?></td>
			<td><?php echo $this->Html->link($document['name'], DS . $documents_folder . DS . $document['name'])?></td>
			<td><?php
				$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
				echo $this->Html->link($icon, array('controller' => 'product_documents', 'action' => 'delete', $document['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null)), array('escape' => false));
			?><br/><br/><?php 
				$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
				echo $this->Html->link($icon, array('controller' => 'product_documents', 'action' => 'move_up', $document['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null)), array('escape' => false));
			?><br/><br/><?php 
				$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
				echo $this->Html->link($icon, array('controller' => 'product_documents', 'action' => 'move_down', $document['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null)), array('escape' => false));
			?>
			</td>
		</tr>
<?
	}
?>
	</table>
<?
	} else {
		echo '<p>Produkt zatím nemá žádné dokumenty</p>';
	}
?>