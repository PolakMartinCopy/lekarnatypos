﻿<script type="text/javascript">
$(function() {
	// Handler for .ready() called.
	$('.status_change_submit').click(function(e) {
		e.preventDefault();
		// zjistim sourozence
		var siblings = $(this).siblings();
		// na poslednim miste mezi sourozenci MUSIM MIT element, ve kterem si predavam index
		var position = siblings.last().val();

		var statusId = $('#Order' + position + 'StatusId option:selected').val();
		var id = $('#Order' + position + 'Id').val();
		var variableSymbol = $('#Order' + position + 'VariableSymbol').val();
		var shippingNumber = $('#Order' + position + 'ShippingNumber').val();

		var logged = true;
		$.ajax({
			url: '/customers/check_admin_logged',
			type: 'post',
			dataType: 'json',
			async: false,
			success: function(data) {
				if (data.message) {
					alert(data.message);
					logged = false;
					window.location.href = '/<?php echo $this->params['url']['url']?>';
				} 
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(textStatus);
			} 
		})

		if (logged) {
			$.ajax({
				url: '/admin/orders/edit_status',
				type: 'post',
				dataType: 'json',
				data: {
					id: id,
					statusId: statusId,
					variableSymbol: variableSymbol,
					shippingNumber: shippingNumber
				},
				success: function(data) {
					alert(data.message);
	
					location.reload(); 
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(textStatus);
				}
			});
		}
	});
});
</script>

<h1><?php echo (isset($status) ? 'Objednávky ' . $status['Status']['name'] : ($status_id == 'active' ? 'Seznam přijatých objednávek' : 'Seznam všech objednávek'))?></h1>
<a href='/administrace/help.php?width=500&id=1' class='jTip' id='1' name='Objednávky (1)'>
	<img src='/images/<?php echo REDESIGN_PATH?>icons/help.png' width='16' height='16' />
</a>
<br/>
<? 
	if (isset($statuses)) {
		foreach ($statuses as $status) {
			$anchor = '<span style="font-size:11px;color:#' . $status['Status']['color'] . '">' . $status['Status']['name'] . ' (' . $status['Status']['count'] . ')</span>';
			echo $this->Html->link($anchor, array('controller' => 'orders', 'action' => 'index', 'status_id' => $status['Status']['id']), array('escape' => false), false) . '&nbsp;|&nbsp;';
		}
	}
	$this->Paginator->options(array('url' => $this->passedArgs));
	echo $this->Paginator->counter(array(
		'format' => '<p>Strana <strong>%page%</strong> z <strong>%pages%</strong> stran celkem, zobrazuji %current% objednávek z %count% objednávek celkem (' . format_price($total_vat) . ').</p>'
	));
?>
<?php echo $this->element(REDESIGN_PATH . 'search_forms/orders')?>
<div class="paging">
<?
	echo $this->Paginator->prev('<< Předchozí', array(), '<< Předchozí');
	echo '&nbsp;&nbsp;' . $this->Paginator->numbers() . '&nbsp;&nbsp;';
	echo $this->Paginator->next('Další >>', array(), 'Další >>');
?>
</div>
<?php echo $this->Form->create('Order', array('url' => array('action' => 'pohoda_view'))) ?>
<table class='tabulka' width='100%'>
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th><?=$this->Paginator->sort('ID', 'Order.id')?></th>
		<th><?=$this->Paginator->sort('Datum', 'Order.created')?></th>
		<th><?=$this->Paginator->sort('Odběratel', 'Order.customer_last_name')?></th>
		<th>Položky</th>
		<th><?=$this->Paginator->sort('Cena', 'Order.total_price')?></th>
		<th>&nbsp;</th>
	</tr>
