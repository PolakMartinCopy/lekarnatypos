<h2><span><?php echo $page_heading?></span></h2>
<p>Zaregistrujte se a budete si moci prohlédnout ceny produktů pro zaregistrované uživatele obchodu.</p>
<?=$form->Create('Customer', array('id' => 'orderForm'))?>
<fieldset>
	<legend id="CustomerInfo">Informace o zákazníkovi</legend>
	<?php 
	// flash, pokud je chyba ve formu pro udaje o zakaznikovi
	if ($this->Session->check('Message.flash')) {
		$flash = $this->Session->read('Message.flash');
		if (isset($flash['params']['type']) && $flash['params']['type'] == 'customer_info') {
			echo $this->Session->flash();
		}
	}
	?>
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

<div id="DeliveryAddressBox">
	<fieldset id="DeliveryAddressTable">
		<legend>Doručovací adresa - nepovinné</legend>
		<div class="row">
			<div class="col-xs-12 col-md-12">
				<div class="form-group">
					<label>Jméno / Název společnosti</label>
					<?=$form->input('Address.0.name', array('label'=> false, 'class' => 'form-control'))?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6">
				<div class="form-group">
					<label>Jméno kontakní osoby</label>
					<?=$form->input('Address.0.contact_first_name', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6">
				<div class="form-group">
					<label>Příjmení kontakní osoby</label>
					<?=$form->input('Address.0.contact_last_name', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
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
			<div class="col-xs-8 col-md-8">
				<div class="form-group">
					<label><sup>*</sup>Město</label>
					<?=$form->input('Address.0.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
				</div>
			</div>
			<div class="col-xs-4 col-md-4">
				<div class="form-group">
					<label><sup>*</sup>PSČ</label>
					<?=$form->input('Address.0.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-12">
				<div class="form-group">
					<label><sup>*</sup>Stát</label>
					<?=$form->input('Address.0.state', array('label' => false, 'div' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika'), 'class' => 'form-control'))?>
				</div>
			</div>
		</div>
	</fieldset>
</div>

<div id="InvoiceAddressBox">
	<div class="input checkbox" style="margin: 0;border-bottom: 1px solid #e5e5e5;margin-bottom:20px">
		<label id="InvoiceAddressChoiceLabel">
			<?php echo $this->Form->input('Customer.is_delivery_address_different', array('label' => false, 'type' => 'checkbox', 'id' => 'isDifferentAddressCheckbox', 'div' => false)); ?>
			<span style="font-size:21px">Fakturační adresa není stejná jako doručovací / doplnit IČO a DIČ</span>
		</label>
	</div>
	
<?php 
	$style = ' style="display:none"';
	if (isset($this->data['Customer']) && array_key_exists('is_delivery_address_different', $this->data['Customer']) && $this->data['Customer']['is_delivery_address_different']) {
		$style = '';
	}
?>

	<fieldset id="InvoiceAddressTable"<?php  echo $style?>>
		<div class="row">
			<div class="col-xs-12 col-md-12">
				<div class="form-group">
					<label>Jméno / Název společnosti</label>
					<?=$form->input('Address.1.name', array('label'=> false, 'class' => 'form-control'))?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6">
				<div class="form-group">
					<label>Jméno kontakní osoby</label>
					<?=$form->input('Address.1.contact_first_name', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6">
				<div class="form-group">
					<label>Příjmení kontakní osoby</label>
					<?=$form->input('Address.1.contact_last_name', array('label' => false, 'div' => false, 'class' => 'form-control')) ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-8 col-md-8">
				<div class="form-group">
					<label><sup>*</sup>Ulice</label>
					<?=$form->input('Address.1.street', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
				</div>
			</div>
			<div class="col-xs-4 col-md-4">
	    		<div class="form-group">
					<label><sup>*</sup>Číslo popisné</label>
					<?=$form->input('Address.1.street_no', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-8 col-md-8">
				<div class="form-group">
					<label><sup>*</sup>Město</label>
					<?=$form->input('Address.1.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
				</div>
			</div>
			<div class="col-xs-4 col-md-4">
	    		<div class="form-group">
	    			<label><sup>*</sup>PSČ</label>
					<?=$form->input('Address.1.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-12">
				<div class="form-group">
					<label><sup>*</sup>Stát</label>
					<?=$form->input('Address.1.state', array('label' => false, 'div' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika'), 'class' => 'form-control'))?>
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
</div>

<?php 
	echo $this->Form->hidden('Customer.newsletter', array('value' => true));
	echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1));
	echo $this->Form->hidden('Customer.active', array('value' => true));

	echo $this->Form->hidden('Address.0.type', array('value' => 'd'));
	echo $this->Form->hidden('Address.1.type', array('value' => 'f'));
	
	echo $this->Form->submit('Zaregistrovat', array('class' => 'btn btn-primary'));
	echo $this->Form->end();
?>