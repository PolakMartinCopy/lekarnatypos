<h1><?php echo $page_heading?></h1>
<table class="table">
	<tr>
		<th style="text-align:right">číslo</th>
		<th style="text-align:right">vytvořena</th>
		<th style="text-align:right">cena</th>
		<th style="text-align:right">stav</th>
		<th style="text-align:right">&nbsp;</th>
	</tr>
<? foreach ( $orders as $order ){ ?>
	<tr>
		<td align="right"><?=$order['Order']['id']?></td>
		<td align="right"><?=cz_date_time($order['Order']['created'], '.')?></td>
		<td align="right"><?=front_end_display_price($order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost']) . '&nbsp;Kč' ?></td>
		<td align="right"><?
				$color = '';
				if ( !empty($order['Status']['color']) ){
					$color = ' style="color:#' . $order['Status']['color'] . '"';
				}
				echo '<span' . $color . '>' . $order['Status']['name'] . '</span>';
			?>
		</td>
		<td align="right">
			<?=$html->link('detaily', array('controller' => 'customers', 'action' => 'order_detail', $order['Order']['id']));?>
		</td>
	</tr>
<? } ?>
</table>