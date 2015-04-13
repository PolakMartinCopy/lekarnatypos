<h2>Editace výrobce</h2>
<div class="option">
<?php echo $form->create('Manufacturer');?>
	<fieldset>
 		<legend>Výrobce</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název výrobce
				</th>
				<td>
					<?=$form->text('name')?>
					<?=$form->error('name')?>
				</td>
			</tr>
			<tr>
				<th>
					adresa www stránek
				</th>
				<td>
					<?=$form->text('www_address')?><br />
					<?=$form->error('www_address')?>
					<span class="formNote">např. http://www.mte.cz/</span>
				</td>
			</tr>
			<tr>
				<th>Popis</th>
				<td><?php echo $this->Form->input('Manufacturer.text', array('label' => false, 'rows' => 15))?></td>
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