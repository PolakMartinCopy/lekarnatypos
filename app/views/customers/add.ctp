<h2><span><?php echo $page_heading?></span></h2>
<p>Zaregistrujte se a budete si moci prohlédnout ceny produktů pro zaregistrované uživatele obchodu.</p>
<?=$form->Create('Customer', array('id' => 'orderForm'))?>
<fieldset>
	<legend>Registrační údaje</legend>
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
			<div class="form-group">
				<label><sup>*</sup>Jméno</label>
				<?=$form->input('Customer.first_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
			<div class="form-group">
				<label><sup>*</sup>Příjmení</label>
				<?=$form->input('Customer.last_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
			<div class="form-group">
				<label><sup>*</sup>Telefon</label>
				<?=$form->input('Customer.phone', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
			<div class="form-group">
				<label><sup>*</sup>Email</label>
				<?=$form->input('Customer.email', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>	
		</div>
	</div>
</fieldset>
<fieldset>
	<legend>Firemní údaje - nepovinné</legend>
	<div class="row">
		<div class="col-xs-12 col-md-12">
			<div class="form-group">
				<label>Název společnosti</label>
				<?=$form->input('Customer.company_name', array('label'=> false, 'class' => 'form-control'))?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
			<div class="form-group">
				<label>IČ</label>
				<?=$form->input('Customer.company_ico', array('label'=> false, 'class' => 'form-control'))?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
			<div class="form-group">
				<label>DIČ</label>
				<?=$form->input('Customer.company_dic', array('label'=> false, 'class' => 'form-control'))?>
			</div>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend>Doručovací adresa - nepovinné</legend>
<div class="row">
	<div class="col-xs-8 col-md-8">
		<div class="form-group">
			<label>Ulice</label>
			<?=$form->input('Address.0.street', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
	</div>
	<div class="col-xs-4 col-md-4">
		<div class="form-group">
			<label>Číslo popisné</label>
			<?=$form->input('Address.0.street_no', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-4 col-md-4">
		<div class="form-group">
			<label>PSČ</label>
			<?=$form->input('Address.0.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
	</div>
	<div class="col-xs-8 col-md-8">
		<div class="form-group">
			<label>Město</label>
			<?=$form->input('Address.0.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-12">
		<div class="form-group">
			<label>Stát</label>
			<input type="text" name="fakeState" value="Česká Republika" disabled class="form-control" />
			<?=$form->hidden('Address.0.state', array('value' => 'Česká Republika'))?>
		</div>
	</div>
</div>	
</fieldset>
<?php
	echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1));
	echo $this->Form->submit('Zaregistrovat', array('class' => 'btn btn-success'));
	echo $this->Form->end();
?>