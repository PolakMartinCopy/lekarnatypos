<script>
	$(function() {
		var selectedTab = 0;
		var pseudoRootCategoryId = false;
<?php 	if (isset($pseudo_root_category_id)) {?>
		pseudoRootCategoryId = parseInt(<?php echo $pseudo_root_category_id?>);
<?php 	} ?>
		if (pseudoRootCategoryId) {
			// potrebuju pole IDcek tabu a z nej pak zjistit pozici daneho indexu
			var tabIndexes = $('#tabs .tab-header');
			$.each(tabIndexes, function(index, value) {
				tabId = $(value).attr('id');
				tabId = tabId.replace('tabs-', '');
				// dodelat, ze podle aktualne otevrene hlavni kategorie se vykresli jeji tab
				if (tabId == pseudoRootCategoryId) {
					selectedTab = index;
				}
			});
		}
		$("#tabs").tabs({
			selected: selectedTab
		});
	});
</script>

<h2>Hlavní kategorie:</h2>
<?php if (!empty($main_categories)) { ?>
<div id="tabs">
	<ul>
<?php 	foreach ($main_categories as $main_category) { ?>
		<li><a href="#tabs-<?php echo $main_category['Category']['id']?>"><?php echo $main_category['Category']['name']?></a></li>
<?php 	} ?>
	</ul>
<?php 	foreach ($main_categories as $main_category) { ?>
	<div id="tabs-<?php echo $main_category['Category']['id']?>" class="tab-header">
	<?php echo $this->Html->link('Přidat hlavní kategorii', array('controller' => 'categories', 'action' => 'add', $main_category['Category']['id']), array('style' => 'color:blue;text-decoration:underline'))?>
	<br /><br />
		<table class="tabulka">
			<tr>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>ID</th>
				<th>Název</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
<?php
			$prefix = '';
			draw_table($this, $main_category['categories'], $prefix);
?>
		</table>
	</div>
<?php } ?>
</div>
<?php } else { ?>
<p><em>V systému nejsou žádné kategorie.</em></p>
<?php } ?>


<?php 
function draw_table($object, $categories, $prefix) {
	foreach ($categories as $category) { ?>
	<tr>
		<td><?php
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $object->Html->link($icon, array('controller' => 'categories', 'action' => 'edit', $category['Category']['id']), array('escape' => false, 'title' => 'Upravit kategorii'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			$action = array('controller' => 'categories', 'action' => 'delete', $category['Category']['id']);
			if (!$category['Category']['active']) {
				$action['action'] = 'delete_from_db';
			}
			echo $object->Html->link($icon, $action, array('escape' => false, 'title' => 'Smazat kategorii'));
		?></td>
		<td><?php echo $category['Category']['id'] ?></td>
		<td><?php
			$style = null;
			if (!$category['Category']['active']) {
				$style = 'color:grey;font-style:italic';
			}
			echo $prefix . $object->Html->link($category['Category']['name'], array('controller' => 'categories', 'action' => 'edit', $category['Category']['id']), array('style' => $style, 'title' => 'Upravit kategorii'))
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt="" />';
			echo $object->Html->link($icon, array('controller' => 'categories', 'action' => 'add', $category['Category']['id']), array('escape' => false, 'title' => 'Přidat podkategorii'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/vcard.png" alt=""/>';
			echo $object->Html->link($icon, array('controller' => 'products', 'action' => 'index', 'category_id' => $category['Category']['id']), array('escape' => false, 'title' => 'Seznam produktů v kategorii'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/book.png" alt=""/>';
			echo $object->Html->link($icon, array('controller' => 'categories', 'action' => 'movenode', $category['Category']['id']), array('escape' => false, 'title' => 'Přesunout kategorii'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/up.png" alt="" />';
			echo $object->Html->link($icon, array('controller' => 'categories', 'action' => 'moveup', $category['Category']['id']), array('escape' => false, 'title' => 'Posunout nahorů'));
		?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/down.png" alt="" />';
			echo $object->Html->link($icon, array('controller' => 'categories', 'action' => 'movedown', $category['Category']['id']), array('escape' => false, 'title' => 'Posunout dolů'));
		?></td>
	</tr>
<?php	if (!empty($category['children'])) {
			draw_table($object, $category['children'], $prefix . '&nbsp;-&nbsp;');
		}
	}
} ?>
<div class='prazdny'></div>
<table class='legenda'>
	<tr>
		<th align='left'><strong>LEGENDA:</strong></th>
	</tr>
	<tr>
		<td>
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/pencil.png' width='16' height='16' /> ... upravit kategorii<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/delete.png' width='16' height='16' /> ... smazat kategorii<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/add.png' width='16' height='16' /> ... přidat podkategorii<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/vcard.png' width='16' height='16' /> ... zobrazit produkty kategorie<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/book.png' width='16' height='16' /> ... přesunout kategorii do jiného uzlu<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/up.png' width='16' height='16' /> ... změnit pořadí v rámci kategorie nahoru<br />
			<img src='/images/<?php echo REDESIGN_PATH ?>icons/down.png' width='16' height='16' /> ... změnit pořadí v rámci kategorie dolů<br />
		</td>
	</tr>
</table>
<div class="prazdny"></div>