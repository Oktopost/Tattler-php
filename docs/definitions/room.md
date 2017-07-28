# Tattler room

Room (or channel) used to combine multiple users. It allows you to send same message to all users within the room.


```php
/** @var IRoom $user */
$room = \Tattler\SkeletonInit::skeleton(IRoom::class);
$room->setName('myRoomNumberOne');
```

For adding tattler user to room use code below
```php
$room->allow($user);
```

For removing tattler user from room use code below
```php
$room->deny($user);
```

Also you can check if user is allowed to receive messages in that room
```php
$room->isAllowed($user); // returns bool
```