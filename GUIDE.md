# Using BitPay for OpenCart
## Prerequisites
Last Cart Version Tested: 2.3.0.2

You must have a BitPay merchant account to use this library.  It's free to [sign-up for a BitPay merchant account](https://bitpay.com/start).
You can also test this plugin with a test BitPay merchant account. For more information about setting up a test BitPay merchant account & a testnet bitcoin wallet, please see https://bitpay.com/docs/testing

## Getting Started
Go to the [latest release](https://github.com/bitpay/opencart-plugin/releases/latest) and download the file called `bitpay-opencart.ocmod.zip`


## Install
### Via Extension Installer
In your OpenCart store's administration section, go to Extensions > Extenstion Installer
Upload `bitpay-opencart.ocmod.zip` *Note: you have to setup the FTP settings in the Store settings to use the Extension Installer
Click 'Continue'

### Via FTP
Upload all files in the `upload` directory in `bitpay-opencart.ocmod.zip` to your OpenCart store's installation directory.

## Setup
### Install the Payment Extension
Go to Extensions > Payments.
Find the BitPay payment extension and click the install button.  The page will refresh, you'll see a success message, and the install button will turn into a red uninstall button.
Click on the edit button.  You are now at the BitPay plugin's configuration screen.

### Connect to BitPay
For live transactions, just press the Connect to BitPay button.  For test transactions, press the drop down button attached to the Connect to Bitpay button and select testnet.
You will be redirected to your BitPay merchant account and asked to approve a token which connects your store to BitPay's API.
Upon pressing Approve, you will be redirected to BitPay plugin's configuration screen.

Configure the settings that work best for you.  Each setting has a tooltip that can help explain what it does.
Set the status setting to enabled and click save at the top right of the page.

You're done!
