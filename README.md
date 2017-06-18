# MVF Developer Tests
## ExchangeRates

### Requirements
- PHP with PDO and SQLite
- Composer

### Installation
1. Clone the repository [https://github.com/zares85/mvf](https://github.com/zares85/mvf)
1. Edit application/config/exchange_rate.php file adding your application id from [openexchangerates.org](openexchangerates.org)
1. Run `composer install` (only for running tests)

### Usage

#### Update exchange rates
Execute `php index.php rate update_exchange_rates`

#### Convert a currency
Execute `php index.php rate convert_currency <amount> <from currency> <to currency>`

Example: `php index.php rate convert_currency 1 GBP EUR`

#### Run tests (PHP >= 5.5 required)
Execute `vendor/bin/php`