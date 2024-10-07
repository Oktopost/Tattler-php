# Network

Tattler uses network for sending socketIds and payloads to tattler-backend.

There are several types of network Connectors. You can use any of them or implement your own.
Also there is no big difference between them, just use any of them that looks more appropriate to you. 

_note: class should implement [INetworkConnector interface](https://github.com/Oktopost/Tattler-php/blob/master/src/Tattler/Base/Connectors/INetworkConnector.php)_  
_note: in fact you don't really have to initialize network Connector - it will be initialized automatically if possible_

## curl
```php
new CurlConnector();
```

## Guzzle
This Connector requires [guzzlehttp/guzzle](https://github.com/guzzle/guzzle) package to be installed.

```php
new GuzzleConnector();
```

## Httpful
This Connector requires [nategood/httpful](https://github.com/nategood/httpful) package to be installed.

```php
new HttpfulConnector();
```
