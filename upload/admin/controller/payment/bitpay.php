<?php
/**
 * @license https://github.com/bitpay/opencart-bitpay/blob/master/LICENSE MIT
 */

class ControllerPaymentBitpay extends Controller
{

    /**
     * @var array
     */
    private $error = array();

    /**
     * @var string
     */
    private $payment_module_name  = 'bitpay';

    /**
     * @return boolean
     */
    private function validate()
    {
        // $log = new Log('bitpay.log');
        // $log->write("Validation ran.");

        if (!$this->user->hasPermission('modify', 'payment/'.$this->payment_module_name))
        {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['bitpay_api_key']) && !preg_match('/[a-zA-Z0-9]{30,50}/', $this->request->post['bitpay_api_key']))
        {
            $this->error['api_key'] = $this->language->get('error_api_key_valid');
        }

        if(!filter_var($this->request->post['bitpay_notify_url'], FILTER_VALIDATE_URL))
        {
            $this->error['notify_url'] = $this->language->get('error_notify_url_valid');
        }

        if(!filter_var($this->request->post['bitpay_return_url'], FILTER_VALIDATE_URL))
        {
            $this->error['return_url'] = $this->language->get('error_return_url_valid');
        }

        if (!$this->request->post['bitpay_api_key'])
        {
            $this->error['api_key'] = $this->language->get('error_api_key');
        }

        if (!$this->request->post['bitpay_notify_url'])
        {
            $this->error['notify_url'] = $this->language->get('error_notify_url');
        }

        if (!$this->request->post['bitpay_return_url'])
        {
            $this->error['return_url'] = $this->language->get('error_return_url');
        }

