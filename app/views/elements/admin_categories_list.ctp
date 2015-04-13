<?
/**
	menu pro administraci
*/
?>
<ul id="categoriesMenu">
	<li><a href="/admin/categories/index">KATALOG</a></li>
	<li>
		<ul id="menu">
<?			foreach ($categories as $category) { ?>
			<li><?php
					echo (isset($opened_category_id) && in_array($category['Category']['id'], $path_ids) ? '<strong>' : '');
					echo $this->Html->link($category['Category']['name'], array('controller' => 'categories', 'action' => 'list_products', $category['Category']['id']));
					echo '(' . $category['Category']['activeProductCount'] . '/' . $category['Category']['productCount'] . ')';
					echo (isset($opened_category_id) && in_array($category['Category']['id'], $path_ids) ? '</strong>&nbsp;&nbsp;<a href="/admin/categories/view/' . $category['Category']['id'] . '">&gt;&gt;&gt;</a>' : '');
					if (!empty($category['children'])) { ?>
				<ul>
<?					foreach ($category['children'] as $subcategory) { ?>
					<li><?php
						echo (isset($opened_category_id) && in_array($subcategory['Category']['id'], $path_ids) ? '<strong>' : '');
						echo $this->Html->link($subcategory['Category']['name'], array('controller' => 'categories', 'action' => 'list_products', $subcategory['Category']['id']));
						echo '(' . $subcategory['Category']['activeProductCount'] . '/' . $subcategory['Category']['productCount'] . ')';
						echo (isset($opened_category_id) && in_array($subcategory['Category']['id'], $path_ids) ? '</strong>&nbsp;&nbsp;<a href="/admin/categories/view/' . $subcategory['Category']['id'] . '">&gt;&gt;&gt;</a>' : '');
						if (!empty($subcategory['children'])) { ?>
						<ul>
<?php		 				foreach ($subcategory['children'] as $subsubcategory) { ?>
							<li><?php
								echo (isset($opened_category_id) && in_array($subsubcategory['Category']['id'], $path_ids) ? '<strong>' : '');
								echo $this->Html->link($subsubcategory['Category']['name'], array('controller' => 'categories', 'action' => 'list_products', $subsubcategory['Category']['id']));
								echo '(' . $subsubcategory['Category']['activeProductCount'] . '/' . $subsubcategory['Category']['productCount'] . ')';
								echo (isset($opened_category_id) && in_array($subsubcategory['Category']['id'], $path_ids) ? '</strong>&nbsp;&nbsp;<a href="/admin/categories/view/' . $subsubcategory['Category']['id'] . '">&gt;&gt;&gt;</a>' : '');
								if (!empty($subsubcategory['children'])) { ?>
								<ul>
									<?php foreach ($subsubcategory['children'] as $leaf_category) { ?>
									<li><?php
										echo (isset($opened_category_id) && in_array($leaf_category['Category']['id'], $path_ids) ? '<strong>' : '');
										echo $this->Html->link($leaf_category['Category']['name'], array('controller' => 'categories', 'action' => 'list_products', $leaf_category['Category']['id']));
										echo '(' . $leaf_category['Category']['activeProductCount'] . '/' . $leaf_category['Category']['productCount'] . ')';
										echo (isset($opened_category_id) && in_array($leaf_category['Category']['id'], $path_ids) ? '</strong>&nbsp;&nbsp;<a href="/admin/categories/view/' . $leaf_category['Category']['id'] . '">&gt;&gt;&gt;</a>' : ''); ?>
									</li>
									<?php } ?>
								</ul>
								<?php }?>
							</li>
<?php		 				} ?>
						</ul>
<?php					} ?>
					</li>
<?		 			} ?>
				</ul>
<?				} ?>
			</li>
<?			} ?>
		</ul>
	</li>
</ul>