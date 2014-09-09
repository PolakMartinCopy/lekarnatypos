<div class="mainContentWrapper">
<? if ( !$session->check('Customer') ){ ?>
<p><strong>Jste-li již našim zákazníkem</strong>, přihlašte se prosím pomocí formuláře v záhlaví stranky,<br />
nebo použijte <a href="/customers/login">příhlašovací formulář</a>.</p>
<? } ?>

<?=$form->Create('Order', array('url' => '/orders/add', 'id' => 'orderForm', 'role' => 'form'))?>
<?if ( !$session->check('Customer.id') ){ ?>
	<fieldset>
		<legend>Adresa doručení</legend>
		<div class="form-group">
			<label><sup>*</sup>Jméno</label>
			<?=$form->input('Customer.first_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label><sup>*</sup>Příjmení</label>
			<?=$form->input('Customer.last_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
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
		<p style="font-size:10px;">Chcete-li vyplnit odlišnou fakturační adresu, můžete tak učinit v dalším kroku objednávky.</p>
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
		<legend>Fakturační údaje</legend>
		<p style="font-size:10px;">Vyplňte pouze jste-li podnikající fyzická osoba, nebo zástupce právnické osoby.</p>
		<div class="form-group">
			<label>Název společnosti</label>
			<?=$form->input('Customer.company_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">	
			<label>IČO</label>
			<?=$form->input('Customer.company_ico', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
		<div class="form-group">
			<label>DIČ</label>
			<?=$form->input('Customer.company_dic', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
		</div>
	</fieldset>
<?}?>
	<fieldset>
		<legend>Detaily objednávky</legend>
		<div class="form-group">
			<label>Způsob doručení<sup>*</sup></label>
			<? 
				if ( !isset($this->data['Order']['shipping_id']) ){
					$this->data['Order']['shipping_id'] = null;
				}
				echo $form->select('Order.shipping_id', $shipping_choices, $this->data['Order']['shipping_id'], array('empty' => false, 'class' => 'form-control'));
			?>
		</div>
		<div class="form-group">
			<label>Váš komentář k objednávce</label>
			<?=$form->textarea('Order.comments', array('cols' => 40, 'rows' => 5, 'class' => 'form-control'))?>
		</div>
	</fieldset>

<?php
	echo $form->Submit('Rekapitulace objednávky', array('class' => 'btn btn-success'));
	echo $form->end();
?>
</div>