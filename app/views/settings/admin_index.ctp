<h2>Nastavení obchodu</h2>
<p>Následující údaje měňte pouze pokud velmi dobře víte, co děláte! V opačném případě kontaktujte za účelem úprav administrátora. Neodborným zásahem můžete způsobnit nefunkčnost webu!</p>
<?php echo $this->Form->create('Setting')?>
<table class="tabulkaedit">
<?php foreach ($this->data['Setting'] as $index => $item) { ?>
	<tr>
		<td><?php echo $item['name']?></td>
		<td><?php
			echo $this->Form->input('Setting.' . $index . '.value', array('label' => false, 'rows' => 1, 'cols' => 100));
			echo $this->Form->hidden('Setting.' . $index . '.name');
			echo $this->Form->hidden('Setting.' . $index . '.id');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>

<script type="text/javascript">
	$(document).ready(function() {
		$('#SettingAdminIndexForm').submit(function(e) {
			if (!confirm('Opravdu chcete hodnoty změnit?')) {
				return false;
			}
		});
	});
</script>