<h1>Vložení nové šablony</h1>
<ul>
	<li><?php echo $this->Html->link('Zpět na seznam šablon', array('controller' => 'mail_templates', 'action' => 'index'))?></li>
</ul>
<?=$form->create('MailTemplate');?>
	<fieldset>
 		<legend>Šablona</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Předmět emailu</th>
				<td><?=$form->input('MailTemplate.subject', array('label' => false, 'size' => 90))?></td>
			</tr>
			<tr>
				<th>Obsah emailu</th>
				<td><?=$form->input('MailTemplate.content', array('label' => false, 'cols' => 68, 'rows' => 15))?></td>
			</tr>
		</table>
	</fieldset>
<?=$form->end('Uložit')?>