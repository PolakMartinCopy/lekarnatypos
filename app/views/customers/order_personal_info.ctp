<script type="text/javascript">
	window.onload = function(){
		$('#copyAddressLink').click(function(e) {
			e.preventDefault();
			$('#Address1Street').val($('#Address0Street').val());
			$('#Address1StreetNo').val($('#Address0StreetNo').val());
			$('#Address1City').val($('#Address0City').val());
			$('#Address1Zip').val($('#Address0Zip').val());
			$('#Address1State').val($('#Address0State option:selected').val());
		});
	};
</script>

<? if (!$session->check('Customer')){ ?>
<p><strong>Jste-li již našim zákazníkem</strong>, přihlašte se prosím pomocí formuláře v záhlaví stranky.</p>
<? } ?>

<?php echo $this->Form->create('Customer', array('url' => array('controller' => 'customers', 'action' => 'order_personal_info')))?>
	<fieldset>
		<legend>Osobní údaje</legend>
		<div class="form-group">
			<label><sup>*</sup>Jméno</label>
			<?=$form->input('Customer.first_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Příjmení</label>
			<?=$form->input('Customer.last_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Telefon</label>
			<?=$form->input('Customer.phone', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Email</label>
			<?=$form->input('Customer.email', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label>Firma</label>
			<?=$form->input('Customer.company_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label>IČ</label>
			<?=$form->input('Customer.ICO', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label>DIČ</label>
			<?=$form->input('Customer.dic', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
	</fieldset>
		
	<fieldset>
		<legend>Fakturační adresa</legend>
		<div class="form-group">
			<label><sup>*</sup>Ulice</label>
			<?=$form->input('Address.0.street', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Číslo popisné</label>
			<?=$form->input('Address.0.street_no', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Město</label>
			<?=$form->input('Address.0.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>PSČ</label>
			<?=$form->input('Address.0.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>		
		<div class="form-group">
			<label><sup>*</sup>Stát</label>
			<?=$form->input('Address.0.state', array('label' => false, 'div' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika'), 'class' => 'form-control'))?>
		</div>		
	</fieldset>

	<fieldset>
		<legend>Doručovací adresa</legend>
		<p><a href="#" id="copyAddressLink">Klikněte zde, pokud je stejná jako fakturační.</a>
		<div class="form-group">
			<label><sup>*</sup>Ulice</label>
			<?=$form->input('Address.1.street', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Číslo popisné</label>
			<?=$form->input('Address.1.street_no', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Město</label>
			<?=$form->input('Address.1.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>PSČ</label>
			<?=$form->input('Address.1.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>		
		<div class="form-group">
			<label><sup>*</sup>Stát</label>
			<?=$form->input('Address.1.state', array('label' => false, 'div' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika'), 'class' => 'form-control'))?>
		</div>	
	</fieldset>
<?php 
	echo $this->Form->hidden('Customer.id');
	echo $this->Form->hidden('Customer.newsletter', array('value' => true));
	echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1));
	echo $this->Form->hidden('Customer.active', array('value' => true));

	echo $this->Form->hidden('Address.0.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'f')));
	echo $this->Form->hidden('Address.1.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'd')));
	
	echo $this->Form->submit('>> Krok 2/4: Výběr dopravy a platby', array('class' => 'btn btn-success'));
	echo $this->Form->end();
?>