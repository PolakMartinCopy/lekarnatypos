<html>
	<head>
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
		<title>Administrace</title>
		<link rel="stylesheet" type="text/css" href="/css/admin.css"/>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH . 'admin/default.css'?>"/>
		<link rel="stylesheet" type="text/css" href="/plugins/jtip/jtip.css"/>
		<link rel="stylesheet" type="text/css" href="/plugins//superfish/superfish.css"/>
		<link rel="stylesheet" type="text/css" href="/plugins/jquery-ui/css/smoothness/jquery-ui.css"/>
	
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH ?>admin/jquery.js"></script>
		<script type="text/javascript" src="/plugins/jquery-ui/jquery-ui.js"></script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH?>katalog.js"></script>
		<script type="text/javascript" src="/plugins/jtip/jtip.js"></script>
		<script type="text/javascript" src="/plugins//superfish/superfish.js"></script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH ?>hoverIntent.js"></script>
	
		<script type="text/javascript">
			// initialise plugins
			jQuery(function(){
				jQuery('ul.sf-menu').superfish();
			});
		</script>
		
		<script type="text/javascript" src="/js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<?php 	if (!isset($tiny_mce_elements)) {
			$tiny_mce_elements = '';
}?>
		<script type="text/javascript">tinyMCE.init({
			mode : "exact",
			language : "cs",
			width : 528,
			elements : "<?php echo $tiny_mce_elements?>",
		    entity_encoding : "raw",
		    relative_urls : false,
			theme : "advanced",
			plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu",
			theme_advanced_buttons1_add_before : "save,separator",
			theme_advanced_buttons1_add : "fontselect,fontsizeselect",
			theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
			theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
			theme_advanced_buttons3_add_before : "tablecontrols,separator",
			theme_advanced_buttons3_add : "emotions,iespell,flash,advhr,separator,print",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_path_location : "bottom",
			plugin_insertdate_dateFormat : "%Y-%m-%d",
			plugin_insertdate_timeFormat : "%H:%M:%S",
			extended_valid_elements : "a[name|href|target|title|onclick|class|rel|style],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
			external_link_list_url : "example_data/example_link_list.js",
			external_image_list_url : "example_data/example_image_list.js",
			flash_external_list_url : "example_data/example_flash_list.js"
		});
		</script>
		
	</head>

	<body>
		<?php echo $this->element(REDESIGN_PATH . 'admin/menu')?>
		<div id='admin_content'>
			<?php 
			if ($session->check('Message.flash')){
				echo $session->flash();
			}
			echo $content_for_layout;
			?>
		</div>
		<div class='prazdny'></div>
		<?php echo $this->element('sql_dump')?>
	</body>
</html>
