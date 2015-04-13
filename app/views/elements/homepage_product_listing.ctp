<table class="homepage-product-list">
	<tr>
<?
	$count = 0;
	foreach ( $products as $product ){
		if ( $count % 3 == 0 AND $count != 0 ){
			echo '</tr><tr>';
		}
		$count = $count + 1;
?>
		<td class="width-wrapper" valign="top">
			<table class="homepage-product-list-cell">
				<tr>
					<td rowspan="2" class="image" valign="top">
					<?
						// pokud ma produkt nastaveny nejaky hlavni obrazek
						if ( isset($product['Image'][0]) ){
							if (file_exists('product-images/small/' . $product['Image'][0]['name'])) {
								// prepocitam velikost small obrazku, aby se mi vlezl
								$image_properties = getimagesize('product-images/small/' . $product['Image'][0]['name']);
								$image_properties[0] = round($image_properties[0]*0.5);
								$image_properties[1] = round($image_properties[1]*0.5);
					?>
								<div class="image-wrapper"><img src="/product-images/small/<?=$product['Image'][0]['name']?>" width="<?=$image_properties[0]?>px" height="<?=$image_properties[0]?>px" alt="" /></div>
					<?
							}
						}

						if ($product['Product']['discount_price'] < $product['Product']['retail_price_with_dph']) {
					?>
						<p class="regular-price"><?=$product['Product']['retail_price_with_dph'] ?>&nbsp;Kč</p>
						<p class="discount-price"><?=$product['Product']['discount_price'] ?>&nbsp;Kč</p>
					<?php } else { ?>
						<p class="discount-price"><?=$product['Product']['retail_price_with_dph'] ?>&nbsp;Kč</p>
					<?php }?>
						<p><?=$html->link('více', '/' . $product['Product']['url']) ?></p>
					</td>
				</tr>
				<tr>
					<td valign="top" class="product-properties">
						<h3><?=$html->link($product['Product']['name'], '/' . $product['Product']['url'], array('escape' => false), false)?></h3>
						<p>
							<?=$product['Product']['short_description'] ?>
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<p>Výrobce: <?=$html->link($product['Manufacturer']['name'], '/' . strip_diacritic($product['Manufacturer']['name']) . '-v' . $product['Manufacturer']['id'], array('title' => 'zobrazit všechny produkty firmy ' . $product['Manufacturer']['name']))?></p>
						<p>Kategorie:
							<?
								foreach ( $product['CategoriesProduct'] as $category ){
									echo '<br />' . $html->link($category['Category']['name'], '/' . $category['Category']['url']);
								}
							?>
						</p>
					</td>
				</tr>
			</table>
		</td>
<?
	}
?>
	</tr>
</table>