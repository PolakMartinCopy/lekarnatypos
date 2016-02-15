<h2>Editace výrobce</h2>
<div class="option">
<?php echo $form->create('Manufacturer');?>
	<fieldset>
 		<legend>Výrobce</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
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
	<?=$form->hidden('id')?>
	<?=$form->end('Upravit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na seznam výrobců', true), array('action'=>'index'));?></li>
	</ul>
</div>