<h2>Nový výrobce</h2>
<?php echo $this->Form->create('Manufacturer');?>
<fieldset>
	<legend>Výrobce</legend>
	<table class="tabulkaedit">
		<tr class="nutne">
			<td>Název</td>
			<td><?php echo $this->Form->input('Manufacturer.name', array('label' => false, 'size' => 60))?></td>
		</tr>
		<tr>
			<th>Nadpis</th>
			<td><?php echo $this->Form->input('Manufacturer.heading', array('label' => false, 'size' => 60))?></td>
		</tr>
		<tr>
			<th>META titulek</th>
			<td><?php echo $this->Form->input('Manufacturer.title', array('label' => false, 'size' => 60))?></td>
		</tr>
		<tr>
			<th>META popis</th>
			<td><?php echo $this->Form->input('Manufacturer.description', array('label' => false, 'size' => 100))?></td>
		</tr>
		<tr>
			<th>Text</th>
			<td><?php echo $this->Form->input('Manufacturer.content', array('label' => false, 'rows' => 15))?></td>
		</tr>
		<tr>
			<th>Do menu?</th>
			<td><?php echo $this->Form->input('Manufacturer.is_sidebar', array('label' => false))?></td>
		</tr>
	</table>
</fieldset>
<?php echo $this->Form->hidden('Manufacturer.active', array('value' => true))?>
<?=$form->end('Vložit');?>