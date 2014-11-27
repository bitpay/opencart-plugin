<?php 
/**
 * @license https://github.com/bitpay/opencart-bitpay/blob/master/LICENSE MIT
 */

class ModelPaymentBitpay extends Model
{

    /**
     * @param string $address
     *
     * @return array
     */
    public function getMethod($address)
    {
        $this->load->language('payment/bitpay');

        if ($this->config->get('bitpay_status'))
        {
            $status = TRUE;
        }
        else
        {
            $status = FALSE;
        }

        $method_data = array();

        if ($status)
        { 
            $method_data = array( 
                'code'       => 'bitpay',
                'title'      => $this->language->get('text_title'),
                'sort_order' => $this->config->get('bitpay_sort_order'),
            );
        }

        return $method_data;
    }
}
