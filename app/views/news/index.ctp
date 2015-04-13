<div class="news">
	<h2><span>Aktuality</span></h2>
	<?php if (empty($news)) { ?>
	<p><em>Nemáme pro Vás žádné aktuality.</em></p>
	<?php } else {
			foreach ($news as $actuality) { ?>
	<div class="actuality">
		<h3><?php echo $actuality['News']['title']?></h3>
		<p><?php echo $actuality['News']['first_sentence']?></p>
		<span class="date"><?php echo $actuality['News']['czech_date']?></span>
	</div>
	<?php	}
	} ?>
</div>