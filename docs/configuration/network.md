# Network

Tattler uses network for sending socketIds and payloads to tattler-backend.

There are several types of network decorators. You can use any of them or implement your own.
Also there is no big difference between them, just use any of them that looks more appropriate to you. 

_note: class should implement [INetworkDecorator interface](https://github.com/Oktopost/Tattler-php/blob/master/src/Tattler/Base/Decorators/INetworkDecorator.php)_  
_note: in fact you don't really have to initialize network decorator - it will be initialized automatically if possible_

## curl
```php
new CurlDecorator();
```

## Guzzle
This decorator requires [guzzlehttp/guzzle](https://github.com/guzzle/guzzle) package to be installed.

```php
new GuzzleDecorator();
```

## Httpful
This decorator requires [nategood/httpful](https://github.com/nategood/httpful) package to be installed.

```php
new HttpfulDecorator();
```
