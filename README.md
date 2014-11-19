Paytoshi Faucet
========================================================

Faucet Script for [Paytoshi](https://paytoshi.org): the micro payment wallet.

Based on [Slim](http://github.com/codeguy/Slim).

## Features
* Ready-to-use script
* Captcha support (SolveMedia)
* Private admin area
* Minimal configuration needed
* Simple templating (based on Twig)
* Clean, lightweight but still Slim-powered

## Requirements
* PHP >= 5.3.0
* MySQL >= 5 (with PDO support)
* (optional) php5-mcrypt extension

## Installation
* Download the faucet zip
* Extract the files in your webserver public area
* Edit ./config/config.yml with the database configuration parameters. For example:
``` yaml
# Standard Configuration
database:
    host: localhost
    username: root
    password: root
    name: paytoshi_faucet
```
* Visit your faucet. A wizard should create the database and admin password. You can edit then the basic parameters in your admin area.

## Development
* ReCaptcha support is planned shortly.
* MySQL is the only supported database
