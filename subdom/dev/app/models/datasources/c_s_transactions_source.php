<?php 
class CSTransactionsSource extends DboMysql {
	
	function __construct($config, $autoConnect = true) {
		parent::__construct($config);
		if ($autoConnect) {
			return $this->connect();
		}
		return true;
	}
	
	function __destruct() {
		$this->close();
		parent::__destruct();
	}
	
	/**
	 * Connects to the database using options in the given configuration array.
	 *
	 * @return boolean True if the database could be connected, else false
	 */
	function connect() {
		$config = $this->config;
		$this->connected = false;
	
		if (!$config['persistent']) {
			$this->connection = mysql_connect($config['host'] . ':' . $config['port'], $config['login'], $config['password'], true);
			$config['connect'] = 'mysql_connect';
		} else {
			$this->connection = mysql_pconnect($config['host'] . ':' . $config['port'], $config['login'], $config['password']);
		}

		if (mysql_select_db($config['database'], $this->connection)) {
			$this->connected = true;
		}
	
		if (!empty($config['encoding'])) {
			$this->setEncoding($config['encoding']);
		}
	
		$this->_useAlias = (bool)version_compare(mysql_get_server_info($this->connection), "4.1", ">=");
	
		return $this->connected;
	}
	
	/**
	 * Disconnects database, kills the connection and says the connection is closed.
	 *
	 * @return void
	 * @access public
	 */
	function close() {
		$this->disconnect();
	}
	
	/**
	 * Disconnects from database.
	 *
	 * @return boolean True if the database could be disconnected, else false
	 */
	function disconnect() {
		if (isset($this->results) && is_resource($this->results)) {
			mysql_free_result($this->results);
		}
		$this->connected = !@mysql_close($this->connection);
		return !$this->connected;
	}
	

	
	/**
	 * Gets full table name including prefix
	 *
	 * @param mixed $model Either a Model object or a string table name.
	 * @param boolean $quote Whether you want the table name quoted.
	 * @return string Full quoted table name
	 * @access public
	 */
	function fullTableName($model, $quote = true) {
		return "
(
	SELECT
			CSCreditNote.id AS id, CSCreditNote.date_of_issue AS date_of_issue, CONCAT(2, CSCreditNote.year, CSCreditNote.month, CSCreditNote.order) AS code,
			CSTransactionItem.id as item_id, CSTransactionItem.c_s_product_name, CSTransactionItem.quantity, CSTransactionItem.price, CSTransactionItem.price_vat, 'dobropis' AS type,
			Unit.shortcut AS unit_shortcut,
			CSProduct.vzp_code AS c_s_product_vzp_code, CSProduct.group_code AS c_s_product_group_code,
			BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name, BusinessPartner.ico AS business_partner_ico, BusinessPartner.dic AS business_partner_dic
	FROM c_s_credit_notes AS CSCreditNote
		LEFT JOIN c_s_transaction_items AS CSTransactionItem ON (CSCreditNote.id = CSTransactionItem.c_s_credit_note_id)
		LEFT JOIN c_s_products AS CSProduct ON (CSProduct.id = CSTransactionItem.c_s_product_id)
		LEFT JOIN units AS Unit ON (Unit.id = CSProduct.unit_id)
		LEFT JOIN business_partners AS BusinessPartner ON (CSCreditNote.business_partner_id = BusinessPartner.id)
UNION
	SELECT
			CSInvoice.id AS id, CSInvoice.date_of_issue AS date_of_issue, CONCAT(1, CSInvoice.year, CSInvoice.month, CSInvoice.order) AS code,
			CSTransactionItem.id as item_id, CSTransactionItem.c_s_product_name, -(CSTransactionItem.quantity), CSTransactionItem.price, CSTransactionItem.price_vat, 'faktura' AS type,
			Unit.shortcut AS unit_shortcut,
			CSProduct.vzp_code AS c_s_product_vzp_code, CSProduct.group_code AS c_s_product_group_code,
			BusinessPartner.id AS business_partner_id, BusinessPartner.name AS business_partner_name, BusinessPartner.ico AS business_partner_ico, BusinessPartner.dic AS business_partner_dic
	FROM c_s_invoices AS CSInvoice
		LEFT JOIN c_s_transaction_items AS CSTransactionItem ON (CSInvoice.id = CSTransactionItem.c_s_invoice_id)
		LEFT JOIN c_s_products AS CSProduct ON (CSProduct.id = CSTransactionItem.c_s_product_id)
		LEFT JOIN units AS Unit ON (Unit.id = CSProduct.unit_id)
		LEFT JOIN business_partners AS BusinessPartner ON (CSInvoice.business_partner_id = BusinessPartner.id)
UNION
	SELECT
			CSStoring.id AS id, CONCAT(CSStoring.date, ' ', CSStoring.time) AS date_of_issue, null AS code,
			CSTransactionItem.id as item_id, CSTransactionItem.c_s_product_name, CSTransactionItem.quantity, CSTransactionItem.price, CSTransactionItem.price_vat, 'naskladnění' AS type,
			Unit.shortcut AS unit_shortcut,
			CSProduct.vzp_code AS c_s_product_vzp_code, CSProduct.group_code AS c_s_product_group_code,
			null AS business_partner_id, null AS business_partner_name, null AS business_partner_ico, null AS business_partner_dic
	FROM c_s_storings AS CSStoring
		LEFT JOIN c_s_transaction_items AS CSTransactionItem ON (CSStoring.id = CSTransactionItem.c_s_storing_id)
		LEFT JOIN c_s_products AS CSProduct ON (CSProduct.id = CSTransactionItem.c_s_product_id)
		LEFT JOIN units AS Unit ON (Unit.id = CSProduct.unit_id)
)";
	}
}
?>