bitpay/opencart-bitpay
======================

# Installation

Copy the files from `upload` to your OpenCart installation directory.

# Upgrade

If you are upgrading from an earlier version of BitPay's OpenCart plugin, you'll need to remove the `bitpay` folder from your OpenCart installation directory, then proceed with the installation instructions above.

# Configuration

1. Create an API key at bitpay.com under the "My Account" section.
2. In the opencart administration under Extensions->Payments, click the "Install"
   link on the Bitpay row.
3. Also under Extensions->Payments, click the "Edit" link on the Bitpay row.
4. Set the API key to the key you created in step 1.  
5. Select a transaction speed.  The high speed will send a confirmation as soon
    as a transaction is received in the bitcoin network (usually a few seconds).  A
    medium speed setting will typically take 10 minutes.  The low speed setting
    usually takes around 1 hour.  See the bitpay.com merchant documentation for a 
    full description of the transaction speed settings.
6. Set the status to enabled (this activates the bitpay payment extension and 
    enabled shoppers to select the bitcoin payment method).
7. Select a sort order.  The sort order determines the ordering of payment options
    presented to the shopper.