<?php
/**
 * @license https://github.com/bitpay/opencart-bitpay/blob/master/LICENSE MIT
 */

class ControllerPaymentBitpay extends Controller
{

    // below is the url that can take you do the order information
    // http://127.0.0.1/~spair/store/index.php?route=account/order/info&order_id=35

    /**
     * @var string
     */
    private $payment_module_name  = 'bitpay';
    private $plugin_verson        = '1.9.2';

    /**
     */
    protected function index()
    {
        $this->language->load('payment/'.$this->payment_module_name);

    	$this->data['button_bitpay_confirm'] = $this->language->get('button_bitpay_confirm');
		$this->data['continue']              = $this->url->link('checkout/success');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/bitpay.tpl'))
        {
			$this->template = $this->config->get('config_template') . '/template/payment/bitpay.tpl';
        }
        else
        {
			$this->template = 'default/template/payment/bitpay.tpl';
		}

		$this->render();
	}

    /**
     * @param string $contents
     */
    function log($type, $contents)
    {
		$log = new Log('bitpay.log');
        $log->write('['.strtoupper($type).'] '.$contents);
	}

    /**
     */
    public function send()
    {
		$this->load->library('bitpay');
        $this->load->model('checkout/order');

        $bp = new Bitpay(array(
            'apiKey'     => $this->config->get($this->payment_module_name.'_api_key'),
            'apiServer'  => $this->config->get($this->payment_module_name.'_api_server'),
            'pluginInfo' => 'opencart-bitpay v'.$this->plugin_verson,
            'verifyPos'  => true
        ));

        if($bp->error()) {

            $this->log('error', $bp->error());
            echo "{\"error\": \"Error: Problem communicating with payment provider.\\nPlease try again later.\"}";

        }else{
            $order    = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $price    = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
            $order_id = (string)$order['order_id'];
            $options  = array(
                'notificationURL'   => $this->config->get($this->payment_module_name.'_notify_url'),
                'redirectURL'       => $this->config->get($this->payment_module_name.'_return_url'),
                'currency'          => $order['currency_code'],
                'physical'          => true,
                'fullNotifications' => true,
                'transactionSpeed'  => $this->config->get($this->payment_module_name.'_risk_speed'),
                'orderID'           => $order_id,
                'posData'           => $order_id
            );

            $invoice = $bp->createInvoice($price, $options);
            if($bp->error()) {
                $this->log('error', $bp->error());
                echo "{\"error\": \"Error: Problem communicating with payment provider.\\nPlease try again later.\"}";
            }else{
                if($this->config->get($this->payment_module_name.'_debug_mode')) {
                    $this->log('debug', 'Created Invoice for Order '.$order_id.' - '.$invoice['url']);
                }
                $this->session->data['bitpay_invoice'] = $invoice['id'];
                echo '{"url": "'.$invoice["url"].'"}';
            }
        }


    }

    /**
     */
    public function callback()
    {
		$this->load->library('bitpay');
        $this->load->model('checkout/order');

		$bp = new Bitpay(array(
            'apiKey'     => $this->config->get($this->payment_module_name.'_api_key'),
            'apiServer'  => $this->config->get($this->payment_module_name.'_api_server'),
            'pluginInfo' => 'OpenCart'.$this->plugin_verson)
        );

        if($bp->error()) {

            $this->log('error', $bp->error());
            return false;

        }else{

            $response = $bp->verifyNotification();

            if($bp->error()) {
                $this->log('error', $bp->error());
            }else{
                $reponse_statuses = array(
                    'paid' => $this->config->get($this->payment_module_name.'_paid_status_id'),
                    'confirmed' => $this->config->get($this->payment_module_name.'_confirmed_status_id'),
                    'complete' => $this->config->get($this->payment_module_name.'_complete_status_id')
                    );

                if( in_array($response['status'], array_keys($reponse_statuses)) ) {
                    $order_status_id = $this->config->get($this->payment_module_name.'_'.$response['status'].'_status_id');
                }else{
                    $this->log('warning', 'Received IPN with invoice status of '.$response['status']);
                    return false;
                }

                if($this->config->get($this->payment_module_name.'_debug_mode')) {
                    $this->log('debug', 'Received IPN for Order '.$response['posData']. ' - '.ucfirst($response['status']));
                }

                $order = $this->model_checkout_order->getOrder($response['posData']);

                if($order['order_status_id'] === '0') {
                    $this->model_checkout_order->confirm($response['posData'], $order_status_id);
                }else{
                    switch ($response['status']) {
                        case 'confirmed':
                            if ($order['order_status_id'] == $reponse_statuses['paid']) {
                                $this->model_checkout_order->update($response['posData'], $order_status_id);
                            }
                            break;
                        case 'complete':
                            if ($order['order_status_id'] == $reponse_statuses['paid'] || $order['order_status_id'] == $reponse_statuses['confirmed']) {
                                $this->model_checkout_order->update($response['posData'], $order_status_id);
                            }
                            break;
                    }
                }


            }
        }
    }

    /**
     */
    public function success()
    {
        $this->load->library('bitpay');
        $this->load->model('checkout/order');

        $order_id = $this->session->data['order_id'];
        $order    = $this->model_checkout_order->getOrder($order_id);

        $bp = new Bitpay(array(
            'apiKey'     => $this->config->get($this->payment_module_name.'_api_key'),
            'apiServer'  => $this->config->get($this->payment_module_name.'_api_server'),
            'pluginInfo' => 'OpenCart'.$this->plugin_verson)
        );

        if($bp->error()) {

            $this->log('error', $bp->error());

        }else{

            $invoice = $bp->getInvoice($this->session->data['bitpay_invoice']);

            if($bp->error()) {
                $this->log('error', $bp->error());
            }else{

                if($this->config->get($this->payment_module_name.'_debug_mode')) {
                    $this->log('debug', 'Return from BitPay for Order '.$order_id. ' - '.$invoice['url']);
                }

                $reponse_statuses = array('paid', 'confirmed', 'complete');

                if( in_array($invoice['status'], $reponse_statuses) ) {
                    $order_status_id = $this->config->get($this->payment_module_name.'_'.$invoice['status'].'_status_id');
                }else{
                    $this->redirect($this->url->link('checkout/checkout'));
                }

                if($order['order_status_id'] === '0') {
                    $this->model_checkout_order->confirm($order_id, $order_status_id);
                }

            }
        }

        $this->session->data['bitpay_invoice'] = null;
        $this->redirect($this->url->link('checkout/success'));

    }

}
