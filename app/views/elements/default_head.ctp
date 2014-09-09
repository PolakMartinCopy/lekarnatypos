<?php 
	if (!isset($title_for_content)) {
		$title_for_content = 'Lékárna Typos Brno';
	}
	
	if (!isset($description_for_content)) {
		$description_for_content = 'Lékárna Typos Brno.';
	}
?>

    <meta charset="utf-8" />
	<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />
	<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <title><?php echo $title_for_content?></title>
    <meta name="description" content="<?php echo $description_for_content?>" />
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
    <meta name="viewport" content="initial-scale=1.0, width=device-width, maximum-scale=1.0, user-scalable=no" />

    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/bootstrap-theme.min.css" />
    
    <link rel="stylesheet" type="text/css" href="/css/style.css" />
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <!--<script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-38268275-1']);
        _gaq.push(['_trackPageview']);
        (function () {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>-->

<?php if (false) { ?>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<meta http-equiv="content-language" content="cs"/>
<title><?php echo $title_for_content?> | MeaVita</title>
<meta name="description" content="<?php echo $description_for_content?>"/>
<link rel="stylesheet" type="text/css" href="/css/styles.css" />
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.js"></script>
<script type="text/javascript" src="/js/login_box_management.js"></script>
<script type="text/javascript" src="/js/search_box_management.js"></script>
<script type="text/javascript" src="/js/footer_forms_management.js"></script>

<link rel="stylesheet" href="/css/fancybox/jquery.fancybox-1.3.2.css" type="text/css" media="screen" />
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
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38268275-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php } ?>