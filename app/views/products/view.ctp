<?php
// pokud ma varianty, skryju pole pro vlozeni ks do kosiku
// tlacitko se bude chovat jako odkaz na kotvu, ktera se nachazi u formulare pro vlozeni produktu s variantami
if (!empty($subproducts) && $product['Availability']['cart_allowed']) { ?>
<script type="text/javascript">
	window.onload = function(){
		$('#ProductQuantity').hide();
		$('#AddToCartButton').click(function(e) {
			e.preventDefault();
			$('html, body').animate({
		        scrollTop: $("#AddProductWithVariantsForm").offset().top
		    }, 1000);
		});
	};
</script>
<?php } ?>

<?php
$image = '/img/' . REDESIGN_PATH . 'na_250_250.jpg';
$has_image = false;
if (isset($product['Image'][0]) && !empty($product['Image'][0])) {
	$path = 'product-images/' . $product['Image'][0]['name'];
	//				if (file_exists($path) && is_file($path) && getimagesize($path)) {
	$image = '/' . $path;
	$has_image = true;
	//				}
}
?>

<div class="module-product-detail">
    <h1><?php echo $product['Product']['heading']?></h1>
    <div class="product-image">
        <img src="<?php echo $image ?>" title="<?php echo $product['Product']['name']?>" alt="Obrázek <?php echo $product['Product']['name']?>" />
		<?php echo $this->element(REDESIGN_PATH . 'product_labels', array('product' => $product))?>
    </div>
    <div class="product-details">
        <div class="product-short-desc">
            <p><?php echo $product['Product']['short_description']?> <a href="#popis" data-role="showDescription">Celý popis</a></p>
        </div>
        <?php if (isset($product['Product']['ean']) && !empty($product['Product']['ean'])) { ?>
        <div class="product-code">
            <div class="param-horizontal">
                <span>EAN:</span>
                <span><?php echo $product['Product']['ean']?></span>
            </div>
        </div>
        <?php } ?>
        <div class="product-parameters">
            <?php if (isset($product['Manufacturer']['name']) && !empty($product['Manufacturer']['name'])) { ?>
            <div class="param-horizontal">
                <span>Výrobce:</span>
                <span><?php echo $this->Html->link($product['Manufacturer']['name'], $product['Manufacturer']['url'])?></span>
            </div>
            <?php } ?>
            <div class="param-horizontal">
                <span>Dostupnost:</span>
                <span><?php if ($product['Product']['active']) { ?>
               		<a href="/cenik-dopravy"><?php echo ucfirst($product['Availability']['name']) ?></a>
               	<?php } else { ?>
               		Nedostupné
               	<?php } ?></span>
            </div>
            <?php if (isset($product['Product']['note']) && !empty($product['Product']['note'])) { ?>
            <div class="param-horizontal">
                <span style="color:red"><strong>Poznámka:</strong></span><br/>
                <span><?php echo $product['Product']['note']?></span>
            </div>
            <?php } ?>
        </div>
        <div class="price-box">
        	<?php if (isset($product['Product']['retail_price_with_dph']) && isset($product['Product']['price']) && floor($product['Product']['retail_price_with_dph']) != $product['Product']['price']) { ?>
            <div class="param-horizontal"><span>Běžná cena:</span> <span class="standard-price"><?php echo front_end_display_price($product['Product']['retail_price_with_dph'])?> Kč</span>
            <?php if (isset($product['Product']['discount']) && $product['Product']['discount']) { ?>
            (ušetříte <span class="discount"><?php echo $product['Product']['discount']?> %</span>)
            <?php } ?>
            </div>
            <?php } ?>
            <div class="param-horizontal"><span>Naše cena:</span> <span class="price"><?php echo front_end_display_price($product['Product']['price'])?> Kč</span></div>

<!-- VLOZENI DO KOSIKU -->
<?php
	if (empty($subproducts) && $product['Availability']['cart_allowed'] && $product['Product']['active']) {
		echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
?>
			<div class="count-input">
<?php echo $this->Form->input('Product.quantity', array('label' => false, 'div' => false, 'value' => 1, 'maxlength' => 4)); ?>
				<div class="count-add">+</div>
				<div class="count-remove">-</div>
			</div>
			<span class="count-unit">ks</span>
<?php 
		echo $this->Form->button('Přidat do košíku', array('id' => 'AddToCartButton', 'class' => 'btn btn-warning btn-lg'));
		echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
		echo $this->Form->end();
	// produkt ma varianty
	} elseif (!empty($subproducts) && $product['Availability']['cart_allowed']) {
		echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false));
		echo $this->Form->button('Přidat do košíku', array('id' => 'AddToCartButton', 'class' => 'btn btn-warning btn-lg'));
		echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
		echo $this->Form->end();
	}
