<h1>Přidat poznámku k objednávce č. <?php echo $order_id?></h1>
<?php echo $this->Form->create('Ordenote', array('url' => array('controller' => 'ordernotes', 'action' => 'add', 'order_id' => $order_id, 'backtrace_url' => base64_encode($backtrace_url))))?>
<table class="tabulka">
	<tr>
		<th>Text</th>
		<td><?php echo $this->Form->input('Ordernote.note', array('label' => false, 'rows' => 3, 'cols' => 70))?></td>
	</tr>
</table>
<?php 
	echo $this->Form->hidden('Ordernote.order_id', array('value' => $order_id));
	echo $this->Form->hidden('Ordernote.administrator_id', array('value' => $administrator_id));
	echo $this->Form->hidden('Ordernote.status_id', array('value' => $status_id));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>