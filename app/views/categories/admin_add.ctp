<h2>Vložit novou podkategorii</h2>
<?php echo $form->create('Category', array('type' => 'file', 'url' => $this->passedArgs));?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Název kategorie</td>
		<td>
			<?=$form->text('name', array('size' => 60))?>
			<?=$form->error('Category.name', 'Název kategorie musí být vyplněn.');?>
		</td>
	</tr>
	<tr>
		<td>Nadpis</td>
		<td><?php echo $form->input('Category.heading', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<td>Obrázek (150px &times; 150px)</td>
		<td><?php echo $this->Form->input('Category.image', array('label' => false, 'type' => 'file'))?></td>
	</tr>
	<tr>
		<td>Breadcrumb</td>
		<td><?php echo $form->input('Category.breadcrumb', array('label' => false, 'size' => 60))?></td>
	</tr>
	<?php if (in_array($parent_id, $pseudo_root_categories_ids)) { ?>
	<tr>
		<td>Homepage class</td>
		<td><?php echo $this->Form->input('Category.homepage_class', array('label' => false, 'size' => 20))?></td>
	</tr>
	<?php } ?>
	<tr>
		<td>Krátký popis</td>
		<td><?php echo $form->input('Category.short_description', array('label' => false, 'cols' => '70'))?></td>
	</tr>
	<tr>
		<td>Popis</td>
		<td><?php echo $form->input('Category.content', array('label' => false, 'style' => 'width:600px;height:350px;'))?></td>
	</tr>
	<tr>
		<td>Veřejná</td>
		<td><?php echo $this->Form->input('Category.public', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Aktivní</td>
		<td><?php echo $this->Form->input('Category.active', array('label' => false))?></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			----------- níže uvedené nevyplňujte -----------
		</td>
	</tr>
	<tr>
		<td>Titulek</td>
		<td><?=$form->text('title', array('size' => 60))?></td>
	</tr>
	<tr>
		<td>Popisek</td>
		<td><?=$form->text('description', array('size' => 60))?></td>
	</tr>
	<tr>
		<td>URL</td>
		<td><?=$form->text('url', array('size' => 60))?></td>
	</tr>
</table>
<?php echo $form->hidden('Category.parent_id', array('value' => $parent_id)); ?>
<?php echo $form->end('Vložit');?>