<h1>Doporučte nás</h1>
<p>Vyplňte prosím všechna pole označená <sup>*</sup> a kontrolní pole.</p>

<?php echo $this->Form->create('Recommendation', array('url' => array('controller' => 'recommendations', 'action' => 'send'), 'encoding' => false))?>
<div class="form-group">
	<label>Vaše jméno:</label>
	<?php echo $this->Form->input('Recommendation.source_name', array('label' => false, 'type' => 'text', 'size' => 50, 'class' => 'form-control'))?>
</div>
<div class="form-group">
	<label>Váš email<sup>*</sup></label>
	<?php echo $this->Form->input('Recommendation.source_email', array('label' => false, 'type' => 'text', 'size' => 50, 'class' => 'form-control'))?>
	<div class="formErrors"></div>
</div>
<div class="form-group">			
	<label>Email adresáta<sup>*</sup></label>
	<?php echo $this->Form->input('Recommendation.target_email', array('label' => false, 'type' => 'text', 'size' => 50, 'class' => 'form-control')); ?>
	<div class="formErrors"></div>
</div>
<?php 
	echo $this->Form->hidden('Recommendation.request_uri', array('value' => $_SERVER['REQUEST_URI'], 'id' => 'RecommendationRequestUriProduct'));
	require_once 'recaptchalib.php';
  	$publickey = "6Le4B_oSAAAAAANtlNhA6j_p0kFyWBItvS31U7Q5"; // you got this from the signup page
  	echo recaptcha_get_html($publickey);
?>
<div id="RecommendationRecaptchaErrorProduct" class="formErrors"></div>
<?php 
	echo $this->Form->submit('Odeslat známému', array('class' => 'btn btn-success btn-lg'));
	echo $this->Form->end();
?>