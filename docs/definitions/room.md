# Tattler room

Room (or channel) used to combine multiple users. It allows you to send same message to all users within the room.


```php
/** @var IRoom $user */
$room = new Room();
$room->setName('myRoomNumberOne');
```

For adding tattler user to room use code below
```php
$tattler->allowAccess($room, $user);
```

For removing tattler user from room use code below
```php
$tattler->denyAccess($room, $user);
```

Also you can check if user is allowed to receive messages in that room
```php
$tattler->isAllowed($room, $user); // returns bool
```