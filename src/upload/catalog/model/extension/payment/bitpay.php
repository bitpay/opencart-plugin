<?php
/**
 * BitPay Payment Model
 */
class ModelExtensionPaymentBitpay extends Model {

	/** @var BitPayLibrary $bitpay */
	private $bitpay;

	/**
	 * BitPay Payment Model Construct
	 * @param Registry $registry
	 */
	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->language('extension/payment/bitpay');
		$this->bitpay = new Bitpay($registry);
	}

	/**
	 * Returns the BitPay Payment Method if available
	 * @param  array $address Customer billing address
	 * @return array|void BitPay Payment Method if available
	 */
	public function getMethod($address)	{
		// Check for connection to BitPay
		$this->bitpay->checkConnection();
		if ($this->bitpay->setting('connection') === 'disconnected') {
			$this->bitpay->log('warn', 'You cannot have BitPay enabled as a payment method without being connected to BitPay.');
			return;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->bitpay->setting('geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		// All Geo Zones configured or address is in configured Geo Zone
		if (!$this->config->get('bitpay_geo_zone_id') || $query->num_rows) {
			return array(
				'code'	   => 'bitpay',
				'title'	  => $this->language->get('text_title'),
				'terms'	  => '',
				'sort_order' => $this->bitpay->setting('sort_order')
			);
		}
	}
}
