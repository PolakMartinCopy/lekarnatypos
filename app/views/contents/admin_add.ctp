<h1>Nová webstránka</h1>
<? echo $form->Create('Content'); ?>
<fieldset>
	<table class="tabulkaedit">
		<tr class="nutne">
			<td>Cesta</td>
			<td><?php echo $this->Form->input('Content.path', array('size' => '60', 'label' => false))?></td>
		</tr>
		<tr class="nutne">
			<td>Nadpis</td>
			<td><?php echo $this->Form->input('Content.heading', array('size' => '60', 'label' => false))?></td>
		</tr>
		<tr>
			<td>Titulek</td>
			<td><?php echo $this->Form->input('Content.title', array('size' => '60', 'label' => false))?></td>
		</tr>
		<tr>
			<td>Popisek</td>
			<td><?php echo $this->Form->input('Content.description', array('rows' => '3', 'cols' => '45', 'label' => false))?>
		</tr>
		<tr class="nutne">
			<td colspan="2">Text</td>
		</tr>
		<tr>
			<td colspan="2"><?php echo $this->Form->input('Content.content', array('cols' => '60', 'rows' => '40', 'label' => false))?></td>
		</tr>
	</table>
</fieldset>
<?
	echo $form->Submit('Uložit');
	echo $form->end();
?>
<div class="prazdny"></div>