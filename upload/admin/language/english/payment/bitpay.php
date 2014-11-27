<?php
/**
 * @license https://github.com/bitpay/opencart-bitpay/blob/master/LICENSE MIT
 */

$_['heading_title'] = 'BitPay';

// Text
$_['text_payment']        = 'Payment';
$_['text_success']        = 'Success: You have modified the BitPay payment module!';
$_['text_changes']        = 'There are unsaved changes.';
$_['text_bitpay']         = '<a onclick="window.open(\'https://www.bitpay.com/\');"><img src="view/image/payment/bitpay.png" alt="BitPay" title="bitpay" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_bitpay_support'] = '<span>For <strong>24/7 support</strong>, please visit our website <a href="https://support.bitpay.com" target="_blank">https://support.bitpay.com</a> or send an email to <a href="mailto:support@bitpay.com" target="_blank">support@bitpay.com</a> for immediate attention</span>';
$_['text_bitpay_apply']   = '<a href="https://bitpay.com/start" title="Apply for a BitPay account">Start Accepting Bitcoin with BitPay</a>';
$_['text_high']           = 'High';
$_['text_medium']         = 'Medium';
$_['text_low']            = 'Low';
$_['text_live']           = 'Live';
$_['text_test']           = 'Test';
$_['text_all_geo_zones']  = 'All Geo Zones';
$_['text_settings']       = 'Settings';
$_['text_log']            = 'Log';
$_['text_general']        = 'General';
$_['text_statuses']       = 'Order Statuses';
$_['text_advanced']       = 'Advanced';


// Entry
$_['entry_api_key']           = 'API Key:';
$_['help_api_key']            = '<a href="https://bitpay.com/api-keys" target="_blank">https://bitpay.com/api-keys</a>';
$_['help_api_key_test']       = '<a href="https://test.bitpay.com/api-keys" target="_blank">https://test.bitpay.com/api-keys</a>';
$_['entry_api_server']        = 'API Server:';
$_['help_api_server']         = 'Use the '.$_['text_live'].' or '.$_['text_test'].' server to process transactions';
$_['entry_risk_speed']        = 'Risk/Speed:';
$_['help_risk_speed']         = '<strong>High</strong> speed confirmations typically take 5-10 seconds, and can be used for digital goods or low-risk items<br><strong>Low</strong> speed confirmations take about 1 hour, and should be used for high-value items';
$_['entry_geo_zone']          = 'Geo Zone:';
$_['entry_status']            = 'Status:';
$_['entry_sort_order']        = 'Sort Order:';
$_['entry_new_status']        = 'New:';
$_['help_new_status']         = 'A new or partially paid invoice awaiting full payment';
$_['entry_paid_status']       = 'Paid:';
$_['help_paid_status']        = 'A fully paid invoice awaiting confirmation';
$_['entry_confirmed_status']  = 'Confirmed:';
$_['help_confirmed_status']   = 'A confirmed invoice per Risk/Speed settings';
$_['entry_complete_status']   = 'Complete:';
$_['help_complete_status']    = 'A confirmed invoice that has been credited to the merchant&#8217;s account';
$_['entry_expired_status']    = 'Expired:';
$_['help_expired_status']     = 'An invoice where full payment was not received and the 15 minute payment window has elapsed';
$_['entry_invalid_status']    = 'Invalid:';
$_['help_invalid_status']     = 'An invoice that was fully paid but wasn&#8217;t confirmed';
$_['entry_notify_url']        = 'Notification URL:';
$_['help_notify_url']         = 'BitPay&#8217;s IPN will post invoice status updates to this URL';
$_['entry_return_url']        = 'Return URL:';
$_['help_return_url']         = 'BitPay will provide a redirect link to the user for this URL upon successful payment of the invoice';
$_['entry_debug_mode']        = 'Debug Mode:';
$_['help_debug_mode']         = 'Logs additional information to the BitPay log';
$_['entry_default']           = 'Default:';

// Error
$_['error_permission']        = 'Warning: You do not have permission to modify bitpay payment module.';

$_['error_api_key']           = 'API Key is required (for authenticated payment notices)';
$_['error_notify_url']        = 'Notification URL is required';
$_['error_return_url']        = 'Return URL is required';
$_['error_api_key_valid']     = 'API Key needs to be a valid BitPay API Access Key';
$_['error_notify_url_valid']  = 'Notification URL needs to be a valid URL resource';
$_['error_return_url_valid']  = 'Return URL needs to be a valid URL resource';
