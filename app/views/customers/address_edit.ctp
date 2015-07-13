<div class="mainContentWrapper">
<?=$form->create('Address', array('url' => array('controller' => 'customers', 'action' => 'address_edit', 'type' => $this->params['named']['type']), '')) ?>
<fieldset>
	<legend>Adresa</legend>
	<div class="form-group">
		<label>Jméno a příjmení / název společnosti</label>
		<?=$form->input('Address.0.name', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>Ulice</label>
		<?=$form->input('Address.0.street', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>Číslo popisné</label>
		<?=$form->input('Address.0.street_no', array('label' => false, 'size' => 5, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>Město / obec</label>
		<?=$form->input('Address.0.city', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>PSČ</label>
		<?=$form->input('Address.0.zip', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>Stát</label>
		<input type="text" name="fakeState" value="Česká Republika" disabled class="form-control" />
		<?=$form->hidden('Address.0.state', array('value' => 'Česká Republika'))?>
	</div>
</fieldset>
<?php if (isset($type) && $type == 'f') { ?>
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
<?php echo $this->Form->hidden('Customer.id')?>
<?php } ?>
<?php 
	echo $this->Form->hidden('Address.0.id');
	echo $this->Form->hidden('Address.0.type');
	echo $this->Form->submit('Uložit', array('class' => 'btn btn-success'));
	echo $this->Form->end();
?>
</div>