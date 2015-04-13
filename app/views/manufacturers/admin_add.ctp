<h2>Nový výrobce</h2>
<?php echo $this->Form->create('Manufacturer');?>
<fieldset>
	<legend>Výrobce</legend>
	<table class="tabulkaedit">
		<tr class="nutne">
			<td>Název výrobce</td>
			<td><?php echo $this->Form->input('Manufacturer.name', array('label' => false))?></td>
		</tr>
		<tr>
			<td>Adresa www stránek</td>
			<td>
				<?php echo $this->Form->input('Manufacturer.www_address', array('label' => false))?>
				<span class="formNote">např. http://www.mte.cz/</span>
			</td>
		</tr>
		<tr>
			<th>Popis</th>
			<td><?php echo $this->Form->input('Manufacturer.text', array('label' => false, 'rows' => 15))?></td>
		</tr>
	</table>
</fieldset>
<?=$form->end('Vložit');?>