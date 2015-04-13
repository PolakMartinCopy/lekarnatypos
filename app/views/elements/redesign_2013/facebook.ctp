<?php 
	if ( $_SERVER['DOCUMENT_ROOT'] != 'C:/wamp/www/sportnutrition.cz' ){
?>
<h3 class="facebook">Facebook</h3>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/cs_CZ/sdk.js#xfbml=1&appId=292634817601030&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="fb-like-box" data-href="https://www.facebook.com/SportNutritionCZ" data-width="193" data-height="400" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="true"></div>
<?php
	}
?>