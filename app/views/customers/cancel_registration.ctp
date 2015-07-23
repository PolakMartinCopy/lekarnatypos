<h1>Zrušení registrace</h1>
<?php if ($verify) { ?>
<?php if ($delete_success) { ?>
<p>Vaše registrace byla úspěšně zrušena.</p>
<?php } else { ?>
<p>Omlouváme se, ale bohužel se nepodařilo zrušit Vaši registraci. Prosím kontaktujte nás s tímto požadavkem na naší emailové adrese <a href="mailto:<?php echo CUST_MAIL?>"><?php echo CUST_MAIL?></a> nebo na telefonním čísle <?php echo CUST_PHONE?>.</p>
<p>S pozdravem tým online obchodu <?php echo CUST_NAME?>.</p>
<?php } ?>
<?php } else { ?>
<p>Omlouváme se, ale bohužel se nepodařilo ověřit Váš požadavek na zrušení registrace. Prosím kontaktujte nás s tímto požadavkem na naší emailové adrese <a href="mailto:<?php echo CUST_MAIL?>"><?php echo CUST_MAIL?></a> nebo na telefonním čísle <?php echo CUST_PHONE?>.</p>
<p>S pozdravem tým online obchodu <?php echo CUST_NAME?>.</p>
<?php } ?>