<div id="search_form_discount_coupons">
<?php echo $this->Form->create('DiscountCoupon')?>
<table class="tabulka">
	<tr>
		<th>Fráze</th>
		<td><?php echo $this->Form->input('AdminDiscountCouponForm.DiscountCoupon.query', array('label' => false, 'type' => 'text', 'size' => 60))?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo $this->Html->link('reset filtru a řazení', array('reset' => 'discount_coupons')) ?></td>
	</tr>
</table>
<?php
	echo $this->Form->submit('Vyhledat', array('div' => false));
	echo $form->hidden('AdminDiscountCouponForm.DiscountCoupon.search_form', array('value' => true));
	echo $form->end();
?>
</div>
<div class="prazdny"></div>