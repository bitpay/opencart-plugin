<?php
/**
 * BitPay Payment Admin Controller
 */
class ControllerPaymentBitpay extends Controller {

	/** @var array $error Validation errors */
	private $error = array();

	/** @var boolean $ajax Whether the request was made via AJAX */
	private $ajax = false;

	/** @var BitPayLibrary $bitpay */
	private $bitpay;

	/**
	 * BitPay Payment Admin Controller Constructor
	 * @param Registry $registry
	 */
	public function __construct($registry) {
		parent::__construct($registry);

		// Make langauge strings and BitPay Library available to all
		$this->load->language('payment/bitpay');

		$this->bitpay = new Bitpay($registry);

		// Setup logging
		$this->logger = new Log('bitpay.log');

		// Is this an ajax request?
		if (!empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$this->ajax = true;
			$this->response->addHeader('Content-type: application/json');
		}

		// Check for connection
		if (!$this->ajax) {
			$this->connected();
		}

	}

	/**
	 * Primary settings page
	 * @return void
	 */
	public function index() {
		// Saving settings
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post['action'] === 'save' && $this->validate()) {

			$this->setting('risk_speed', $this->request->post['bitpay_risk_speed']);
			$this->setting('send_buyer_info', $this->request->post['bitpay_send_buyer_info']);
			$this->setting('geo_zone_id', $this->request->post['bitpay_geo_zone_id']);
			$this->setting('status', $this->request->post['bitpay_status']);
			$this->setting('sort_order', $this->request->post['bitpay_sort_order']);
			$this->setting('paid_status', $this->request->post['bitpay_paid_status']);
			$this->setting('confirmed_status', $this->request->post['bitpay_confirmed_status']);
			$this->setting('complete_status', $this->request->post['bitpay_complete_status']);
			$this->setting('notify_url', $this->request->post['bitpay_notify_url']);
			$this->setting('return_url', $this->request->post['bitpay_return_url']);
			$this->setting('debug', $this->request->post['bitpay_debug']);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		// Send Support Request form submitted
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post['action'] === 'send' && $this->validateSupportRequest()) {
			$this->bitpay->sendSupportRequest();
			$this->session->data['success'] = $this->language->get('success_support_request');
		}

		$this->document->setTitle($this->language->get('heading_title'));

		// #HEADER and globals
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_general'] = $this->language->get('text_general');
		$data['text_statuses'] = $this->language->get('text_statuses');
		$data['text_advanced'] = $this->language->get('text_advanced');
		$data['text_connect_to_bitpay'] = $this->language->get('text_connect_to_bitpay');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_livenet'] = $this->language->get('text_livenet');
		$data['text_testnet'] = $this->language->get('text_testnet');
		$data['text_high'] = $this->language->get('text_high');
		$data['text_medium'] = $this->language->get('text_medium');
		$data['text_low'] = $this->language->get('text_low');
		$data['text_forum'] = $this->language->get('text_forum');
		$data['text_bitpay_labs'] = $this->language->get('text_bitpay_labs');
		$data['text_send_request'] = $this->language->get('text_send_request');
		$data['text_support'] = $this->language->get('text_support');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_popup_blocked'] = $this->language->get('text_popup_blocked');
		$data['text_popup'] = $this->language->get('text_popup');
		$data['text_are_you_sure'] = $this->language->get('text_are_you_sure');

