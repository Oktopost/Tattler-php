# Send message
 
First prepare tattler instance for sending message
```php
$prepared = $tattler->message($message);
``` 

## send to single user

```php
$prepared->user($user)->say();
```

## send to two users
```php
$prepared->user($user1)->user($user2)->say();
```

## send to room
```php
$prepared->room($room)->say();
```

## send to multiple rooms
```php
$prepared->room($room1)->room($room2)->say();
```

## send to everyone
```php
$prepared->broadcast()->say();
```

After calling `say()` method all targets (users and room) and message will be removed from `$tattler`. 
