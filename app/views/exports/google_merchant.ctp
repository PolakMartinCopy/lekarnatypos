<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
	<channel>
	<? foreach ($products as $product) {
		$id = 'CZ_' . $product['Product']['id'];
		$title = $product['Product']['name'];
		$description = $product['Product']['short_description'];
		$google_product_category = $product['CategoriesComparator']['path'];
		$link = 'http://www.lekarnatypos.cz/' . $product['Product']['url'];
		$image_link = 'http://www.lekarnatypos.cz/product-images/' . (empty($product['Image']['name']) ? '' : str_replace(" ", "%20", $product['Image']['name']));
		$price = $product['Product']['price'];
		$brand = $product['Manufacturer']['name'];
		$product_type = $product['Product']['type_text'];
		$ean = $product['Product']['ean'];
		$item_group_id = null;
		if (isset($product['Subproduct']) && !empty($product['Subproduct'])) {
			$item_id = $id;
			$item_title = $title;
			$item_price = $price;
			foreach ($product['Subproduct'] as $subproduct) {
				$item_group_id = $item_id;
				$id = $item_id . '_' . $subproduct['id'];
				$title = $item_title . ', ' . $subproduct['name'];
				$price = $item_price + $subproduct['price_with_dph'];
				echo $this->element(REDESIGN_PATH . 'google_merchant_item', compact('id', 'title', 'description', 'google_product_category', 'link', 'image_link', 'price', 'brand', 'product_type', 'ean', 'item_group_id'));
			}
		} else {
			echo $this->element(REDESIGN_PATH . 'google_merchant_item', compact('id', 'title', 'description', 'google_product_category', 'link', 'image_link', 'price', 'brand', 'product_type', 'ean', 'item_group_id'));
		}
	} ?>
	</channel>
</rss>