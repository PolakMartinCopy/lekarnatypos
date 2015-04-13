<?php
	if ( !empty($similar_products) ){
?>
		<h2 id="details"><?php echo $product['Product']['name']?> - podobné produkty</h2>
<?php
		foreach ( $similar_products as $product ){
			$related_name = null;
			if (isset($product['Product']['related_name'])) {
				$related_name = $product['Product']['related_name'];
			}
			if (!$related_name) {
				$related_name = $product['Product']['name'];
			}
?>
	<div class="similarProductBox">

		<h3><a href="/<?=$product['Product']['url']?>"><?=$related_name?></a></h3>
		<table class="related_image_holder" cellpadding="0" cellspacing="0">
			<tr>
				<td align="center" valign="middle">
				
<?php
			if ( !empty($product['Image']) ){
?>

				<a href="/<?php echo $product['Product']['url']?>"><img src="/product-images/small/<?php echo $product['Image'][0]['name']?>" alt="" width="<?php echo $product['Image'][0]['x']?>px" height="<?php echo $product['Image'][0]['y']?>px" /></a>

<?php
			} else {
?>
				<img src="/product-images/small/na.gif" alt="" />
<?php
			}
?>
				</td>
			</tr>
		</table>
<?php
		if ( $product['Product']['discount_price'] < $product['Product']['retail_price_with_dph'] ){
?>
			<span class="puv_cena_rp">původně: <?php echo intval($product['Product']['retail_price_with_dph'])?> Kč</span><br />
			<span class="nova_cena_rp">nyní: <?=intval($product['Product']['discount_price'])?> Kč</span>
<?php
		} else {
?>
			<span class="puv_cena_rp">&nbsp;</span><br />
			<span class="nova_cena_rp">cena: <?=intval($product['Product']['retail_price_with_dph'])?> Kč</span>
<?php
		}
?>
		<p><?=$product['Product']['short_description']?></p>
	</div>
<?php
		}
?>
	<div class="clearer"></div>
<?php
	}
?>