# Database

Tattler storage used for storing users and rooms and synchronization with tattler backend.

There are several types of storage, you can use any of them or implement your own.

_note: class should implement [IDBConnector interface](https://github.com/Oktopost/Tattler-php/blob/master/src/Tattler/Base/Connectors/IDBConnector.php)_

_note: If DB Connector is not configured, `Redis` Connector [will be used automatically](https://github.com/Oktopost/Tattler-php/blob/master/src/Tattler/Connectors/DB/RedisConnector.php)_

_note: existing Connectors may contain undocumented features. They will be added to documentation later. 
Or they will be removed from code. Who knows._

## Redis
This is the most simple way to store tattler-stuff. Install [Predis library](https://github.com/nrk/predis) and 
start using Connector.

```php
$host = 'localhost';
$port = 6379;
$prefix = 'php-tattler';

new RedisConnector($host, $port, $prefix);
```

## Squid
SquidConnector used for storing data in mysql database. Install [Squid library](https://github.com/Oktopost/Squid) and 
pass ObjectConnector and your tableName to Connector.

```php
new SquidConnector($objectConector, $tableName);
```

_note: Use [mysql.sql](https://github.com/Oktopost/Tattler-php/blob/master/db/mysql.sql) for creating table._

## Squanch
SquanchConnector used for storing data in cache layer. That layer could be anywhere - redis, squid, etc. Install 
[Squanch library](https://github.com/Oktopost/Squanch) and pass CachePlugin and bucket name to Connector.

```php
new SquanchConnector($cachePlugin, 'php-tattler');
```