<h3>Přihlášení k administraci</h3>
<?=$form->Create('Administrator', array('url' => array('action' => 'login') + $this->params['named']))?>
<fieldset>
	<label>LOGIN:</label>
	<?=$form->text('Administrator.login')?>
</fieldset>

<fieldset>
	<label>HESLO:</label>
	<?=$form->password('Administrator.password')?>
</fieldset>
<?=$form->submit('přihlásit se')?>
<?=$form->end()?>