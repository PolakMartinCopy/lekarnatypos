<h1><?php echo $category['Category']['heading']?></h1>
<?php if (isset($category['Category']['content']) && !empty($category['Category']['content'])) { ?>
<div class="category-desc">
    <p><?php echo $category['Category']['content']?></p>
</div>
<?php } ?>

<?php if (!empty($subcategories)) { ?>
<div class="module-subcategories row">
<?php foreach ($subcategories as $subcategory) { ?>
	<div class="subcategory-item col-xs-12 col-sm-6 col-md-4 col-lg-4">
		<a href="/<?php echo $subcategory['Category']['url']?>">
			<span class="subcategory-thumb"><img src="/<?php echo $subcategory['Category']['image']?>"/></span>
			<span class="title"><?php echo $subcategory['Category']['name']?></span>
		</a>
	</div>
<?php } ?>
</div>
<?php } ?>

<?php echo $this->element(REDESIGN_PATH . 'product_carousel', array('module_class' => 'module-most-favourite', 'element_id' => 'most-favorite-products', 'title' => 'Nejprodávanější výrobky z této kategorie', 'products' => $category_most_sold_products))?>

<?php if (empty($products) && empty($filter_manufacturers)){?>
	<div id="mainContentWrapper">
		<p>Kategorie je prázdná.</p>
	</div>
<?php } else {
	 	echo $this->element(REDESIGN_PATH . $listing_style);
} ?>