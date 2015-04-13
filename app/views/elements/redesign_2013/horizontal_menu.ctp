<?php if (isset($categories_submenu) && !empty($categories_submenu)) { ?>
<ul class="menu">
	<?php foreach ($categories_submenu as $category) { ?>
	<li><a href="/<?php echo $category['Category']['url']?>"><?php echo ucfirst(mb_convert_case($category['Category']['name'], MB_CASE_LOWER))?></a></li>
	<?php } ?>
	<li class="phone"><?php echo CUST_PHONE?></li>
	<li class="mail"><a href="mailto:<?php echo CUST_MAIL ?>"><?php echo CUST_MAIL?></a></li>
</ul>
<?php } ?>