<? foreach ($orders as $index => $order) { ?>
	<tr>
		<td><?php echo $this->Form->input('Order.' . $order['Order']['id'] . '.export', array('label' => false, 'type' => 'checkbox', 'value' => true));?></td>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'orders', 'action' => 'view', $order['Order']['id']), array('escape' => false));
			echo '<br/>';
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/email.png" alt="" />';
			echo $this->Html->link($icon, array('controller' => 'orders', 'action' => 'notify_admin', $order['Order']['id']), array('escape' => false));
		?></td>
		<td id="orderId"><?php 
				echo $this->Html->link($order['Order']['id'], array('action' => 'view', $order['Order']['id']));
				$img = '<img src="/images/' . REDESIGN_PATH . 'icons/printer.png" alt="" />';
				echo '<br/>';
				echo $this->Html->link($img, array('controller' => 'orders', 'action' => 'print', $order['Order']['id']), array('escape' => false));
				echo '<br/>';
				echo $this->Html->link('EFORM', array('controller' => 'orders', 'action' => 'eform_download', $order['Order']['id']));
		?></td>
		<td>
			<?php echo $order['Order']['date'] ?>
			<br/>
			<img src="/images/<?php echo REDESIGN_PATH?>icons/cz.gif" width="18" height="12" alt="" />
		</td>
		<td><?php 
			$customerName = $order['Customer']['first_name'] . ' ' . $order['Customer']['last_name'];
			$customerInfo = $this->Html->link($customerName, array('controller' => 'customers', 'action' => 'view', $order['Customer']['id'])) . ' <strong>(' . $order['Customer']['orders_count'] . ')</strong>';
			if (isset($order['Customer']['CustomerType'])) {
				$customerInfo .= ' - ' . $order['Customer']['CustomerType']['name'] . '';
			}
			echo $customerInfo;	
			echo '<br/>';
			// dorucovaci adresa
			$delivery_address_info = '<br/>';
			// fakturacni adresa
			$invoice_address_info = '<br/>';
			
			if ($order['Order']['shipping_id'] != PERSONAL_PURCHASE_SHIPPING_ID) {
				$delivery_address_info = 'DA: ';
				$delivery_address_info .= $order['Order']['delivery_name'];
				$delivery_name = full_name($order['Order']['delivery_first_name'], $order['Order']['delivery_last_name'], ', ');
				if (!empty($delivery_name) && $delivery_name != $order['Order']['delivery_name']) {
					$delivery_address_info .= ', ' . $delivery_name;
				}
				$delivery_address_info .= ', ' . $order['Order']['delivery_street'];
				if (!empty($order['Order']['delivery_street'])) {
					$delivery_address_info .= ', ';
				}
				$delivery_address_info .= $order['Order']['delivery_zip'];
				if (!empty($order['Order']['delivery_zip'])) {
					$delivery_address_info .= ' ';
				}
				$delivery_address_info .= $order['Order']['delivery_city'];
				if (!empty($delivery_address_info)) {
					$delivery_address_info .= '<br/>';
				}
				$delivery_address_info = '<strong>' . $delivery_address_info . '</strong>';

				$invoice_address_info = '<strong>FA:</strong> ';
				$invoice_address_info .= $order['Order']['customer_name'];
				$invoice_name = full_name($order['Order']['customer_first_name'], $order['Order']['customer_last_name']);
				if (!empty($invoice_name) && $invoice_name != $order['Order']['customer_name']) {
					$invoice_address_info .= ', ' . $invoice_name;
				}
				$invoice_address_info .= ', ' . $order['Order']['customer_street'];
				if (!empty($order['Order']['customer_street'])) {
					$invoice_address_info .= ', ';
				}
				$invoice_address_info .= $order['Order']['customer_zip'];
				if (!empty($order['Order']['customer_zip'])) {
					$invoice_address_info .= ' ';
				}
				$invoice_address_info .= $order['Order']['customer_city'];
				if (!empty($invoice_address_info)) {
					$invoice_address_info .= '<br/>';
				}
			}
			
			echo $delivery_address_info;
			echo $invoice_address_info;
			
			$contact_info = '';
			if (!empty($order['Order']['customer_phone'])) {
				$contact_info .= 'telefon: ' . $order['Order']['customer_phone'] . ', ';
			}
			if (!empty($order['Order']['customer_email'])) {
				$contact_info .= 'email: <a href="mailto:' . $order['Order']['customer_email'] . '">' . $order['Order']['customer_email'] . '</a>';
			}
			if (!empty($contact_info)) {
				$contact_info .= '<br/>';
			}
			echo $contact_info;
			
			if (!empty($order['Order']['comments'])) {
				echo 'Poznámka: <font color="#ff0000">' . $order['Order']['comments'] . '</font><br/>';
			}
