<div class="mainContentWrapper">
	<h2>Změna údajů zákazníka</h2>
	<?=$form->Create('Customer', array('url' => array('action' => 'edit')))?>
	<div class="form-group">
		<label>jméno</label>
		<?=$form->input('Customer.first_name', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>příjmení</label>
		<?=$form->input('Customer.last_name', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>telefon</label>
		<?=$form->input('Customer.phone', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>email</label>
		<?=$form->input('Customer.email', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>login</label>
		<?=$form->input('Customer.login', array('label'=> false, 'class' => 'form-control'))?>
	</div>
<?php 
	$form->hidden('Customer.edit', array('value' => 'info'));
	echo $this->Form->submit('uložit', array('class' => 'btn btn-success'));
	echo $form->end();
?>

	<h2>Změna hesla</h2>
	<?=$form->Create('Customer', array('url' => array('action' => 'edit')))?>
	<div class="form-group">
		<label>původní heslo</label>
		<?=$form->input('Customer.old_password', array('label'=> false, 'type' => 'password', 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>nové heslo</label>
		<?=$form->input('Customer.new_password', array('label'=> false, 'type' => 'password', 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>zopakujte nové heslo</label>
		<?=$form->input('Customer.new_password_rep', array('label'=> false, 'type' => 'password', 'class' => 'form-control'))?>
	</div>
<?php 
	$form->hidden('Customer.edit', array('value' => 'pass'));
	echo $this->Form->submit('změnit heslo', array('class' => 'btn btn-success'));
	echo $this->Form->end()
?>
</div>