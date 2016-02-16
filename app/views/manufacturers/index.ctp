<h1>Výrobci</h1>
<p>U nás nakoupíte zboží těchto výrobců</p>
<div class="three-columns">
<?php
$first_letter = null;
foreach ($manufacturers as $manufacturer) {
	if (!$first_letter) {
		// inicializace
		$first_letter = $manufacturer['Manufacturer']['name'][0];
?>
	<h2><?php echo $first_letter?></h2>
<?php }
	// prvni pismeno dalsiho vyrobce je jine, nez u predchoziho
	if ($first_letter != $manufacturer['Manufacturer']['name'][0]) {
		$first_letter = $manufacturer['Manufacturer']['name'][0];
?>
	<h2><?php echo $first_letter?></h2>
<?php }
	echo $this->Html->link($manufacturer['Manufacturer']['name'], '/' . $manufacturer['Manufacturer']['url']) . ' (' . $manufacturer['Manufacturer']['products_count'] . ')<br/>';
}?>
</div>