<?php
	if (!isset($_title) || $_title == '') {
		$_title = 'Sportovní výživa, doplňky stravy, kloubní výživa, vitamíny';
	}
	if (!isset($_description) || $_description == '') {
		$_description = 'Sportovní výživa od Sport Nutrition. Proteinové a sacharidové přípravky (proteiny, sacharidy, gainery), vitamíny a aminokyseliny.';
	}
?>
		<meta charset="UTF-8" />
		<meta http-equiv="content-language" content="cs" />
		<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE" />
		<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
		<link rel="stylesheet" href="/css/<?php echo REDESIGN_PATH?><?php echo ($_SERVER['REMOTE_ADDR'] == IMAGE_IP ? 'style000-new.css' : 'style000.css?v=1') ?>" type="text/css" media="screen" />
		<title><?php echo $_title?> : Sportnutrition</title>
		<meta name="description" content="<?php echo $_description?>" />
<?php
	if (isset($_keywords) && !empty($_keywords) && is_string($_keywords)) {
?>
		<meta name="keywords" content="<?php echo $_keywords?>" />
<?php
	}
?>