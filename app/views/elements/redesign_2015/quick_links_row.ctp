  <div class="quick-links row">
     <ul>
         <li class="hidden-xs hidden-sm hidden-md<?php echo ($_SERVER['REQUEST_URI'] == '/' ? ' selected' : '') ?>"><a href="/">Domů</a></li>
         <li class="hidden-xs hidden-sm<?php echo ($_SERVER['REQUEST_URI'] == '/novinky' ? ' selected' : '') ?>"><a href="/novinky">Novinky</a></li>
         <li<?php echo ($_SERVER['REQUEST_URI'] == '/prodejna' ? ' class="selected"' : '') ?>><a href="/prodejna">Kamenná prodejna</a></li>
         <li<?php echo ($_SERVER['REQUEST_URI'] == '/vse-o-nakupu' ? ' class="selected"' : '') ?>><a href="/vse-o-nakupu">Vše o nákupu</a></li>
         <li<?php echo ($_SERVER['REQUEST_URI'] == '/kontakty' ? ' class="selected"' : '') ?>><a href="/kontakty">Kontakty</a></li>
     </ul>
 </div>
 <div class="quick-contact row">
     <span class="phone"><i class="fa fa-phone"></i><strong>Zákaznická linka</strong> (+420) 778 437 811</span>
     <span class="email hidden-xs hidden-sm"><i class="fa fa-envelope"></i><strong>info@lekarnatypos.cz</strong></span>
 </div>