bitpay/opencart-plugin
======================

# Installation

Copy these files into your OpenCart directory.

# Configuration

1. Create an API key at bitpay.com under the "My Account" section.
2. In the opencart administration under Extensions->Payments, click the "Install"
   link on the Bitpay row.
3. Also under Extensions->Payments, click the "Edit" link on the Bitpay row.
4. Set the API key to the key you created in step 1.  
5. Set the confirmed status to the order status that you would like to to use
   when a bitcoin payment is confirmed (according to your speed preference).  A
   status of "pending" is typically used.
6. Set the invalid status to the order status that you would like to use when a
    bitcoin payment has been determine to be invalid.  A status of "reversed" is
    typically used.  If you've chosen the low speed setting, an invoice will never
    become invalid.  For the medium and high speed settings, the invalid status is
    possible, but extremely rare.
7. Select a transaction speed.  The high speed will send a confirmation as soon
    as a transaction is received in the bitcoin network (usually a few seconds).  A
    medium speed setting will typically take 10 minutes.  The low speed setting
    usually takes around 1 hour.  See the bitpay.com merchant documentation for a 
    full description of the transaction speed settings.
8. Set the status to enabled (this activates the bitpay payment extension and 
    enabled shoppers to select the bitcoin payment method).
9. Select a sort order.  The sort order determines the ordering of payment options
    presented to the shopper.

# Usage

When a shopping chooses the Bitcoin payment method, they will be presented with an
order summary as the next step (prices are shown in whatever currency they've selected
for shopping).  They will be presented with a button called "Pay with Bitcoin."  This
button takes the shopper to a bitpay.com invoice where the user is presented with
bitcoin payment instructions.  Once payment is received, a link is presented to the 
shopper that will take them back to the order summary.

You can add BTC as a currency to your opencart installation.  This will allow shoppers
to view prices in BTC and when they checkout, the final price will be transmitted to
bitpay.com in BTC.  The exact BTC amount transmitted is what will be requested of the 
shopper.  If the final price is expressed in another currency, then bitpay.com will
convert that rate to a BTC total based on current exchanges rates (if bitpay.com
supports that currency).  

Note: This extension does not provide a means of automatically pulling a current
BTC exchange rate for presenting BTC prices to shoppers.

# Support

## BitPay Support

* [Github Issues](https://github.com/bitpay/opencart-plugin/issues)
  * Open an Issue if you are having issues with this plugin
* [Support](https://support.bitpay.com/)
  * Checkout the BitPay support site

## OpenCart Support

* [Homepage](http://www.opencart.com/)
* [Documentation](http://docs.opencart.com/)
* [Forums](http://forum.opencart.com/)

# Contribute

To contribute to this project, please fork and submit a pull request.

# License

The MIT License (MIT)

Copyright (c) 2011-2014 BitPay

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