?>
        </div>
    </div>
    
    
<!-- VLOZENI DO KOSIKU, KDYZ PRODUKT MA VARIANTY -->
<?php if (!empty($subproducts) && $product['Availability']['cart_allowed'] && $product['Product']['active']) { ?>
	<div class="product-variants row">
		<div class="col-xs-12">
			<h4 class="headline">Zvolte si variantu</h4>

<?php echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'encoding' => false, 'id' => 'AddProductWithVariantsForm')); ?>
			<table class="table">
				<thead>
					<tr>
						<th>Varianta</th>
						<th>Naše cena</th>
						<th class="hidden-xs hidden-sm">Množství</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
<?php 
foreach ($subproducts as $subproduct) {
	$information = '';
	foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
		$information .= $attributes_subproduct['Attribute']['Option']['name'] . ': ' . $attributes_subproduct['Attribute']['value'] . '<br/>';
	}
	
	$subproduct['Subproduct']['price_with_dph'] += $product['Product']['price']; 
?>
					<tr>
						<td><?php echo $information ?></td>
						<td class="center"><span class="price"><?php echo $subproduct['Subproduct']['price_with_dph']?>&nbsp;Kč</span></td>
						<td class="hidden-xs hidden-sm">
							<div class="count-input">
								<?php echo $this->Form->input('Subproduct.' . $subproduct['Subproduct']['id'] . '.quantity', array('label' => false, 'div' => false, 'value' => 1))?>
								<div class="count-add">+</div><div class="count-remove">-</div>
							</div>
							<span class="count-unit">ks</span>
							<?php echo $this->Form->hidden('Subproduct.' . $subproduct['Subproduct']['id'] . '.id', array('value' => $subproduct['Subproduct']['id']))?>
						</td>
						<td class="center"><button name="data[Subproduct][<?php echo $subproduct['Subproduct']['id'] ?>][chosen]" value="1" class="btn btn-warning">Do košíku</button></td>
					</tr>
<?php } ?>
				</tbody>
			</table>
<?php echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']))?>
<?php echo $this->Form->end()?>
		</div>
	</div>
