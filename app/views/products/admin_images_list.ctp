<h1>Obrázky k produktu</h1>
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

	<a name="imagesform"></a>
	<h3>Vložit nový obrázek</h3>
<?
	echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'images_list', $product['Product']['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null))));
	echo $form->submit('Zobrazit');
	echo $form->text('Product.image_fields', array('size' => '1')) . ' polí';
	echo $form->end();

	echo $form->Create('Image', array('url' => '/admin/images/add', 'type' => 'file')); ?>
	<fieldset>
		<legend>Nový obrázek</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<td>
					<?
						if ( !isset($this->data['Product']['image_fields']) OR $this->data['Product']['image_fields'] > 10 OR $this->data['Product']['image_fields'] < 1 ) {
							$this->data['Product']['image_fields'] = 1;
						}
						for ( $i = 0; $i < $this->data['Product']['image_fields']; $i++ ){
							echo '<input type="file" name="data[Image][image' . $i . ']" /><br />';
						}
					?>
				</td>
			</tr>
		</table>
<?
	echo $form->hidden('Image.image_fields', array('value' => $this->data['Product']['image_fields']));
	echo $form->hidden('Image.product_id', array('value' => $product['Product']['id']));
	echo $this->Form->hidden('Image.category_id', array('value' => isset($category['Category']['id']) ? $category['Category']['id'] : null))
?>
	</fieldset>
<?
	echo $form->submit('Vložit obrázek');
	echo $form->end();
?>
<br/><br/>

<? if (count($product['Image']) > 0) { ?>
	<table class="topHeading" cellpadding="5" cellspacing="3">
		<tr>
			<th>ID</th>
			<th>Náhled</th>
			<th>Název</th>
			<th>&nbsp;</th>
		</tr>
<?
	foreach ($product['Image'] as $image){
		$imageSize = @getimagesize('product-images/small/' . $image['name']);
		$class = '';
		if ($image['is_main']) {
			$class = ' class="selected"';
		}
?>
		<tr<?php echo $class?>>
			<td><?php echo $image['id']?></td>
			<td valign="middle" style="height:100px;width:100px;">
				<img style="border:1px solid black;" src="/product-images/small/<?=$image['name']?>" width="<?=$imageSize[0]?>px" height="<?=$imageSize[1]?>" alt="" />
			</td>
			<td><?=$image['name']?></td>
			<td><?php
				if (!$image['is_main']) {
					$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/accept.png" alt="" />';
					echo $this->Html->link($icon, array('controller' => 'images', 'action' => 'set_as_main', $image['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null)), array('escape' => false));
				?><br/><br/>
			<?php
				}
				 
				$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
				echo $this->Html->link($icon, array('controller' => 'images', 'action' => 'delete', $image['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null)), array('escape' => false));
			?><br/><br/><?php 
				$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
				echo $this->Html->link($icon, array('controller' => 'images', 'action' => 'move_up', $image['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null)), array('escape' => false));
			?><br/><br/><?php 
				$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
				echo $this->Html->link($icon, array('controller' => 'images', 'action' => 'move_down', $image['id'], (isset($category['Category']['id']) ? $category['Category']['id'] : null)), array('escape' => false));
			?>
			</td>
		</tr>
<?
	}
?>
	</table>
<?
	} else {
		echo '<p>Produkt zatím nemá žádné obrázky</p>';
	}
?>