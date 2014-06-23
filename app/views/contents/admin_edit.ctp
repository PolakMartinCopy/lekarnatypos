<?
	echo $form->Create('Content');
?>
	<fieldset>
 		<legend>Obsah pro: <strong><?=$this->data['Content']['title']?></strong></legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Cesta obsahu:
				</th>
				<td>
					<?php echo $form->input('Content.path', array('size' => '60', 'label' => false)); ?>
				</td>
			</tr>
			<tr>
				<th>
					Titulek obsahu
				</th>
				<td>
					<?=$form->text('Content.title', array('size' => '60'))?>
				</td>
			</tr>
			<tr>
				<th>
					Popisek obsahu
				</th>
				<td>
					<?=$form->textarea('Content.description', array('rows' => '3', 'cols' => '45'))?>
				</td>
			</tr>
			<tr>
				<th>Textový obsah</th>
				<td>
					<?=$form->textarea('Content.content', array('cols' => '60', 'rows' => '40', 'class' => 'ContentContent'))?>
				</td>
			</tr>
		</table>
	</fieldset>
<?
	echo $form->Submit('UPRAVIT');
	echo $form->end();
?>