<div id="cart" class="page-order-basket">
<h1 id="ShoppingCart">Nákupní košík</h1>
<?php 
// flash, pokud je chyba ve formu pro prihlaseni
if ($this->Session->check('Message.flash')) {
	$flash = $this->Session->read('Message.flash');
	if (isset($flash['params']['type']) && $flash['params']['type'] == 'shopping_cart') {
		echo $this->Session->flash();
	}
}
?>
<? if (empty($cart_products)) { ?>
	<p>Nákupní košík je prázdný. Prosím vyberte si <a href="/">z naší nabídky</a>.</p>
<? } else { ?>
	<table id="cartContents" cellpadding="0" cellspacing="0" class="table basket-table">
		<thead>
			<tr>
				<th style="width:60%">Produkt</th>
				<th style="width:16%">Množství</th>
				<th style="width:9%;white-space:nowrap" class="hidden-xs hidden-sm">Cena za kus</th>
				<th style="width:9%;white-space:nowrap" class="hidden-xs hidden-sm">Cena celkem</th>
				<th style="width:6%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?		$final_price = 0;
		foreach ( $cart_products as $cart_product) {
			$final_price = $final_price + $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'];
			
			$image = '/img/na_small.jpg';
			if (isset($cart_product['Product']['Image']) && !empty($cart_product['Product']['Image'])) {
				$path = 'product-images/small/' . $cart_product['Product']['Image'][0]['name'];
				if (file_exists($path) && is_file($path) && getimagesize($path)) {
					$image = '/' . $path;
				}
			}
?>
		<tr>
			<td class="product">
				<div class="image_holder" style="float:left">
					<a href="/<?php echo $cart_product['Product']['url']?>">
						<img src="<?php echo $image?>" alt="Obrázek <?php echo $cart_product['Product']['name']?>" width="45" />
					</a>
				</div>
				<div style="margin: 10px 0 0 50px">
					<a href="/<?php echo $cart_product['Product']['url'] ?>"><?php echo $cart_product['Product']['name'] ?></a>
<?php 	if (!empty($cart_product['CartsProduct']['product_attributes'])) { ?>
					<br />
					<div style="font-size:11px;padding-left:20px;">
<?php 		foreach ($cart_product['CartsProduct']['product_attributes'] as $option => $value) { ?>
						<strong><?php echo $option ?></strong>: <?php echo $value ?><br />
<?php 		} ?>
					</div>
<?php 	} ?>
				</div>
			</td>
			<td class="count">
				<div class="input-group">
<?php 			echo $this->Form->Create('CartsProduct', array('url' => array('controller' => 'orders', 'action' => 'one_step_order', '#ShoppingCart'), 'encoding' => false));
				echo $this->Form->hidden('Order.action', array('value' => 'cart_edit'));
				echo $this->Form->hidden('CartsProduct.id', array('value' => $cart_product['CartsProduct']['id']));
?>
					<div class="count-input">
<?php 					echo $this->Form->input('CartsProduct.quantity', array('label' => false, 'size' => 1, 'value' => $cart_product['CartsProduct']['quantity'], 'type' => 'text', 'div' => false)); ?>
                    	<div class="count-add">+</div>
						<div class="count-remove">-</div>
					</div>
<?php  			echo $this->Form->end(); ?>
				</div>
			</td>
			<td class="price-per-unit hidden-xs hidden-sm" align="right" style="vertical-align:middle"><span class="price"><?php echo intval($cart_product['CartsProduct']['price_with_dph']) ?></span>&nbsp;Kč</td>
			<td class="price hidden-xs hidden-sm" align="right" style="vertical-align:middle"><span class="price"><?php echo intval($cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity']) ?></span>&nbsp;Kč</td>
			<td class="remove" align="right" style="vertical-align:middle">
				<?php echo $this->Html->link('<i class="fa fa-fw fa-times"></i>', array('controller' => 'carts_products', 'action' => 'delete', $cart_product['CartsProduct']['id'], 'back' => base64_encode($_SERVER['REQUEST_URI'] . '#ShoppingCart')), array('title' => 'odstranit z košíku', 'escape' => false, 'class' => 'text-danger'), 'Opravdu chcete produkt odstranit z košíku?'); ?>
			</td>
		</tr>
<?php	} ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2" style="text-align:right;font-size:18px">cena za zboží celkem:</th>
				<td colspan="2" align="right" style="vertical-align:middle;font-size:18px"><strong><span class="final-price"><?php echo intval($final_price) ?></span> Kč</strong></td>
				<td>&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	
	<?php echo $this->Html->link('Zpět do obchodu', '/', array('class' =>  'btn btn-sm btn-default'))?>
	<?php echo $this->Html->link('Přejít k objednání', '#OrderDetails', array('style' => 'float:right', 'class' => 'btn btn-primary'))?>

	<div style="clear:both"></div>
<h2 id="OrderDetails">Objednávka</h2>
<?php 
// flash, pokud je chyba ve formu pro info o zakaznikovi
if ($this->Session->check('Message.flash')) {
	$flash = $this->Session->read('Message.flash');
	if (isset($flash['params']['type']) && $flash['params']['type'] == 'customer_login') {
		echo $this->Session->flash();
	}
}
?>
<?php if (!$is_logged_in) { ?>
<ul style="list-style-type:none">
	<li>
		<?php
			$value = 1;
			$checked = '';
			if (isset($this->data['Customer']['is_registered']) && $this->data['Customer']['is_registered'] == $value) {
				$checked = 'checked="checked"';
			}
		?>
		<input type="radio" class="customer-is-registered" value="1" id="CustomerIsRegistered1" name="data[Customer][is_registered]" <?php echo $checked?>/> Přihlásit se, jsem již zaregistrován
		<div id="CustomerOneStepOrderDiv" style="display:none">
			<?=$form->Create('Customer', array('url' => array('controller' => 'orders', 'action' => 'one_step_order', '#OrderDetails'), 'encoding' => false));?>
			<fieldset>
				<div class="form-group">
					<label><sup>*</sup>Login:</label>
					<?=$form->input('Customer.login', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
				</div>
				<div class="form-group">
					<label><sup>*</sup>Heslo:</label>
					<?=$form->input('Customer.password', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
				</div>
			</fieldset>
			<?php echo $this->Form->hidden('Order.action', array('value' => 'customer_login'))?>
			<?php echo $this->Form->submit('Přihlásit')?>
			<?php echo $this->Form->end()?>
			<?=$html->link('zapomněl(a) jsem heslo', array('controller' => 'customers', 'action' => 'password')) ?>
		</div>
	</li>
	<li>
		<?php
			$value = 0;
			$checked = '';
			if (isset($this->data['Customer']['is_registered']) && $this->data['Customer']['is_registered'] == $value) {
				$checked = 'checked="checked"';
			}
		?>
		<input type="radio" class="customer-is-registered" value="<?php echo $value?>" id="CustomerIsRegistered0" name="data[Customer][is_registered]" <?php echo $checked?>/> Toto je moje první objednávka
	</li>
</ul>
<?php } ?>

<?php echo $this->Form->create('Order', array('url' => array('controller' => 'orders', 'action' => 'one_step_order', '#CustomerInfo'), 'encoding' => false))?>
<fieldset>
	<legend id="ShippingInfo">Doprava</legend>
	<?php if (!empty($providers)) { ?>
	<table style="width:100%">
	<?php
		$first = true;
		foreach ($providers as $provider) {
			$show_provider_row = true;
			foreach ($provider['shippings'] as $shipping) { 
				$checked = '';
				if (isset($this->data['Order']['shipping_id']) && $this->data['Order']['shipping_id'] == $shipping['Shipping']['id']) {
					$checked = ' checked="checked"';
				}
				if (!isset($this->data['Order']['shipping_id']) && $first) {
					$checked = ' checked="checked"';
				}
	?>
		<tr>
			<?php
				$border_top = '';  
				if ($show_provider_row) {
					if (!$first) {
						$border_top = ';border-top:1px solid #c0c0c0';
					}
			 ?>
			<td style="width:15%;padding:3px<?php echo $border_top?>" nowrap rowspan="<?php echo count($provider['shippings'])?>" valign="top" ><?php echo $provider['Shipping']['provider_name']?></td>
			<?php
				$show_provider_row = false; 
			} ?>
			<td style="width:5%;padding:3px<?php echo $border_top?>"><input name="data[Order][shipping_id]" type="radio" value="<?php echo $shipping['Shipping']['id']?>" id="OrderShippingId<?php echo $shipping['Shipping']['id']?>"<?php echo $checked?>/></td>
			<td style="width:70%;padding:3px<?php echo $border_top?>"><?php echo $shipping['Shipping']['name']?></td>
			<td style="width:10%;padding:3px<?php echo $border_top?>" align="right"><?php echo round($shipping['Shipping']['price'])?>&nbsp;Kč</td> 
		</tr>
	<?php		$first = false;
			}
		} ?>
	</table>
	<?php } ?>
</fieldset>

<fieldset>
	<legend>Poznámka k objednávce</legend>
	<?php echo $this->Form->input('Order.comments', array('label' => false, 'class' => 'form-control'))?>
</fieldset>

<!-- INFORMACE O ZAKAZNIKOVI -->
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

<div class="row">
	<fieldset id="DeliveryAddressTable" class="col-md-6">
		<legend>Doručovací adresa</legend>
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
	
	<fieldset style="display:block" class="col-md-6">
		<legend>
			<div class="input checkbox" style="margin: 0;">
				<label>
<?php echo $this->Form->input('Customer.is_company', array('label' => false, 'type' => 'checkbox', 'id' => 'isCompany', 'div' => false)); ?> Jste firma?
				</label>
			</div>
<?php 
	$style = ' style="display:none"';
	if (isset($this->data['Customer']) && array_key_exists('is_company', $this->data['Customer']) && $this->data['Customer']['is_company']) {
		$style = '';
	}
?>
		</legend>
		<div id="companyTable"<?php echo $style?>>
			<div class="row">
				<div class="col-xs-12 col-md-12">
					<div class="form-group">
						<label>Firma</label>
						<?=$form->input('Customer.company_name', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6 col-md-6">
					<div class="form-group">
						<label>IČ</label>
						<?=$form->input('Customer.ICO', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
					</div>
				</div>
				<div class="col-xs-6 col-md-6">
					<div class="form-group">
						<label>DIČ</label>
						<?=$form->input('Customer.dic', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</div>
	
<?php echo $this->Form->input('Customer.is_delivery_address_different', array('label' => 'Fakturační adresa není stejná jako doručovací', 'type' => 'checkbox', 'id' => 'isDifferentAddressCheckbox'));
	$style = ' style="display:none"';
	if (isset($this->data['Customer']) && array_key_exists('is_delivery_address_different', $this->data['Customer']) && $this->data['Customer']['is_delivery_address_different']) {
		$style = '';
	}
?>

<fieldset id="InvoiceAddressTable"<?php echo $style?>>
	<legend>Fakturační adresa</legend>
	<div class="row">
		<div class="col-xs-8 col-md-4">
			<div class="form-group">
				<label><sup>*</sup>Ulice</label>
				<?=$form->input('Address.1.street', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
		<div class="col-xs-4 col-md-2">
    		<div class="form-group">
				<label><sup>*</sup>Číslo popisné</label>
				<?=$form->input('Address.1.street_no', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-8 col-md-4">
			<div class="form-group">
				<label><sup>*</sup>Město</label>
				<?=$form->input('Address.1.city', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
		<div class="col-xs-4 col-md-2">
    		<div class="form-group">
    			<label><sup>*</sup>PSČ</label>
				<?=$form->input('Address.1.zip', array('label' => false, 'div' => false, 'class' => 'form-control'))?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="form-group">
				<label><sup>*</sup>Stát</label>
				<?=$form->input('Address.1.state', array('label' => false, 'div' => false, 'type' => 'select', 'options' => array('Česká republika' => 'Česká republika'), 'class' => 'form-control'))?>
			</div>
		</div>
	</div>		
</fieldset>

<?php 
	echo $this->Form->hidden('Customer.id');
	echo $this->Form->hidden('Customer.newsletter', array('value' => true));
	echo $this->Form->hidden('Customer.customer_type_id', array('value' => 1));
	echo $this->Form->hidden('Customer.active', array('value' => true));

	echo $this->Form->hidden('Address.0.type', array('value' => (isset($customer['Address'][0]['type']) ? $customer['Address'][0]['type'] : 'd')));
	echo $this->Form->hidden('Address.1.type', array('value' => (isset($customer['Address'][1]['type']) ? $customer['Address'][1]['type'] : 'f')));
	
	echo $this->Form->hidden('Order.action', array('value' => 'order_finish'));

	echo $this->Form->submit('Objednat', array('style' => 'float:right', 'class' => 'btn btn-primary'));
	echo $this->Form->end();
?>
<? } ?>
</div>