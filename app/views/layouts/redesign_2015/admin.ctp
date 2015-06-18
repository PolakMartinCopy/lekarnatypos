<html>
	<head>
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
		<title>Administrace</title>
		
		<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
		
		<link rel="stylesheet" type="text/css" href="/css/admin.css"/>
		<link rel="stylesheet" type="text/css" href="/css/<?php echo REDESIGN_PATH . 'admin/default.css'?>"/>
		<link rel="stylesheet" type="text/css" href="/plugins/jtip/jtip.css"/>
		<link rel="stylesheet" type="text/css" href="/plugins//superfish/superfish.css"/>
		<link rel="stylesheet" type="text/css" href="/plugins/jquery-ui/css/smoothness/jquery-ui.css"/>
	
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH ?>admin/jquery.js"></script>
		<script type="text/javascript" src="/plugins/jquery-ui/jquery-ui.js"></script>
		<script type="text/javascript" src="/js/<?php echo REDESIGN_PATH ?>admin/jquery.ui.datepicker-cs.js"></script>
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
		
		<?php 
		if (!isset($tinyMceElement)) {
			$tinyMceElement = 'ProductDescription';
		}
		?>
		<script type="text/javascript" src="/js/tinymce_4/tinymce.min.js"></script>
		<script type="text/javascript">
			function fileBrowserCallBack (field_name, url, type, win) {
				tinyMCE.activeEditor.windowManager.open({
					file : '/admin/tiny_images/index',
					title : 'Prohlížeč',
					width : 800,  // Your dimensions may differ - toy around with them!
					height : 600,
					resizable : "yes",
					inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
					close_previous : "no"
				}, {
					window : win,
					input : field_name,
					oninsert : function(url){
						 win.document.getElementById(field_name).value = url;
					}
				});
				return false;
			}
			
			tinymce.init({
				content_css: "/css/<?php echo REDESIGN_PATH?>style.css",
			   	selector: "#<?php echo $tinyMceElement ?>",
			   	language : "cs",
			   	plugins: [
			        "advlist autolink lists link image charmap print preview anchor",
			       	"searchreplace visualblocks code fullscreen",
			       	"insertdatetime media table contextmenu paste"
			   	],
			   	file_browser_callback: fileBrowserCallBack,
			   	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
			   	relative_urls: false
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
