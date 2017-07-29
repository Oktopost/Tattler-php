# Database

Tattler storage used for storing users and rooms and synchronization with tattler backend.

There are several types of storage, you can use any of them or implement your own.

_note: class should implement [IDBDecorator interface](https://github.com/Oktopost/Tattler-php/blob/master/src/Tattler/Base/Decorators/IDBDecorator.php)_

_note: existing decorators may contain undocumented features. They will be added to documentation later. 
Or they will be removed from code. Who knows._

## Redis
This is the most simple way to store tattler-stuff. Install [Predis library](https://github.com/nrk/predis) and 
start using decorator.

```php
$host = 'localhost';
$port = 6379;
$prefix = 'php-tattler';
\Tattler\SkeletonInit::skeleton()->set(IDBDecorator::class, new RedisDecorator($host, $port, $prefix));
```

## Squid
SquidDecorator used for storing data in mysql database. Install [Squid library](https://github.com/Oktopost/Squid) and 
pass ObjectConnector and your tableName to decorator.

```php
\Tattler\SkeletonInit::skeleton()->set(IDBDecorator::class, new SquidDecorator($objectConector, $tableName));
```

_note: Use [mysql.sql](https://github.com/Oktopost/Tattler-php/blob/master/db/mysql.sql) for creating table._

## Squanch
SquanchDecorator used for storing data in cache layer. That layer could be anywhere - redis, squid, etc. Install 
[Squanch library](https://github.com/Oktopost/Squanch) and pass CachePlugin and bucket name to decorator.

```php
\Tattler\SkeletonInit::skeleton()->set(IDBDecorator::class, new SquanchDecorator($cachePlugin, 'php-tattler'));
```