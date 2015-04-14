<div class="mainContentWrapper">
	<?=$form->Create('Address', array('url' => array('controller' => 'orders', 'action' => 'address_edit', 'type' => $this->params['named']['type']))) ?>
		<fieldset>
			<legend><?=( $this->params['named']['type'] == 'd' ? 'Adresa doručení' : 'Fakturační adresa' ) ?></legend>
			<div class="form-group">
				<label><sup>*</sup>Jméno</label>
				<?=$form->input('Address.name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
			<div class="form-group">
				<label><sup>*</sup>Ulice</label>
				<?=$form->input('Address.street', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
			<div class="form-group">
				<label><sup>*</sup>Číslo popisné</label>
				<?=$form->input('Address.street_no', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
			<div class="form-group">
				<label><sup>*</sup>PSČ</label>
				<?=$form->input('Address.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
			<div class="form-group">
				<label><sup>*</sup>Město</label>
				<?=$form->input('Address.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>			
			<div class="form-group">
				<label><sup>*</sup>Stát</label>
				<?=$form->input('Address.state_field', array('label' => false, 'div' => false, 'class' => 'form-control', 'disabled' => true, 'value' => $this->data['Address']['state']))?>
				<?=$form->hidden('Address.state', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>			
		</fieldset>
<?php 
	echo $this->Form->submit('Upravit', array('class' => 'btn btn-success'));
	echo $this->Form->end();
?>
</div>