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
				<?=$form->select('Address.state', array('Česká Republika' => 'Česká Republika'), null, array('empty' => false, 'class' => 'form-control')) ?>
			</div>
		</fieldset>
<?php 
	$form->submit('Upravit', array('class' => 'btn btn-success'));
	$form->end()
?>
</div>