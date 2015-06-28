<h1><?php echo $page_heading?></h1>
<?=$form->Create('Customer', array('url' => array('action' => 'edit')))?>
<fieldset>
	<div class="form-group">
		<label>Jméno</label>
		<?=$form->input('Customer.first_name', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>Příjmení</label>
		<?=$form->input('Customer.last_name', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>Telefon</label>
		<?=$form->input('Customer.phone', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>Email</label>
		<?=$form->input('Customer.email', array('label'=> false, 'class' => 'form-control'))?>
	</div>
</fieldset>
<?php echo $this->Form->submit('Uložit', array('class' => 'btn btn-success'))?>
<br/><br/>

<fieldset>
	<legend>Firemní údaje</legend>
	<div class="form-group">
		<label>Název</label>
		<?=$form->input('Customer.company_name', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>IČ</label>
		<?=$form->input('Customer.company_ico', array('label'=> false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>DIČ</label>
		<?=$form->input('Customer.company_dic', array('label'=> false, 'class' => 'form-control'))?>
	</div>
</fieldset>
<?php echo $this->Form->submit('Uložit', array('class' => 'btn btn-success'))?>
<br/><br/>

<fieldset>
	<legend>Přístupové údaje</legend>
<?php foreach ($customer['CustomerLogin'] as $index => $customer_login) { ?>
	<div class="form-group">
		<label>Login</label>
		<?php echo $this->Form->input('CustomerLogin.' . $index . '.login', array('label' => false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>původní heslo</label>
		<?=$form->input('CustomerLogin.' . $index . '.old_password', array('label' => false, 'type' => 'password', 'class' => 'form-control'))?>
		<?php echo $this->Form->hidden('CustomerLogin.' . $index . '.password')?>
	</div>
	<div class="form-group">
		<label>nové heslo</label>
		<?=$form->input('CustomerLogin.' . $index . '.new_password', array('label'=> false, 'type' => 'password', 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>zopakujte nové heslo</label>
		<?=$form->input('CustomerLogin.' . $index . '.new_password_rep', array('label'=> false, 'type' => 'password', 'class' => 'form-control'))?>
		<?php echo $this->Form->hidden('CustomerLogin.' . $index . '.id', array('value' => $customer_login['id']))?>
	</div>
</fieldset>
<?php } ?>
<?php echo $this->Form->hidden('Customer.id')?>
<?php echo $this->Form->submit('Uložit', array('class' => 'btn btn-success'))?>
<?php echo $this->Form->end()?>