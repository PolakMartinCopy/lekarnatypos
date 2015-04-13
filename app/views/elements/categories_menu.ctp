<ul><?
	foreach ( $categories as $category ){
		// nastavim si isActive
		$isActive = false;
		if ( $category['Category']['id'] == $opened_category_id ){
			$isActive = true;
		}

		$spaces = 10;
		$offset = array_search($category['Category']['parent_id'], $ids_to_find);
		for ( $i = 0; $i < $offset; $i++ ){
			$spaces += 4;
		}

		$padding = '';
		$spacer = '';
		if ( $spaces != 10 ){
			$padding = ' style="padding-left:' . $spaces . 'px"';
			$spacer = '-&nbsp;';
		}
		
		$class = 'menuItem';
		if (strlen($category['Category']['name']) > 30) {
			$class = 'menuItem2Lines';
		}
		echo '<li><a class="' . $class . '"' . $padding . ' href="/' . $category['Category']['url'] . '">'; // otevru si list item
		
		
		// test jestli to ma byt v tucnem
		if ( $isActive ){
			echo '<strong id="activeMenuItem">';
		}
		
		// vypisu obsah list itemu
		echo '' . $spacer . '' . $category['Category']['name'] . '';

		// ukonceni tucneho pisma, pokud je zacato
		if ( $isActive ){
			echo '</strong>';
		}
		
		echo '</a></li>'; // ukonceni list item
	}
?></ul>				

