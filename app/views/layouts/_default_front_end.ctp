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
	$title_for_content = 'MTE-Shop';
}

if ( !isset($description_for_content) ){
	$description_for_content = "Online obchod se zdravotními potřebami a doplňky pro diabetiky.";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="cs" lang="cs" xmlns="http://www.w3.org/1999/xhtml">	
	<head>		
		<meta http-equiv="content-type" content="text/html; charset=windows-1250" />
		<meta http-equiv="content-language" content="cs" />
		<title><?=$title_for_content?> | obchod.mte.cz</title>
		<?
			if ( isset($meta['robots']) && !empty($meta['robots']) ){
				echo $meta['robots'] . "\n";
			}
		?>
		<meta name="description" content="<?=$description_for_content?>" />
		<?=$html->css('front_end')?>
		
		<meta name="verify-v1" content="lOvhpFFBhCAy7HYfaV0Ty3RC91UVbDgdf2WpFPLTICY=" />
	</head>	

	<body>		
		<div id="centerContainer1">

			<div id="centerContainer2">
			
			<div id="banner1">
				<div id="logo">
					<a href="/"><img src="/images/logo-obchodu.gif" width="156px" height="59px" alt="MTE - obchod.mte.cz" /></a>
				</div>
				<div id="homeBox">
					<a id="homeLink" class="imageLink" href="/">Domů</a>
					<a id="contactLink" class="imageLink" href="/kontakt.htm">Kontakt</a>
				</div>
				<div id="loginBox">
					<?
						echo $this->element('login_box');
					?>
				</div>
				<?
					echo $this->element(
						'cart_info_box',
						$this->requestAction('/carts_products/stats')
					);
				?>				
			</div>
			<div id="blueRedLine">
				<div id="bluePart">
				<?=$form->create('Search', array('action' => 'parsequery')) ?>
				<?=$form->text('Search.q', array('class' => 'text'))  ?>
				<?=$form->submit('Hledej', array('div' => false, 'class' => 'submit')) ?>
				<?=$form->end() ?>
				</div>
				<h1 id="redPart"><?=$page_heading?></h1>
			</div>
			<div id="banner2"></div>
			<div id="mainContainer">
				<div id="menu">
					<?
					echo $this->element(
							'categories_menu',
							$this->requestAction('/categories/getCategoriesMenuList/' . $opened_category_id)
						);
					echo $this->element(
							'most_sold',
							$this->requestAction('/statistics/most_sold/' . $opened_category_id)
						);
					?>
				</div>
				<div id="mainContent">
					
					<?php
						if ($session->check('Message.flash')){
							echo $session->flash();
						}
						echo $content_for_layout
					?>

				</div><!-- ID: mainContent -->
				<div class="clearer"></div>
			</div>
			<div id="bottomLine">
				<div>
					<!--Cenník | Přidat mezi oblíbené | Nastavit jako domovskou stránku | Kontakt | Právní informace a pravidla | Mapa stránek-->
					<a href="/reklamacni-rad.htm">reklamační řád</a> | <a href="/dodaci-podminky.htm">dodací podmínky</a>
				</div>
				<div>Copyright &copy; MTE Obchod, 2008</div>
			</div>

			</div>
			</div>
			<div id="bottomShadow"></div>
			<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
			</script>
			<script type="text/javascript">

			_uOsr[28]="zbozi.seznam.cz";  _uOkw[28]="q";
			_uOsr[29]="mapy.cz";          _uOkw[29]="query";
			_uOsr[30]="search.centrum.cz";       _uOkw[30]="q";
			_uOsr[31]="jyxo.cz";          _uOkw[31]="s";
			_uOsr[32]="atlas.cz";         _uOkw[32]="q";
			_uOsr[33]="zoohoo.cz";        _uOkw[33]="q";
			_uOsr[34]="tiscali.cz";       _uOkw[34]="query";
			_uOsr[35]="1.cz";             _uOkw[35]="q";
			_uOsr[36]="volny.cz";         _uOkw[36]="search";
			_uOsr[37]="zoznam";           _uOkw[37]="s";
			_uOsr[38]="atlas.sk";         _uOkw[38]="phrase";
			_uOsr[39]="centrum.sk";       _uOkw[39]="q";
			_uOsr[40]="morfeo.sk";        _uOkw[40]="q";
			_uOsr[41]="szm";              _uOkw[41]="ws";
			_uOsr[42]="azet";             _uOkw[42]="sq";
			_uOsr[43]="zoohoo.sk";        _uOkw[43]="q";
			_uOsr[44]="seznamzbozi.cz";	_uOkw[44]="st";

			_uacct = "UA-294748-15";
			urchinTracker();
			</script>
	</body>
</html>