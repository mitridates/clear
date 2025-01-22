(function (w){
    /* Polyfill EventEmitter. */
    /**
     * @mixin
     * @constructor
     */
    function EventEmitter() {
        this.listeners = {};
    }

    /**
     * @name EventEmitter#on
     * @method
     * @param {string} event
     * @param {function} listener
     */
    EventEmitter.prototype.on = function (event, listener) {
        this.listeners = this.listeners || {};
        if (typeof this.listeners[event] !== 'object') {
            this.listeners[event] = [];
        }
        this.listeners[event].push(listener);
        return this;
    };
    /**
     * @name EventEmitter#removeListener
     * @method
     * @param {string} event
     * @param {function} listener
     */
    EventEmitter.prototype.removeListener = function (event, listener) {
        let idx;
        if (typeof this.listeners[event] === 'object') {
            idx = this.listeners[event].indexOf(listener);

            if (idx > -1) {
                this.listeners[event].splice(idx, 1);
            }
        }
        return this;
    };
    /**
     * Shortcut to removeListener
     * @name EventEmitter#off
     * @method
     * @param {string} event
     * @param {function} listener
     */
    EventEmitter.prototype.off = function (event, listener) {
        return this.removeListener(event, listener);
    };
    /**
     * Call function/s for event
     * @name EventEmitter#emit
     * @method
     * @param {string} event
     * return {array} Listener return data if any
     */
    EventEmitter.prototype.emit = function (event) {
        let
            args = [].slice.call(arguments, 1),
            data= [],
            result, i, listeners, length;
        this.listeners = this.listeners || {};

        if (typeof this.listeners[event] === 'object') {
            listeners = this.listeners[event].slice();
            length = listeners.length;
            for (i = 0; i < length; i++) {
                result = listeners[i].apply(this, args);
                if(typeof result!== "undefined") data.push(result);
            }
            return data.length? data : null;
        }
    };
    /**
     * Call one listener for event
     * @name EventEmitter#once
     * @method
     * @param {string} event
     * @param {function} listener
     */
    EventEmitter.prototype.once = function (event, listener) {
        this.on(event, function g () {
            this.removeListener(event, g);
            listener.apply(this, arguments);
        });
        return this;
    };

    /**
     * Event mixin
     * @param  {Object} obj
     * @return {Object}
     */
    EventEmitter.mixin = function(obj) {
        let i= 0,
            props = ['on', 'off', 'emit', 'once', 'removeListener'];

        for (; i < props.length; i++) {
            if (typeof obj === 'function') {
                obj.prototype[props[i]] = EventEmitter.prototype[props[i]];
            } else {
                obj[props[i]] = EventEmitter.prototype[props[i]];
            }
        }
        return obj;
    };

    w.EventEmitter = EventEmitter;
})(window)
