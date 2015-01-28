<ul class="categories">
	<?
	foreach ( $categories as $category ){
		$link_options = array('title' => $category['Category']['name'], 'escape' => false);
		$lvl1_active = '';
		if (in_array($category['Category']['id'], $path_ids)) {
			$lvl1_active = ' class="active"';
		}
	?>
	<li<?php echo $lvl1_active?>><?php
		echo $this->Html->link(mb_strtoupper($category['Category']['name'], 'utf-8'), '/' . $category['Category']['url'], $link_options);
		if (!empty($category['children'])) { ?>
		<ul>
	<?php 	foreach ($category['children'] as $child) {
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
						echo $this->Html->link($anchor, '/' . $grandchild['Category']['url'], $link_options);
						if (!empty($grandchild['children'])) { ?>
						<ul>
<?php 						foreach ($grandchild['children'] as $g_grandchild) {
								$link_options = array('title' => $g_grandchild['Category']['name'], 'escape' => false);
								$lvl4_active = '';
								if (in_array($g_grandchild['Category']['id'], $path_ids)) {
									$lvl4_active = ' class="selected"';
								} ?>
							<li<?php echo $lvl4_active?>><?php
								$anchor = '<i class="fa fa-caret-right"></i>' . mb_strtoupper($g_grandchild['Category']['name'], 'utf-8');
								echo $this->Html->link($anchor, '/' . $g_grandchild['Category']['url'], $link_options);
								if (!empty($g_grandchild['children'])) { ?>
								<ul>
<?php		 						foreach ($g_grandchild['children'] as $gg_grandchild) {
										$link_options = array('title' => $gg_grandchild['Category']['name'], 'escape' => false);
										$lvl5_active = '';
										if (in_array($gg_grandchild['Category']['id'], $path_ids)) {
											$lvl5_active = ' class="selected"';
										} ?>
									<li<?php echo $lvl5_active?>><?php
										$anchor = '<i class="fa fa-caret-right"></i>' . mb_strtoupper($gg_grandchild['Category']['name'], 'utf-8');
										echo $this->Html->link($anchor, '/' . $gg_grandchild['Category']['url'], $link_options);?>
									</li>
<?php 								} ?>
								</ul>
<?php 							} ?>
							</li>
<?php 						} ?>
						</ul>
						<?php } ?>
					</li>
	<?php 			}?>
				</ul>
	<?php		}?>	
			</li>
	<?php 	} ?>
		</ul>
	<?php } ?>
	</li>
	<?php } ?>
</ul>