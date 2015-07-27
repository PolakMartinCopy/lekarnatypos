<h1>Upravit šablonu</h1>
<ul>
	<li><?php echo $this->Html->link('Zpět na seznam šablon', array('controller' => 's_m_s_templates', 'action' => 'index'))?></li>
</ul>
<?=$form->create('SMSTemplate');?>
	<fieldset>
 		<legend>Šablona</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>Obsah</th>
				<td><?=$form->input('SMSTemplate.content', array('label' => false, 'cols' => 68, 'rows' => 3))?></td>
			</tr>
		</table>
	</fieldset>
<?php echo $this->Form->hidden('SMSTemplate.id')?>
<?=$form->end('Uložit')?>
<div class='prazdny'></div>