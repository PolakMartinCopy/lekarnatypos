<?php $path_ids = $categories['path_ids']; ?>
<ul class="categories">
	<? foreach ($categories['categories'] as $category) {
		$link_options = array('title' => $category['Category']['name'], 'escape' => false);
		$lvl1_active = '';
		if (in_array($category['Category']['id'], $path_ids)) {
			$lvl1_active = ' class="selected"';
		} ?>
    <li<?php echo $lvl1_active?> id="cat-<?php echo $category['Category']['id']?>">
    	<?php echo $this->Html->link($category['Category']['name'], '/' . $category['Category']['url'], $link_options);?>
		<?php if (!empty($category['children'])) { ?>
        <ul>
        	<?php foreach ($category['children'] as $child) {
				$link_options = array('title' => $child['Category']['name'], 'escape' => false);
				$lvl2_active = '';
				if (in_array($child['Category']['id'], $path_ids)) {
					$lvl2_active = ' class="selected"';
				} ?>
            <li<?php echo $lvl2_active?> id="cat-<?php echo $child['Category']['id']?>">
            	<?php echo $this->Html->link($child['Category']['name'], '/' . $child['Category']['url'], $link_options);?>
            	<?php if (!empty($child['children'])) { ?>
            	<ul>
            	   <?php foreach ($child['children'] as $gChild) {
						$link_options = array('title' => $gChild['Category']['name'], 'escape' => false);
						$lvl3_active = '';
						if (in_array($gChild['Category']['id'], $path_ids)) {
							$lvl3_active = ' class="selected"';
						} ?>
					<li<?php echo $lvl3_active?> id="cat-<?php echo $gChild['Category']['id']?>">
					<?php echo $this->Html->link($gChild['Category']['name'], '/' . $gChild['Category']['url'], $link_options);?>
					<?php } ?>
            	</ul>
            	<?php } ?>
            </li>
			<?php } ?>
        </ul>
        <?php } ?>
    </li>
    <?php } ?>
</ul>