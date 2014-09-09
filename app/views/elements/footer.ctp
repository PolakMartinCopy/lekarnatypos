<div class="footer">
    <div class="container clearfix">
        <div class="col-3">
            <div class="footer-headline">
                Informace o nákupu
            </div>
            <ul class="links">
                <li><a href="/jak-nakupovat">Jak nakupovat</a></li>
                <li><a href="/cenik-dopravy">Způsoby a ceny dopravy</a></li>
                <li><a href="/osobni-odber">Osobní odběr</a></li>
                <li><a href="/obchodni-podminky">Obchodní podmínky</a></li>
                <li><a href="/o-provozovateli">Informace o provozovateli</a></li>
                <li><a href="/prodejna">Naše prodejna</a></li>
                <li><a href="/kontakty">Jak nás kontaktovat</a></li>
            </ul>
        </div>
        <div class="col-3">
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
                    <input type="hidden" name="data[Recommendation][request_uri]" id="RecommendationRequestUri" value="<?php echo $_SERVER['REQUEST_URI']?>"/>
                    <span class="input-group-btn">
                        <input class="btn btn-primary" type="submit" value="ODESLAT" />
                    </span>
                </div>
            </form>
        </div>
        <div class="col-3">
            <div class="footer-headline">
                Provozovatel
            </div>
            <p>
                <strong>Meavita s.r.o.</strong><br />
                IČ: 29248400<br />
                DIČ: CZ29248400<br />
                tel.: 778 437 811
            </p>
        </div>
        <div class="col-3">
            <div class="footer-headline" id="subscription">
                Novinky e-mailem
            </div>
            <p>
                Odebírejte naše novinky e-mailem:
            </p>
		<?php echo $this->Form->create('Subscriber', array('url' => array('controller' => 'subscribers', 'action' => 'add'), 'encoding' => false, 'class' => 'form-inline')); ?>
		<div class="input-group">
		
		</div>
            <form id="SubscriberViewForm" method="post" action="/subscribers/add" class="form-inline">
                <input type="hidden" name="_method" value="POST" />
                <div class="input-group">
                    <input name="data[Subscriber][email]" type="text" class="form-control" placeholder="emailová adresa" maxlength="150" id="SubscriberEmail" />
                    <input type="hidden" name="data[Subscriber][request_uri]" value="/" id="SubscriberRequestUri" />
                    <span class="input-group-btn">
                        <input class="btn btn-primary" type="submit" value="ODEBÍRAT" />
                    </span>
                </div>
                <?php echo $this->Form->error('Subscriber.email');?>
            </form>
        </div>
    </div>
</div>