<div class="module-products">
    <div class="module-filters clearfix">
        <form id="filterForm" action="#filterForm" autocomplete="off" method="get">
            <div class="select" id="brand">
            	<?php $filter_select_expanded = (isset($filter_tab) && $filter_tab == 'brand'); ?>
                <span data-default="Výrobce" data-more-format="Výrobce: %d" class="filter-selector<?php echo ($filter_select_expanded ? ' expanded' : '')?>">
				<?php 
					$filter_heading = 'Výrobce';
					if (isset($_GET['filter']['manufacturer_id']) && !empty($_GET['filter']['manufacturer_id'])) {
						$selected_manufacturer_ids = $_GET['filter']['manufacturer_id'];
						$selected_manufacturers_count = count($selected_manufacturer_ids); 
						if ($selected_manufacturers_count == 1) {
							$filter_heading = 'Výrobce: ' . $selected_manufacturers_count;
							foreach ($filter_manufacturers as $manufacturer) {
								if ($manufacturer['Manufacturer']['id'] == $selected_manufacturer_ids[0]) {
									$manufacturer_name = $manufacturer['Manufacturer']['name'];
									if (strlen($manufacturer_name) > 15) {
										$manufacturer_name = mb_substr($manufacturer_name, 0, 15) . '...';
									}
									$filter_heading =  $manufacturer_name;
								}
							}
						} else {
							$filter_heading = 'Výrobci: ' . $selected_manufacturers_count;
						}
					}
					echo $filter_heading;
				?>
           	    </span>
                <div class="items opened"<?php echo ($filter_select_expanded ? '' : ' style="display: none;"')?>>
                    <ul>
                        <?php
                        foreach ($filter_manufacturers as $manufacturer) {
							// pokud mam zakliknuteho vyrobce v GET parametrech, musim dat class checked
							$found = false;
							if (isset($_GET['filter']['manufacturer_id'])) {
								foreach ($_GET['filter']['manufacturer_id'] as $checked_manufacturer_id) {
									if ($manufacturer['Manufacturer']['id'] == $checked_manufacturer_id) {
										$found = true;
										break;
									}
								}
							}
                        ?>
                        <li>
                            <a href="#"<?php echo ($found ? ' class="checked"' : '')?> data-manufacturer-id="<?php echo $manufacturer['Manufacturer']['id']?>"><?php echo $manufacturer['Manufacturer']['name']?></a>
                        </li>
                        <?php } ?>
                    </ul>
                    <select name="filter[manufacturer_id][]" class="hide" id="filter_filter_brand" multiple="multiple">
                        <?php foreach ($filter_manufacturers as $manufacturer) {
                        	// pokud mam zakliknuteho vyrobce v GET parametrech, musim dat class checked
                        	$found = false;
                        	foreach ($_GET['filter']['manufacturer_id'] as $checked_manufacturer_id) {
                        		if ($manufacturer['Manufacturer']['id'] == $checked_manufacturer_id) {
                        			$found = true;
                        			break;
                        		}
                        	}
                        	?>
                        <option value="<?php echo $manufacturer['Manufacturer']['id']?>" data-available="<?php echo $manufacturer['Manufacturer']['id']?>"<?php echo ($found ? ' selected' : '')?>><?php echo $manufacturer['Manufacturer']['name']?></option>
						<?php } ?>
                    </select>
                    <a href="#" class="clear-filter" data-identificator="brand">Zrušit filtr</a>
                </div>
            </div>
            <div class="select" id="price">
            	<?php $filter_select_expanded = (isset($filter_tab) && $filter_tab == 'price'); ?>
                <span data-default="Cena" data-format="%d Kč - %d Kč" class="filter-selector<?php echo ($filter_select_expanded ? ' expanded' : '')?>">
				<?php 
					$filter_heading = 'Cena';
					if (isset($_GET['filter']['price']['min']) && !empty($_GET['filter']['price']['min']) && isset($_GET['filter']['price']['max']) && !empty($_GET['filter']['price']['max'])) {
						$price_range_min = $_GET['filter']['price']['min'];
						$price_range_max = $_GET['filter']['price']['max'];
						// pokud jsem pohnul se sliderem, nastavim vybrane rozmezi do hlavicky filtru
						if ($price_range_min != $cheapest_price || $price_range_max != $most_expensive_price) {
							$filter_heading = $price_range_min . ' - ' . $price_range_max;
						}
					}
					echo $filter_heading;
				?>
                </span>
                <div class="items opened"<?php echo ($filter_select_expanded ? ' style="height:100px; display:block;"' : ' style="display: none;"')?>>
                	<p>Cenové rozpětí</p>
                    <b><?php echo $cheapest_price?> Kč</b>
                    <input type="text" class="slider" value="" data-slider-min="<?php echo $cheapest_price?>" data-slider-max="<?php echo $most_expensive_price?>" data-slider-step="1" data-slider-value="[<?php echo (isset($_GET['filter']['price']['min']) && !empty($_GET['filter']['price']['min']) ? $_GET['filter']['price']['min'] : $cheapest_price)?>,<?php echo (isset($_GET['filter']['price']['max']) && !empty($_GET['filter']['price']['max']) ? $_GET['filter']['price']['max'] : $most_expensive_price)?>]" id="sl2" style="width:163px">
                    <b><?php echo $most_expensive_price?> Kč</b>
                    <input type="hidden" name="filter[price][min]" id="filterPriceMin" value="<?php echo (isset($_GET['filter']['price']['min']) && !empty($_GET['filter']['price']['min']) ? $_GET['filter']['price']['min'] : $cheapest_price)?>" />
                    <input type="hidden" name="filter[price][max]" id="filterPriceMax" value="<?php echo (isset($_GET['filter']['price']['max']) && !empty($_GET['filter']['price']['max']) ? $_GET['filter']['price']['max'] : $most_expensive_price)?>" />
                    <a href="#" class="clear-filter" data-identificator="price">Zrušit filtr</a>
                </div>
            </div>
            <div class="select" id="sorting">
            	<?php $filter_select_expanded = (isset($filter_tab) && $filter_tab == 'sorting'); ?>
                <span data-default="Řazení"  class="filter-selector<?php echo ($filter_select_expanded ? ' expanded' : '')?>">
				<?php 
					$filter_heading = 'Řazení';
					if (isset($_GET['filter']['sorting']) && !empty($_GET['filter']['sorting']) && $_GET['filter']['sorting'][0] != 0) {
						$filter_heading = $sorting_options[$_GET['filter']['sorting'][0]]['name'];
					}
					echo $filter_heading;
				?>
                </span>
                <div class="items opened"<?php echo ($filter_select_expanded ? '' : ' style="display: none;"')?>>
                    <ul>
                    	<?php foreach ($sorting_options as $index => $sorting_option) { ?>
                    	<li><a href="#"<?php echo ((isset($_GET['filter']['sorting']) && $_GET['filter']['sorting'][0] == $index) ? ' class="checked"' : '')?> data-sorting-id="<?php echo $index?>"><?php echo $sorting_option['name'] ?></a>
                    	<?php } ?>
                    </ul>
                    <select name="filter[sorting][]" class="hide" id="filter_filter_brand">
						<?php foreach ($sorting_options as $index => $sorting_option) { ?>
                        <option value="<?php echo $index ?>"<?php echo ((isset($_GET['filter']['sorting']) && $_GET['filter']['sorting'][0] == $index) ? ' selected' : '')?>><?php echo $sorting_option['name']?></option>
                        <?php } ?>
                    </select>
                    <a href="#" class="clear-filter" data-identificator="sorting">Zrušit filtr</a>
                </div>
            </div>
            <input type="hidden" id="filterDefaultTab" name="filter[default_tab]" value="" />
            <input type="hidden" id="resetFilter" name="filter[reset_filter]" value="" />
            <input type="hidden" id="searchQuery" name="filter[query]" value="<?php echo (isset($query) ? $query : '')?>" />
        </form>
    </div>
    <div class="product-list">
    	<?php if (empty($products)) { ?>
    	<p>Zadané kombinaci filtrů neodpovídají žádné produkty v našem obchodě.</p>
    	<?php } else { ?>
        <div class="product-table">
        	<?php foreach ($products as $product) { ?>
            <div class="product-card ">
                <div class="title"><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></div>
                <a href="/<?php echo $product['Product']['url']?>"><img src="/product-images/small/<?php echo $product['Image']['name']?>" title="<?php echo $product['Product']['name']?>" alt="<?php echo $product['Product']['name']?>" /></a>
                <div class="price-box">
                    <div class="param-horizontal"><span>Naše cena:</span><span class="price"><?php echo front_end_display_price($product['Product']['price'])?> Kč</span></div>
                    
                    <?php
                    $visibility = ' style="visibility:hidden"';
                    if (isset($product['Product']['discount']) && $product['Product']['discount']) {
						$visibility = '';
                    }
                    ?>
                    <div class="param-horizontal"<?php echo $visibility?>><span>Běžná cena:</span><span class="standard-price"><?php echo front_end_display_price($product['Product']['retail_price_with_dph'])?> Kč</span></div>
                    <div class="param-horizontal"<?php echo $visibility?>><span>Ušetříte:</span><span class="discount"><?php echo front_end_display_price($product['Product']['discount'])?> %</span></div>
                    
                    <?php // form pro vlozeni do kosiku
                    	echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
					?>
					<div class="count-input">
						<?php echo $this->Form->input('Product.quantity', array('label' => false, 'div' => false, 'value' => 1, 'maxlength' => 4)); ?>
						<div class="count-add">+</div>
						<div class="count-remove">-</div>
					</div>
					<span class="count-unit">ks</span>
					<?php
						echo $this->Form->button('Koupit', array('id' => 'AddToCartButton', 'class' => 'btn btn-warning btn-sm'));
						echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
						echo $this->Form->end();
					?>
                </div>
                <p class="desc"><?php echo $product['Product']['short_description']?></p>
                <?php if ($product['Product']['is_akce'] || $product['Product']['is_novinka'] || $product['Product']['is_doprodej'] || $product['Product']['is_bestseller'] || $product['Product']['is_darek_zdarma']) { ?>
				<?php echo $this->element(REDESIGN_PATH . 'product_labels', array('product' => $product))?>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
<?php 
	//extract the get variables
	$url = $this->params['url'];
	unset($url['url']);
	$get_var = http_build_query($url);
	
	$arg1 = array(); $arg2 = array();
	//take the named url
	if(!empty($this->params['named']))
		$arg1 = $this->params['named'];
	
	//take the pass arguments
	if(!empty($this->params['pass']))
		$arg2 = $this->params['pass'];
	
	//merge named and pass
	$args = array_merge($arg1,$arg2);
	
	//add get variables
	$args["?"] = $get_var;
	
	// kotva
	$args['#'] = 'filterForm';
	
	$this->Paginator->options(array('url' => $args));    
?>
	<?php if ($this->Paginator->hasNext() || $this->Paginator->hasPrev()) { ?>
	<div class="module-pagination">
        <ul class="pagination hidden-xs">
        	<?php if ($this->Paginator->hasPrev()) {?>
            <li><?php echo $this->Paginator->prev('<span aria-hidden="true">&laquo;</span>', array('aria-label' => 'Previous', 'escape' => false), null, array('class' => 'disabled', 'escape' => false)); ?></li>
            <?php } ?>
            <li><?php echo $this->Paginator->numbers(array('separator' => '</li><li>', 'first' => 0, 'last' => 0, 'modulus' => 4));?></li>
            <?php if ($this->Paginator->hasNext()) {?>
            <li><?php echo $this->Paginator->next('<span aria-hidden="true">&raquo;</span>', array('aria-label' => 'Next', 'escape' => false), null, array('class' => 'disabled', 'escape' => false)); ?></li>
            <?php } ?>
        </ul>
        <a href="#" class="btn btn-primary hidden-md hidden-lg hidden-sm"><i class="fa fa-fw  fa-arrow-circle-down"></i>Načíst další produkty</a>
    </div>
    <?php } ?>
</div>