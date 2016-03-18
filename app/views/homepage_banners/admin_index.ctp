<h1>Banner na hlavní stránce</h1>
<?php echo $this->Form->create('HomepageBanner', array('type' => 'file'))?>
<table class="tabulka">
	<tr>
		<th width="5%">Aktivní</th>
		<th width="55%">Obrázek</th>
		<th>URL</th>
	</tr>
	<tr>
		<td><?php echo $this->Form->input('HomepageBanner.active', array('label' => false, 'type' => 'checkbox'))?></td>
		<td>
			<?php if ($image) { ?>
			<div style="float:left">
				<a href="<?php echo $url?>" target="_blank">
					<img src="/<?php echo $image?>" width="334px" />
				</a>
			</div>
			<?php } ?>
			<?php echo $this->Form->input('HomepageBanner.image', array('label' => false, 'type' => 'file'))?>
		</td>
		<td><?php echo $this->Form->input('HomepageBanner.url', array('label' => false, 'type' => 'text', 'rows' => 1, 'cols' => 70))?></td>
	</tr>
</table>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>