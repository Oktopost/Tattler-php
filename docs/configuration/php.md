# Tattler php

First create TattlerConfig instance
```php
$config = new TattlerConfig();
$tattlerConfig->fromArray([
        'WsAddress'      => 'TATTLER_WEBSOCKET_ADDRESS',
        'ApiAddress'     => 'TATTLER_API_ADDRESS',
        'Namespace'      => 'YOUR APPLICATION_NAME',
        'Secret'         => 'TATTLER_SECRET',
        'TokenTTL'       => 'USER_TOKEN_TTL'
]);

/** @var ITattler::class $tattler */
$tattler = \Tattler\SkeletonInit::skeleton(ITattler::class);
$tattler->setConfig($tattlerConfig);
```

```
* TATTLER_WEBSOCKET_ADDRESS - websocket transport address e.g. ws://websocket.domain.tld:80 or wss://websocket.domain.tld:443
* TATTLER_API_ADDRESS - api address e.g. http://websocket.domain.tld:80 or https://websocket.domain.tld:443
* YOUR APPLICATION_NAME - namespace for your application. You can use same tattler server with multiple applications.
* TATTLER_SECRET - same secret that was defined in tattler-server configuration
* USER_TOKEN_TTL - time in seconds for users auth tokens used with tattler-server
```

Then create php file available from your website. 
By default Tattler will try to work with route `/_tattler` (i.e. `/_tattler/ws`, `/_tattler/auth`, etc.).
You can change this by settings js configuration (see [Initializing tattler.js](js.md))  
_note: See example in [DummyControllerExample](https://github.com/Oktopost/Tattler-php/blob/master/controller/DummyControllerExample.php)_