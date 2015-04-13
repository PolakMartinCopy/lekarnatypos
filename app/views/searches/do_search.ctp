<h2><span>Výsledky vyhledávání</span></h2>
<?php if (!empty($products) ){?>
<?php 	echo $this->element(REDESIGN_PATH . $listing_style); ?>
<?php } else { ?>
	<div id="mainContentWrapper">
		<p>Zadanému dotazu neodpovídají žádné produkty v našem obchodě.</p>
	</div>
<? } ?>