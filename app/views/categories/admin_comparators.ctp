<h1>Přiřazení kategorie do taxonomií srovnánačů</h1>

<?php echo $this->Html->link('ZPĚT NA SEZNAM KATEGORIÍ', array('controller' => 'categories', 'action' => 'index'))?>
<br /><br />
<h2><?php echo $category['Category']['name']?></h2>

<div class='prazdny'></div>
<?php echo $form->create('Category', array('url' => array('controller' => 'categories', 'action' => 'comparators', $id)));?>
<table class="tabulkaedit">
<?php 
	foreach ($comparators as $comparator) {
		$path = '';
		foreach ($categories_comparators as $cp) {
			if ($cp['CategoriesComparator']['comparator_id'] == $comparator['Comparator']['id']) {
				$path = $cp['CategoriesComparator']['path'];
			}
		}
	?>
	<tr valign="top">
		<td>Srovnávač:</td>
		<td><?php echo $comparator['Comparator']['name']?></td>
		<td>Cesta:</td>
		<td><?php
			echo $this->Form->input('CategoriesComparator.' . $comparator['Comparator']['id'] . '.path', array('label' => false, 'value' => $path, 'type' => 'text', 'size' => 130));
			echo $this->Form->hidden('CategoriesComparator.' . $comparator['Comparator']['id'] . '.category_id', array('value' => $category['Category']['id']));
			echo $this->Form->hidden('CategoriesComparator.' . $comparator['Comparator']['id'] . '.comparator_id', array('value' => $comparator['Comparator']['id']));
		?></td>
	</tr>
<?php } ?>
</table>
<?php
	echo $this->Form->submit('VLOŽIT');
	echo $this->Form->end();
?>
<div class='prazdny'></div>