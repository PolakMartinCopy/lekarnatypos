<h1>Produkty</h1>
<?php echo $this->element(REDESIGN_PATH . 'search_forms/products')?>

<?php if (!empty($products)) { ?>
<div class="paging">
<?
	echo $this->Paginator->prev('<< Předchozí', array(), '<< Předchozí');
	echo '&nbsp;&nbsp;' . $this->Paginator->numbers() . '&nbsp;&nbsp;';
	echo $this->Paginator->next('Další >>', array(), 'Další >>');
?>
</div>

<?php echo $this->Form->create('Product', array('url' => array('controller' => 'products', 'action' => 'bulk_process'), 'id' => 'ProductBulkProcessForm'))?>
<table class="tabulka">
	<tr>
		<th><input type="checkbox" id="BulkOperationsCheckAll"/></th>
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
		<th>&nbsp;</th>
		<th><?php echo (empty($products) ? 'Priorita' : $this->Paginator->sort('Priorita', 'Product.priority'))?></th>
	</tr>
	<tr>
		<td style="text-align:center"><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'add'), array('escape' => false));
		?></td>
		<td colspan="17">&nbsp;</td>
	</tr>
	<?php foreach ($products as $product) { ?>
	<tr>
		<td style="text-align:center"><?php echo $this->Form->input('Product.check.' . $product['Product']['id'], array('label' => false, 'type' => 'checkbox', 'value' => $product['Product']['id'], 'class' => 'bulk-operations-checkbox'))?></td>
		<td><?php echo $this->Html->link($product['Product']['id'], '/' . $product['Product']['url'], array('target' => '_blank'))?></td>
		<td><?php
			$style = '';
			// neaktivni produkty vypisuju sede
			if (!$product['Product']['active']) {
				$style = 'color:grey;font-style:italic';
			// produkty, ktere se nedaji objednat, vypisuju cervene
			} elseif (!$product['Availability']['cart_allowed']) {
				$style = 'color:red';
			// produkty, ktere nejsou prirazeny v kategorii vypisuju oranzove
				} elseif (!isset($product['CategoriesProduct'])) {
				$style = ' color:orange';
			}
			echo $this->Html->link($product['Product']['name'], array('controller' => 'products', 'action' => 'edit_detail', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('style' => $style));
		?></td>
		<td><?php echo $product['Manufacturer']['name']?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'products', 'action' => 'edit_detail', $product['Product']['id'], (isset($category_id) ? $category_id : null)), array('escape' => false));
		?></td>
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
<?php echo $this->element(REDESIGN_PATH . 'admin/product_bulk_operations')?>
<div style="clear:both"></div>
<div class="paging">
<?
	echo $this->Paginator->prev('<< Předchozí', array(), '<< Předchozí');
	echo '&nbsp;&nbsp;' . $this->Paginator->numbers() . '&nbsp;&nbsp;';
	echo $this->Paginator->next('Další >>', array(), 'Další >>');
?>
</div>
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
<script type="text/javascript">
	$(document).ready(function() {
		$('#BulkOperationsCheckAll').change(function() {
			var check = false;
			if ($(this).is(':checked')) {
				// zaskrtnout vsechny checkboxy
				check = true;
			}
			$('.bulk-operations-checkbox').each(function() {
				$(this).prop('checked', check);
			});
		});
	});
</script>