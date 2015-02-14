<div class="shopping-info-login-box">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#basketinfo" role="tab" data-toggle="tab">Nákupní košík</a></li>
        <li><a href="#loginform" role="tab" data-toggle="tab">Přihlášení</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="basketinfo">
        	<?php if ($carts_stats['products_count']) { ?>
				Celková cena: <strong><?php echo number_format($carts_stats['total_price'], 0, ',', ' ')?> Kč</strong>
			<?php } else { ?>
				Košík je prázdný.
			<?php } ?>
        	<a class="btn btn-default btn-sm" href="/kosik">Do košíku</a>
        </div>
        <div class="tab-pane" id="loginform">
<?php	
		$is_logged_in = false;
		if ($this->Session->check('Customer')) {
			$customer = $this->Session->read('Customer');
			if (isset($customer['id']) && !empty($customer['id']) && !isset($customer['noreg'])) {
				$is_logged_in = true;
			}
		}
		if ($is_logged_in) {
			$customer = $this->Session->read('Customer'); ?>
			Jste přihlášen jako <strong><?php echo $customer['first_name']?> <?php echo $customer['last_name']?></strong>.<br/>
            <ul class="loginform-links">
                <li>
                    <a href="/customers">Zákaznická sekce</a>
                </li>
                <li>
                    <a href="/customers/logout">Odhlásit se</a>
                </li>
            </ul>	
<?php } else { ?> 
            <form id="login_form_top" method="post" action="/customers/login" class="form-inline">
                <input type="hidden" name="_method" value="POST" />
                <div class="form-group">
                    <input name="data[Customer][login]" type="text" class="form-control" id="loginUsername" placeholder="Login" maxlength="100" />
                </div>
                <div class="form-group">
                    <input type="password" name="data[Customer][password]" class="form-control" id="loginPassword" placeholder="Heslo" />
                </div>
                <input type="hidden" name="data[Customer][backtrace_url]" value="/" id="CustomerBacktraceUrl" />
                
                <input class="btn btn-primary" type="submit" value="OK" />
            </form>
            <ul class="loginform-links">
                <li>
                    <a href="/obnova-hesla">Zapomenuté heslo</a>
                </li>
                <li>
                    <a href="/registrace">Nová registrace</a>
                </li>
            </ul>		
<?php }?>
        </div>
    </div>
</div>