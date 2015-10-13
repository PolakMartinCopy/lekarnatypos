Kategorie;Produkt
<?php
foreach ($supplier['SupplierCategory'] as $supplier_category) {
	foreach ($supplier_category['Product'] as $product) {
		echo '"' . $supplier_category['name'] . '";"' . $product['name'] . "\"\n";
	}
}?>