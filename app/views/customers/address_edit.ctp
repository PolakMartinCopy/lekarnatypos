<div class="mainContentWrapper">
<?=$form->create('Address', array('url' => array('controller' => 'customers', 'action' => 'address_edit', 'type' => $this->params['named']['type']), '')) ?>
<fieldset>
	<legend>Adresa</legend>
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
				<label><sup>*</sup>Jméno</label>
				<?=$form->input('Customer.first_name', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
			<div class="form-group">
				<label><sup>*</sup>Příjmení</label>
				<?=$form->input('Customer.last_name', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-8 col-md-8">
			<div class="form-group">
				<label><sup>*</sup>Ulice</label>
				<?=$form->input('Address.0.street', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
		<div class="col-xs-4 col-md-4">
    		<div class="form-group">
				<label><sup>*</sup>Číslo popisné</label>
				<?=$form->input('Address.0.street_no', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-4 col-md-4">
    		<div class="form-group">
    			<label><sup>*</sup>PSČ</label>
				<?=$form->input('Address.0.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
		<div class="col-xs-8 col-md-8">
			<div class="form-group">
				<label><sup>*</sup>Město</label>
				<?=$form->input('Address.0.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-12">
			<div class="form-group">
				<label><sup>*</sup>Stát</label>
				<input type="text" name="fakeState" value="Česká Republika" disabled class="form-control" />
				<?=$form->hidden('Address.0.state', array('value' => 'Česká Republika'))?>
			</div>
		</div>
	</div>		
<?php if (isset($type) && $type == 'f') { ?>
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
<?php } ?>
</fieldset>
<?php 
	echo $this->Form->hidden('Customer.id');
	echo $this->Form->hidden('Address.0.id');
	echo $this->Form->hidden('Address.0.type');
	echo $this->Form->submit('Uložit', array('class' => 'btn btn-success'));
	echo $this->Form->end();
?>
</div>