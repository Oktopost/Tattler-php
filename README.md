# Tattler PHP client

[![Build Status](https://travis-ci.org/Oktopost/Tattler-php.svg)](https://travis-ci.org/Oktopost/Tattler-php)
[![Coverage Status](https://coveralls.io/repos/github/Oktopost/Tattler-php/badge.svg?branch=master)](https://coveralls.io/github/Oktopost/Tattler-php?branch=master)
[![License MIT](https://img.shields.io/badge/license-MIT-green.svg)](https://github.com/Oktopost/Tattler-php/blob/master/LICENSE)

Send async messages to your users using [Tattler](https://github.com/grohman/tattler)

- [Simple example project](https://github.com/grohman/tattler-php-chat-example)

## Installation

```bash
$ composer require oktopost/tattler-php
```
Or add to `composer.json`:

```json
"require": {
    "oktopost/tattler-php": "^1.0"
}
```
and then run `composer update`.

## Setup

```php
$config = new TattlerConfig();
$tattlerConfig->fromArray([
        'WsAddress'         => 'TATTLER_WEBSOCKET_ADDRESS',
        'ApiAddress'        => 'TATTLER_API_ADDRESS',
        'Namespace'         => 'YOUR APPLICATION_NAME',
        'Secret'            => 'TATTLER_SECRET',
        'TokenTTL'          => 'USER_TOKEN_TTL',
        'DBDecorator'       => new RedisDecorator(),
        'NetworkDecorator'  => new CurlDecorator()
]);

/** @var ITattlerModule::class $tattler */
$tattler = Tattler::getInstance($tattlerConfig);
```
_note: for using redis db decorator you need to install [predis](https://github.com/nrk/predis)_

* TATTLER_WEBSOCKET_ADDRESS - websocket transport address e.g. ws://websocket.domain.tld:80 or wss://websocket.domain.tld:443
* TATTLER_API_ADDRESS - api address e.g. http://websocket.domain.tld:80 or https://websocket.domain.tld:443
* YOUR APPLICATION_NAME - namespace for your application. You can use same tattler server with multiple applications.
* TATTLER_SECRET - same secret that was defined in tattler-server configuration
* USER_TOKEN_TTL - time in seconds for users auth tokens used with tattler-server

Then create TattlerController available from your website. See example in [DummyControllerExample](https://github.com/Oktopost/Tattler-php/blob/master/controller/DummyControllerExample.php)  
_note: all methods from that controller should response with JSON body_

When php configuration is done, include [tattler.min.js](https://github.com/Oktopost/Tattler-js) to your html and initialize tattler
```javascript
window.tattler = TattlerFactory.create();
```

## Usage

Setup listener in your js code
```javascript
window.tattler.addHandler('myMessage', 'globalNamespace', function(data){
	alert(data.message);
});
```

Send payload to all users from php
```php
/** var ITattlerMessage::class $message */
$message = new TattlerMessage();
$message->setHandler('myMessage')->setNamespace('globalNamespace')->setPayload(['message' => 'Hello world']]);

$tattler->message($message)->broadcast()->say();
```

See more docs in [docs/](docs/README.md)
