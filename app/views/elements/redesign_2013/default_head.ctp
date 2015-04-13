<?php
	if (!isset($_title) || $_title == '') {
		$_title = 'Online Lékárna';
	}
	if (!isset($_description) || $_description == '') {
		$_description = 'Léky a doplňky stravy online od LékárnaTypos CZ. Přípravky proti bolesti, nachlazení a mnohé další.';
	}
?>
		<meta charset="UTF-8" />
		<meta http-equiv="content-language" content="cs" />
		<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />
		<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
		<link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH?><?php echo ($_SERVER['REMOTE_ADDR'] == IMAGE_IP ? 'style000-new.css' : 'style000.css') ?>" type="text/css" media="screen" />
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.js" type="text/javascript"></script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.js"></script>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH?>fancybox/jquery.fancybox.css" media="screen" />
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.easing.js" type="text/javascript"></script>
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery.slidorion.js" type="text/javascript"></script>
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>main.js" type="text/javascript"></script>
		<title><?php echo $_title?> : Sportnutrition</title>
		<meta name="description" content="<?php echo $_description?>" />
<?php if (isset($_keywords) && !empty($_keywords) && is_string($_keywords)) {?>
		<meta name="keywords" content="<?php echo $_keywords?>" />
<?php } ?>
<?php if ($this->params['controller'] == 'searches' && $this->params['action'] == 'do_search') { ?>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>search_filter.js"></script>
<?php } ?>
		<link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH ?>jqueryui/style.css" />
		<script charset="utf-8" src="/js/<?php echo REDESIGN_PATH?>jquery-ui.js" type="text/javascript"></script>
		
		<!-- JRATING -->
		<!-- include CSS & JS files -->
		<!-- CSS file -->
		<link rel="stylesheet" type="text/css" href="/jRating-master/jquery/jRating.jquery.css" media="screen" />
		<!-- jQuery files -->
		<script type="text/javascript" src="/jRating-master/jquery/jRating.jquery.js"></script>
		<script type="text/javascript">
			var ratingStarType = 'small';
			<?php if (isset($this->params['controller']) && isset($this->params['action']) && $this->params['controller'] == 'products' && $this->params['action'] == 'view') { ?>
				ratingStarType = 'big';
			<?php } ?> 
		</script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>/product_rating_management.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			var url = document.URL;
			var a = $('<a>', { href:url } )[0];
			if (a.hash == '#nutrishop_redirect') {
				$('#banner').fancybox({
					width: 600,
					height: 470,
					autoSize: false,
				}).trigger('click');
			}
		});
		</script>