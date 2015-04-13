<div class="product-list favorite-products">
    <div class="product-list-headline">Produkty</div>
    <div class="item">
        <ul class="clearfix">
<?php		foreach ($products as $product) {
				$image = '/img/' . REDESIGN_PATH . 'na_250_250.jpg';
				if (isset($product['Image']['name']) && !empty($product['Image']['name'])) {
					$path = 'product-images/medium/' . $product['Image']['name'];
					$image = '/' . $path;
				}   
?>
            <li><?php 
            	$options = array(
					'product_url' => $product['Product']['url'],
            		'product_name' => $product['Product']['name'],
					'product_price' => $product['Product']['price'],
					'image' => $image,
					'product_description' => $product['Product']['short_description']	
				);
				echo $this->element(REDESIGN_PATH . 'product_box', $options);
            ?></li>
		<?php } ?>
		</ul>
	</div>
</div>