<?
class SitemapsController extends AppController{
	var $name = 'Sitemaps';
	
	function admin_generate(){
		// otevrit soubor pro zapis, s vymazanim obsahu
		$fp = fopen('sitemap.xml', 'w');

	$start_string = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
    		<loc>http://www.' . CUST_ROOT . '/</loc>
    		<changefreq>daily</changefreq>
    		<priority>1</priority>
	</url>';

		fwrite($fp, $start_string);

		// projdu vsechny produkty
		App::import('Model', 'Product');
		$this->Sitemap->Product = new Product;
		
		$products = $this->Sitemap->Product->find('all', array(
			'conditions' => array('Product.active' => true),
			'contain' => array(),
			'fields' => array('Product.url', 'Product.modified')
		));

		foreach ( $products as $product ){
			// pripnout k sitemape
			$mod = explode(' ', $product['Product']['modified']);
			$mod = $mod[0];
			$string = '
	<url>
    		<loc>http://www.' . CUST_ROOT . '/' . $product['Product']['url'] . '</loc>
    		<lastmod>' . $mod . '</lastmod>
    		<changefreq>weekly</changefreq>
    		<priority>0.9</priority>
	</url>';  

			fwrite($fp, $string);
		}
		
		// projdu vsechny kategorie
		App::import('Model', 'Category');
		$this->Sitemap->Category = new Category;
		
		// nechci kategorie, ktere jsou v podstromu neaktivnich
		$category_conditions = array('Category.public' => true);
		$unactive_categories = $this->Sitemap->Category->find('all', array(
			'conditions' => array('Category.active' => false),
			'contain' => array(),
			'fields' => array('Category.id')
		));

		$unactive_categories_ids = array(ROOT_CATEGORY_ID);
		foreach ($unactive_categories as $unactive_category) {
			$subtree_ids = $this->Sitemap->Category->subtree_ids($unactive_category['Category']['id']);
			$unactive_categories_ids = array_merge($unactive_categories_ids, $subtree_ids);
		}
		if (!empty($unactive_categories_ids)) {
			$category_conditions[] = 'Category.id NOT IN (' . implode(',', $unactive_categories_ids) . ')';
		}

		$categories = $this->Sitemap->Category->find('all', array(
			'conditions' => $category_conditions,
			'contain' => array(),
			'fields' => array('Category.id', 'Category.url')
		));

		foreach ( $categories as $category ){
			$mod = date('Y-m-d');

			// pripnout k sitemape
			$string = '
	<url>
    		<loc>http://www.' . CUST_ROOT . '/' . $category['Category']['url'] . '</loc>
    		<changefreq>weekly</changefreq>
    		<priority>0.8</priority>
	</url>';  

			fwrite($fp, $string);
			
		}
		
/* 		// projdu vsechny vyrobce
		App::import('Model', 'Manufacturer');
		$this->Sitemap->Manufacturer = new Manufacturer;
		
		$manufacturers = $this->Sitemap->Manufacturer->find('all', array(
			'contain' => array(),
			'fields' => array('Manufacturer.id', 'Manufacturer.name')
		));
		
		foreach ( $manufacturers as $manufacturer ){
			// pripnout k sitemape
			// vytvorim si url z name a id
			$string = '
	<url>
    		<loc>http://www.' . CUST_ROOT . '/' . strip_diacritic($manufacturer['Manufacturer']['name']) . '-v' . $manufacturer['Manufacturer']['id'] . '</loc>
    		<changefreq>weekly</changefreq>
    		<priority>0.8</priority>
	</url>';
			fwrite($fp, $string);
		} */
		
		// projdu vsechny obsahove stranky
		App::import('Model', 'Content');
		$this->Sitemap->Content = new Content;
		
		$contents = $this->Sitemap->Content->find('all', array(
			'contain' => array(),
			'fields' => array('Content.path')
		));
		
		foreach ( $contents as $content ){
			// pripnout k sitemape
			if ( $content['Content']['path'] == 'index' ){
				continue;
			}
			$string = '
	<url>
    		<loc>http://www.' . CUST_ROOT . '/' . $content['Content']['path'] . '</loc>
    		<changefreq>weekly</changefreq>
    		<priority>0.7</priority>
	</url>';
			fwrite($fp, $string);
		}
		
		$end_string = '
</urlset>';
		fwrite($fp, $end_string);
		fclose($fp);
		// uzavrit soubor
		die('here');
	}
}
?>