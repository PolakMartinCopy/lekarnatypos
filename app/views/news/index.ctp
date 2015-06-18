<div class="news">
	<h1>Aktuality</span></h1>
	<?php if (empty($news)) { ?>
	<p><em>Nemáme pro Vás žádné aktuality.</em></p>
	<?php } else {
			foreach ($news as $actuality) {
				$image = $image_path . '/' . $actuality['News']['image'];
				$image_tag = '<img src="/' . $image . '" alt="' . $actuality['News']['title'] . '" style="margin:0 5px 0 0" align="left"/>';
	?>
	<div class="actuality">
		<h2><?php echo $this->Html->link($actuality['News']['title'], array('controller' => 'news', 'action' => 'view', $actuality['News']['id']))?></h2>
		<?php echo $this->Html->link($image_tag, array('controller' => 'news', 'action' => 'view', $actuality['News']['id']), array('escape' => false))?><?php echo $actuality['News']['first_paragraph']?>
		<div style="clear:both"></div>
		<div class="date" style="float:right"><?php echo $actuality['News']['czech_date']?></div>
	</div>
	<?php	}
	} ?>
</div>