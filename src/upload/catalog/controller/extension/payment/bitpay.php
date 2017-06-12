<?php
/**
 * BitPay Payment Controller
 */
class ControllerExtensionPaymentBitpay extends Controller {

	/** @var boolean $ajax Whether the request was made via AJAX */
	private $ajax = false;

	/** @var BitPayLibrary $bitpay */
	private $bitpay;

	/**
	 * BitPay Payment Controller Constructor
	 * @param Registry $registry
	 */
	public function __construct($registry) {
		parent::__construct($registry);

		// Make langauge strings and BitPay Library available to all
		$this->load->language('extension/payment/bitpay');
		$this->bitpay = new Bitpay($registry);

		// Setup logging
		$this->logger = new Log('bitpay.log');

		// Is this an ajax request?
		if (!empty($this->request->server['HTTP_X_REQUESTED_WITH']) &&
			strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$this->ajax = true;
		}

		// Check for connection
		if ($this->setting('connection') !== 'disconnected' && $this->setting('connection') !== null) {
			$was_connected = ($this->setting('connection') === 'connected');
			$was_network = $this->setting('network');
			$this->bitpay->checkConnection();
		}
	}

	/**
	 * Displays the Payment Method (a redirect button)
	 * @return void
	 */
	public function index() {
		$data['testnet'] = ($this->setting('network') === 'livenet') ? false : true;
		$data['warning_testnet'] = $this->language->get('warning_testnet');
		$data['url_redirect'] = $this->url->link('extension/payment/bitpay/confirm', $this->config->get('config_secure'));
		$data['button_confirm'] = $this->language->get('button_confirm');

		if (isset($this->session->data['error_bitpay'])) {
			unset($this->session->data['error_bitpay']);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/bitpay.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/extension/payment/bitpay.tpl', $data);
		} else {
			return $this->load->view('extension/payment/bitpay.tpl', $data);
		}
	}

	/**
	 * Generates redirect to invoice url
	 * @return void
	 */
	public function confirm() {
		$this->load->model('checkout/order');
		if (!isset($this->session->data['order_id'])) {
			$this->response->redirect($this->url->link('checkout/cart'));
			return;
		}
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		if (false === $order_info) {
			$this->response->redirect($this->url->link('checkout/cart'));
			return;
		}

		try {
			$invoice = $this->prepareInvoice($order_info);
			$invoice = $this->bitpay->createInvoice($invoice);
		} catch (Exception $e) {
			$this->session->data['error_bitpay'] = 'Sorry, but there was a problem communicating with BitPay for Bitcoin checkout.';
			$this->response->redirect($this->url->link('checkout/checkout'));
			return;
		}

		$this->session->data['bitpay_invoice'] = $invoice->getId();
		$this->response->redirect($invoice->getUrl());
	}

	/**
	 * Convenience wrapper for bitpay logs
	 * @param string $level The type of log.
	 *					  Should be 'error', 'warn', 'info', 'debug', 'trace'
	 *					  In normal mode, 'error' and 'warn' are logged
	 *					  In debug mode, all are logged
	 * @param string $message The message of the log
	 * @param int $depth Depth addition for debug backtracing
	 * @return void
	 */
	public function log($level, $message, $depth = 0) {
		$depth += 1;
		$this->bitpay->log($level, $message, $depth);
	}

	/**
	 * Convenience wrapper for bitpay settings
	 *
	 * Automatically persists to database on set and combines getting and setting into one method
	 * Assumes 'bitpay_' prefix
	 *
	 * @param string $key Setting key
	 * @param string $value Setting value if setting the value
	 * @return string|null|void Setting value, or void if setting the value
	 */
	public function setting($key, $value = null) {
		// Set the setting
		if (func_num_args() === 2) {
			return $this->bitpay->setting($key, $value);
		}

		// Get the setting
		return $this->bitpay->setting($key);
	}

