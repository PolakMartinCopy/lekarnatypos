<div class="mainContentWrapper">
	<h2><span><?php echo $page_heading?></span></h2>
	<p>Vaše objednávka byla nyní <strong>přijata do našeho systému</strong>, již brzy Vás budeme informovat o stavu Vaší objednávky.</p>
<?	// jedna se o neprihlaseneho zakaznika, pro ktereho byl vytvoren ucet
	if (!empty($order['Customer']['login']) && !empty($order['Customer']['password'])) { ?>
	<p>Pro Vaše větší pohodlí jsme pro Vás vytvořili zákaznický účet. Pokud jste uvedl(a) v objednávce Vaši emailovou adresu,
	byly Vám odeslány tyto přihlašovací údaje:<br /><br />
	<strong>LOGIN: </strong><?=$order['Customer']['login']?><br />
	<strong>HESLO: </strong><?=$order['Customer']['password']?></p>
	<p>Doporučujeme Vám, <strong>poznamenat si tyto údaje</strong>, abyste mohl(a) plně využívat výhod Vašeho zákaznického účtu. Pro komunikaci
	emailem a telefonicky Vám stačí poznamenat si login.</p>
<? } else { ?>
	<p>Pomocí Vašeho <a href="/customers">uživatelského účtu</a> můžete kontrolovat stav Vaší objednávky.</p>
<? } ?>
	<h3>Děkujeme za Vaši důvěru.</h3>
</div>

<script type="text/javascript">
	<?php echo $jscript_code ?>
</script>
	
<!-- Merici kod pro Heureka.cz -->
<script type="text/javascript">
var _hrq = _hrq || [];
    _hrq.push(['setKey', 'B07B416DBB1B526966038D02638F5E20']);
    _hrq.push(['setOrderId', '<?php echo $order['Order']['id'] ?>']);

<?php foreach ($order['OrderedProduct'] as $op) {?>
    _hrq.push(['addProduct', '<?php echo $op['Product']['name'] ?>', '<?php echo $op['product_price_with_dph'] ?>', '<?php echo $op['product_quantity'] ?>']);
<?php } ?>
    _hrq.push(['trackOrder']);

(function() {
    var ho = document.createElement('script'); ho.type = 'text/javascript'; ho.async = true;
    ho.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.heureka.cz/direct/js/cache/1-roi-async.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ho, s);
})();
</script>

<?php if (false) { ?>
<!--
MERICI KOD ZBOZI.CZ
<iframe src="http://www.zbozi.cz/action/106872/conversion?chsum=GVklV4sGGa3C-NTZuv2b9w==&price=<?php echo $order['Order']['id']?>&uniqueId=<?php echo $order['Order']['orderfinaltotal']?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="position:absolute; top:-3000px; left:-3000px; width:1px; height:1px; overflow:hidden;"></iframe>
 -->
<?php } ?>