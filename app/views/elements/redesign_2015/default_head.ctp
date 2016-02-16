<?php 
	if (!isset($title_for_content)) {
		$title_for_content = 'Online lékárna Brno střed';
		if (isset($_title)) {
			$title_for_content = $_title;
		}
	}
	
	if (!isset($description_for_content)) {
		$description_for_content = 'Online Lékárna v centru Brna - LékárnaTypos CZ.';
		if (isset($_description)) {
			$description_for_content = $_description;
		}
	}
?>

<meta charset="utf-8" />
<title><?php echo $title_for_content?> | <?php echo CUST_NAME?></title>
<meta name="description" content="<?php echo $description_for_content?>" />
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
<meta name="viewport" content="initial-scale=1.0, width=device-width, maximum-scale=1.0, user-scalable=no" />

<link rel="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH ?>css/bootstrap.css?v=5" />
<link rel="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH ?>css/custom-styles.css?v=9" />

<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>

<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-50091083-1']);
    _gaq.push(['_trackPageview']);
    (function () {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>

<script src="/js/<?php echo REDESIGN_PATH ?>customer_tracking/bootstrap.js"></script>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','//connect.facebook.net/en_US/fbevents.js');

fbq('init', '1163921323618771');
fbq('track', "PageView");</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1163921323618771&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->