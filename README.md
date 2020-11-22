# Simple Playcoin JSON-RPC client based on GuzzleHttp

[![Latest Stable Version](https://poser.pugx.org/denpaw/php-playcoinrpc/v/stable)](https://packagist.org/packages/denpaw/php-playcoinrpc)
[![License](https://poser.pugx.org/denpaw/php-playcoinrpc/license)](https://packagist.org/packages/denpaw/php-playcoinrpc)
[![Build Status](https://travis-ci.org/denpawmusic/php-playcoinrpc.svg)](https://travis-ci.org/denpawmusic/php-playcoinrpc)
[![Code Climate](https://codeclimate.com/github/denpawmusic/php-playcoinrpc/badges/gpa.svg)](https://codeclimate.com/github/denpawmusic/php-playcoinrpc)
[![Code Coverage](https://codeclimate.com/github/denpawmusic/php-playcoinrpc/badges/coverage.svg)](https://codeclimate.com/github/denpawmusic/php-playcoinrpc/coverage)
[![Join the chat at https://gitter.im/php-playcoinrpc/Lobby](https://badges.gitter.im/php-playcoinrpc/Lobby.svg)](https://gitter.im/php-playcoinrpc/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Installation
Run ```php composer.phar require denpaw/php-playcoinrpc``` in your project directory or add following lines to composer.json
```javascript
"require": {
    "denpaw/php-playcoinrpc": "^2.1"
}
```
and run ```php composer.phar install```.

## Requirements
PHP 7.1 or higher  
_For PHP 5.6 and 7.0 use [php-playcoinrpc v2.0.x](https://github.com/denpawmusic/php-playcoinrpc/tree/2.0.x)._

## Usage
Create new object with url as parameter
```php
/**
 * Don't forget to include composer autoloader by uncommenting line below
 * if you're not already done it anywhere else in your project.
 **/
// require 'vendor/autoload.php';

use Denpaw\Playcoin\Client as PlaycoinClient;

$playcoind = new PlaycoinClient('http://rpcuser:rpcpassword@localhost:8332/');
```
or use array to define your playcoind settings
```php
/**
 * Don't forget to include composer autoloader by uncommenting line below
 * if you're not already done it anywhere else in your project.
 **/
// require 'vendor/autoload.php';

use Denpaw\Playcoin\Client as PlaycoinClient;

$playcoind = new PlaycoinClient([
    'scheme'        => 'http',                 // optional, default http
    'host'          => 'localhost',            // optional, default localhost
    'port'          => 8332,                   // optional, default 8332
    'user'          => 'rpcuser',              // required
    'password'      => 'rpcpassword',          // required
    'ca'            => '/etc/ssl/ca-cert.pem',  // optional, for use with https scheme
    'preserve_case' => false,                  // optional, send method names as defined instead of lowercasing them
]);
```
Then call methods defined in [Playcoin Core API Documentation](https://playcoin.org/en/developer-reference#playcoin-core-apis) with magic:
```php
/**
 * Get block info.
 */
$block = $playcoind->getBlock('000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f');

$block('hash')->get();     // 000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f
$block['height'];          // 0 (array access)
$block->get('tx.0');       // 4a5e1e4baab89f3a32518a88c31bc87f618f76673e2cc77ab2127b7afdeda33b
$block->count('tx');       // 1
$block->has('version');    // key must exist and CAN NOT be null
$block->exists('version'); // key must exist and CAN be null
$block->contains(0);       // check if response contains value
$block->values();          // array of values
$block->keys();            // array of keys
$block->random(1, 'tx');   // random block txid
$block('tx')->random(2);   // two random block txid's
$block('tx')->first();     // txid of first transaction
$block('tx')->last();      // txid of last transaction

/**
 * Send transaction.
 */
$result = $playcoind->sendToAddress('mmXgiR6KAhZCyQ8ndr2BCfEq1wNG2UnyG6', 0.1);
$txid = $result->get();

/**
 * Get transaction amount.
 */
$result = $playcoind->listSinceBlock();
$playcoin = $result->sum('transactions.*.amount');
$satoshi = \Denpaw\Playcoin\to_satoshi($playcoin);
```
To send asynchronous request, add Async to method name:
```php
$playcoind->getBlockAsync(
    '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f',
    function ($response) {
        // success
    },
    function ($exception) {
        // error
    }
);
```

You can also send requests using request method:
```php
/**
 * Get block info.
 */
$block = $playcoind->request('getBlock', '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f');

$block('hash');            // 000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f
$block['height'];          // 0 (array access)
$block->get('tx.0');       // 4a5e1e4baab89f3a32518a88c31bc87f618f76673e2cc77ab2127b7afdeda33b
$block->count('tx');       // 1
$block->has('version');    // key must exist and CAN NOT be null
$block->exists('version'); // key must exist and CAN be null
$block->contains(0);       // check if response contains value
$block->values();          // get response values
$block->keys();            // get response keys
$block->first('tx');       // get txid of the first transaction
$block->last('tx');        // get txid of the last transaction
$block->random(1, 'tx');   // get random txid

/**
 * Send transaction.
 */
$result = $playcoind->request('sendtoaddress', 'mmXgiR6KAhZCyQ8ndr2BCfEq1wNG2UnyG6', 0.06);
$txid = $result->get();

```
or requestAsync method for asynchronous calls:
```php
$playcoind->requestAsync(
    'getBlock',
    '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f',
    function ($response) {
        // success
    },
    function ($exception) {
        // error
    }
);
```

## Multi-Wallet RPC
You can use `wallet($name)` function to do a [Multi-Wallet RPC call](https://en.playcoin.it/wiki/API_reference_(JSON-RPC)#Multi-wallet_RPC_calls):
```php
/**
 * Get wallet2.dat balance.
 */
$balance = $playcoind->wallet('wallet2.dat')->getbalance();

echo $balance->get(); // 0.10000000
```

## Exceptions
* `Denpaw\Playcoin\Exceptions\BadConfigurationException` - thrown on bad client configuration.
* `Denpaw\Playcoin\Exceptions\BadRemoteCallException` - thrown on getting error message from daemon.
* `Denpaw\Playcoin\Exceptions\ConnectionException` - thrown on daemon connection errors (e. g. timeouts)


## Helpers
Package provides following helpers to assist with value handling.
#### `to_playcoin()`
Converts value in satoshi to playcoin.
```php
echo Denpaw\Playcoin\to_playcoin(100000); // 0.00100000
```
#### `to_satoshi()`
Converts value in playcoin to satoshi.
```php
echo Denpaw\Playcoin\to_satoshi(0.001); // 100000
```
#### `to_ubtc()`
Converts value in playcoin to ubtc/bits.
```php
echo Denpaw\Playcoin\to_ubtc(0.001); // 1000.0000
```
#### `to_mbtc()`
Converts value in playcoin to mbtc.
```php
echo Denpaw\Playcoin\to_mbtc(0.001); // 1.0000
```
#### `to_fixed()`
Trims float value to precision without rounding.
```php
echo Denpaw\Playcoin\to_fixed(0.1236, 3); // 0.123
```

## License

This product is distributed under MIT license.

## Donations

If you like this project, please consider donating:<br>
**BTC**: 3L6dqSBNgdpZan78KJtzoXEk9DN3sgEQJu<br>
**Bech32**: bc1qyj8v6l70c4mjgq7hujywlg6le09kx09nq8d350

❤Thanks for your support!❤
