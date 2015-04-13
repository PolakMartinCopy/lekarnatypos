<?
	foreach ( $products as $product ){
?>

	<div class="productBox">
		<h2><?=$html->link($product['Product']['name'], '/' . $product['Product']['url'], array('escape' => false), false);?></h2>
		<div class="imageBox">
			<div class="topShadow"></div>
			<div class="shadow">
				<table>
					<tr>
						<td align="center" valign="middle" style="height:100px;width:114px;">
				<? if (!empty($product['Product']['Image'])) {
					// zjistim hlavni obrazek
					$main_image = $product['Product']['Image'][0];
					foreach ($product['Product']['Image'] as $image) {
						if ($image['is_main']) {
							$main_image = $image;
							break;
						}
					}
					?>

				<a href="/<?=$product['Product']['url']?>"><img src="/product-images/small/<?=$main_image['name']?>" alt="" /></a>

				<?
					} else {
				?>
					<img src="/product-images/small/na.gif" alt="" />
				<?
					}
				?>

						</td>
					</tr>
				</table>

			</div>
			<div class="bottomShadow"></div>
			<?
				if ( $product['Product']['discount_price'] < $product['Product']['retail_price_with_dph'] ){
			?>
					<span class="old_price"><?=$product['Product']['retail_price_with_dph']?> Kč</span><br />
           			<span class="price"><?=$product['Product']['discount_price']?> Kč</span>
           			<?
           				if ( !$session->check('Customer.id') ){
           			?>
							<br /><span style="font-size:9px;"><a href="/slevy-sportovni-vyziva.htm" style="font-size:9px;">chcete lepší cenu?</a></span>
           			<?
           				}	
				} else {
			?>
					<span class="price"><?=$product['Product']['retail_price_with_dph']?> Kč</span>
			<?
				}
			?>
		</div>

		<div class="detailBox">
			<div class="manufacturer">Výrobce: <?=$product['Product']['Manufacturer']['name']?></div>
		</div>
		<p><?=$product['Product']['short_description']?></p>
		<div class="buyBox">
				<?	
					if ( $product['Product']['Availability']['cart_allowed'] == '1' ){
				?>
						počet kusů
						<?=$form->Create(array('url' => array('controller' => 'products', 'action' => 'view', $product['Product']['id']), 'id' => 'addP' . $product['Product']['id']));?>
						<div style="display:inline">
							<input name="data[Product][quantity]" class="quantity" value="1" type="text" />
							<input style="display:inline" type="submit" class="buyNow" value="Koupit" />
							<input type="hidden" name="data[Product][id]" value="<?=$product['Product']['id']?>" />
						</div>
						<?=$form->end() ?>
				<?
					} else {
						echo '<span>Produkt nyní nelze objednat kvůli nedostupnosti.</span>';
					}
				?>
			<a href="/<?=$product['Product']['url']?>">Zobrazit detaily</a>
		</div>						
		<div style="clear:both"></div>
	</div>
<?
	}
?>