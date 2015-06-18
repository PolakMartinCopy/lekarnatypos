<h2><span>Výsledky vyhledávání</span></h2>
<?php if (empty($products) && empty($filter_manufacturers)){?>
	<div id="mainContentWrapper">
		<p>Zadanému dotazu neodpovídají žádné produkty v našem obchodě.</p>
	</div>
<?php } else {
	 	echo $this->element(REDESIGN_PATH . $listing_style);
} ?>