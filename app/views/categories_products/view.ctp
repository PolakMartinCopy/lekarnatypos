<h1><?php echo $category['Category']['heading']?></h1>
<?php if ($category['Category']['content']) { ?>
<div class="category-desc"><?php echo $category['Category']['content']; ?></div>
<?php } ?>

<?php if (!empty($category_most_sold_products)) { ?>
<div class="product-list category-most-sold-products">
   <?php 
		$carousel_id = 'carousel-example-generic';
		$header = 'Nejprodávanější z kategorie';
		$options = array(
			'carousel_id' => $carousel_id,
			'header' => $header,
			'products' => $category_most_sold_products
		);
		echo $this->element('products_carousel', $options);
	?>
</div>
<?php }
	if (!empty($products)) {
		echo $this->element($listing_style);
	}
?>