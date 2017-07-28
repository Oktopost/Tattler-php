# Tattler message

For creating message instance use code below:
```php
$handler='consoleEcho';
$namespace='secretNamespace';
$payload=['anything', 'you' => ['want'], ['to' => 'send']];

/** var ITattlerMessage::class $message */
$message = \Tattler\SkeletonInit::skeleton(ITattlerMessage::class);
$message->setHandler($handler)->setNamespace($namespace)->setPayload($payload);
```

`$handler` and `$namespace` will allow javascript code to understand how to process your payload.
E.g.:
```javascript
window.tattler.addHandler('consoleEcho', 'secretNamespace', function(data) {
	console.log(data);
});
```
You can have same handler defined within multiple namespaces, that will allow you to treat same payload differently.

By default tattler.js contains several predefined handlers:
* 'console.log' for echoing payloads to browser's console
* 'alert' for passing payload.title and payload.message to `alert` function
* 'confirm' for passing payload.message,payload.yes and payload.no to `confirm` function. 'payload.yes' and 'payload.no'
must contain names of javascript functions defined in current scope.
* 'addChannel' for notifying user about adding him to new room 'payload.channel'. User will try to join that room then.
* 'removeChannel' for notifying user about removing him from room 'payload.channel'. User will not process messages 
for that room. 