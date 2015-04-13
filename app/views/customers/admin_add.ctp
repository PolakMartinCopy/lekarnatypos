<h1>Nový uživatel</h1>
<a href='/administrace/help.php?width=500&id=20' class='jTip' id='20' name='Uživatelé (20)'><img src='/images/<?php echo REDESIGN_PATH ?>icons/help.png' width='16' height='16' /></a>
<?php echo $this->Form->create('Customer')?>
<table class="tabulkaedit">
	<tr class="nutne">
		<td>Křestní jméno</td>
		<td><?php echo $this->Form->input('Customer.first_name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr class="nutne">
		<td>Příjmení</td>
		<td><?php echo $this->Form->input('Customer.last_name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<td>Firma</td>
		<td><?php echo $this->Form->input('Customer.company_name', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<td>IČ</td>
		<td><?php echo $this->Form->input('Customer.ico', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<td>DIČ</td>
		<td><?php echo $this->Form->input('Customer.dic', array('label' => false, 'size' => 60))?></td>
	</tr>
	<tr>
		<td>Aktivní</td>
		<td><?php echo $this->Form->input('Customer.active', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Cenová kategorie</td>
		<td><?php echo $this->Form->input('Customer.customer_type_id', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>Ulice</td>
		<td><?php echo $this->Form->input('Address.0.street', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Číslo popisné</td>
		<td><?php echo $this->Form->input('Address.0.street_no', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>Město</td>
		<td><?php echo $this->Form->input('Address.0.city', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>PSČ</td>
		<td><?php echo $this->Form->input('Address.0.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<td>Stát</td>
		<td><?php echo $this->Form->input('Address.0.state', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>Telefon</td>
		<td><?php echo $this->Form->input('Customer.phone', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>Email</td>
		<td><?php echo $this->Form->input('Customer.email', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>Login</td>
		<td><?php echo $this->Form->input('CustomerLogin.0.login', array('label' => false))?></td>
	</tr>
	<tr class="nutne">
		<td>Heslo</td>
		<td><?php echo $this->Form->input('CustomerLogin.0.password', array('label' => false))?></td>
	</tr>
</table>
<br/>
<?php 
	echo $this->Form->hidden('Address.0.type', array('value' => 'd'));
	echo $this->Form->submit('Uložit');
	echo $this->Form->end();
?>
<div class="prazdny"></div>