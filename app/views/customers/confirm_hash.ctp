<h2><span>Obnova zapomenutého hesla</span></h2>
<p>Zvolte si prosím nové heslo. Poté co jej odešlete budete automaticky přihlášen(a).</p>
<p>Ponecháte-li pole pro nové heslo prázdné, budete ihned přihlášen(a). Nové heslo vygenerujeme za vás a odešleme vám jej emailem.</p>
<?=$form->create('Customer', array('action' => 'confirm_hash', 'url' => $this->passedArgs)) ?>
<fieldset>
	<legend>Nové heslo</legend>
	<div class="form-group">
		<label>Heslo:</label>
		<?php echo $this->Form->hidden('Customer.id', array('value' => $customer_id))?>
		<?=$form->input('Customer.password', array('label' => false, 'class' => 'form-control')) ?>
	</div>
</fieldset>
<?=$form->submit('ODESLAT', array('class' => 'btn btn-success'))?>
<?=$form->end() ?>