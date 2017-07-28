# Tattler.js

First you need to include tattler.min.js to your html.
Then you need to create `setting` object. By default tattler will use settings below
```javascript
var settings = {
    urls: {
        ws: '/_tattler/ws', // where php will tell address of tattler-backend
        channels: '/_tattler/channels', // where php will tell which channels are allowed
        auth: '/_tattler/auth' // where php will tell auth token
    },
    requests: {
        ws: 'get', // get or post
        channels: 'get', // get or post
        auth: 'get' // get or post
    },
    readyCallback: false, // will be called each time user is connected to socket
    readyCallbackOnce: false, // will be called after first time user is connected to socket
    autoConnect: true, // automatically init plugin
    debug: false // show messages in console
}
```

Then you can initialize tattler instance from tattlerFactory
```javascript
window.tattler = tattlerFactory.create(settings);
```