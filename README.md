Paytoshi Faucet
========================================================

Faucet Script for [Paytoshi](https://paytoshi.org): the Bitcoin micropayment wallet. 
Create a Paytoshi account, get an apikey and start using your Bitcoin faucet website.


Based on [Slim](http://github.com/codeguy/Slim) and [Twig](https://github.com/fabpot/Twig).

## Features
* Ready-to-use script
* Captcha support (SolveMedia, ReCaptcha)
* Private admin area
* Minimal configuration needed
* Support to theming (Twig-powered)
* Clean and lightweight

## Requirements
* Apache >= 2.2
* PHP >= 5.3.0
* MySQL >= 5 (with PDO support)
* php5-mcrypt extension (optional)

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
* Visit your faucet. A wizard will create the admin password and populate the database. You will then be able to edit your faucet in your admin area.
