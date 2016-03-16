<h1>Slevové kupóny</h1>
<?php echo $this->element(REDESIGN_PATH . 'search_forms/discount_coupons')?>

<table class="tabulka">
	<tr>
		<th>&nbsp;</th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('ID', 'DiscountCoupon.id') : 'ID')?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Název', 'DiscountCoupon.name') : 'Název') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Hodnota', 'DiscountCoupon.value') : 'Hodnota') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Min. objednávka', 'DiscountCoupon.min_amount') : 'Min. objednávka') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Platnost', 'DiscountCoupon.valid_until') : 'Platnost') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Zákazník', 'DiscountCoupon.customer_name') : 'Zákazník') ?></th>
		<th><?php echo (isset($this->Paginator) ? $this->Paginator->sort('Objednávka', 'DiscountCoupon.order_id') : 'Objednávka') ?></th>
	</tr>
	<tr>
		<td colspan="2"><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/add.png" alt=""/>';
			echo $this->Html->link($icon, array('controller' => 'discount_coupons', 'action' => 'add'), array('escape' => false, 'title' => 'Přidat kupón'));
		?></td>
		<td colspan="5">&nbsp;</td>
	</tr>
	<?php foreach ($coupons as $coupon) { ?>
	<tr>
		<td><?php 
			$icon = '<img src="/images/' . REDESIGN_PATH . 'icons/delete.png" alt="" />';
			echo  $this->Html->link($icon, array('controller' => 'discount_coupons', 'action' => 'delete', $coupon['DiscountCoupon']['id']), array('escape' => false), 'Opravdu chcete kupón odstranit?');
		?></td>
		<td><?php echo $coupon['DiscountCoupon']['id']?></td>
		<td><?php echo $this->Html->link($coupon['DiscountCoupon']['name'], array('controller' => 'discount_coupons', 'action' => 'view', $coupon['DiscountCoupon']['id']))?></td>
		<td align="right"><?php echo $coupon['DiscountCoupon']['value']?></td>
		<td align="right"><?php echo $coupon['DiscountCoupon']['min_amount']?></td>
		<td><?php echo ($coupon['DiscountCoupon']['valid_until'] ? cz_date($coupon['DiscountCoupon']['valid_until'], '.') : '')?></td>
		<td><?php echo ($coupon['DiscountCoupon']['customer_id'] ? $this->Html->link($coupon['DiscountCoupon']['customer_name'], array('controller' => 'customers', 'action' => 'view', $coupon['DiscountCoupon']['customer_id'])) : '')?></td>
		<td><?php echo ($coupon['DiscountCoupon']['order_id'] ? $this->Html->link($coupon['DiscountCoupon']['order_id'], array('controller' => 'orders', 'action' => 'view', $coupon['DiscountCoupon']['order_id'])) : '')?></td>
	</tr>
	<?php } ?>
</table>
<div class="paging">
<?
	echo $this->Paginator->prev('<< Předchozí', array(), '<< Předchozí');
	echo '&nbsp;&nbsp;' . $this->Paginator->numbers() . '&nbsp;&nbsp;';
	echo $this->Paginator->next('Další >>', array(), 'Další >>');
?>
</div>
<div class="prazdny"></div>