Paytoshi Faucet
========================================================

Faucet Script for [Paytoshi](https://paytoshi.org): the Bitcoin micropayment wallet. 
Create a Paytoshi account, get an apikey and start using your Bitcoin faucet website.


Based on [Slim](http://github.com/codeguy/Slim) and [Twig](https://github.com/fabpot/Twig).

## Features
* Ready-to-use script
* Captcha support (ReCaptcha v2, SolveMedia, FunCaptcha)
* Private admin area
* Minimal configuration needed
* Support to theming (Twig-powered)
* Cloudflare support
* Clean and lightweight

## Requirements
* Apache >= 2.2 (with mod_rewrite)
* PHP >= 5.3
* MySQL >= 5 (with PDO support)
* php5-mcrypt extension

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
* Visit your faucet. A wizard will create the admin password and populate the database. 
* Customize your faucet in your admin area.

## Contributing
When contributing code to Paytoshi Faucet, you must follow its coding standards.

Paytoshi Faucet follows the standards defined in the [PSR-0](http://www.php-fig.org/psr/psr-0/),
[PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/) and
[PSR-4](http://www.php-fig.org/psr/psr-4/) documents.

## License
Paytoshi Faucet is [BSD licensed](./LICENSE).
