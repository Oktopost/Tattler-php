# Tattler.js

First you need to include [tattler.min.js](https://github.com/Oktopost/Tattler-js/blob/master/dist/tattler.min.js) to your html.
Then you need to create `settings` object. By default tattler will use settings below
```javascript
var settings = {
	ws: undefined, // you can set address of tattler-backend here, then urls.ws will not be used
	auth: undefined, // you can set auth token here, then urls.auth will not be used
    urls: {
        ws: '/_tattler/ws', // where php will tell address of tattler-backend
        auth: '/_tattler/auth', // where php will tell auth token
        channels: '/_tattler/channels' // where php will tell which channels are allowed
    },
    requests: {
        ws: 'get', // get or post
        channels: 'get', // get or post
        auth: 'get' // get or post
    },
    readyCallback: false, // will be called each time user is connected or reconnected to socket
    readyCallbackOnce: false, // will be called after first time user is connected to socket
    autoConnect: true, // automatically init plugin
    debug: false // show messages in console
}
``` 

Then you can initialize tattler instance from tattlerFactory
```javascript
window.tattler = TattlerFactory.create(settings);
```

To add connection to room use code below
```javascript
window.tattler.addChannel('roomName');
```

To remove connection to room use code below
```javascript
window.tattler.removeChannel('roomName');
```
