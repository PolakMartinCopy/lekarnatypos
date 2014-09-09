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

    <?php if ($this->params['controller'] != 'orders' && $this->params['action'] != 'finished') { ?>
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-38268275-1']);
        _gaq.push(['_trackPageview']);
        (function () {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
    <?php } ?>