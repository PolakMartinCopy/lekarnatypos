<h1>Emaily pro novinky</h1>
<?php echo $this->Form->create('Customer', array('type' => 'file'))?>
<table>
	<tr class="nutne">
		<td>Soubor s blacklistem</td>
		<td><?php echo $this->Form->input('newsletter_file', array('label' => false, 'type' => 'file'))?></td>
		<td><?php echo $this->Form->submit('Odeslat')?></td>
	</tr>
</table>
<?php echo $this->Form->end()?>

<p><?php echo count($emails)?> záznamů.</p>
<p><?php
foreach ($emails as $email) {
	echo $email['Customer']['email'] . '<br/>';
} ?></p>