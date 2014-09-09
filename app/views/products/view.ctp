<?php 
$image = '/img/na_250_250.jpg';
$has_image = false;
if (isset($product['Image'][0]) && !empty($product['Image'][0])) {
	$path = 'product-images/medium/' . $product['Image'][0]['name'];
	//				if (file_exists($path) && is_file($path) && getimagesize($path)) {
	$image = '/' . $path;
	$has_image = true;
	//				}
}
?>
<div class="product-image">
	<?php if ($has_image) { ?>
    <a href="/product-images/<?php echo $product['Image'][0]['name']?>" data-lightbox="product-image" data-title="<?php echo $product['Product']['name']?>">
    <?php } ?>
        <img src="<?php echo $image?>" />
    <?php if ($has_image) { ?>
    </a>
    <?php } ?>
</div>
<div class="product-desc">
    <h1><?php echo $product['Product']['heading']?></h1>
    <p class="manufacturer">Výrobce: <?php echo $product['Manufacturer']['name']?></p>
    <?php 
    $product['Product']['note'] = trim($product['Product']['note']);
    if (!empty($product['Product']['note'])) { ?>
    	<p class="note"><span class="header">Poznámka: </span><?php echo $product['Product']['note']?></p>
    <?php } ?>
	<p class="availability"><span class="header">Dostupnost:</span> <?php echo $product['Availability']['name']?><?php if ($product['Availability']['cart_allowed']) { ?> (<a href="/cenik-dopravy">kdy zboží dostanu</a>)<?php  } ?></p>
	<p class="price"><span class="header">Cena:</span> <span class="nominal"><?php echo round($product['Product']['discount_price'])?>,-</span></p>
	
	<?php // form pro vlozeni do kosiku
		if ($product['Availability']['cart_allowed']) {
			echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'id' => 'cart', 'encoding' => false, 'class' => 'form-inline'));
			echo $this->element('subproducts_choices', $this->requestAction('/subproducts/control/' . $product['Product']['id']));
		?>
		<div class="input-group">
			<?php echo $this->Form->input('Subproduct.quantity', array('type' => 'text', 'value' => 1, 'label' => false, 'div' => false, 'class' => 'form-control input-lg', 'style' => 'width: 50px;'));?>
		</div>
		<button class="btn btn-success btn-lg" type="submit"><i class="fa fa-shopping-cart"></i>&nbsp;Koupit produkt</button>
		<?php echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id'])); ?>
		<?php echo $this->Form->end();
		}
	?>

    <div class="product-shop-info">
        <div class="col-3">
            <strong>778 437 811</strong><br />
            Po - Čt 7:30 - 18:00<br />
            Pá: 7:30 - 16:30
        </div>
        <div class="col-3">
            <strong>info@lekarnatypos.cz</strong><br />
            Na e-maily reagujeme zpravidla do 24 hodin.
        </div>
    </div>
</div>
<div class="clearfix"></div>

<div class="product-detail">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#popis" data-toggle="tab">Detailní popis produktu</a></li>
        <li><a href="#komentare" data-toggle="tab">Komentáře / dotazy</a></li>
        <li><a href="#poslat" data-toggle="tab">Poslat známému</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="popis">
<?php // vypisuju podminene atributy - pokud obsahuji hodnotu, vypisu je
	if ($product['Product']['product_type_id'] || $product['Product']['code'] || $product['Product']['ean'] || $product['Product']['sukl'] || $product['Product']['group']) { ?>
		<ul>
	<?php if ($product['Product']['product_type_id']) { ?>
			<li>Skupina: <?php echo $product['ProductType']['name']?></li>
	<?php } ?>
	<?php if ($product['Product']['ean']) { ?>
			<li>EAN: <?php echo $product['Product']['ean']?></li>
	<?php } ?>
	<?php if ($product['Product']['code']) { ?>
			<li>Kód zboží: <?php echo $product['Product']['code']?></li>
	<?php } ?>
	<?php if ($product['Product']['sukl']) { ?>
			<li>Kód SÚKL: <?php echo $product['Product']['sukl']?></li>
	<?php } ?>
	<?php if ($product['Product']['group']) { ?>
			<li>Farmakoterapeutická skupina: <?php echo $product['Product']['group']?></li>
	<?php } ?>
		</ul>
<?php } ?>
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
			  	$publickey = "6LdMatsSAAAAAIq9qS9oC_fOWb7hCwFcyoYQcYSc"; // you got this from the signup page
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