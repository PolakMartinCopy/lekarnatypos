<div class="product-list favorite-products">
    <div class="product-list-headline">Produkty</div>
    <div class="item">
        <ul class="clearfix">
<?php		foreach ($products as $product) {
				$image = '/img/na_250_250.jpg';
				if (isset($product['Image'][0]) && !empty($product['Image'][0])) {
					$path = 'product-images/medium/' . $product['Image'][0]['name'];
					//				if (file_exists($path) && is_file($path) && getimagesize($path)) {
					$image = '/' . $path;
					//				}
				}   
?>
            <li><?php 
            	$options = array(
					'product_url' => $product['Product']['url'],
            		'product_name' => $product['Product']['name'],
					'product_price' => $product['Product']['discount_price'],
					'image' => $image,
					'product_description' => $product['Product']['short_description']	
				);
				echo $this->element('product_box', $options);
            ?></li>
		<?php } ?>
		</ul>
	</div>
</div>
<?
if (false) {
$i = 0;
foreach ($products as $product) {
	$main_image = array();
	// element pouzivam pro vykreslovani produktu v kategoriich
	if (!empty($product['Product']['Image'])) {
		$main_image = $product['Product']['Image'][0];
	// i vysledku vyhledavani, proto tato rozboceni
	} elseif (!empty($product['Image'])) {
		$main_image = $product['Image'][0];
	}

	$availability_name = '';
	$availability_allowed = null;
	if (!empty($product['Product']['Availability'])) {
		$availability_name = $product['Product']['Availability']['name'];
		$availability_allowed = $product['Product']['Availability']['cart_allowed'];
	} elseif (!empty($product['Availability'])) {
		$availability_name = $product['Availability']['name'];
		$availability_allowed = $product['Availability']['cart_allowed'];
	}
?>
	<div class="product_listing <?php echo ($i % 3 == 0 ? ' first' : '')?>">
<?php  	if (!empty($main_image)) {?>
		<a href="/<?php echo $product['Product']['url']?>">
			<img src="/product-images/medium/<?php echo $main_image['name']?>" width="210px" height="210px" alt="produkt <?php echo $product['Product']['name']?>" />
		</a>
<?php 	} ?>
		<h3><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></h3>
		<p class="description"><?php echo $product['Product']['short_description']?></p>
		<?php if ($availability_allowed) { ?>
		<a href="/<?php echo $product['Product']['url']?>" class="buy_now">KOUPIT ZA <?php echo round($product['Product']['discount_price'])?>,-</a>
		<?php } else { ?>
		<div class="buy_now">NELZE OBJEDNAT</div>
		<?php } ?>
		<p class="availability"><?php echo $availability_name?></p>
	</div>
<?php 
	$i++;
} 
}?>