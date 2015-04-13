<h2><span><?php echo $page_heading?></span></h2>
<p>Zaregistrujte se a budete si moci prohlédnout ceny produktů pro zaregistrované uživatele obchodu.</p>
<?=$form->Create('Customer', array('id' => 'orderForm'))?>
	<fieldset>
		<legend>Registrační údaje</legend>
		<table id="orderForm">
			<tr>
				<th><sup>*</sup>Jméno</th>
				<td><?=$form->input('Customer.first_name', array('label' => false, 'div' => false, 'class' => 'content'))?></td>
			</tr>
			<tr>
				<th><sup>*</sup>Příjmení</th>
				<td><?=$form->input('Customer.last_name', array('label' => false, 'div' => false, 'class' => 'content'))?></td>
			</tr>	
			<tr>
				<th><sup>*</sup>Kontaktní telefon</th>
				<td><?=$form->input('Customer.phone', array('label' => false, 'div' => false, 'class' => 'content'))?></td>
			</tr>
			<tr>
				<th><sup>*</sup>Emailová adresa</th>
				<td><?=$form->input('Customer.email', array('label' => false, 'div' => false, 'class' => 'content'))?></td>
			</tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Doručovací adresa - nepovinné</legend>
		<table id="orderForm">
			<tr>
				<th>Ulice</th>
				<td><?=$form->input('Address.0.street', array('label' => false, 'class' => 'content'))?></td>
			</tr>	
			<tr>
				<th>Číslo popisné</th>
				<td><?=$form->input('Address.0.street_no', array('label' => false))?></td>
			</tr>	
			<tr>
				<th>PSČ</th>
				<td><?=$form->input('Address.0.zip', array('label' => false))?></td>
			</tr>	
			<tr>
				<th>Město</th>
				<td><?=$form->input('Address.0.city', array('label' => false, 'class' => 'content'))?></td>
			</tr>
			<tr>
				<th>Stát</th>
				<td>
					<?php echo $this->Form->input('Address.0.state', array('label' => false, 'empty' => false, 'type' => 'select', 'options' => array('Česká Republika' => 'Česká Republika', 'Slovensko' => 'Slovensko')))?>
					<?php echo $this->Form->hidden('Address.0.type', array('value' => 'd'))?>
				</td>
			</tr>
		</table>
	</fieldset>
	
	<table id="orderForm">
		<tr>
			<th>&nbsp;</th>
			<td>
				<?php echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1))?>
				<?=$form->Submit('zaregistrovat', array('class' => 'content'));?>
			</td>
		</tr>
	</table>
<?=$form->end()?>