<div class="mainContentWrapper">
	<h1><?php echo $page_heading?></h1>
	<p>Do pole prosím vložte Vaši emailovou adresu, kterou používáte ve spojitosti s účtem na www.<?php echo CUST_ROOT?></p>
	<?=$form->create('Customer', array('action' => 'password', 'url' => $this->passedArgs)) ?>
	<div class="form-group">
		<label>Emailová adresa:</label>
		<?=$form->input('Customer.email', array('label' => false, 'class' => 'form-control')) ?>
		<?php echo $this->Form->hidden('Customer.back', array('value' => $back))?>
	</div>
	<?php echo $this->Form->submit('ODESLAT', array('class' => 'btn btn-success'))?>
	<?=$form->end() ?>
</div>