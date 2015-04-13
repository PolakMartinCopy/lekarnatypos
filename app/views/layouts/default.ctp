<?
/**
 * ZAKLADNI TEMPLATE PRO VSECHNY OBSAHOVE STRANKY
*/

if ( !isset( $opened_category_id ) ){
	$opened_category_id = ROOT_CATEGORY_ID;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="cs" />
	<title>Administrace</title>
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
	<?
		// prihodim si javascriptovy soubor s funkcemi
	if ( isset($javascript) ){
		echo $javascript->link('functions');
	}

		// kdyz potrebuju vlozit tinyMce JavaScript
		// musim si predat $tinyMce true
		if ( isset( $tinyMce ) AND $tinyMce === true ){
			echo $javascript->link('tinymce/jscripts/tiny_mce/tiny_mce');

			if ( !isset($tinyMceElement) ){
				$tinyMceElement = 'ProductDescription';
			}
			echo '<script type="text/javascript">tinyMCE.init({
				mode : "exact",
				language : "cs-win1250",
				elements : "' . $tinyMceElement . '",
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
				extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
				external_link_list_url : "example_data/example_link_list.js",
				external_image_list_url : "example_data/example_image_list.js",
				flash_external_list_url : "example_data/example_flash_list.js"
			});</script>';
		}
		
		if (isset($script)) {
			echo $script;
		}

	// prihodim si soubor s csskem
	echo $html->css('admin');
	?>
</head>

<body>
	<table id="mainWrapper">
		<tr>
			<td valign="top" class="leftSideWrapper">
				<?
					echo $form->create('Search', array('action' => 'do'));
					echo $form->text('query');
					echo $form->submit('Hledej');
					echo $form->end();
				?>
				<a href="/admin/orders">Seznam objednávek</a><br />
				&nbsp;&nbsp;<?=$html->link('kontrola doručených objednávek', array('controller' => 'orders', 'action' => 'track'), array('style' => 'font-size:9px;')) ?><br />
				<?=$html->link('Seznam zákazníků', array('controller' => 'customers', 'action' => 'list')) ?><br />
				<?=$html->link('Dotazy / komentáře', array('controller' => 'comments', 'action' => 'index')) ?><br />
				<?=$html->link('Statistiky', array('controller' => 'statistics', 'action' => 'index'))?><br /><br />
				<?
				echo $this->element(
						'admin_categories_list',
						$this->requestAction('/categories/getSubcategoriesMenuList/' . $opened_category_id . '/0/1')
					);
				echo '<br />';
				echo $html->link(__('Zobrazit smazané produkty', true), array('controller' => 'products', 'action' => 'deleted'));
				echo '<br /><br />';
				echo $html->link(__('Atributy produktů', true), array('controller' => 'attributes', 'action' => 'index')) . '
				&raquo;<a href="/admin/attributes/add">nový</a><br />';
				echo '&nbsp;&nbsp;' . $html->link(__('Názvy atributů', true), array('controller' => 'options', 'action' => 'index')) . '
				&raquo;<a href="/admin/options/add">nový</a><br />';
				echo $html->link(__('Výrobci', true), array('controller' => 'manufacturers', 'action' => 'index')) . '
				&raquo;<a href="/admin/manufacturers/add">nový</a><br />';
				echo $html->link(__('DPH', true), array('controller' => 'tax_classes', 'action' => 'index')) . '
				&raquo;<a href="/admin/tax_classes/add">nová</a><br />';
				echo $html->link(__('Stavy objednávek', true), array('controller' => 'statuses', 'action' => 'index')) . '
				&raquo;<a href="/admin/statuses/add">nový</a><br />';
				echo $html->link(__('Způsoby dopravy', true), array('controller' => 'shippings', 'action' => 'index')) . '
				&raquo;<a href="/admin/shippings/add">nový</a><br />';
				echo $html->link('Emailové šablony', array('controller' => 'mail_templates', 'action' => 'index'));
				echo '<br />';
				echo $html->link('Přesměrování', array('controller' => 'redirects', 'action' => 'index'));
				echo '<br />';
				echo $html->link('Doporučujeme', array('controller' => 'recommended_products', 'action' => 'index'));
				echo '<br />';
				echo $html->link('Nejprodávanější', array('controller' => 'most_sold_products', 'action' => 'index'));
				echo '<br />';
				echo $html->link('Aktuality', array('controller' => 'news', 'action' => 'index'));
				echo '<br /><br />';
				echo '<a href="/admin/administrators/logout">odhlásit se</a>';

				?>
				<br /><br />
				<a href="/">ukázat LIVE</a>
			</td>
			<td valign="top">
				<div id="flash_box"><?php
					if ($session->check('Message.flash')){
						echo $session->flash();
					} ?>
				</div>
				<?php echo $content_for_layout ?>
			</td>
		</tr>
	</table>
</body>
</html>
<?php echo $this->element('sql_dump'); ?>