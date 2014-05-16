<?php
/*
©2011 BIT-PAY LLC.
Permission is hereby granted to any person obtaining a copy of this software
and associated documentation for use and/or modification in association with
the bit-pay.com service.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

class ControllerPaymentBitpay extends Controller {

    // below is the url that can take you do the order information
    // http://127.0.0.1/~spair/store/index.php?route=account/order/info&order_id=35

    private $payment_module_name  = 'bitpay';
	protected function index() {
        $this->language->load('payment/'.$this->payment_module_name);
    	$this->data['button_bitpay_confirm'] = $this->language->get('button_bitpay_confirm');

		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/bitpay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/bitpay.tpl';
		} else {
			$this->template = 'default/template/payment/bitpay.tpl';
		}	
		
		$this->render();
	}
	
	function log($contents){
		error_log($contents);
	}

    public function send() {
		require DIR_APPLICATION.'../bitpay/bp_lib.php';
		
        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $price = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
        $posData = $order['order_id'];

        $options = array(
			'apiKey' => $this->config->get($this->payment_module_name.'_api_key'),
            'notificationURL' => $this->url->link('payment/bitpay/callback'),
            'redirectURL' => $this->url->link('account/order/info&order_id=' . $order['order_id']),
            'currency' => $order['currency_code'],
            'physical' => 'true',
            'fullNotifications' => 'true',
            'transactionSpeed' => $this->config->get($this->payment_module_name.'_transaction_speed')
        );
        $response = bpCreateInvoice($order['order_id'], $price, $posData, $options);
		
        if(array_key_exists('error', $response)) {
            $this->log("communication error");
			$this->log($response['error']);
            echo "{\"error\": \"Error: Problem communicating with payment provider.\\nPlease try again later.\"}";
        } else {
            echo "{\"url\": \"" . $response["url"] . "\"}";
        }
    }

    public function callback() {
		require DIR_APPLICATION.'../bitpay/bp_lib.php';
		
		$apiKey = $this->config->get($this->payment_module_name.'_api_key');
		$response = bpVerifyNotification($apiKey);
		
		if (is_string($response)) {
			$this->log("bitpay interface error: $response");            
        } 
		else
			switch($response['status']) {
				case 'confirmed':
				case 'complete':
                    $this->load->model('checkout/order');
                    $order_id = $response['posData'];
                    $order = $this->model_checkout_order->getOrder($order_id);
                    $this->model_checkout_order->confirm($order_id, $this->config->get('bitpay_confirmed_status_id'));
					break;
				case 'invalid':
                    $this->load->model('checkout/order');
                    $order_id = $response['posData'];
                    $order = $this->model_checkout_order->getOrder($order_id);
                    $this->model_checkout_order->confirm($order_id, $this->config->get('bitpay_invalid_status_id'));
					break;
			}
    }
}
?>