<?php } ?>


    <div class="product-shop-info clearfix">
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="headline">Máte rádi toto zboží? Dejte to všem vědět!</div>
            <div class="share-buttons">
                <div class="fb-like" data-href="https://developers.facebook.com/docs/plugins/" data-layout="button" data-action="like" data-show-faces="false" data-share="true"></div>
                <a href="https://twitter.com/share" class="twitter-share-button" data-via="lekarnatypos" data-count="none">Tweet</a>
                <script>!function (d, s, id) { var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https'; if (!d.getElementById(id)) { js = d.createElement(s); js.id = id; js.src = p + '://platform.twitter.com/widgets.js'; fjs.parentNode.insertBefore(js, fjs); } }(document, 'script', 'twitter-wjs');</script>
                <div class="g-plusone" data-size="medium" data-annotation="none"></div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6">
            <div class="headline">Potřebujete pomoci?</div>
            <span class="contact"><i class="fa fa-fw fa-phone"></i> <strong>778 437 811</strong></span> (Po - Čt 7:30 - 18:00, Pá: 7:30 - 16:30)<br />
            <span class="contact"><i class="fa fa-fw fa-envelope"></i> <strong>info@lekarnatypos.cz</strong></span> Na e-maily reagujeme zpravidla do 24 hodin.
        </div>
    </div>
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#popis" data-toggle="tab" data-role="showDescription">Detailní popis produktu</a></li>
        <li class="hidden-xs"><a href="#komentare" data-toggle="tab" data-role="showComments">Komentáře / dotazy</a></li>
        <li class="hidden-xs"><a href="#poslat" data-toggle="tab">Poslat známému</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="popis">
            <?php echo $product['Product']['description']?>
        </div>
        <div class="tab-pane" id="komentare">
            <?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'add'), 'id' => 'CommentAddForm', 'encoding' => false, 'role' => 'form'))?>
			<div class="form-group">
				<label>Jméno:</label>
				<?php echo $this->Form->input('Comment.author', array('label' => false, 'size' => 50, 'class' => 'form-control'))?>
				<div class="formErrors"></div>
			</div>
			<div class="form-group">
				<label>Email:</label>
				<?php echo $this->Form->input('Comment.email', array('label' => false, 'size' => 50, 'class' => 'form-control'))?>
				<div class="formErrors"></div>
			</div>
			<div class="form-group">
				<label>Předmět:</label>
				<?php echo $this->Form->input('Comment.subject', array('label' => false, 'size' => 50, 'class' => 'form-control'))?>
				<div class="formErrors"></div>
			</div>
			<div class="form-group">
				<label>Dotaz</label>
				<?php echo $this->Form->input('Comment.body', array('label' => false, 'cols' => 63, 'rows' => 10, 'class' => 'form-control'))?>
				<div class="formErrors"></div>
			</div>
			<?php
			echo $this->Form->hidden('Comment.product_id', array('value' => $product['Product']['id']));
			echo $this->Form->hidden('Comment.request_uri', array('value' => $_SERVER['REQUEST_URI'])); 
			echo $this->Form->submit('Odeslat dotaz', array('class' => 'btn btn-success btn-lg'));
			echo $this->Form->end();

			if (empty($product['Comment'])) { ?>
			<p>Diskuze neobsahuje žádné komentáře pro tento produkt.</p>
			<?php } else { ?>
			<div style="margin-top:10px;">
			<?php foreach ($product['Comment'] as $comment) { ?>
				<div style="background-color:silver;padding:3px;">
					<p><strong><?php echo $comment['subject']?></strong> od <strong><?php echo $comment['author']?></strong> ze dne <em><?php echo cz_date_time($comment['created'])?></em></p>
				</div>
				<?php echo $comment['body']?>
				<?php if (!empty($comment['reply'])) { ?>
					<div style="margin-top:5px;padding-left:5px;margin-left:15px;border-left:1px solid black;">
						<p><?php echo $comment['reply']?></p>
						<p>za <em><?php echo CUST_NAME?></em><br /><?php echo $comment['Administrator']['first_name']?> <?php echo $comment['Administrator']['last_name']?></p>
					</div>
				<?php } ?>
			</div>
			<?php }
			} ?>
        </div>
        <div class="tab-pane" id="poslat">
        	<?php echo $this->Form->create('Recommendation', array('url' => array('controller' => 'recommendations', 'action' => 'send'), 'id' => 'RecommendationFormProduct', 'encoding' => false))?>
			<div class="form-group">
				<label>Vaše jméno:</label>
				<?php echo $this->Form->input('Recommendation.source_name', array('label' => false, 'type' => 'text', 'size' => 50, 'class' => 'form-control'))?>
			</div>
			<div class="form-group">
				<label>Váš email<sup>*</sup></label>
				<?php echo $this->Form->input('Recommendation.source_email', array('label' => false, 'type' => 'text', 'size' => 50, 'class' => 'form-control'))?>
				<div class="formErrors"></div>
			</div>
			<div class="form-group">			
				<label>Email adresáta<sup>*</sup></label>
				<?php echo $this->Form->input('Recommendation.target_email', array('label' => false, 'type' => 'text', 'size' => 50, 'class' => 'form-control')); ?>
				<div class="formErrors"></div>
			</div>
			<?php 
				echo $this->Form->hidden('Recommendation.request_uri', array('value' => $_SERVER['REQUEST_URI'], 'id' => 'RecommendationRequestUriProduct'));
				require_once 'recaptchalib.php';
			  	$publickey = "6Le4B_oSAAAAAANtlNhA6j_p0kFyWBItvS31U7Q5"; // you got this from the signup page
			  	echo recaptcha_get_html($publickey);
			?>
			<div id="RecommendationRecaptchaErrorProduct" class="formErrors"></div>
			<?php 
				echo $this->Form->submit('Odeslat známému', array('class' => 'btn btn-success btn-lg'));
				echo $this->Form->end();
			?>
        </div>
    </div>
</div>
<?php echo $this->element(REDESIGN_PATH . 'product_carousel', array('module_class' => 'module-last-visited', 'element_id' => 'last-visited-products', 'title' => 'Naposledy prohlížené zboží', 'products' => $last_visited_products))?>
<?php echo $this->element(REDESIGN_PATH . 'product_carousel', array('module_class' => 'module-related', 'element_id' => 'related-products', 'title' => 'Související zboží', 'products' => $similar_products))?>

<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH ?>customer_tracking/product.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	productId = <?php echo $product['Product']['id'] ?>;
	productVisit(productId);
	productDescClick(productId);
	productCommentsClick(productId);
});
</script>