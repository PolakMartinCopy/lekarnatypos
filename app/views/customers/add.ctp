<h1>Registrace nového účtu</h1>
<p>Jestliže již máte vlastní účet na webu <?php echo CUST_ROOT ?>, <?php echo $this->Html->link('přihlašte se', array('controller' => 'customers', 'action' => 'login'))?> prosím <?php echo $this->Html->link('zde', array('controller' => 'customers', 'action' => 'login'))?>.</p>
<?=$form->Create('Customer', array('id' => 'orderForm'))?>
<fieldset>
	<legend>Registrační údaje</legend>
	<div class="form-group">
		<label>Jméno</label>
		<?=$form->input('Customer.first_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label><sup>*</sup>Příjmení</label>
		<?=$form->input('Customer.last_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label><sup>*</sup>Kontaktní telefon</label>
		<?=$form->input('Customer.phone', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label><sup>*</sup>Emailová adresa</label>
		<?=$form->input('Customer.email', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
	</div>
</fieldset>

<fieldset>
	<legend>Doručovací adresa</legend>
	<div class="form-group">
		<label>Ulice</label>
		<?=$form->input('Address.0.street', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>Číslo popisné</label>
		<?=$form->input('Address.0.street_no', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>PSČ</label>
		<?=$form->input('Address.0.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>Město</label>
		<?=$form->input('Address.0.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
	</div>
	<div class="form-group">
		<label>Stát</label>
		<input type="text" name="fakeState" value="Česká republika" disabled class="form-control"/>
		<?=$form->hidden('Address.0.state', array('value' => 'Česká republika'))?>
	</div>
</fieldset>

<?=$form->Submit('zaregistrovat', array('class' => 'btn btn-success'));?></td>

<?=$form->end()?>