	/**
	 * Prepares an Invoice to send to BitPay
	 *
	 * @param array $order_info OpenCart checkout order
	 * @return Invoice
	 */
	private function prepareInvoice($order_info = array()) {
		$invoice = new \Bitpay\Invoice();
		if (empty($order_info['order_id'])) {
			$this->log('error', 'Cannot prepare invoice without `order_id`');
			throw Exception('Cannot prepare invoice without `order_id`');
		}
		$this->log('info', sprintf('Preparing Invoice for Order %s', (string)$order_info['order_id']));
		$invoice->setOrderId((string)$order_info['order_id']);
		if (empty($order_info['currency_code'])) {
			$this->log('error', 'Cannot prepare invoice without `currency_code`');
			throw Exception('Cannot prepare invoice without `currency_code`');
		}
		$invoice->setCurrency(new \Bitpay\Currency($order_info['currency_code']));
		if (empty($order_info['total'])) {
			$this->log('error', 'Cannot prepare invoice without `total`');
			throw Exception('Cannot prepare invoice without `total`');
		}
                
                (float)$foo = $order_info['total']*$order_info['currency_value'];
                $total = (float)number_format((float)$foo, 2, '.', ''); 
		$invoice->setPrice($total);

		// Send Buyer Information?
		if ($this->setting('send_buyer_info')) {
			$buyer = new \Bitpay\Buyer();
			$buyer->setFirstName($order_info['firstname'])
					->setLastName($order_info['lastname'])
					->setEmail($order_info['email'])
					->setPhone($order_info['telephone'])
					->setAddress(array($order_info['payment_address_1'], $order_info['payment_address_2']))
					->setCity($order_info['payment_city'])
					->setState($order_info['payment_zone_code'])
					->setZip($order_info['payment_postcode'])
					->setCountry($order_info['payment_country']);
			$invoice->setBuyer($buyer);
		}

		$invoice->setFullNotifications(true);

		$return_url = $this->setting('return_url');
		if (empty($return_url)) {
			$return_url = $this->url->link('extension/payment/bitpay/success', $this->config->get('config_secure'));
		}

		$notify_url = $this->setting('notify_url');
		if (empty($notify_url)) {
			$notify_url = $this->url->link('extension/payment/bitpay/callback', $this->config->get('config_secure'));
		}

		$invoice->setRedirectUrl($return_url);
		$invoice->setNotificationUrl($notify_url);
		$invoice->setTransactionSpeed($this->setting('risk_speed'));
		return $invoice;
	}

	/**
	 * Success return page
	 *
	 * Progresses the order if valid, and redirects to OpenCart's Checkout Success page
	 *
	 * @return void
	 */
	public function success() {
		$this->load->model('checkout/order');
		$order_id = $this->session->data['order_id'];
		if (is_null($order_id)) {
			$this->response->redirect($this->url->link('checkout/success'));
			return;
		}

		$order = $this->model_checkout_order->getOrder($order_id);
		try {
			$invoice = $this->bitpay->getInvoice($this->session->data['bitpay_invoice']);
		} catch (Exception $e) {
			$this->response->redirect($this->url->link('checkout/success'));
			return;
		}

		switch ($invoice->getStatus()) {
			case 'paid':
				$order_status_id = $this->setting('paid_status');
				$order_message = $this->language->get('text_progress_paid');
				break;
			case 'confirmed':
				$order_status_id = $this->setting('confirmed_status');
				$order_message = $this->language->get('text_progress_confirmed');
				break;
			case 'complete':
				$order_status_id = $this->setting('complete_status');
				$order_message = $this->language->get('text_progress_complete');
				break;
			default:
				$this->response->redirect($this->url->link('checkout/checkout'));
				return;
		}

		// Progress the order status
		$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
		$this->session->data['bitpay_invoice'] = null;
		$this->response->redirect($this->url->link('checkout/success'));
	}

	/**
	 * IPN Handler
	 * @return void
	 */
	public function callback() {
		$this->load->model('checkout/order');

		$post = file_get_contents("php://input");
		if (empty($post)) {
			$this->log('warn', 'IPN handler called with no data');
			return;
		}

		$json = @json_decode($post, true);
		if (empty($json)) {
			$this->log('warn', 'IPN handler called with invalid data');
			$this->log('trace', 'Invalid JSON: ' . $post);
			return;
		}

		if (!array_key_exists('id', $json)) {
			$this->log('warn', 'IPN handler called with invalid data');
			$this->log('trace', 'Invoice object missing ID field: ' . $var_export($json, true));
			return;
		}

		if (!array_key_exists('url', $json)) {
			$this->log('warn', 'IPN handler called with invalid data');
			$this->log('trace', 'Invoice object missing URL field: ' . $var_export($json, true));
			return;
		}

		// Try to set the network based on the url first since the merchant may have
		// switched networks while test invoices are still being confirmed
		$network = null;
		if (true === strpos($json['url'], 'https://test.bitpay.com')) {
			$network = 'testnet';
		} elseif (true === strpos($json['url'], 'https://bitpay.com')) {
			$network = 'livenet';
		}

		$invoice = $this->bitpay->getInvoice($json['id'], $network);

		switch ($invoice->getStatus()) {
			case 'paid':
				$order_status_id = $this->setting('paid_status');
				$order_message = $this->language->get('text_progress_paid');
				break;
			case 'confirmed':
				$order_status_id = $this->setting('confirmed_status');
				$order_message = $this->language->get('text_progress_confirmed');
				break;
			case 'complete':
				$order_status_id = $this->setting('complete_status');
				$order_message = $this->language->get('text_progress_complete');
				break;
			default:
				$this->response->redirect($this->url->link('checkout/checkout'));
				return;
		}

		// Progress the order status
		$this->model_checkout_order->addOrderHistory($invoice->getOrderId(), $order_status_id);
	}
}