		$data['entry_api_access'] = $this->language->get('entry_api_access');
		$data['entry_risk_speed'] = $this->language->get('entry_risk_speed');
		$data['entry_send_buyer_info'] = $this->language->get('entry_send_buyer_info');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_paid_status'] = $this->language->get('entry_paid_status');
		$data['entry_confirmed_status'] = $this->language->get('entry_confirmed_status');
		$data['entry_complete_status'] = $this->language->get('entry_complete_status');
		$data['entry_notify_url'] = $this->language->get('entry_notify_url');
		$data['entry_return_url'] = $this->language->get('entry_return_url');
		$data['entry_debug'] = $this->language->get('entry_debug');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email_address'] = $this->language->get('entry_email_address');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_send_logs'] = $this->language->get('entry_send_logs');
		$data['entry_send_server_info'] = $this->language->get('entry_send_server_info');

		$data['help_api_access'] = $this->language->get('help_api_access');
		$data['help_risk_speed'] = $this->language->get('help_risk_speed');
		$data['help_send_buyer_info'] = $this->language->get('help_send_buyer_info');
		$data['help_paid_status'] = $this->language->get('help_paid_status');
		$data['help_confirmed_status'] = $this->language->get('help_confirmed_status');
		$data['help_complete_status'] = $this->language->get('help_complete_status');
		$data['help_notify_url'] = $this->language->get('help_notify_url');
		$data['help_return_url'] = $this->language->get('help_return_url');
		$data['help_debug'] = $this->language->get('help_debug');
		$data['help_send_server_info'] = $this->bitpay->getServerInfo();

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_support'] = $this->language->get('button_support');
		$data['button_disconnect'] = $this->language->get('button_disconnect');
		$data['button_regenerate'] = $this->language->get('button_regenerate');
		$data['button_clear'] = $this->language->get('button_clear');
		$data['button_send'] = $this->language->get('button_send');
		$data['button_continue'] = $this->language->get('button_continue');

		$data['tab_settings'] = $this->language->get('tab_settings');
		$data['tab_log'] = $this->language->get('tab_log');
		$data['tab_support'] = $this->language->get('tab_support');

		$data['url_action'] = $this->url->link('payment/bitpay', 'token=' . $this->session->data['token'], 'SSL');
		$data['url_cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		$data['url_reset'] = $this->url->link('payment/bitpay/reset', 'token=' . $this->session->data['token'], 'SSL');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/bitpay', 'token=' . $this->session->data['token'], 'SSL')
		);

		// #GENERAL
		$data['bitpay_connection'] = $this->setting('connection');
		$data['bitpay_network'] = $this->setting('network');
		$data['url_connect_livenet'] = $this->url->link('payment/bitpay/connect', 'network=livenet&token=' . $this->session->data['token'], 'SSL');
		$data['url_connect_testnet'] = $this->url->link('payment/bitpay/connect', 'network=testnet&token=' . $this->session->data['token'], 'SSL');
		if (isset($this->request->get['network']) && !empty($this->request->get['network']['url']) && !empty($this->request->get['network']['port'])) {
			$network = $this->request->get['network'];
			$network_param = 'network[url]=' . urlencode($network['url']) . '&network[port]=' . $network['port'];
			$network_param .= (isset($network['port_required'])) ? '&network[port_required]' : '';
			$data['url_connect_livenet'] = $this->url->link('payment/bitpay/connect', $network_param . '&token=' . $this->session->data['token'], 'SSL');
		}
		$data['url_disconnect'] = $this->url->link('payment/bitpay/disconnect', 'network=testnet&token=' . $this->session->data['token'], 'SSL');
		$data['url_connected'] = str_replace('&amp;', '&', $this->url->link('payment/bitpay/connected', 'token=' . $this->session->data['token'], 'SSL'));

		if (is_array($this->setting('network'))) {
			$network_title = 'Customnet(' . $this->setting('network')['url'] . ')';
		} else {
			$network_title = ucwords($this->setting('network'));
		}
		$data['text_connected'] = sprintf($this->language->get('text_connected'), $network_title);

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['bitpay_risk_speed'] = (isset($this->request->post['bitpay_risk_speed'])) ? $this->request->post['bitpay_risk_speed'] : $this->setting('risk_speed');
		$data['bitpay_send_buyer_info'] = (isset($this->request->post['bitpay_send_buyer_info'])) ? $this->request->post['bitpay_send_buyer_info'] : $this->setting('send_buyer_info');
		$data['bitpay_geo_zone_id'] = (isset($this->request->post['bitpay_geo_zone_id'])) ? $this->request->post['bitpay_geo_zone_id'] : $this->setting('geo_zone_id');
		$data['bitpay_status'] = (isset($this->request->post['bitpay_status'])) ? $this->request->post['bitpay_status'] : $this->setting('status');
		$data['bitpay_sort_order'] = (isset($this->request->post['bitpay_sort_order'])) ? $this->request->post['bitpay_sort_order'] : $this->setting('sort_order');

		// #ORDER STATUSES
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['bitpay_paid_status'] = (isset($this->request->post['bitpay_paid_status'])) ? $this->request->post['bitpay_paid_status'] : $this->setting('paid_status');
		$data['bitpay_confirmed_status'] = (isset($this->request->post['bitpay_confirmed_status'])) ? $this->request->post['bitpay_confirmed_status'] : $this->setting('confirmed_status');
		$data['bitpay_complete_status'] = (isset($this->request->post['bitpay_complete_status'])) ? $this->request->post['bitpay_complete_status'] : $this->setting('complete_status');

		// #ADVANCED
		$data['bitpay_notify_url'] = (isset($this->request->post['bitpay_notify_url'])) ? $this->request->post['bitpay_notify_url'] : $this->setting('notify_url');
		$data['bitpay_return_url'] = (isset($this->request->post['bitpay_return_url'])) ? $this->request->post['bitpay_return_url'] : $this->setting('return_url');

		$default_notify_url = $this->url->link('payment/bitpay/callback', $this->config->get('config_secure'));
		$default_notify_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $default_notify_url);
		$default_notify_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $default_notify_url);
		$data['default_notify_url'] = $default_notify_url;

		$default_return_url = $this->url->link('payment/bitpay/success', $this->config->get('config_secure'));
		$default_return_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $default_return_url);
		$default_return_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $default_return_url);
		$data['default_return_url'] = $default_return_url;

		$data['bitpay_debug'] = (isset($this->request->post['bitpay_debug'])) ? $this->request->post['bitpay_debug'] : $this->setting('debug');

		// #LOG
		$file = DIR_LOGS . 'bitpay.log';
		$data['log'] = '';

		$matches_array = array();

		if (file_exists($file)) {
			$lines = file($file, FILE_USE_INCLUDE_PATH, null);
			foreach ($lines as $line_num => $line) {
				if (preg_match('/^([^\\[]*)\\[([^\\]]*)\\](\\{([^}]*)\\})?(.*)/', $line, $matches)) {
					unset($matches[3]);
					$level = strtolower($matches[2]);
					$matches[0] = '';
					$matches[1] = '<span class="bp-log-date">' . $matches[1] . '</span>';
					$matches[2] = '<span class="bp-log-level">[<span>' . $matches[2] . '</span>]</span>';
					if (!empty($matches[4])) {
						$matches[4] = '<span class="bp-log-locale">{<span>' . $matches[4] . '</span>}</span>';
						$matches[4] = preg_replace('/((->)|(::))/', '<span>$1</span>', $matches[4]);
					}
					$matches[5] = '<span class="bp-log-message">' . $matches[5] . '</span>';
					$line = '<span class="bp-log bp-log-' . $level . '">' . implode('', $matches) . '</span>';
				}

				$data['log'] .= '<div>' . $line . "</div>\n";
			}
		}

		$data['url_clear'] = $this->url->link('payment/bitpay/clear', 'token=' . $this->session->data['token'], 'SSL');

		// #SUPPORT
		$data['request_name'] = (isset($this->request->post['request_name'])) ? $this->request->post['request_name'] : $this->config->get('config_owner');
		$data['request_email_address'] = (isset($this->request->post['request_email_address'])) ? $this->request->post['request_email_address'] : $this->config->get('config_email');
		$data['request_send_logs'] = (isset($this->request->post['request_send_logs'])) ? $this->request->post['request_send_logs'] : true;
		$data['request_send_server_info'] = (isset($this->request->post['request_send_server_info'])) ? $this->request->post['request_send_server_info'] : true;
		$data['request_subject'] = (isset($this->request->post['request_subject'])) ? $this->request->post['request_subject'] : '';
		$data['request_description'] = (isset($this->request->post['request_description'])) ? $this->request->post['request_description'] : '';

		// #LAYOUT
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// #NOTIFICATIONS
		$data['error_warning'] = '';
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		$data['success'] = '';
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data['error_status'] = '';
		if (isset($this->error['status'])) {
			$data['error_status'] = sprintf($this->error['status'], $data['url_connect_livenet']);
		}

		$data['error_notify_url'] = '';
		if (isset($this->error['notify_url'])) {
			$data['error_notify_url'] = $this->error['notify_url'];
		}

		$data['error_return_url'] = '';
		if (isset($this->error['return_url'])) {
			$data['error_return_url'] = $this->error['return_url'];
		}

		$data['error_request_name'] = '';
		if (isset($this->error['request_name'])) {
			$data['error_request_name'] = $this->error['request_name'];
		}

		$data['error_request_email_address'] = '';
		if (isset($this->error['request_email_address'])) {
			$data['error_request_email_address'] = $this->error['request_email_address'];
		}

		$data['error_request_subject'] = '';
		if (isset($this->error['request_subject'])) {
			$data['error_request_subject'] = $this->error['request_subject'];
		}

		$data['error_request_description'] = '';
		if (isset($this->error['request_description'])) {
			$data['error_request_description'] = $this->error['request_description'];
		}

		$data['error_request'] = false;
		if (isset($this->error['request'])) {
			$data['error_request'] = true;
		}

		$this->response->setOutput($this->load->view('payment/bitpay.tpl', $data));
	}

	/**
	 * Attempts to connect to BitPay API
	 * @return void
	 */
	public function connect() {
		$network = $this->bitpay->setNetwork($this->request->get['network']);
		$this->log('debug', 'Attempting to connect to ' . $network);
		$redirect = str_replace('&amp;', '&', $this->url->link('payment/bitpay', 'token=' . $this->session->data['token'], 'SSL'));
		try {
			$url = $this->bitpay->getPairingUrl() . '&redirect=' . urlencode($redirect);
			$this->response->redirect($url);
			return;
		} catch (Exception $e) {
			$this->log('error', $this->language->get('log_unable_to_connect'));
			$this->session->data['warning'] = $this->language->get('warning_unable_to_connect');
			$this->response->redirect($this->url->link('payment/bitpay', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}

	/**
	 * Disconnects from BitPay API
	 * @return void
	 */
	public function disconnect() {
		$this->setting('connection', 'disconnected');
		$this->setting('network', null);
		$this->setting('token', null);
		$this->setting('pairing_code', null);
		$this->setting('pairing_expiration', null);
		$this->session->data['success'] = $this->language->get('success_disconnect');
		$this->session->data['manual_disconnect'] = true;
		$this->response->redirect($this->url->link('payment/bitpay', 'token=' . $this->session->data['token'], 'SSL'));
	}

	/**
	 * Checks the connection to BitPay API
	 * @return void
	 */
	public function connected() {
		if ($this->setting('connection') !== 'disconnected' && $this->setting('connection') !== null) {
			$was_connected = ($this->setting('connection') === 'connected');
			$was_network = $this->setting('network');
			$was_network = (is_array($was_network)) ? 'testnet' : $was_network;
			$this->bitpay->checkConnection();

			// Connection attempt successful!
			if (!$was_connected && $this->setting('connection') === 'connected') {
				if (is_array($this->setting('network'))) {
					$network_title = 'Customnet(' . $this->setting('network')['url'] . ')';
				} else {
					$network_title = ucwords($this->setting('network'));
				}

				$this->session->data['success'] = sprintf($this->language->get('success_connected'), $network_title);
			}

			// Helpful message if a connection breaks (token revoked, etc)
			if ($was_connected && $this->setting('connection') === 'disconnected' && true !== $this->session->data['manual_disconnect']) {
				$pair_url = $this->url->link('payment/bitpay/connect', 'network=' . $was_network . '&token=' . $this->session->data['token']);
				$notification = sprintf($this->language->get('warning_disconnected'), $pair_url);
				if ($this->ajax) {
					$data = array('error' => $notification);
					$this->response->setOutput(json_encode($data));
					return;
				} else {
					$this->session->data['warning'] = sprintf($this->language->get('warning_disconnected'), $pair_url);
				}
			}

			// Heartbeat for ajax
			if ($this->ajax) {
				$data = array('data' => 'connected');
				$this->response->setOutput(json_encode($data));
				return;
			}
		}
	}

	/**
	 * Clears the BitPay log
	 * @return void
	 */
	public function clear() {
		$file = DIR_LOGS . 'bitpay.log';
		$handle = fopen($file, 'w+');
		fclose($handle);

		$this->session->data['success'] = $this->language->get('success_clear');
		$this->response->redirect($this->url->link('payment/bitpay', 'token=' . $this->session->data['token'], 'SSL'));
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
	private function log($level, $message, $depth = 0) {
		$depth += 1;
		$this->bitpay->log($level, $message, $depth);
	}

	/**
	 * Convenience wrapper for bitpay settings
	 *
	 * Automatically persists to database on set and combines getting and setting into one method
	 * Assumes bitpay_ prefix
	 *
	 * @param string $key Setting key
	 * @param string $value Setting value if setting the value
	 * @return string|null|void Setting value, or void if setting the value
	 */
	private function setting($key, $value = null) {
		// Set the setting
		if (func_num_args() === 2) {

			return $this->bitpay->setting($key, $value);
		}

		// Get the setting
		return $this->bitpay->setting($key);
	}

	/**
	 * Validate the primary settings for the BitPay extension
	 * @return boolean True if the settings provided are valid
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/bitpay')) {
			$this->error['warning'] = $this->language->get('warning_permission');
		}
		if (!empty($this->request->post['bitpay_notify_url']) && false === filter_var($this->request->post['bitpay_notify_url'], FILTER_VALIDATE_URL)) {
			$this->error['notify_url'] = $this->language->get('error_notify_url');
		}
		if (!empty($this->request->post['bitpay_return_url']) && false === filter_var($this->request->post['bitpay_return_url'], FILTER_VALIDATE_URL)) {
			$this->error['return_url'] = $this->language->get('error_return_url');
		}
		if ($this->request->post['bitpay_status'] && $this->setting('connection') !== 'connected') {
			$this->error['status'] = $this->language->get('error_status');
		}

		return !$this->error;
	}

	/**
	 * Validate the values for sending a Support Request
	 * @return boolean True if the values provided are valid
	 */
	private function validateSupportRequest() {
		if (empty($this->request->post['request_name'])) {
			$this->error['request_name'] = $this->language->get('error_request_name');
		}
		if (empty($this->request->post['request_email_address'])) {
			$this->error['request_email_address'] = $this->language->get('error_request_email_address');
		} elseif (false === filter_var($this->request->post['request_email_address'], FILTER_VALIDATE_EMAIL)) {
			$this->error['request_email_address'] = $this->language->get('error_request_email_address_invalid');
		}
		if (empty($this->request->post['request_subject'])) {
			$this->error['request_subject'] = $this->language->get('error_request_subject');
		}
		if (empty($this->request->post['request_description'])) {
			$this->error['request_description'] = $this->language->get('error_request_description');
		}
		if (!empty($this->error)) {
			$this->error['request'] = true;
		}

		return !$this->error;
	}

	/**
	 * Install the extension by setting up some smart defaults
	 * @return void
	 */
	public function install() {
		$this->load->model('localisation/order_status');
		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
		$default_paid = null;
		$default_confirmed = null;
		$default_complete= null;
		foreach ($order_statuses as $order_status) {
			if ($order_status['name'] == 'Processing') {
				$default_paid = $order_status['order_status_id'];
			} elseif ($order_status['name'] == 'Processed') {
				$default_confirmed = $order_status['order_status_id'];
			} elseif ($order_status['name'] == 'Complete') {
				$default_complete = $order_status['order_status_id'];
			}
		}

		$this->load->model('setting/setting');
		$default_settings = array(
			'bitpay_private_key' => null,
			'bitpay_public_key' => null,
			'bitpay_connection' => 'disconnected',
			'bitpay_network' => null,
			'bitpay_token' => null,
			'bitpay_risk_speed' => 'high',
			'bitpay_send_buyer_info' => '0',
			'bitpay_geo_zone_id' => '0',
			'bitpay_status' => '0',
			'bitpay_sort_order' => null,
			'bitpay_paid_status' => $default_paid,
			'bitpay_confirmed_status' => $default_confirmed,
			'bitpay_complete_status' => $default_complete,
			'bitpay_notify_url' => null,
			'bitpay_return_url' => null,
			'bitpay_debug' => '0',
			'bitpay_version' => $this->bitpay->version,
		);
		$this->model_setting_setting->editSetting('bitpay', $default_settings);
		$this->bitpay->generateId();
	}

	/**
	 * Uninstall the extension by removing the settings
	 * @return void
	 */
	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('bitpay');
	}
}
