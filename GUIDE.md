# Using BitPay for OpenCart
## Prerequisites
Last Cart Version Tested: 2.3.0.2

If you have OpenCart v3, please go to https://github.com/bitpay/opencart3-plugin/releases

You must have a BitPay merchant account to use this library.  It's free to [sign-up for a BitPay merchant account](https://bitpay.com/start).
You can also test this plugin with a test BitPay merchant account. For more information about setting up a test BitPay merchant account & a testnet bitcoin wallet, please see https://bitpay.com/docs/testing

## Getting Started
Go to the [latest release](https://github.com/bitpay/opencart-plugin/releases/latest) and download the file called `bitpay-opencart.ocmod.zip`

Note: if you're running an older version of OpenCart (e.g. v2.2), please select the plugin version that's applicable to your OpenCart version.


## Install
### Via Extension Installer
In your OpenCart store's administration section, go to Extensions > Extension Installer

Upload `bitpay-opencart.ocmod.zip`

OpenCart needs a working FTP server to install files. If the progress bar hangs half way, it probably means that your OpenCart FTP settings are incorrect. You can configure the FTP credentials of your server under System -> Settings -> FTP

After the installation indicates it's successful, you can continue with the setup.

### Via FTP
Upload all directories and files in `bitpay-opencart.ocmod.zip` to your OpenCart store's root directory.
Note that older versions of the plugin (e.g. v2.01 for OpenCart v2.2) contain all files in the `upload` directory.

## Setup
### Install the Payment Extension
Go to Extensions > Payments.
Find the BitPay payment extension and click the green install button. The page will refresh, you'll see a success message, and the install button will turn into a red uninstall button.
Click on the edit button.  You are now at the BitPay plugin's configuration screen.

### Connect to BitPay
For live transactions, just press the Connect to BitPay button.  For test transactions, press the drop down button attached to the Connect to Bitpay button and select testnet.
You will be redirected to your BitPay merchant account and asked to approve a token which connects your store to BitPay's API.
After pressing Approve, please go back to the BitPay/OpenCart plugin configuration screen.

Configure the settings that work best for you.  Each setting has a tooltip that can help explain what it does.
Set the status setting to enabled and click save at the top right of the page.

You're done!
