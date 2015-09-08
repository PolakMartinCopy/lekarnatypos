<ul class="row">
	<?php foreach ($categories as $category) { ?>
    <li class="col-sm-6 col-md-4 col-lg-3">
        <a href="/<?php echo $category['Category']['url']?>">
<?php 		if (isset($category['Category']['homepage_class']) && !empty($category['Category']['homepage_class'])) { ?>
        	<span class="icon flaticon-<?php echo $category['Category']['homepage_class']?>"></span>
<?php } ?>
        	<?php echo $category['Category']['name']?>
        </a>
        <?php if (!empty($category['children'])) { ?>
        <div class="subcategories">
        	<?php 
            	$limit = 5;
            	$count = 0;
            	while ($count < $limit && isset($category['children'][$count])) {
            		$subcategory = $category['children'][$count];
            		$count++;
            ?>
            <a href="/<?php echo $subcategory['Category']['url']?>"><?php echo $subcategory['Category']['name']?></a>,
            <?php } // end while ?>
			<a href="/<?php echo $category['Category']['url']?>">další…</a>
        </div>
        <?php } // end if (!empty($category['children'])) ?>
    </li>
    <?php } // end foreach ($categories_menu as $category) { ?>
</ul>