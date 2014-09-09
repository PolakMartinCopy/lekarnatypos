<ul>
    <li<?php echo ($_SERVER['REQUEST_URI'] == '/' ? ' class="selected"' : '') ?>><a href="/">Domů</a></li>
    <li<?php echo ($_SERVER['REQUEST_URI'] == '/novinky' ? ' class="selected"' : '') ?>><a href="/novinky">Novinky</a></li>
    <li<?php echo ($_SERVER['REQUEST_URI'] == '/prodejna' ? ' class="selected"' : '') ?>><a href="/prodejna">Kammená prodejna</a></li>
    <li<?php echo ($_SERVER['REQUEST_URI'] == '/vse-o-nakupu' ? ' class="selected"' : '') ?>><a href="/vse-o-nakupu">Vše o nákupu</a></li>
    <li<?php echo ($_SERVER['REQUEST_URI'] == '/kontakty' ? ' class="selected"' : '') ?>><a href="/kontakty">Kontakty</a></li>
</ul>