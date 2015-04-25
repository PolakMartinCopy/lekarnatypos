<h2><span><?php echo $page_heading?></span></h2>
<p>Zaregistrujte se a budete si moci prohlédnout ceny produktů pro zaregistrované uživatele obchodu.</p>
<?=$form->Create('Customer', array('id' => 'orderForm'))?>
	<fieldset>
		<legend>Registrační údaje</legend>
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
	</fieldset>

	<fieldset>
		<legend>Doručovací adresa - nepovinné</legend>
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
	
	<table id="orderForm">
		<tr>
			<th>&nbsp;</th>
			<td>
				<?php echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1))?>
				<?=$form->Submit('zaregistrovat', array('class' => 'btn btn-success'));?>
			</td>
		</tr>
	</table>
<?=$form->end()?>