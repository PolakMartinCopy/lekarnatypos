<script type="text/javascript">
$(function() {
	$('#CategoryId').change(function() {
		$('#ProductName').val('');
		$('#ProductAdminIndexForm').submit();
	});

	$('#ProductNameButton').click(function(e) {
		e.preventDefault();
		$('#CategoryId option:selected').removeAttr('selected');
		$('#ProductAdminIndexForm').submit();
	});
});
</script>

<h1>Produkty</h1>
<?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'index')))?>
<table class="tabulka">
	<tr>
		<th>Vyberte kategorii</th>
		<td><?php echo $this->Form->input('Category.id', array('label' => false, 'type' => 'select', 'options' => $categories, 'empty' => true))?></td>
	</tr>
	<tr>
		<th>nebo vyhledejte</th>
		<td><?php
			echo $this->Form->input('Product.name', array('label' => false, 'type' => 'text', 'div' => false, 'size' => 50));
			echo $this->Form->submit('Vyhledat', array('div' => false, 'id' => 'ProductNameButton'));
		?></td>
	</tr>
</table>
<?php echo $this->Form->end()?>

<?php
	if (!empty($products)) { 
		$options = array();
		if (isset($category_id)) {
			$options['category_id'] = $category_id;
		}
		if (isset($this->data['Product']['name']) && !empty($this->data['Product']['name'])) {
			$options['product_name'] = $this->data['Product']['name'];
		}
		
		$this->Paginator->options(array('url' => $options));
?>
<br/>
<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th><?php echo (empty($products) ? 'ID' : $this->Paginator->sort('ID', 'Product.id'))?></th>
		<th><?php echo (empty($products) ? 'Název' : $this->Paginator->sort('Název', 'Product.name'))?></th>
		<th><?php echo (empty($products) ? 'Výrobce' : $this->Paginator->sort('Výrobce', 'Manufacturer.name'))?></th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th><?php echo (empty($products) ? 'Priorita' : $this->Paginator->sort('Priorita', 'Product.priority'))?></th>
	</tr>
	<?php if (isset($this->data['Category']['id']) && !empty($this->data['Category']['id'])) { ?>
	<tr>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'add', $this->data['Category']['id']), array('escape' => false));
		?></td>
		<td colspan="16">&nbsp;</td>
	</tr>
	<?php }?>
	<?php foreach ($products as $product) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			$action = array('controller' => 'products', 'action' => 'delete', $product['Product']['id'], (isset($category_id) ? $category_id : null));
			$notice = 'Opravdu chcete produkt deaktivovat?';
			// pokud uz je produkt deaktivovan, dalsim pozadavkem jej smazu uplne ze systemu
			if (!$product['Product']['active']) {
				$action = array('controller' => 'products', 'action' => 'delete_from_db', $product['Product']['id'], (isset($category_id) ? $category_id : null));
				$notice = 'Opravdu chcete produkt zcela odstranit ze systému?';
			}
			echo  $this->Html->link($icon, $action, array('escape' => false), $notice);
		?></td>
		<td><?php echo $this->Html->link($product['Product']['id'], array('controller' => 'products', 'action' => 'view', 'admin' => false, $product['Product']['id'], (isset($category_id) ? $category_id : null)))?></td>
		<td><?php
			$style = '';
			if (!$product['Product']['active']) {
				$style = 'color:grey;font-style:italic';
			} elseif (!$product['Availability']['cart_allowed']) {
				$style = 'color:red';
			}
			echo $this->Html->link($product['Product']['name'], array('controller' => 'products', 'action' => 'edit_detail', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('style' => $style));
		?></td>
		<td><?php echo $product['Manufacturer']['name']?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'edit_detail', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/money.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'edit_price_list', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/image_add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'images_list', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/acrobat.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'edit_documents', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/link_external.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'edit_related', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/book.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'edit_categories', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/flag_blue.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'attributes_list', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/flag_red.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'comparator_click_prices', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/page_white_code_red.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'duplicate', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
		<td><?php echo $product['Product']['priority']?></td>
	</tr>
	<?php }?>
</table>
<br/>
<table class="legenda">
	<tr>
		<th align="left"><strong>LEGENDA:</strong></th>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/add.png" width='16' height='16' /> ... přidat produkt</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/delete.png" width='16' height='16' /> ... smazat produkt</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/pencil.png" width='16' height='16' /> ... upravit produkt</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/money.png" width='16' height='16' /> ... ceník produktu</td>
	</tr>
<!--	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/alias.gif" width='16' height='16' /> ... parametry produktu</td>
	</tr> -->
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/image_add.png" width='16' height='16' /> ... fotogalerie produktu</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/acrobat.png" width='16' height='16' /> ... dokumenty produktu</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/link_external.png" width='16' height='16' /> ... související produkty</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/book.png" width='16' height='16' /> ... přiřazení ke kategoriím</td>
	</tr>
<!--	<tr>
		<td>
			<img src="/images/<?php echo REDESIGN_PATH ?>icons/flag_yellow.png" width='16' height='16' /> ... povinný text
			<a href='/administrace/help.php?width=500&id=51' class='jTip' id='51' name='Povinný text (51)'>
				<img src="/images/<?php echo REDESIGN_PATH ?>icons/help.png" width='16' height='16' />
			</a>
		</td>
	</tr> -->
	<tr>
		<td>
			<img src="/images/<?php echo REDESIGN_PATH ?>icons/flag_blue.png" width='16' height='16' /> ... povinný výběr
			<a href='/administrace/help.php?width=500&id=52' class='jTip' id='52' name='Povinný výběr (52)'>
				<img src="/images/<?php echo REDESIGN_PATH ?>icons/help.png" width='16' height='16' />
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<img src="/images/<?php echo REDESIGN_PATH ?>icons/flag_red.png" width='16' height='16' /> ... ceny za proklik
		</td>
	</tr>
	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/page_white_code_red.png" width='16' height='16' /> ... duplikace produktu</td>
	</tr>
<!-- 	<tr>
		<td><img src="/images/<?php echo REDESIGN_PATH ?>icons/user_comment.png" width='16' height='16' /> ... komentáře produktu</td>
	</tr> -->
</table>
<?php } ?>
<div class="prazdny"></div>