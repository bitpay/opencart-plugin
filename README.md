# Notice

This is a Community-supported project.

If you are interested in becoming a maintainer of this project, please contact us at integrations@bitpay.com. Developers at BitPay will attempt to work along the new maintainers to ensure the project remains viable for the foreseeable future.

# BitPay for OpenCart

[![Latest Version](https://img.shields.io/github/release/bitpay/opencart-plugin.svg?style=flat-square)](https://github.com/bitpay/opencart-plugin/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/bitpay/opencart-plugin/master.svg?style=flat-square)](https://travis-ci.org/bitpay/opencart-plugin)
[![Join the chat at https://gitter.im/bitpay/opencart-plugin](http://sambohler.github.io/gitter.svg)](https://gitter.im/bitpay/opencart-plugin?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Installation

Follow the instructions found in the [BitPay for OpenCart Guide](GUIDE.md)

## Development Setup

``` bash
# Clone the repo
$ git clone https://github.com/bitpay/opencart-plugin.git
$ cd ./opencart-plugin

# Install dependencies via Composer
$ composer install

# Set Environment Variables (variables needed can be found in .env.sample)
$ cp .env.sample .env

# After modifying the Environment Variables for your environment setup OpenCart
$ ./bin/robo setup
```

## Development Workflow

``` bash
# Run PHP Server of OpenCart installation and redirect bash I/O
$ ./bin/robo server &

# Watch for source code changes and copy them to the OpenCart installation
$ ./bin/robo watch
```

## Testing

``` bash
$ ./bin/robo test
```

## Build

``` bash
$ ./bin/robo build

# Outputs:
# ./build/bitpay-opencart - the distribution files
# ./build/bitpay-opencart.ocmod.zip - the distribution archive
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
