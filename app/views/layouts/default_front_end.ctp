<?
/**
 * ZAKLADNI TEMPLATE PRO STRANKU PRODUKTU
*/
if ( !isset( $opened_category_id ) ){
	$opened_category_id = 5;
}

if ( !isset($page_heading) ){
	$page_heading = '_TITULEK_STRANKY_';
}

if ( !isset($title_for_content) ){
	$title_for_content = 'Nutrishop.cz';
}

if ( !isset($description_for_content) ){
	$description_for_content = "Online obchod se sportovní výživou a výživou pro fitness.";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="cs" lang="cs" xmlns="http://www.w3.org/1999/xhtml">	
	<head>		
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="content-language" content="cs" />
		<title><?=$title_for_content?> | nutrishop.cz</title>
		<?
			if ( isset($meta['robots']) && !empty($meta['robots']) ){
				echo $meta['robots'] . "\n";
			}
		?>
		<meta name="description" content="<?=$description_for_content?>" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>	
		<?php if (isset($fancybox)) { ?>
		<link rel="stylesheet" href="/css/fancybox/jquery.fancybox-1.3.2.css" type="text/css" media="screen" />
				
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
		<script type="text/javascript" src="/js/fancybox/jquery.fancybox-1.3.2.pack.js"></script>
		<script type="text/javascript" src="/js/fancybox/jquery.easing-1.3.pack.js"></script>
		<script type="text/javascript" src="/js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				/* Apply fancybox to multiple items */
				$("a.thickbox").fancybox({
					'transitionIn'	:	'elastic',
					'transitionOut'	:	'elastic',
					'speedIn'		:	600, 
					'speedOut'		:	200, 
					'overlayShow'	:	false
				});
				
			});
		</script>
		<?php } ?>
		
		<?=$html->css('front_end')?>
		
		<script type="text/javascript">
			$(document).ready(function(){
				if ( !$.browser.msie ){
					$("body").append('<img class="preloaded" src="http://www.nutrishop.cz/images/ajax-loader.gif" width="100px" height="100px" alt="" style="display:none"/>');
					$("body").append('<img class="preloaded" src="http://www.nutrishop.cz/images/pruhledny-pixel.png" width="1px" height="1px" alt="" style="display:none"/>');
				}

				$("a#finalLink").click(function(){
					$("body").append('<div id="darkLayer" class="darkClass" style="height:'+ $(document).height() +'px"><div id="loading"><img src="http://www.nutrishop.cz/images/ajax-loader.gif" width="100px" height="100px" alt="" /><p>Ukládám Vaši objednávku.</p></div></div>');
				});
			});
		</script>
		
		<?php if ($this->params['controller'] == 'products' && $this->params['action'] == 'view') {?>
		<script type="text/javascript">
			$(document).ready(function() {
				$(".similarProductBox").mouseover(function(evObj){
					$(this).css({"backgroundColor":"#A9CCDE", "border":"1px solid silver"});
				});
				$(".similarProductBox").mouseout(function(evObj){
					$(this).css({"background":"none", "border":"1px solid #F5F5F5"});
				});
			});
		</script>
		<?php } ?>
		
		<meta name="verify-v1" content="lUBgsUIMHBPxU+9HodgKsDei9JAdhILicUNu1L7+KEM=" />
	</head>
	<body>
		<div id="content-wrapper">
			<div id="top-links">
				<ul>
					<li><a href="/darek-zdarma-c54">dárek zdarma</a></li>
					<li><a href="/novinky-v-nabidce-c55">novinky v nabídce</a></li>
					<li><a href="/vyhodne-balicky-c56">výhodné balíčky</a></li>
					<li><a href="/vyprodej-skladovych-zasob-c57">výprodej skladových zásob</a></li>
				</ul>
			</div>

			<div id="top-baner">
				<div id="cart-info-box">
					<? echo $this->element('cart_info_box', $carts_stats); ?>				
				</div>
			</div>
			
			<div id="top-stripe-navi"></div>

			<div id="mid-left-column-wrapper">
				<div id="middle-column">
					<?
						if ($session->check('Message.flash')){
							echo $session->flash();
						}
					?>
					<h1><?=$page_heading?></h1>
					<?=$content_for_layout?>
				</div>
				<div id="left-column">
					<div class="menu">
						<? echo $this->element('categories_menu', $categories_menu); ?>
					</div>
					
					<div id="sportovni-obleceni-vrch"></div>
					<div class="menu">
						<? echo $this->element('categories_menu', $nebbia_menu); ?>
					</div>
					
					<div id="posilovaci-stroje-vrch"></div>
					<div class="menu">
						<? echo $this->element('categories_menu', $other_menu);?>
					</div>
					<div id="spoluprace-vrch"></div>
					<div id="spoluprace">
						<?
						//echo $this->element('we_support');
						?>

					</div>
				</div>
			</div>
			<div id="right-column">
				<div id="hledani-vrch"></div>
				<div id="hledani-form">
					<?=$form->create('Search', array('action' => 'do_search')) ?>
					<div>
						<?=$form->text('Search.q', array('class' => 'text', 'value' => '- hledat -', 'onclick' => 'return this.select();'))  ?>
						<?=$form->submit('Hledej', array('div' => false, 'class' => 'submit')) ?>
					</div>
					<?=$form->end() ?>
				</div>
				<div id="user-menu-vrch"></div>
				<ul id="user-menu">
					<li><a href="/">úvodní stránka</a></li>
					<?
						if ( $session->check('Customer.id') ){
							echo '<li><a href="/customers">zákaznický panel</a></li>';
							echo '<li><a href="/customers/orders_list">mé objednávky</a></li>';
							echo '<li><a href="/customers/edit">změna hesla</a></li>';
							echo '<li><a href="/customers/logout">odhlásit</a></li>';
						} else {
							echo '<li><a href="/customers/login">přihlášení</a></li>';
							echo '<li><a href="/customers/add">registrace</a></li>';
						}
					?>
				
					<li><hr /></li>
					<li><a href="/firma.htm">o firmě</a></li>
					<li><a href="/obchodni-podminky.htm">obchodní podmínky</a></li>
					<!-- <li><a href="/jak-nakupovat.htm">jak nakupovat</a></li> -->
					<li><a href="/reklamacni-rad.htm">reklamační řád</a></li>
					<li><a href="/firma.htm">kontakty</a></li>
					<li><a href="/slovensko.htm">Slovensko</a></li>
					<li><a href="/katalog.htm">katalog</a></li>
				</ul>
				<div id="novinky-vrch"></div>
				<div id="novinky">
					<? echo $this->element('products_newest', $newest_product); ?>
				</div>
				<div id="vyrobci-vrch"></div>
				<div id="vyrobci">
					<? echo $this->element('manufacturers_list', $manufacturers_list); ?>
				</div>
			</div>
			<div style="clear:both;"></div>			
		
			<div id="footer">
				&copy;2007 - <?=date('Y')?> Stanislav Vávra, <a href="/">sportovní výživa - nutrishop.cz</a>
			</div>
		</div>
		<!-- navrcholu.cz -->
		<script src="http://c1.navrcholu.cz/code?site=108553;t=lb14" type="text/javascript"></script>
		<noscript>
			<div>
				<a href="http://navrcholu.cz/">
					<img src="http://c1.navrcholu.cz/hit?site=108553;t=lb14;ref=;jss=0" width="14" height="14" alt="NAVRCHOLU.cz" style="border:none" />
				</a>
			</div>
		</noscript>
<?php if ($this->params['controller'] != 'orders' && $this->params['action'] != 'finished') { ?>
 		<script type="text/javascript" src="/js/ga-add.js"></script>
<?php } ?>
	</body>
</html>
<?php echo $this->element('sql_dump'); ?>