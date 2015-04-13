<div>
	<h2>Vyhledávání</h2>
<?
		$admin_id = $session->read('Administrator.id');
		$superadmin = false;
		if (in_array($admin_id, array(3, 10))) {
			$superadmin = true;
		}
		
		echo $form->create('Search', array('action' => 'do'));
		echo $form->text('query');
		echo $form->end('Hledej');
	
		if ( !empty($products) ){
	?>
			<table class="topHeading" cellpadding="5" cellspacing="3">
				<tr>
					<th>Id</th>
					<th>Název</th>
					<th>Cena</th>
					<th>Smazáno</th>
					<th>Kategorie</th>
					<th>&nbsp;</th>
				</tr>
	<?
		foreach ( $products as $product ){
			// oznacim si vyhledavane vyrazy v query
			$split_query = explode(" ", $this->data['Search']['query']);
			for ( $i = 0; $i < count($split_query); $i++ ){
				$product['Product']['name'] = preg_replace('/' . $split_query[$i] . '/', '<strong style="color:red;">' . $split_query[$i] . '</strong>', $product['Product']['name']);
			}
			$style = '';
			if (!$product['Product']['active']) {
				$style = ' style="color:grey"';
			}
	?>
				<tr <?php echo  $style?>>
					<td><?=$html->link($product['Product']['id'], '/' . $product['Product']['url']);?></td>
					<td style="font-size:10px;"><?=$product['Product']['name']?></td>
					<td style="font-size:10px;"><?=$product['Product']['retail_price_with_dph']?></td>
					<td style="font-size:10px;"><?php echo ($product['Product']['active'] == 1) ? 'Ne' : 'Ano'?></td>
					<td style="font-size:9px;">
						<? 
							foreach ( $product['CategoriesProduct'] as $category){
								echo '<a href="/admin/categories/list_products/' . $category['category_id'] . '">';
								foreach ( $category['path'] as $item ){
									echo $item['Category']['name'] . '&nbsp;&raquo;&nbsp;';
								}
								echo '</a> - <a href="/admin/categories_products/edit/' . $category['id'] . '">přesunout</a><br/><br/>';
							}
						?>
					</td>
					<td style="font-size:12px;">
						<a href="/admin/products/edit/<?=$product['Product']['id']?>">Editovat</a> |
						<a href="/admin/products/attributes_list/<?=$product['Product']['id']?>">Varianty</a> |
<!-- 						<a href="/admin/products/related/<?=$product['Product']['id']?>">Související</a> | -->
						<a href="/admin/products/images_list/<?=$product['Product']['id']?>">Obrázky</a> |
<!--    					<a href="/admin/dirimages/list/<?=$product['Product']['id']?>">FTP</a> | -->
						<a href="/admin/products/copy/<?=$product['Product']['id']?>">Duplikovat</a> |
						<?php
						if ($product['Product']['active']) { 
							echo $html->link('Smazat', array('controller' => 'products', 'action' => 'delete', $product['Product']['id']), array(), 'Opravdu chcete tento produkt smazat?');
						} else {
							echo $html->link('Obnovit', array('controller' => 'products', 'action' => 'activate', $product['Product']['id']));
						}
						if ($superadmin) {
							echo ' | ' . $html->link('Smazat z DB', array('controller' => 'products', 'action' => 'delete_from_db', $product['Product']['id']), array(), 'Opravdu chcete tento produkt a vše, co k němu náleží, zcela odstranit z databáze?');
						}
						?>
					</td>
				</tr>
	<? } ?>
			</table>
	<? } ?>
	
</div>