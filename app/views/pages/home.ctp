<div class="module-categories" role="tabpanel">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"<?php echo ($categories_bothers_tab == 'categories' ? ' class="active"' : '')?>><a href="#categories" aria-controls="categories" role="tab" data-toggle="tab" class="categories-bothers-switch">Kategorie</a></li>
        <?php if (false) { // UKRYTE KATEGORIE ZBOZI PODLE PRIZNAKU?>
        <li role="presentation"<?php echo ($categories_bothers_tab == 'bothers' ? ' class="active"' : '')?>><a href="#bothers" aria-controls="bothers" role="tab" data-toggle="tab" class="categories-bothers-switch">Co vás trápí</a></li>
        <?php } ?>
    </ul>
    <div class="tab-content">
    	<?php if (!empty($categories_menu['categories'])) {?>
        <div role="tabpanel" class="tab-pane fade<?php echo ($categories_bothers_tab == 'categories' ? ' in active' : '')?>" id="categories">
        	<?php echo $this->element(REDESIGN_PATH . 'homepage_categories', array('categories' => $categories_menu['categories']))?>
        </div>
        <?php } // end if (!empty($categories_menu)) { ?>
        <?php if (!empty($bothers_menu['categories'])) { ?>
        <div role="tabpanel" class="tab-pane fade<?php echo ($categories_bothers_tab == 'bothers' ? ' in active' : '')?>" id="bothers">
            <?php echo $this->element(REDESIGN_PATH . 'homepage_categories', array('categories' => $bothers_menu['categories']))?>
        </div>
        <?php } ?>
    </div>
</div>

<?php if (isset($hp_most_sold) && !empty($hp_most_sold)) { ?>
<div class="module-action-products">
    <h3 class="hidden-md hidden-lg">Akční zboží</h3>
    <?php // verze akcnich produktu pro velke obrazovky?>
    <div class="hidden-md hidden-lg">
        <ul class="on-mobile">
        	<?php foreach ($hp_most_sold as $product) { ?>
            <li>
                <a href="/<?php echo $product['Product']['url']?>">
                    <img src="/<?php echo $product['MostSoldProduct']['image']?>" />
                    <span class="title"><?php echo $product['Product']['name']?></span>
                    <span class="price"><?php echo front_end_display_price($product['Product']['price'])?> Kč</span>
                    <?php if (isset($product['Product']['discount']) && $product['Product']['discount']) { ?>
                    <span class="discount">-<?php echo $product['Product']['discount'] ?> %</span>
                    <?php } ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
    <div class="hidden-xs hidden-sm on-desktop">
    	<?php // verze akcnich produktu pro velke obrazovky?>
        <div class="tab-content hidden-xs hidden-sm">
        	<?php
        	$count = 1;
        	foreach ($hp_most_sold as $product) { ?>
            <div role="tabpanel" class="tab-pane fade<?php echo ($count == 1 ? ' in active' : '')?>" id="action<?php echo $count?>">
                <img src="/product-images/medium/<?php echo $product['Image']['name']?>" />
                <div class="title"><?php echo $product['Product']['name']?></div>
                <div class="desc"><?php echo $product['Product']['short_description']?></div>
                <div class="price-box"><span class="price"><?php echo front_end_display_price($product['Product']['price'])?> Kč</span>
                <?php if (isset($product['Product']['discount']) && $product['Product']['discount']) { ?>
                <span class="discount">-<?php echo $product['Product']['discount'] ?> %</span>
                <?php } ?>
                </div>
                <div><a href="/<?php echo $product['Product']['url']?>" class="btn btn-warning">Detail produktu</a></div>
            </div>
            <?php $count++; 
        	} ?>
            <div class="info-box">Tato akční nabídka platí do vyprodání zásob. Chcete-li dostávat aktuální slevy, promoakce a další zajímavé nabídky, odebírejte náš newsletter.</div>
        </div>
        <ul class="nav nav-tabs" role="tablist">
        	<?php
        	$count = 1;
        	foreach ($hp_most_sold as $product) { ?>
            <li role="presentation"<?php echo ($count == 1 ? ' class="active"' : '')?>>
                <a href="#action<?php echo $count?>" aria-controls="action<?php echo $count?>" role="tab" data-toggle="tab">
                    <img src="/<?php echo $product['MostSoldProduct']['image']?>" /><span class="title"><?php echo $product['Product']['name']?></span>
                    <?php if (isset($product['Product']['discount']) && $product['Product']['discount']) { ?>
                	<span class="discount">-<?php echo $product['Product']['discount'] ?> %</span>
                	<?php } ?>
                </a>
            </li>
            <?php $count++;
        	} ?>
        </ul>
    </div>
</div>
<?php } ?>