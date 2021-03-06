<div class="footer">
    <div class="container clearfix">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 hidden-xs">
            <div class="footer-headline">
                Informace pro zákazníka
            </div>
            <ul class="links">
                <li><a href="/jak-nakupovat">Jak nakupovat</a></li>
                <li><a href="/cenik-dopravy">Způsoby a ceny dopravy</a></li>
                <li><a href="/osobni-odber">Osobní odběr</a></li>
                <li><a href="/obchodni-podminky">Obchodní podmínky</a></li>
            </ul>
			<a href="/statni-ustav-pro-kontrolu-leciv">
   				<img src="/images/<?php echo REDESIGN_PATH?>sukl-logo.jpg" alt="Státní ústav pro kontrolu léčiv" title="Státní ústav pro kontrolu léčiv" width="132px" height="43px"  style="margin:10px 10px 0 0"/> 
			</a>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 hidden-xs">
            <div class="footer-headline">
                Vše o Lékárně Typos
            </div>
            <ul class="links">
                <li><a href="/o-provozovateli">Informace o provozovateli</a></li>
                <li><a href="/prodejna">Naše prodejna</a></li>
                <li><a href="/kontakty">Jak nás kontaktovat</a></li>
                <li style="list-style-type:none">
            		<div id="showHeurekaBadgeHere-2"></div><script type="text/javascript">
//<![CDATA[
var _hwq = _hwq || [];
    _hwq.push(['setKey', 'B07B416DBB1B526966038D02638F5E20']);_hwq.push(['showWidget', '2', '43810', 'Lékárna Typos', 'lekarnatypos-cz']);(function() {
    var ho = document.createElement('script'); ho.type = 'text/javascript'; ho.async = true;
    ho.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.heureka.cz/direct/i/gjs.php?n=wdgt&sak=B07B416DBB1B526966038D02638F5E20';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ho, s);
})();
//]]>
</script>
				</li>
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 hidden-xs">
            <div class="footer-headline">
                Doporučte známému
            </div>
            <p>
                Zadejte emailovou adresu,<br />
                kam máme poslat odkaz:
            </p>

            <form id="RecommendationViewForm" method="post" action="/recommendations/send" class="form-inline">
                <input type="hidden" name="_method" value="POST" />
                <div class="input-group">
                    <input name="data[Recommendation][target_email]" type="text" class="form-control" placeholder="emailová adresa" id="RecommendationTargetEmail" />
                    <input type="hidden" name="data[Recommendation][request_uri]" id="RecommendationRequestUri" value="/prodejna" />
                    <span class="input-group-btn">
                        <input class="btn btn-warning" type="submit" value="ODESLAT" />
                    </span>
                </div>
            </form>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <div class="footer-headline">
                Provozovatel
            </div>
            <p>
                <strong>Meavita s.r.o.</strong><br />
                IČ: 29248400<br />
                DIČ: CZ29248400<br />
                tel.: 778 437 811
            </p>
<?php if ($_SERVER['REQUEST_URI'] == '/') { ?>
			<a href="https://www.sukl.cz/modules/apotheke/verify.php?kod=72995264000&verify_eu" target="_blank">
		    	<img src="/images/<?php echo REDESIGN_PATH?>sukl-overeni.png" alt="Ověřit lékárnu" title="Ověřit lékárnu" width="132px" height="110px"  style="margin:10px 10px 0 0"/> 
	    	</a>
<?php } ?>
	    </div>
    </div>
</div>