?>
			
			Interní poznámka:
			<?php 
			$order_note_add = '<img src="/images/' . REDESIGN_PATH . 'icons/pencil.png"' . ' id="OrderNoteButton"/>';
			echo $this->Html->link($order_note_add, array('controller' => 'ordernotes', 'action' => 'add', 'order_id' => $order['Order']['id'], 'backtrace_url' => base64_encode($_SERVER['REQUEST_URI'])), array('escape' => false));
				
			if (!empty($order['Ordernote'])) { ?>
			<table class="topHeading" style="width:80%">
				<tr>
					<th>datum</th>
					<th>status</th>
					<th>kdo</th>
					<th>poznámka</th>
				</tr>
			<?  foreach ( $order['Ordernote'] as $note ){ ?>
				<tr>
					<td><?php echo $note['created'] ?></td>
					<td><?php echo $note['Status']['name'] ?></td>
					<td><?php echo $note['Administrator']['first_name'] . ' ' . $note['Administrator']['last_name'] ?></td>
					<td><?php echo $note['note'] ?></td>
				</tr>
			<?php } ?>
		</table>
		<?php } ?></td>
		<td>
			<table class="tabulka" style="width:100%">
				<?php foreach ($order['OrderedProduct'] as $ordered_product) {
					if ($ordered_product['show']) { ?>
				<tr>
					<td><?php
						$quantity_info = $ordered_product['product_quantity'];
						if ($ordered_product['product_quantity'] > 1) {
							$quantity_info = '<font color="#ff0000" size="+1"><strong>' . $quantity_info . '</strong></font>';
						}
						echo $quantity_info;
					?>x</td>
					<td style="width:80%"><?php
						$ordered_product_name = $ordered_product['product_name'];
						if (!empty($ordered_product['Product'])) {
							$ordered_product_name = $this->Html->link($ordered_product['product_name'], '/' . $ordered_product['Product']['url'], array('target' => 'blank'));
						}
						$attribute_info = array();
						if (!empty($ordered_product['OrderedProductsAttribute'])) {
							foreach ($ordered_product['OrderedProductsAttribute'] as $attribute) {
								$attribute_info[] = $attribute['Attribute']['Option']['name'] . ': ' . $attribute['Attribute']['value'];
							}
						}
						$attribute_info = implode(', ', $attribute_info);
						if (!empty($attribute_info)) {
							$attribute_info = '<br/>' . $attribute_info;
						}
						echo $ordered_product_name . $attribute_info;
					?></td>
					<td><?php echo round($ordered_product['product_price_wout_dph'], 2)?></td>
					<td><?php echo round($ordered_product['product_price_with_dph'], 2)?></td>
					<td><?php echo round($ordered_product['product_price_with_dph'] * $ordered_product['product_quantity'], 2)?></td>
				</tr>
				<?php } // end if ordered_product show?>
				<?php } ?>
				<?php if (!empty($order['DiscountCoupon']['id'])) { ?>
				<tr>
					<td>1x</td>
					<td>Slevový kupón</td>
					<td>&nbsp;</td>
					<td>-<?php echo round($order['DiscountCoupon']['value'], 2)?></td>
					<td>-<?php echo round($order['DiscountCoupon']['value'], 2)?></td>
				</tr>
				<?php } ?>
				<tr>
					<td>1x</td>
					<td><?php echo $order['Shipping']['name']?></td>
					<td>&nbsp;</td>
					<td><?php echo round($order['Order']['shipping_cost'], 2)?></td>
					<td><?php echo round($order['Order']['shipping_cost'], 2)?></td>
				</tr>
				<tr>
					<td>1x</td>
					<td><?php echo $order['Payment']['name']?></td>
					<td>&nbsp;</td>
					<td>0</td>
					<td>0</td>
				</tr>
			</table>
		</td>
		<td><?php
			if (!$adminIsRestricted) { 
				echo format_price(round($order['Order']['orderfinaltotal'], 2));
				if ($order['Order']['invoice']) {
					echo '<br/><strong>FAKTUROVÁNO</strong>';
				}
			}
		?></td>
		<td><?
			echo $this->Form->input('Order.' . $index . '.status_id', array('label' => false, 'div' => false, 'options' => $statuses_options, 'value' => $order['Order']['status_id']));
			echo $this->Form->hidden('Order.' . $index . '.id', array('value' => $order['Order']['id']));
			echo $this->Form->input('Order.' . $index . '.shipping_number', array('type' => 'text', 'label' => '<abbr title="číslo balíku">ČB</abbr>:&nbsp;'));
			echo $this->Form->input('Order.' . $index . '.variable_symbol', array('type' => 'text', 'label' => '<abbr title="variabilní symbol">VS</abbr>:&nbsp;'));
			echo $this->Form->hidden('Order.' . $index . '.index', array('value' => $index, 'class' => 'OrderIndex', 'id' => false));
			echo $this->Form->button('»', array('div' => false, 'type' => 'submit', 'class' => 'status_change_submit'));
		?></td>
	</tr>
<? } ?>
</table>

<div>
<?
	echo $this->Paginator->prev('<< Předchozí', array(), '<< Předchozí');
	echo '&nbsp;&nbsp;' . $this->Paginator->numbers() . '&nbsp;&nbsp;';
	echo $this->Paginator->next('Další >>', array(), 'Další >>');
?>
</div>
<?php echo $this->Form->hidden('backtrace_url', array('value' => $_SERVER['REQUEST_URI']))?>
<div style="padding:10px">
	<button type="submit">Fakturovat (účetní systém Pohoda)</button>
	<button type="submit" formaction="/admin/orders/geis_export">Export pro Geis</button>
	<button type="submit" formaction="/admin/orders/cpost_export">Export pro ČP</button>
	<button type="submit" formaction="/admin/orders/zasilkovna_export">Export pro Zásilkovnu</button>
</div>
<?php echo $this->Form->end()?>