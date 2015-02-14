<div class="mainContentWrapper">
	<p>Na této stránce můžete zkontrolovat obsah Vašeho nákupního košíku.<br />
	Chcete-li dokončit objednávku a <a href="/orders/add">zaplatit</a>, klikněte <a href="/orders/add">zde</a>.</p>

	<h2>Seznam produktů v nákupním košíku</h2>
<?php	if ( empty($cart_products) ){ ?>
		<p>V košíku nemáte žádné zboží.</p>
<?php 	} else { ?>
		<table id="cartContents" cellpadding="0" cellspacing="0" class="topHeading">
			<tr>
				<th style="width:50%">Název produktu</th>
				<th style="width:20%">Množství</th>
				<th style="width:10%">Cena za kus</th>
				<th style="width:10%">Cena celkem</th>
				<th style="width:10%">&nbsp;</th>
			</tr>
<?php 		$final_price = 0;
			foreach ( $cart_products as $cart_product ){
				$final_price = $final_price + $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity']; ?>
			<tr>
				<td>
					<a href="/<?php echo $cart_product['Product']['url']?>"><?php echo $cart_product['Product']['name']?></a>
<?php 			if ( !empty($cart_product['CartsProduct']['product_attributes']) ){ ?>
					<br />
					<div style="font-size:11px;padding-left:20px;">
<?php 				foreach ( $cart_product['CartsProduct']['product_attributes'] as $option => $value ){ ?>
						- <strong><?php echo $option ?></strong>: <?php echo $value ?><br />
<?php 				} ?>
					</div>
<?php 			} ?>
				</td>
				<td>
					<div class="input-group">
<?php 				echo $this->Form->Create('CartsProduct', array('url' => array('action' => 'edit', $cart_product['CartsProduct']['id'])));
					echo $this->Form->hidden('CartsProduct.id', array('value' => $cart_product['CartsProduct']['id']));
					echo $this->Form->input('CartsProduct.quantity', array('label' => false, 'size' => 1, 'value' => $cart_product['CartsProduct']['quantity'], 'type' => 'text', 'class' => 'form-control input-lg', 'div' => false, 'style' => 'width:50px'));
					echo $this->Form->Submit('Upravit', array('class' => 'changeAmount', 'class' => 'btn btn-success btn-lg', 'div' => false));
					echo $this->Form->end(); ?>
					</div>
				</td>
				<td align="right"><?php echo intval($cart_product['CartsProduct']['price_with_dph']) ?>&nbsp;Kč</td>
				<td align="right"><?php echo intval( $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'] ) ?>&nbsp;Kč</td>
				<td align="right">
					<a title="odstranit z košíku" href="/carts_products/delete/<?php echo $cart_product['CartsProduct']['id'] ?>">smazat</a>
				</td>
			</tr>
<?php 		} ?>
			<tr>
				<th colspan="2" align="right">cena za zboží celkem:</td>
				<td colspan="2" align="right"><strong><?php echo intval($final_price) ?> Kč</strong></td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<a id="orderAndPay" href="/orders/add" style="float:right">Přejít k pokladně >></a>
<?php	} ?>
</div>