        if (!$this->error)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     */
    public function index()
    {
        $this->load->language('payment/'.$this->payment_module_name);
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate()))
        {
            $this->model_setting_setting->editSetting($this->payment_module_name, $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
        }
        if (isset($this->error['warning']))
        {
            $this->data['error_warning'] = $this->error['warning'];
        }
        else
        {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success']))
        {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        else
        {
            $this->data['success'] = '';
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['heading_title']           = $this->language->get('heading_title');
        $this->data['text_enabled']            = $this->language->get('text_enabled');
        $this->data['text_disabled']           = $this->language->get('text_disabled');
        $this->data['text_bitpay_support']     = $this->language->get('text_bitpay_support');
        $this->data['text_live']               = $this->language->get('text_live');
        $this->data['text_test']               = $this->language->get('text_test');
        $this->data['text_high']               = $this->language->get('text_high');
        $this->data['text_medium']             = $this->language->get('text_medium');
        $this->data['text_low']                = $this->language->get('text_low');
        $this->data['text_all_geo_zones']      = $this->language->get('text_all_geo_zones');
        $this->data['entry_api_key']           = $this->language->get('entry_api_key');
        $this->data['help_api_key']            = $this->language->get('help_api_key');
        $this->data['help_api_key_test']       = $this->language->get('help_api_key_test');
        $this->data['entry_api_server']        = $this->language->get('entry_api_server');
        $this->data['help_api_server']         = $this->language->get('help_api_server');
        $this->data['entry_risk_speed']        = $this->language->get('entry_risk_speed');
        $this->data['help_risk_speed']         = $this->language->get('help_risk_speed');
        $this->data['entry_geo_zone']          = $this->language->get('entry_geo_zone');
        $this->data['entry_status']            = $this->language->get('entry_status');
        $this->data['entry_sort_order']        = $this->language->get('entry_sort_order');
        $this->data['entry_paid_status']       = $this->language->get('entry_paid_status');
        $this->data['help_paid_status']        = $this->language->get('help_paid_status');
        $this->data['entry_confirmed_status']  = $this->language->get('entry_confirmed_status');
        $this->data['help_confirmed_status']   = $this->language->get('help_confirmed_status');
        $this->data['entry_complete_status']   = $this->language->get('entry_complete_status');
        $this->data['help_complete_status']    = $this->language->get('help_complete_status');
        $this->data['entry_notify_url']        = $this->language->get('entry_notify_url');
        $this->data['help_notify_url']         = $this->language->get('help_notify_url');
        $this->data['entry_return_url']        = $this->language->get('entry_return_url');
        $this->data['help_return_url']         = $this->language->get('help_return_url');
        $this->data['entry_debug_mode']        = $this->language->get('entry_debug_mode');
        $this->data['help_debug_mode']         = $this->language->get('help_debug_mode');
        $this->data['button_save']             = $this->language->get('button_save');
        $this->data['button_cancel']           = $this->language->get('button_cancel');
        $this->data['button_clear']            = $this->language->get('button_clear');
        $this->data['text_settings']           = $this->language->get('text_settings');
        $this->data['text_log']                = $this->language->get('text_log');
        $this->data['text_general']            = $this->language->get('text_general');
        $this->data['text_statuses']           = $this->language->get('text_statuses');
        $this->data['text_advanced']           = $this->language->get('text_advanced');
        $this->data['text_changes']            = $this->language->get('text_changes');
        $this->data['entry_default']           = $this->language->get('entry_default');

        $notify_url = $this->url->link('payment/bitpay/callback');
        $notify_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $notify_url);
        $notify_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $notify_url);
        $return_url = $this->url->link('payment/bitpay/success');
        $return_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $return_url);
        $return_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $return_url);
        $this->data['notify_default']          = $notify_url;
        $this->data['return_default']          = $return_url;

        if (isset($this->error['api_key']))
        {
            $this->data['error_api_key'] = $this->error['api_key'];
        }
        else
        {
            $this->data['error_api_key'] = '';
        }

        if (isset($this->error['notify_url']))
        {
            $this->data['error_notify_url'] = $this->error['notify_url'];
        }
        else
        {
            $this->data['error_notify_url'] = '';
        }

        if (isset($this->error['return_url']))
        {
            $this->data['error_return_url'] = $this->error['return_url'];
        }
        else
        {
            $this->data['error_return_url'] = '';
        }

        $this->data['breadcrumbs']   = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/bitpay', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/'.$this->payment_module_name.'&token=' . $this->session->data['token'];
        $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];

        $this->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');
        $this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $file = DIR_LOGS . 'bitpay.log';

        if (file_exists($file)) {
            $this->data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
        } else {
            $this->data['log'] = '';
        }

        if (isset($this->request->post[$this->payment_module_name.'_api_key']))
        {
            $this->data[$this->payment_module_name.'_api_key'] = $this->request->post[$this->payment_module_name.'_api_key'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_api_key'] = $this->config->get($this->payment_module_name.'_api_key');
        }

        if (isset($this->request->post[$this->payment_module_name.'_api_server']))
        {
            $this->data[$this->payment_module_name.'_api_server'] = $this->request->post[$this->payment_module_name.'_api_server'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_api_server'] = $this->config->get($this->payment_module_name.'_api_server');
        }

        if (isset($this->request->post[$this->payment_module_name.'_risk_speed']))
        {
            $this->data[$this->payment_module_name.'_risk_speed'] = $this->request->post[$this->payment_module_name.'_risk_speed'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_risk_speed'] = $this->config->get($this->payment_module_name.'_risk_speed');
        }

        if (isset($this->request->post[$this->payment_module_name.'_geo_zone']))
        {
            $this->data[$this->payment_module_name.'_geo_zone'] = $this->request->post[$this->payment_module_name.'_geo_zone'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_geo_zone'] = $this->config->get($this->payment_module_name.'_geo_zone');
        }

        if (isset($this->request->post[$this->payment_module_name.'_status']))
        {
            $this->data[$this->payment_module_name.'_status'] = $this->request->post[$this->payment_module_name.'_status'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_status'] = $this->config->get($this->payment_module_name.'_status');
        }

        if (isset($this->request->post[$this->payment_module_name.'_sort_order']))
        {
            $this->data[$this->payment_module_name.'_sort_order'] = $this->request->post[$this->payment_module_name.'_sort_order'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_sort_order'] = $this->config->get($this->payment_module_name.'_sort_order');
        }

        if (isset($this->request->post[$this->payment_module_name.'_paid_status_id']))
        {
            $this->data[$this->payment_module_name.'_paid_status_id'] = $this->request->post[$this->payment_module_name.'_paid_status_id'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_paid_status_id'] = $this->config->get($this->payment_module_name.'_paid_status_id');
        }

        if (isset($this->request->post[$this->payment_module_name.'_confirmed_status_id']))
        {
            $this->data[$this->payment_module_name.'_confirmed_status_id'] = $this->request->post[$this->payment_module_name.'_confirmed_status_id'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_confirmed_status_id'] = $this->config->get($this->payment_module_name.'_confirmed_status_id');
        }

        if (isset($this->request->post[$this->payment_module_name.'_complete_status_id']))
        {
            $this->data[$this->payment_module_name.'_complete_status_id'] = $this->request->post[$this->payment_module_name.'_complete_status_id'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_complete_status_id'] = $this->config->get($this->payment_module_name.'_complete_status_id');
        }

        if (isset($this->request->post[$this->payment_module_name.'_notify_url']))
        {
            $this->data[$this->payment_module_name.'_notify_url'] = $this->request->post[$this->payment_module_name.'_notify_url'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_notify_url'] = $this->config->get($this->payment_module_name.'_notify_url');
        }
        if (isset($this->request->post[$this->payment_module_name.'_return_url']))
        {
            $this->data[$this->payment_module_name.'_return_url'] = $this->request->post[$this->payment_module_name.'_return_url'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_return_url'] = $this->config->get($this->payment_module_name.'_return_url');
        }
        if (isset($this->request->post[$this->payment_module_name.'_debug_mode']))
        {
            $this->data[$this->payment_module_name.'_debug_mode'] = $this->request->post[$this->payment_module_name.'_debug_mode'];
        }
        else
        {
            $this->data[$this->payment_module_name.'_debug_mode'] = $this->config->get($this->payment_module_name.'_debug_mode');
        }

        $this->data['clear'] = $this->url->link('payment/bitpay/clear', 'token=' . $this->session->data['token'], 'SSL');

        $this->template = 'payment/'.$this->payment_module_name.'.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    public function clear() {
        $this->language->load('payment/bitpay');

        $file = DIR_LOGS . 'bitpay.log';

        $handle = fopen($file, 'w+');

        fclose($handle);

        $this->session->data['success'] = $this->language->get('text_success');

        $this->redirect($this->url->link('payment/bitpay', 'token=' . $this->session->data['token'], 'SSL'));
    }

    public function install() {
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $order_statuses = $this->model_localisation_order_status->getOrderStatuses();
        $statuses = array();
        foreach ($order_statuses as $order_status) {
            $statuses[$order_status['name']] = $order_status['order_status_id'];
        }
        $order_status_default = $this->config->get('config_order_status_id');
        $notify_url = $this->url->link('payment/bitpay/callback');
        $notify_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $notify_url);
        $notify_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $notify_url);
        $return_url = $this->url->link('payment/bitpay/success');
        $return_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $return_url);
        $return_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $return_url);

        $this->model_setting_setting->editSetting($this->payment_module_name, array(
            $this->payment_module_name.'_api_server'          => 'live',
            $this->payment_module_name.'_paid_status_id'      => (isset($statuses['Processing'])) ? $statuses['Processing'] : $order_status_default,
            $this->payment_module_name.'_confirmed_status_id' => (isset($statuses['Processed'])) ? $statuses['Processed'] : $order_status_default,
            $this->payment_module_name.'_complete_status_id'  => (isset($statuses['Complete'])) ? $statuses['Complete'] : $order_status_default,
            $this->payment_module_name.'_notify_url'          => $notify_url,
            $this->payment_module_name.'_return_url'          => $return_url
        ));
    }
}
