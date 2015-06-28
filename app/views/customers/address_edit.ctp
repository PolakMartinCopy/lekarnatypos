<div class="mainContentWrapper">
<h1>Upravit adresu</h1>
	<?=$form->create('Address', array('url' => array('controller' => 'customers', 'action' => 'address_edit', 'type' => $this->params['named']['type']), '')) ?>
	<div class="form-group">
		<label>jméno a příjmení / název společnosti</label>
		<?=$form->input('Address.name', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>ulice</label>
		<?=$form->input('Address.street', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>číslo popisné</label>
		<?=$form->input('Address.street_no', array('label' => false, 'size' => 5, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>město / obec</label>
		<?=$form->input('Address.city', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>psč</label>
		<?=$form->input('Address.zip', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
	</div>
	<div class="form-group">
		<label>stát</label>
		<input type="text" name="fakeState" value="Česká Republika" disabled class="form-control" />
		<?=$form->hidden('Address.state', array('value' => 'Česká Republika'))?>
	</div>
<?php 
	echo $form->hidden('Address.type');
	echo $this->Form->submit('uložit adresu', array('class' => 'btn btn-success'));
	echo $this->Form->end();
?>
</div>