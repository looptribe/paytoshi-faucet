Paytoshi Faucet
========================================================

[![Build Status](https://img.shields.io/travis/looptribe/paytoshi-faucet.svg)](https://travis-ci.org/looptribe/paytoshi-faucet)
[![Coverage Status](https://img.shields.io/coveralls/looptribe/paytoshi-faucet.svg)](https://coveralls.io/github/looptribe/paytoshi-faucet)

Faucet Script for [Paytoshi](https://paytoshi.org): the Bitcoin micropayment wallet. It integrates the [Paytoshi API library](https://github.com/looptribe/paytoshi-library-php) with the faucet frontend and the admin area.

Create a Paytoshi account, get an apikey and start using your Bitcoin faucet website.

Based on [Silex](http://silex.sensiolabs.org/) and [Twig](https://github.com/fabpot/Twig).

[Changelog](CHANGELOG.md)

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
* PHP >= 5.3.3 (but NOT PHP 5.3.16)
* MySQL >= 5 (with PDO support)
* php5-mcrypt extension
* `date.timezone` entry in php.ini must be set

## Installation
* Download the [faucet zip](https://paytoshi.org/faucet-script)
* Extract the files in your webserver public area
* Visit your faucet. A wizard will ask for database connection information, create the admin password and populate the database.
* Customize your faucet in your admin area.

## Upgrade from Paytoshi Faucet Script v1
The new version should be mostly compatible with your faucet installation.

Before attempting an upgrade always backup your database and installation folder first.

Be aware that we changed our theming support so if you customized the files under the `themes` directory you may need to redo your changes on the new files. In any other case just download the faucet zip and extract it on top of your existing installation.

## Contributing
When contributing code to Paytoshi Faucet, you must follow its coding standards.

Paytoshi Faucet follows the standards defined in the [PSR-0](http://www.php-fig.org/psr/psr-0/),
[PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/) and
[PSR-4](http://www.php-fig.org/psr/psr-4/) documents.

## License
Paytoshi Faucet is [BSD licensed](./LICENSE).
