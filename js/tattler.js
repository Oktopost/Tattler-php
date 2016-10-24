(function () {
    'use strict';

    var defaults = {
        urls: {
            ws: '/_tattler/ws',
            channels: '/_tattler/channels'
        },
        requests: {
            ws: 'get',
            channels: 'get'
        },
        wsPort: {
            secure: 443,
            notSecure: 80
        },
        autoConnect: true,
        debug: false
    };

    var tattlerInstances = {};

    function extendConfig(defaultConfig, newConfig) {
        var result = defaultConfig;

        for (var key in newConfig) {
            if(newConfig.hasOwnProperty(key)) {
                result[key] = newConfig[key];
            }
        }

        return result;
    }

    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }

        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }

    function ajax(type, url, data, onSuccess, onError, onComplete) {
        var serialize = function(obj, prefix) {
            var str = [], p;
            for(p in obj) {
                if(obj.hasOwnProperty(p)) {
                    var k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
                    str.push((v !== null && typeof v === "object") ?
                        serialize(v, k) :
                    encodeURIComponent(k) + "=" + encodeURIComponent(v));
                }
            }
            return str.join("&");
        };

        var xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function() {
            if(xmlhttp.readyState == XMLHttpRequest.DONE ) {
                if(xmlhttp.status >= 200 && xmlhttp.status < 300) {
                    if(typeof onSuccess === 'function') {
                        onSuccess(JSON.parse(xmlhttp.responseText));
                    }

                    if(typeof onComplete === 'function') {
                        onComplete(true);
                    }
                }
                else
                {
                    if(typeof onError === 'function') {
                        onError(xmlhttp.response);
                    }

                    if(typeof onComplete === 'function') {
                        onComplete(false);
                    }
                }
            }
        };

        type = type.toUpperCase();

        if(type === 'GET') {
            var glue = '?';

            if (url.match(/\?/)) {
                glue = '&';
            }

            url += glue + serialize(data);
            data = '';
        } else {
            data = JSON.stringify(data);
        }

        xmlhttp.open(type, url, true);
        return xmlhttp.send(data);
    }

    function isEmpty(obj) {
        for(var prop in obj) {
            if(obj.hasOwnProperty(prop))
                return false;
        }

        return JSON.stringify(obj) === JSON.stringify({});
    }


    var tattlerFactory = {
        getInstance: function(instanceName){
            return tattlerInstances[instanceName];
        },
        create: function(config){
            var instance = new Tattler(config);
            tattlerInstances[guid()] = instance;
            return instance;
        }
    };

    var Tattler = function(options) {
        var settings = extendConfig(defaults, options);
        var callbacks = {
            getWs: {
                onSuccess: function (data) {
                    manufactory.ws = data.ws;
                    if(data.ws.match(/wss/)) {
                        manufactory.wsPort = settings.wsPort.secure;
                    } else {
                        manufactory.wsPort = settings.wsPort.notSecure;
                    }

                    connectToSocket();
                },

                onError: function () {
                    log('error', 'Failed to get ws address');
                }
            },
            getChannels: {
                onSuccess: function(data) {
                    for(var i in data.channels) {
                        if(data.channels.hasOwnProperty(i)) {
                            addChannel(data.channels[i], true);
                        }
                    }
                    callbacks.socket.handleEvents();
                },
                onError: function(){
                    log('error', 'Failed to get channels listing');
                }
            },
            socket: {
                connected: function () {
                    log('warn', 'connected to socket');
                    requestChannels();
                },
                disconnected: function () {
                    log('error', 'disconnected from socket');
                    for (var i in manufactory.channels) {
                        if(manufactory.channels.hasOwnProperty(i)) {
                            manufactory.channels[i] = false;
                        }
                    }
                },
                handleEvents: function () {
                    if(typeof manufactory.socket._callbacks['$defaultEvent'] !== 'undefined') {
                        return;
                    }

                    /** @namespace data.payload */
                    manufactory.socket.on('defaultEvent', function (data) {
                        var handler = data.handler;
                        var namespace = data.namespace || 'global';

                        if(handlerExists(namespace, handler) === false) {
                            log('error', 'handler ' + handler + ' with namespace ' + namespace + ' not defined', data);
                        } else {
                            if(typeof data.payload === 'undefined') {
                                // backward compatibility to old version of Tattler backend
                                manufactory.handlers[namespace][handler](data);
                            } else {
                                manufactory.handlers[namespace][handler](data.payload);
                            }
                        }
                    })
                }
            }
        };
        var manufactory = {
            socket: null,
            ws: null,
            wsPort: null,
            channels: {},
            handlers: {
                /** @namespace data.channel */
                global: {
                    'console.log': function (data) {
                        if (typeof data.force !== 'undefined') {
                            console.warn(data);
                            return;
                        }

                        if(settings.debug === true) {
                            log('warn', '-------------------------------------------------------------');
                            log('warn', 'remote: ' + data['message']);
                            log('warn', '-------------------------------------------------------------');
                        } else {
                            log('warn', 'remote', data['message']);
                        }
                    },
                    'alert': function (data) {
                        var text;
                        if (typeof data.title !== 'undefined') {
                            text = data['title']
                        }

                        if (text !== '') {
                            text = '--------------------------' + text.toUpperCase() + '--------------------------';
                            text += "\n";
                        }

                        text += data['message'];

                        alert(text);
                    },
                    'confirm': function (data) {
                        if (confirm(data.message)) {
                            if (data['yes'] !== undefined && typeof[data['yes']] === 'function') {
                                data['yes']();
                            }
                        } else {
                            if (data['no'] !== undefined && typeof data['no'] === 'function') {
                                window[data['no']]();
                            }
                        }
                    },
                    'addChannel': function (data, state) {
                        addChannel(data.channel, state);
                    },
                    'removeChannel': function (data) {
                        removeChannel(data.channel);
                    }
                }
            }
        };
        var logs = [];

        var handlerExists = function(namespace, event){
            return typeof manufactory.handlers[namespace] !== 'undefined' &&
                typeof manufactory.handlers[namespace][event] !== 'undefined';
        };

        var addChannel = function(channel, state) {
            if(typeof manufactory.channels[channel] === 'undefined' || manufactory.channels[channel] !== state) {
                manufactory.channels[channel] = state;

                if(manufactory.socket !== null) {
                    log('info', 'joining channel «' + channel + '»');
                    manufactory.socket.emit('subscribe', channel);
                } else {
                    log('info', 'adding channel «' + channel + '»');
                }
            } else {
                log('error', 'channel «' + channel + '» already defined');
            }
        };

        var removeChannel = function(channel) {
            if(typeof manufactory.channels[channel] === 'undefined') {
                log('error', 'failed to unsubscribe from «' + channel + '» - channel not defined');
            } else {
                delete(manufactory.channels[channel]);
                manufactory.socket.emit('unsubscribe', channel);
                log('warn', 'unsubscribed from «' + channel + '»')
            }
        };

        var addHandler = function(event, namespace, fn) {
            if(typeof namespace === 'function') {
                // backward compatibility with old handlers
                fn = namespace;
                namespace = 'global';
            }

            if(handlerExists(namespace, event) === false) {
                if(typeof manufactory.handlers[namespace] === 'undefined') {
                    manufactory.handlers[namespace] = {};
                }

                manufactory.handlers[namespace][event] = fn;

                log('info', 'added handler for event «' + event + '» in namespace «' + namespace + '»');
            } else {
                log('error', 'preventing handler creation for event «' + event + '» in «' + namespace + '»: already exists. Check your code.')
            }
        };

        var log = function() {
            var args = [];
            var result = {};

            for(var i=0; i<arguments.length;i++) {
                args.push(arguments[i]);
            }

            var type=args.shift();

            result.type = type;
            result.date = new Date();
            result.data = args;

            logs.push(result);

            if(settings.debug === true) {
                args.unshift('Tattler:');
                for(var x in args) {
                    if(typeof args[x] === 'object') {
                        console[type](args);
                        return;
                    }
                }
                console[type](args.join(' '));
            }
        };

        var debug =  function(){
            for(var item in logs) {
                console[logs[item].type](logs[item].date, logs[item].data);
            }
        };

        var init = function(){
            log('info', 'requesting WS url');
            ajax(settings.requests.ws, settings.urls.ws, {}, callbacks.getWs.onSuccess, callbacks.getWs.onError);
        };

        var connectToSocket = function(){
            if(manufactory.socket === null) {
                if(manufactory.ws === null) {
                    log('error', 'Failed to connect to socket: address unknown');
                    return;
                }

                manufactory.socket = io(manufactory.ws + ':' + manufactory.wsPort);
                manufactory.socket.on('connect', callbacks.socket.connected);
                manufactory.socket.on('disconnect', callbacks.socket.disconnected);

                log('info', 'connecting to socket at ' + manufactory.ws + ':' + manufactory.wsPort);
            } else {
                log('error', 'socket already connected');
            }
        };

        var requestChannels = function() {
            var socketId = manufactory.socket.io.engine.id;
            var savedChannels = [];

            if(isEmpty(manufactory.channels)) {
                log('log', 'requesting channels with socketId='+socketId);
            } else {
                log('log', 'connecting to saved channels');
                for(var room in manufactory.channels) {
                    if(manufactory.channels.hasOwnProperty(room)) {
                        savedChannels.push(room);
                    }
                }
            }

            ajax(settings.requests.channels,
                settings.urls.channels,
                {
                    socketId: socketId,
                    channels: savedChannels
                },
                callbacks.getChannels.onSuccess,
                callbacks.getChannels.onError);
        };

        if (settings.autoConnect) {
            init();
        }

        log('info', "creating socket's stuff...");
        this['debug'] = debug;
        this['addHandler'] = addHandler;
        this['addChannel'] = addChannel;
        this['run'] = init;
    };

    window.tattlerFactory = tattlerFactory;
})();