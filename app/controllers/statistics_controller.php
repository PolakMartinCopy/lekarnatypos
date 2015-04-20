<?
class StatisticsController extends AppController {
	var $name = 'Statistics';

	function admin_index() {
		
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Statistic.filter');
			$this->redirect(array('controller' => 'statistics', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			$this->Session->write('Statistic.filter', $this->data['Statistic']);
		} elseif ($this->Session->check('Statistic.filter')) {
			$this->data['Statistic']['from'] = $this->Session->read('Statistic.filter.from');
			$this->data['Statistic']['to'] = $this->Session->read('Statistic.filter.to');
			$this->data['Statistic']['shipping_id'] = $this->Session->read('Statistic.filter.shipping_id');
			$this->data['Statistic']['status_id'] = $this->Session->read('Statistic.filter.shipping_id');
		} else {
			$this->data['Statistic']['from'] = date('d.m.Y', strtotime('-1 day'));
			$this->data['Statistic']['to'] = date('d.m.Y', strtotime('-1 day'));
			$this->data['Statistic']['shipping_id'] = null;
			$this->data['Statistic']['status_id'] = null;
		}
		
		if (isset($this->data)) {

			$conditions = array();
			$from = false;
			if (isset($this->data['Statistic']['from']) && !empty($this->data['Statistic']['from'])) {
				$from = cz2db_date($this->data['Statistic']['from']);
				$conditions['DATE(Order.created) >='] = $from;
			}
			$to = false;
			if (isset($this->data['Statistic']['to']) && !empty($this->data['Statistic']['to'])) {
				$to = cz2db_date($this->data['Statistic']['to']);
				$conditions['DATE(Order.created) <='] = $to;
			}
			$shipping_id = $this->data['Statistic']['shipping_id'];
			if (isset($shipping_id) && !empty($shipping_id)) {
				$conditions['Order.shipping_id'] = $shipping_id;
			}
			$status_id = $this->data['Statistic']['status_id'];
			if (isset($status_id) && !empty($status_id)) {
				$conditions['Order.status_id'] = $status_id;
			}

			App::import('Model', 'Order');
			$this->Statistic->Order = &new Order;
			$this->Statistic->Order->virtualFields['products_count'] = 'SUM(OrderedProduct.product_quantity)';
			$this->Statistic->Order->virtualFields['price'] = 'Order.subtotal_with_dph + Order.shipping_cost';
/* 			$this->paginate['Order'] = array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array(
					'Order.id',
					'Order.created',
					'Order.products_count',
					'Order.subtotal_with_dph',
					'Order.shipping_cost',
					'Order.price'
				),
				'joins' => array(
					array(
						'table' => 'ordered_products',
						'alias' => 'OrderedProduct',
						'type' => 'LEFT',
						'conditions' => array('OrderedProduct.order_id = Order.id')
					),
				),
				'group' => array('Order.id'),
				'order' => array('Order.products_count' => 'desc'),
				'show' => 'all'
			);
			$orders = $this->paginate('Order'); */
			$orders = $this->Statistic->Order->find('all', array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array(
						'Order.id',
						'Order.created',
						'Order.products_count',
						'Order.subtotal_with_dph',
						'Order.shipping_cost',
						'Order.price'
				),
				'joins' => array(
					array(
						'table' => 'ordered_products',
						'alias' => 'OrderedProduct',
						'type' => 'LEFT',
						'conditions' => array('OrderedProduct.order_id = Order.id')
					),
				),
				'group' => array('Order.id'),
				'order' => array('Order.products_count' => 'desc'),
			));

			unset($this->Statistic->Order->virtualFields['products_count']);
			unset($this->Statistic->Order->virtualFields['price']);
			$this->set('orders', $orders);

			$orders_income = 0;
			foreach ($orders as $order) {
				$orders_income += $order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'];
			}
			$this->set('orders_income', $orders_income);
			
			App::import('Model', 'Product');
			$this->Statistic->Product = &new Product;
			$this->Statistic->Product->virtualFields['ordered_count'] = 'SUM(OrderedProduct.product_quantity)';
			$this->paginate['Product'] = array(
				'conditions' => $conditions,
				'contain' => array(),
				'fields' => array(
					'Product.id',
					'Product.name',
					'Product.ordered_count',
					'Product.url'
				),
				'joins' => array(
					array(
						'table' => 'ordered_products',
						'alias' => 'OrderedProduct',
						'type' => 'INNER',
						'conditions' => array('OrderedProduct.product_id = Product.id')
					),
					array(
						'table' => 'orders',
						'alias' => 'Order',
						'type' => 'INNER',
						'conditions' => array('OrderedProduct.order_id = Order.id')
					),
				),
				'group' => array('Product.id'),
				'order' => array('Products.ordered_count' => 'desc'),
				'show' => 'all'
			);
			$products = $this->paginate('Product');
			unset($this->Statistic->Product->virtualFields['ordered_count']);
			$this->set('products', $products);
		}
		
		App::import('Model', 'Shipping');
		$this->Statistic->Shipping = &new Shipping;
		$shippings = $this->Statistic->Shipping->find('list', array(
			'order' => array('Shipping.order' => 'asc')
		));
		$this->set('shippings', $shippings);
		
		App::import('Model', 'Status');
		$this->Statistic->Status = &new Status;
		$states = $this->Statistic->Status->find('list', array(
			'order' => array('Status.order' => 'asc')
		));
		$this->set('states', $states);
		
		$this->layout = REDESIGN_PATH . 'admin';
	}

	function admin_p(){
		if ( !isset($this->data) ){
			$this->data['Statistic']['from']['hour'] = '0';
			$this->data['Statistic']['from']['min'] = '00';
			$this->data['Statistic']['from']['day'] = '1';
			$this->data['Statistic']['from']['month'] = date('m');
			$this->data['Statistic']['from']['year'] = date('Y');

			$this->data['Statistic']['to']['hour'] = '0';
			$this->data['Statistic']['to']['min'] = '00';
			$this->data['Statistic']['to']['day'] = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')); // 31  ;
			$this->data['Statistic']['to']['month'] = date('m');
			$this->data['Statistic']['to']['year'] = date('Y');
		}

		$date_from = $this->data['Statistic']['from']['year'] . '-' . $this->data['Statistic']['from']['month'] . '-' . $this->data['Statistic']['from']['day'] . ' ' . $this->data['Statistic']['from']['hour'] . ':' . $this->data['Statistic']['from']['min'];
		$date_to = $this->data['Statistic']['to']['year'] . '-' . $this->data['Statistic']['to']['month'] . '-' . $this->data['Statistic']['to']['day'] . ' ' . $this->data['Statistic']['to']['hour'] . ':' . $this->data['Statistic']['to']['min'];

		$this->set('provisions', $this->Statistic->orders_wout_tax($date_from, $date_to));
	}
	

	function most_sold($id){
		if ( $id == 5 ){
			$id = null;
		}
		return array('most_sold' => $this->Statistic->most_sold(null, null, 5, $id));
	}
	
	function similar_products($id){
		return array('similar_products' => $similar_products = $this->Statistic->similar_products($id));
	}
}
?>