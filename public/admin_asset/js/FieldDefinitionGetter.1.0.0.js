/**
 * Get Field definitions.
 * @requires JsonApiManager
 */
(function(window, JsonApiManager) {

    /**
     * @name FieldDefinitionGetter
     * @param {string|NodeList|HTMLElement} selector
     * @param {string} url
     * @param {string|null} locale
     * @requires EventEmitter
     * @requires JsonApiManager
     * @mixes EventEmitter
     * @constructor
     */
    function FieldDefinitionGetter(selector, url, locale=null)
    {
        if (!(this instanceof FieldDefinitionGetter)) return new FieldDefinitionGetter(selector, url, locale)

        this.length=0
        EventEmitter.mixin(this)
        this.action= url
        this.locale= locale
        this.method= 'POST'
        this.listeners= {
            formSubmitEventHandler: formSubmitEventHandler.bind(this),
            clickEventHandler: clickEventHandler.bind(this),
        };

        setSelector.call(this, selector)


        for(let i=0; i<this.length; i++) {
            if(this[i].nodeType && this[i].nodeName.toLowerCase()==='form'){
                this[i].addEventListener("submit", this.listeners.formSubmitEventHandler);
            }else{
                this[i].addEventListener("click", this.listeners.clickEventHandler);
            }
        }
    }

    /**
     * Cache codes and translations
     */
    FieldDefinitionGetter.cache= (function() {
        let defs = {};
        return { // public interface
            getCode: function getCode(code, locale){
                let c= locale? locale+'_'+code : code
                return defs.hasOwnProperty(c)? defs[c] : null;
            },
            addCode: function addCode(code, def, locale){
                defs[locale? locale+'_'+code : code]=def
                return this
            }
        };
    })()


    /** current request  **/
    let _req={},

        setSelector= function (selector){
            /**
             * @this FieldDefinitionGetter
             * @param nodeList
             * @private
             */
           let _fnToArray= function (nodeList){
               let arr = Array.prototype.slice.call(nodeList)
               for(let i = 0; i< arr.length; i++){
                   this[i] = arr[i]
               }
               this.length= arr.length
           }

            if(NodeList.prototype.isPrototypeOf(selector)){
                _fnToArray.call(this, selector)
            }
            else if (typeof selector === 'string')
            {
                _fnToArray.call(this, document.querySelectorAll(selector))
            }
            else if ( selector.nodeType )
            {
                this.length = 1
                this[0]= selector
            }
        },
        /**
         * @this FieldDefinitionGetter
         */
    getRequest= function (data, elm)
        {
            let self= this,
                locale= elm.dataset.locale || this.locale,
                reqId= data.code+locale,
                cod, xhr,  fd;

                data['locale']= locale;

            cod = FieldDefinitionGetter.cache.getCode(data.code, locale)

            if(cod){
                cod.setMeta('is_cache', true)
                this.emit('fdget.success.request', cod, elm)
                return;
            }

            //abort double click
            try {_req[reqId].abort(); } catch(e){}

            this.emit('fdget.before.request', elm, self)

            //new request
            _req[reqId] = xhr =  new XMLHttpRequest()

            xhr.responseType='json' //errors only returns status & statusText

            xhr.onload = () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    let res, jam, jas, valueCodes;
                    res= xhr.response
                    //debug: comment xhr.responseType='json' & uncomment here
                    //  res= xhr.response.hasOwnProperty('data')? xhr.response : JSON.parse(xhr.response),

                    jam= new JsonApiManager(res.data, res.included??null)
                    jas= jam.getParsed();//resourceType===document
                    jas.setMeta('_randid', 'fdID'+jas.id+Date.now())
                    FieldDefinitionGetter.cache.addCode(jas.id, jas, locale)
                    self.emit('fdget.success.request', jas, elm)
                    elm.removeEventListener("click", self.listeners.clickEventHandler);
                } else {
                    self.emit('fdget.error.request', self, xhr)
                    if(window.JError){//debug
                        JError(xhr).show()
                    }else{
                        alert(`Error ${xhr.status} : ${xhr.statusText}`)
                    }
                }
            };

            xhr.onloadend = () => {
                delete _req[reqId];
            }
            // if(this.method === 'GET') //set queryString
            // {
            //     url= new URLSearchParams(data)
            //     i= this.action.indexOf('?')
            //     if(i===-1){
            //        this.action= this.action+'?'+url.toString()
            //     }else{
            //         this.action= this.action + (this.action.charAt(this.action.length-1)==='?'? '' : '&' ) + url.toString()
            //     }
            //     xhr.open('GET', this.action)
            //     xhr.send();
            // }else{
                fd = new FormData()
                xhr.open('POST', this.action)
                for ( let key in data ) {
                    fd.append(key, data[key]);
                }
                xhr.send(fd);
            // }

    },
        /**
         *
         * @param {Event} ev
         * @this FieldDefinitionGetter
         */
        formSubmitEventHandler = function(ev)
        {
            //todo
            ev.stopPropagation();
            ev.preventDefault();
            // getRequest.call(this, {})

        },
        /**
         * @param {Event} ev
         * @this FieldDefinitionGetter
         */
        clickEventHandler= function (ev)
        {
            ev.preventDefault();
            ev.stopPropagation()
            let elm= ev.currentTarget
            if(elm.dataset.isrendered) return
            else elm.dataset.isrendered='true'
            getRequest.call(this, {code: elm.dataset.fielddefinition}, elm)
        };

    window.FieldDefinitionGetter = FieldDefinitionGetter

}(window, JsonApiManager));