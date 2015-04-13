<h1><?php echo $category['Category']['heading']?></h1>
<?php if ($category['Category']['content']) { ?>
<div class="category-desc"><?php echo $category['Category']['content']; ?></div>
<?php } ?>

<?php if (!empty($subcategories)) { ?>
<div class="category-subcategories">
	<div class="category-list-headline">Podkategorie</div>
	<?php 
	$limit = 8;
	$i = 1;
	foreach ($subcategories as $subcategory) {
		?>
	<div class="category-subcategories-subcategory<?php echo ($i % 4 == 0 ? ' last' : '')?>">
		<a href="/<?php echo $subcategory['Category']['url']?>">
			<div class="category-subcategories-subcategory-image">
				<img src="/<?php echo $subcategory['Category']['image']?>" width="150" height="150"/>
			</div>
		</a>
		<div class="category-subcategories-subcategory-name">
			<a href="/<?php echo $subcategory['Category']['url']?>"><?php echo $subcategory['Category']['name']?></a>
		</div>
	</div>
	<?php
		// chci vypsat pouze $limit kategorii s obrazky, zbytek vypisu textove
		if ($i == $limit) {
			break;
		}
		$i++;
	} ?>
	<div style="clear:both"></div>
	<?php if (isset($subcategories[$i])) { ?>
	<div id="seeMoreCategoriesDiv">
		<a href="#" id="seeMoreCategoriesLink" class="btn btn-success">Další kategorie</a>
		<ul id="seeMoreCategoriesList">
		<?php while($i < count($subcategories)) { ?>
			<li><?php echo $this->Html->link($subcategories[$i]['Category']['name'], '/' . $subcategories[$i]['Category']['url'])?></li>
		<?php
			$i++;
		} ?>
		</ul>
	</div>
	<?php } ?>
</div>
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
		echo $this->element(REDESIGN_PATH . 'products_carousel', $options);
	?>
</div>
<?php }
	if (!empty($products)) {
		echo $this->element(REDESIGN_PATH . $listing_style);
	}
?>