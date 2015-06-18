<div>
	<div style="margin: 10px 3px 3px;float:left"><span>Označené:</span></div>
	<div class="product-bulk-operations-box">
		<button type="submit" value="activate" name="data[Product][BulkProcess][action]">Aktivovat</button>
		<button type="submit" value="deactivate" name="data[Product][BulkProcess][action]">Deaktivovat</button>
		<button type="submit" value="delete" name="data[Product][BulkProcess][action]" onclick="return confirm('Opravdu chcete zvolené produkty odstranit ze systému?')">Smazat</button>
	</div>
	<div class="product-bulk-operations-box">
		<div id="CategoriesProductsOperationsCombobox" style="margin:4px 5px;float:left;padding-right:30px;">
<?php 
	echo $this->element(REDESIGN_PATH . 'admin/combobox', array('name' => 'Product.BulkProcess.category_id', 'options' => $categories));
	echo $this->Form->end();
?>
		</div>
		<div style="float:left">
			<button type="submit" value="copy" name="data[Product][BulkProcess][action]">Kopírovat</button>
			<button type="submit" value="move" name="data[Product][BulkProcess][action]">Přesunout</button>
			<button type="submit" value="clon" name="data[Product][BulkProcess][action]">Duplikovat</button>
		</div>
	</div>
	<div style="clear:both"></div>
</div>