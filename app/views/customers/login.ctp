<div class="mainContentWrapper">
	<?=$form->Create('Customer', array('url' => array('action' => 'login'), 'id' => 'orderForm'));?>
	<fieldset>
		<legend>Přihlašovací údaje</legend>
		<div class="form-group">
			<label>Login:</label>
			<?=$form->text('Customer.login', array('class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label>Heslo:</label>
			<?=$form->password('Customer.password', array('class' => 'form-control'))?>
		</div>
	</fieldset>
<?php 
	echo $html->link('zapomněl(a) jsem heslo', array('controller' => 'customers', 'action' => 'password'));
	echo $this->Form->submit('přihlásit', array('class' => 'btn btn-success'));
	echo $this->Form->end()
?>
</div>