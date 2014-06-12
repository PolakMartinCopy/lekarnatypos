<h1>Objednávka byla dokončena</h1>
<div class="mainContentWrapper">
	<p>Vaše objednávka byla nyní <strong>přijata do našeho systému</strong>, již brzy Vás budeme informovat o stavu Vaší objednávky.</p>
<?
	if (!$session->check('Customer.id')){
	// jedna se o neprihlaseneho zakaznika
?>
	<p>Pro Vaše větší pohodlí jsme pro Vás vytvořili zákaznický účet. Pokud jste uvedl(a) v objednávce Vaši emailovou adresu,
	byly Vám odeslány tyto přihlašovací údaje:<br /><br />
	<strong>LOGIN: </strong><?=$order['Customer']['login']?><br />
	<strong>HESLO: </strong><?=$order['Customer']['password']?></p>
	<p>Doporučujeme Vám, <strong>poznamenat si tyto údaje</strong>, abyste mohl(a) plně využívat výhod Vašeho zákaznického účtu. Pro komunikaci
	emailem a telefonicky Vám stačí poznamenat si login.</p>
<?
	} else {
?>
	<p>Pomocí Vašeho <a href="/customers">uživatelského účtu</a> můžete kontrolovat stav Vaší objednávky.</p>
<?
	}
?>
	<h3>Děkujeme za Vaši důvěru.</h3>
</div>

<!-- MERICI KOD ZBOZI.CZ -->
<iframe src="http://www.zbozi.cz/action/106872/conversion?chsum=GVklV4sGGa3C-NTZuv2b9w==&price=<?php echo $order['Order']['id']?>&uniqueId=<?php echo $order['Order']['orderfinaltotal']?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="position:absolute; top:-3000px; left:-3000px; width:1px; height:1px; overflow:hidden;"></iframe>


<script type="text/javascript">
	<?php echo $jscript_code ?>
</script>