<?
	if ( $session->check('Customer.id') ){
		$customer = $session->read('Customer');
		echo '<div id="loggedAs">Zákazník: <strong>' . $customer['first_name'] . ' ' . $customer['last_name'] . '</strong></div>
		<ul id="customerActions">
			<li>' . $html->link('úvod - zákaznický panel', array('controller' => 'customers', 'action' => 'index')) . '</li>
			<li>' . $html->link('mé objednávky', array('controller' => 'customers', 'action' => 'orders_list')) . '</li>
			<li>&nbsp;</li>
			<li><a href="/customers/logout">odhlásit se</a></li>
		</ul>';
	} else {
?>
	<form action="/customers/login" method="post">
	<div>
		<fieldset class="horni">
			<label for="username">Login:</label>						
			<input class="textBox" id="username" name="data[Customer][login]" type="text" /> 
		</fieldset>
		<fieldset>
			<label for="password">Heslo:</label>
			<input class="textBox" id="password" name="data[Customer][password]" type="password" />
		</fieldset>
		<fieldset>
			<input class="buttonPrihlasit" size="3" type="submit" value="přihlásit" />
		</fieldset>
	</div>  
		<input type="hidden" name="data[backtrace_url]" value="<?=$this->here?>" />
	</form>
	
	<!--<a href="#">Zaregistrujte se</a>-->
	<!--<a href="/customer/login">Přihlásit</a>-->
<?
	}
?>