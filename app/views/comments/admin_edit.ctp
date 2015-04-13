<h2>Detail komentáře</h2>

<?=$form->create('Comment', array('action' => 'edit')) ?>
<table class="tabulkaedit">
	<tr>
		<th>Autor</th>
		<td><?php echo $comment['Comment']['author'] . ' - ' . $comment['Comment']['email']; ?></td>
	</tr>
	<tr class="nutne">
		<th>Předmět</th>
		<td><?=$form->input('Comment.subject', array('label' => false)) ?></td>
	</tr>
	<tr class="nutne">
		<th>Obsah</th>
		<td><?=$form->input('Comment.body', array('label' => false, 'rows' => 7, 'cols' => 100)) ?></td>
	</tr>
	<tr>
		<th>K produktu</th>
		<td><?php echo $this->Html->link($comment['Product']['name'], '/' . $comment['Product']['url'], array('target' => 'blank'))?></td>
	</tr>
	<tr>
		<th>Odpověď</th>
		<td><?=$form->input('Comment.reply', array('label' => false, 'rows' => 7, 'cols' => 100)) ?></td>
	</tr>
	<tr>
		<th>Zobrazovat na webu</th>
		<td><?php echo $this->Form->input('Comment.confirmed', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Odpověď poslat emailem</th>
		<td><?php echo $this->Form->input('Comment.sent', array('label' => false, 'type' => 'checkbox', 'value' => false))?></td>
	</tr>
</table>
<?php
	echo $this->Form->hidden('Comment.administrator_id', array('value' => $this->Session->read('Administrator.id')));
	echo $this->Form->hidden('Comment.id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>
			