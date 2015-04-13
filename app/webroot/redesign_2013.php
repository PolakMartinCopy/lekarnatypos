<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="/css/redesign_2013/style.css" />
		<script charset="utf-8" src="js/redesign_2013/jquery.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/redesign_2013/fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
		<script type="text/javascript" src="js/redesign_2013/fancybox/jquery.fancybox.js"></script>
		<link rel="stylesheet" type="text/css" href="js/redesign_2013/fancybox/jquery.fancybox.css" media="screen" />
		<script charset="utf-8" src="js/redesign_2013/jquery.easing.js" type="text/javascript"></script>
		<script charset="utf-8" src="js/redesign_2013/jquery.slidorion.js" type="text/javascript"></script>
		<script charset="utf-8" src="js/redesign_2013/main.js" type="text/javascript"></script>
		<title></title>
	</head>
<body>

<div id="body">
	<div id="header">
		<h1><a href="#">SNV - Sport Nutrition Vávra<span></span></a></h1>

		<ul class="accordion">
			<li class="login active"><a href="#login">Přihlášení</a></li>
			<li class="basket"><a href="#basket">Nákupní košík</a></li>
			<li class="info"><a href="http://localhost">Vše o nákupu</a></li>
		</ul>
		<div id="login">
			<form method="post">
				<input type="text" name="name" placeholder="Přihlašovací jméno" />
				<input type="password" name="pass" placeholder="Heslo" />
				<button name="login">OK</button>
			</form>
		</div>
		<div id="basket"></div>

		<ul class="menu">
			<li><a href="#">Loreum ipsum</a></li>
			<li><a href="#">Kuktur set</a></li>
			<li><a href="#">Mulset dell</a></li>
			<li><a href="#">Julternester</a></li>
			<li><a href="#">Tulterw</a></li>
			<li class="phone">800 458 458</li>
			<li class="mail"><a href="mailtp:info@snv.cz">info@snv.cz</a></li>
		</ul>
	</div>
	<div id="sidebox">
		<h2><b>Výhody</b> našeho eshopu</h2>
		<ul>
			<li><b class="red">Denní expedice</b> zásilek</li>
			<li>Zboží <b>skladem</b></li>
			<li><b class="red">Garance</b> nejnižších cen</li>
			<li><b>Vrácení zboží</b> do 14 dnů</li>
		</ul>
	</div>
	<div id="search">
		<a href="#">Lorem ispum</a>
		<a href="#">Kuldere mutes</a>
		<form method="post">
			<div class="pair">
				Dle hledaných slov:
				<input type="text" name="search" placeholder="Zde zadejte hledaný výraz" />
				<button name="send"> </button>
			</div>
		</form>
	</div>

	<hr class="cleaner" />

	<div id="sidebar">
		<h2>Menu</h2>
		<ul id="menu">
			<li><a href="#"><span></span>Test</a></li>
			<li><a href="#"><span></span>Test</a></li>
			<li class="open"><span></span><a href="#">Test</a>
				<ul>
					<li><a href="#">Test</a></li>
					<li><a href="#">Test</a></li>
					<li><a href="#">Test</a></li>
					<li class="open"><span></span><a href="#">Test</a>
						<ul>
							<li><a href="#">Test</a></li>
							<li><a href="#">Test</a></li>
							<li class="open"><a href="#">Test</a></li>
							<li><a href="#">Test</a></li>
							<li><a href="#">Test</a></li>
							<li><a href="#">Test</a></li>
							<li><a href="#">Test</a></li>
						</ul>
					</li>
					<li><a href="#">Test</a></li>
					<li><a href="#">Test</a></li>
					<li><a href="#">Test</a></li>
				</ul>
			</li>
			<li><a href="#"><span></span>Test</a></li>
			<li><a href="#"><span></span>Test</a></li>
			<li><a href="#"><span></span>Test</a></li>
			<li><a href="#"><span></span>Test</a></li>
		</ul>

		<h3 class="star">Ocenění</h3>
		<img src="images/heureka.jpg" alt="Heureka ShopRoku 2012 Finalista"/>

		<h3 class="facebook">Facebook</h3>
	</div>

	<div id="main">
		<div id="slides" class="slidorion">
			<div class="accordion">
				<div class="header"><div style="padding-top: 10px">Svalus preporus <strong>250 Kč</strong></div></div>
				<div class="content"></div>
				<div class="header"><div><b>SUPER FAT BURNER</b><i>Super cena 50%</i></div></div>
				<div class="content"></div>
				<div class="header red"><div style="padding-top: 10px"><b>IOREM ISUM 25. 12. 2014</b></div></div>
				<div class="content"></div>
				<div class="header"><div style="padding-top: 10px">Svalus preporus <strong>250 Kč</strong></div></div>
				<div class="content"></div>
			</div>
			<div class="slider">
				<div class="slide"><a href="#"><img src="banner.jpg" alt=""></a></div>
				<div class="slide"><a href="#"><img src="banner.jpg" alt=""></a></div>
				<div class="slide"><a href="#"><img src="banner.jpg" alt=""></a></div>
				<div class="slide"><a href="#"><img src="banner.jpg" alt=""></a></div>
			</div>
		</div>

		<h2><span>Doporučujeme</span></h2>
		<div class="product card">
			<h3><a href="#">GNC Mega Men® 50 Plus 120 Caplets</a></h3>
			<a href="#"><img src="product.jpg" alt="" /></a>
			<span class="rating r5"><i></i></span>
			<p>Aenean commodo ligula eget dolor. Aenean massa. </p>
			<b class="price">890 Kč</b>
		</div>
		<div class="product card">
			<h3><a href="#">GNC Mega Men® 50 Plus 120 Caplets</a></h3>
			<a href="#"><img src="product.jpg" alt="" /></a>
			<span class="rating r4"><i></i></span>
			<p>Aenean commodo ligula eget dolor. Aenean massa. </p>
			<b class="price">890 Kč</b>
		</div>
		<div class="product card">
			<h3><a href="#">GNC Mega Men® 50 Plus 120 Caplets</a></h3>
			<a href="#"><img src="product.jpg" alt="" /></a>
			<span class="rating r3"><i></i></span>
			<p>Aenean commodo ligula eget dolor. Aenean massa. </p>
			<b class="price">890 Kč</b>
		</div>

		<div class="left">
			<h2><span>Nejprodávanější</span></h2>
			<div class="product card big">
				<h3>GNC Pro Performance® AMP Amplified Wheybolic 120 Caplets </h3>
				<a href="#"><img src="product_big.jpg" alt="" /></a>
				<span class="rating r4"><i></i></span>
				<p>Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
				<b class="price">890 Kč</b>
				<a href="#" class="info">Více informací o produktu</a>
			</div>
			<div class="right best">
				<a href="#">GNC Mega Men® 50 Plus</a>
				<a href="#">BSN® N.O.-XPLODE™</a>
				<a href="#">Cellucor® C4™ Extreme 3</a>
				<a href="#">Swole® PreWrek</a>
				<a href="#">GNC Mega Men® 50 Plus</a>
				<a href="#">BSN® N.O.-XPLODE™</a>
				<a href="#">Cellucor® C4™ Extreme 3</a>
				<a href="#">Swole® PreWrek</a>
				<a href="#">Swole® PreWrek</a>
				<a href="#" class="open">Zobrazit všechny</a>
			</div>
		</div>
		<div class="right news">
			<h2><span>Aktuality</span></h2>

			<h3><a href="#">Aktuality</a></h3>
			<p>Aenean commodo ligula eget dolor. Aenean massa ...</p>
			<span class="date">23.12.2014</span>
			<h3><a href="#">Aktuality</a></h3>
			<p>Aenean commodo ligula eget dolor. Aenean massa ...</p>
			<span class="date">23.12.2014</span>
			<h3><a href="#">Aktuality</a></h3>
			<p>Aenean commodo ligula eget dolor. Aenean massa ...</p>
			<span class="date">23.12.2014</span>

			<a href="#" class="open">Všechny aktuality</a>
		</div>

		<div class="left">
			<h2><span>Kde nás najdete</span></h2>
		</div>
		<div class="right open">
			<h2><span>Otevírací doba Lékárny</span></h2>
			<table>
				<tr><th>Pondělí</th><td>7.30 : 18.00</td></tr>
				<tr><th>Úterý</th><td>7.30 : 18.00</td></tr>
				<tr><th>Středa</th><td>7.30 : 18.00</td></tr>
				<tr><th>Čtvrtek</th><td>7.30 : 18.00</td></tr>
				<tr><th>Pátek</th><td>7.30 : 18.00</td></tr>
				<tr><th>Sobota</th><td><b>Zavřeno</b></td></tr>
				<tr><th>Neděle</th><td><b>Zavřeno</b></td></tr>
			</table>
		</div>
		<hr class="cleaner" />
	</div>

	<div id="footer">
		<div class="info">
			<h2>Informace</h2>
			<ul>
				<li><a href="#">Jak nakupovat?</a></li>
				<li><a href="#">Často kladené dotazy</a></li>
				<li><a href="#">Kontaktní a reklamační údaje</a></li>
				<li><a href="#">Ceník dopravy</a></li>
			</ul>
		</div>
		<div class="recommend">
			<h2>Doporučit známému</h2>
			<p>Zadejte e-mailovou adresu kam máme zaslat odkaz</p>
			<form method="post">
				<div class="pair">
				<input type="email" placeholder="Napište e-mail" name="email">
				<button name="recommend">OK</button>
				</div>
			</form>
		</div>
		<div class="company">
			<h2>Provozovatel</h2>
			<p>Sport Nutrition s.r.o<br />
			IČO: 456 258 456<br />
			DIČ: 458 154 212<br />
			TEL: <b>+420 159 452 159</b></p>
		</div>
		<div class="news">
			<h2>Novinky e-mailem</h2>
			<p>Odebírejte naše novinky e-mailem</p>
			<form method="post">
				<div class="pair">
					<input type="email" name="email" placeholder="Napiště e-mail" />
					<button name="news">OK</button>
				</div>
			</form>
		</div>

		<hr />

		<div class="copyright">
			<img src="images/logo_dark.png" alt="Sport Nutrition Vávra" />
			© <a href="#">www.neconeco.cz</a> All rights Reserved.
			<a href="#"><img src="images/logo_facebook.png" alt="Facebook" /></a>
			<a href="#"><img src="images/logo_google.png" alt="Google+" /></a>
			<a href="#"><img src="images/logo_youtube.png" alt="YouTube" /></a>
			<a href="#" class="right"><img src="images/webdesign.png" alt="webdesign superlogo" /></a>
		</div>
	</div>
</div>

</body>
</html>