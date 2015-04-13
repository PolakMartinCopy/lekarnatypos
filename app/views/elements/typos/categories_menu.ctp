<div id="categoriesMenu">
	<ul class="categories">
		<? foreach ($categories as $category) {
			$link_options = array('title' => $category['Category']['name'], 'escape' => false);
			$lvl1_active = '';
			if (in_array($category['Category']['id'], $path_ids)) {
				$lvl1_active = ' class="active"';
			} ?>
		<li<?php echo $lvl1_active?> id="cat-<?php echo $category['Category']['id'] ?>"><?php
			echo $this->Html->link(mb_strtoupper($category['Category']['name'], 'utf-8'), '/' . $category['Category']['url'], $link_options);
			if (!empty($category['children'])) { ?>
			<div class="subcategories-box">
				<?php foreach ($category['children'] as $child) { ?>
				<div class="subcategories-box-subcategory">
					<div class="subcategories-box-header"><a href="/<?php echo $child['Category']['url']?>"><?php echo $child['Category']['name']?></a></div>
					<?php if (!empty($child['children'])) { ?>
					<div class="subcategories-box-categories-list">
						<ul>
							<?php foreach ($child['children'] as $gChild) { ?>
							<li><a href="/<?php echo $gChild['Category']['url']?>"><?php echo $gChild['Category']['name']?></a></li>
							<?php } ?>
						</ul>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
			
			<?php }
			
			if (isset($category['subtree']) && !empty($category['subtree'])) { ?> 
			<ul>
<?php		foreach ($category['subtree'] as $child) {
				$link_options = array('title' => $child['Category']['name'], 'escape' => false);
				$lvl2_active = '';
				if (in_array($child['Category']['id'], $path_ids)) {
					$lvl2_active = ' class="active"';
				} ?>
			<li<?php echo $lvl2_active?>><?php
				$anchor = '<i class="fa fa-caret-right"></i>' . mb_strtoupper($child['Category']['name'], 'utf-8');
				echo $this->Html->link($anchor, '/' . $child['Category']['url'], $link_options);
				if (!empty($child['children'])) { ?>
				<ul>
	<?php 			foreach ($child['children'] as $grandchild) {
						$link_options = array('title' => $grandchild['Category']['name'], 'escape' => false);
						$lvl3_active = '';
						if (in_array($grandchild['Category']['id'], $path_ids)) {
							$lvl3_active = ' class="selected"';
						} ?>
					<li<?php echo $lvl3_active?>><?php
						$anchor = '<i class="fa fa-caret-right"></i>' . mb_strtoupper($grandchild['Category']['name'], 'utf-8');
						echo $this->Html->link($anchor, '/' . $grandchild['Category']['url'], $link_options); ?>
					</li>
<?php	 			} ?>
				</ul>
<?php			}?>
			</li>
<?php	 	} ?>
			</ul>
			<?php } ?>
<?php } //end if false?>
		</li>
	</ul>
</div>
<!-- 
<div id="categoriesMenu">
	<ul class="categories">
		<li><a href="#">Kategorie 1</a></li>
		<li>
			<a href="#">Kategorie 2</a>
			<div class="subcategories-box">
				<ul>
					<li><a href="#">Podkategorie 1</a></li>
					<li><a href="#">Podkategorie 2</a></li>
					<li><a href="#">Podkategorie 3</a></li>
				</ul>
			</div>
		</li>
		<li><a href="#">Kategorie 3</a></li>
		<li><a href="#">Kategorie 4</a>
			<div class="subcategories-box">
				<ul>
					<li><a href="#">Podkategorie 1</a></li>
					<li><a href="#">Podkategorie 2</a></li>
					<li><a href="#">Podkategorie 3</a></li>
				</ul>
			</div>	
		</li>
		<li><a href="#">Kategorie 5</a></li>
	</ul>
</div>
 -->