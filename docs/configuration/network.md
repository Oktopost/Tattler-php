# Network

Tattler uses network for sending socketIds and payloads to tattler-backend.

There are several types of network decorators. You can use any of them or implement your own.
Also there is no big difference between them, just use any of them that looks more appropriate to you. 

_note: class should implement [INetworkDecorator interface](https://github.com/Oktopost/Tattler-php/blob/master/src/Tattler/Base/Decorators/INetworkDecorator.php)_

## curl
```php
\Tattler\SkeletonInit::skeleton()->set(INetworkDecorator::class, CurlDecorator::class);
```

## Guzzle
This decorator requires guzzlehttp/guzzle package to be installed.

```php
\Tattler\SkeletonInit::skeleton()->set(INetworkDecorator::class, GuzzleDecorator::class);
```

## Httpful
This decorator requires nategood/httpful package to be installed.

```php
\Tattler\SkeletonInit::skeleton()->set(INetworkDecorator::class, HttpfulDecorator::class);
```
