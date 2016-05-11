<h1><?php echo $manufacturer['Manufacturer']['heading']?></h1>
<?php if (isset($manufacturer['Manufacturer']['content']) && !empty($manufacturer['Manufacturer']['content'])) { ?>
<div class="category-desc">
    <p><?php echo $manufacturer['Manufacturer']['content']?></p>
</div>
<?php } ?>

<?php if (empty($products) && empty($filter_manufacturers)){?>
	<div id="mainContentWrapper">
		<p>V obchodě nejsou žádné produkty tohoto výrobce.</p>
	</div>
<?php } else {
		echo $this->element(REDESIGN_PATH . 'product_carousel', array('module_class' => 'module-most-favourite', 'element_id' => 'most-favorite-products', 'title' => 'Nejprodávanější výrobky', 'products' => $most_sold_products));
	
	 	echo $this->element(REDESIGN_PATH . $listing_style);
} ?>