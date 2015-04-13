<h2><span>Přihlášení k uživatelskému účtu</span></h2>
<div class="mainContentWrapper">
	<?=$form->Create('Customer', array('url' => array('action' => 'login'), 'id' => 'orderForm'));?>
	<fieldset>
		<legend>Přihlašovací údaje</legend>
		<div class="form-group">
			<label><sup>*</sup>Login:</label>
			<?=$form->input('Customer.login', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Heslo:</label>
			<?=$form->input('Customer.password', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
	</fieldset>
	<?=$form->submit('Přihlásit', array('class' => 'btn btn-success'));?>
	<?php echo $this->Form->end()?>
	<?=$html->link('zapomněl(a) jsem heslo', array('controller' => 'customers', 'action' => 'password')) ?>
</div>