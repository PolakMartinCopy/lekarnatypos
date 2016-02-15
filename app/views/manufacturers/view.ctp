<h2><span><?php echo $manufacturer['Manufacturer']['name']?></span></h2>
<?php if (empty($products) && empty($filter_manufacturers)){?>
	<div id="mainContentWrapper">
		<p>V obchodě nejsou žádné produkty tohoto výrobce.</p>
	</div>
<?php } else {
		echo $this->element(REDESIGN_PATH . 'product_carousel', array('module_class' => 'module-most-favourite', 'element_id' => 'most-favorite-products', 'title' => 'Nejprodávanější výrobky výrobce', 'products' => $most_sold_products));
	
	 	echo $this->element(REDESIGN_PATH . $listing_style);
} ?>