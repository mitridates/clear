/**
 * Immediately-Invoked Function Expression (IIFE).
 * @function
 * @param {object} window - Global window object.
 * @returns {window} window.repopulate
 */
(function(window, JsonApiManager, EventEmitter, undefined) {
    'use strict';
    
    /** cache request && response **/
    let
        _req={},
        _resp={}
    ;

    /**
     * @constructor
     * @param {string} type
     * @param {string} expected
     */
    function InvalidSelectorException(type, expected) {
        this.type = type;
        this.expected = expected;
        this.mensaje = `Invalid tag "${type}",  expected tag name: "${expected}"`;
        this.toString = function () {
            return this.mensaje;
        }
    }

    /**
     * @memberof Repopulate
     * @param {HTMLSelectElement} elm element type SELECT
     * @param {{data:Object, included: (Object|undefined)}} response XMLHttpRequest response JSON:API format
     * @param {{origin: string, placeholder: string, attributes: (string|null), parameters: string, url: string, parentid: string, child: string}} attr
     * @requires JsonApiManager
     * @returns this
     */
    function _setOptions(elm, response, attr){
        let
            keys= attr.attributes? attr.attributes.split(',') :['id', 'name', null],
            parsed, option, spec;

        if(!response['data'].length) return this;
        /**
         * @type {JsonApiSpec|JsonApiSpec[]}
         */
        parsed = (new JsonApiManager(response.data, response.included||null)).getParsed()

        if(attr.placeholder)
        {
            option = document.createElement('option');
            option.label = attr.placeholder;
            elm.add(option);
        }
        
        for (let i = 0; i < parsed.length; i++)
        {
            /**@type JsonApiSpec */
            spec= parsed[i]
            option = document.createElement('option');
            option.value = spec.get(keys[0]);
            option.text = spec.get(keys[1])? spec.get(keys[1]) : spec.toString();//name puede existir o no
            if(keys.length>2 && keys[2]) option.title = spec.get(keys[2]);
            elm.add(option);
        }
    }


        /**
     * @name clearSelectors
     * @this Repopulate
     * @param {HTMLSelectElement} elm
     * @param {boolean} deep
     * @returns void
     */
    function clearSelectors(elm, deep){
        let 
            i=0, 
            nodeList, nodeItem;

        elm.options.length=0;

        if(deep){
            nodeList = document.querySelectorAll(elm.getAttribute('data-child'));
            for(; i<nodeList.length; i++)
            {
                nodeItem = nodeList[i];
                nodeItem.options.length=0;
                if (document.querySelectorAll(nodeItem.getAttribute('data-child')).length>0) clearSelectors.call(this, nodeItem, true);
            }
        }
    }

    /**
     * @name populateChild
     * @this Repopulate
     * @param {HTMLSelectElement} elm
     * @param {string} value get options by this value
     * @returns Repopulate
     */
    function  populateChild(elm, value){
        let
            /** small id generator */
            randId= function (){
                let s;
                for (s=''; s.length < 4; s += 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.charAt(Math.random()*62|0));
                return s;
            },
            /**
             * Get data attributes
             * url - todo Posible JSON|callback???
             * parentid - url?parentid=value
             * parameters - json enconded parameters key=value
             * child - seletor(s)
             * placeholder - Default empty selector
             * attributes - JsonApiSpec attributes "id, text, title" to options  default [id,name|toString(),null]
             * method - default POST
             */
            attr= (function (e){
                    let d = {};
                    ['url', 'parentid', 'parameters', 'child', 'placeholder', 'attributes', 'method', 'cache'].forEach(function (i){
                        d[i]= e.getAttribute('data-'+i)
                    })
                    if(!d.placeholder) d.placeholder= 'Select option'
                    if(!d.method) d.method= 'POST'
                    return d
                })(elm),
            self = this,
            fd= new FormData(),
            /** Extra post parameters as json string */
            p= attr.parameters? JSON.parse(attr.parameters):{},
            //asociamos cada elemento a un id para abortar el request en caso de duplicados
            reqId= (function (e){
                let id;
                if(e.lastrequest) return e.lastrequest;
                e.lastrequest= id = randId();
                return id;
            })(elm.dataset),
            // reqId= elm.name+ (elm.form? elm.form.id : elm.offsetWidth),
            /** current request data*/
            xhr, qstr, res
        ;

        //check request to avoid overlap & abort if necessary
        try {_req[reqId].abort(); } catch(e){}

        _req[reqId] = xhr =  new XMLHttpRequest()
        xhr.responseType='json'

        this.emitter.emit('js.repopulate.beforeSend', this, elm, attr)

        for(let k in p){
            fd.append(k, p[k])
        }
        fd.append(attr.parentid, value)
        //query string para cache||GET
        qstr = [...fd.entries()].map(x => `${encodeURIComponent(x[0])}=${encodeURIComponent(x[1])}`).join('&')

        //get cache
        if(this.root.dataset.cache && _resp[reqId]!== undefined && _resp[reqId][qstr]!== undefined){
            _setOptions.call(self, elm,  _resp[reqId][qstr], attr);
            self.emitter.emit('js.repopulate.onLoad', _resp[reqId][qstr], elm, attr)
            delete _req[reqId]
            return this
        }

        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                res= xhr.response
                //para debug comentar xhr.responseType='json' y descomentar aquí
                //  res= xhr.response.hasOwnProperty('data')? xhr.response : JSON.parse(xhr.response),


                _setOptions.call(self, elm,  res, attr);
                self.emitter.emit('js.repopulate.onLoad', res, elm, attr)

                if(self.root.dataset.cache){
                    if(!_resp[reqId]) _resp[reqId]={}
                    _resp[reqId][qstr]= res
                }

            } else {
                self.emitter.emit('js.repopulate.onError', xhr)
                //para debug, descomentar aquí y arriba
                // if(window.JError){
                //     JError(xhr).show()
                // }else{
                alert(`Error ${xhr.status} : ${xhr.statusText}`)
                // }
            }
        };

        xhr.onloadend = () => {
            delete _req[reqId];
        }

        if(attr.method === 'GET') //set queryString
        {
            let arr = attr.url.split('?');
            if (arr.length > 1) {
                attr.url = arr[0] + (arr[1]!== ''? arr[1]+'&'+qstr : qstr)
            }else{
                attr.url = attr.url +'?'+qstr
            }
            xhr.open('GET', attr.url)
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
            xhr.send();

        }else{

            xhr.open('POST', attr.url)
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
            xhr.send(fd);
        }
        return this;
    }

/**
 * Nested select with XMLHttpRequest & json
 * @name Repopulate
 * @requires EventEmitter
 * @requires JsonApiManager
 * @constructor
 * @param {string|Object} selector
 * @example
 * The top select must be populated and must have 1-n data-child ID to populate:
 *
 * the nested select must have:
 *  - data-parentid: property name to post with the parent selected value.
 *  - data-url: to post [parentid:selectedValue]
 *  - data-child (optional): select 1-n child/s to attach event listener and follow the nested select
 *
 *  <select id="country"  data-child="#admin1">
 *     <option value="" selected="selected">Select Country</option>
 *     <option value="ES">Spain</option>
 *  </select>
 *
 *  <select id="admin1" data-child="#admin2a #admin2b #admin2c" data-url="/jsonResponse/return/admin1"
 *      data-parentid="countryid" data-placeholder="ON RESET USE THIS PLACEHOLDER">
 *      <option value="">PLACEHOLDER</option>
 *   </select>
 *
 *   <select id="admin2a" data-child="#admin3" data-url="/jsonResponse/return/admin2"
 *      data-parentid="admin1" data-placeholder="ON RESET USE THIS PLACEHOLDER">
 *   </select>
 */
function Repopulate(selector) {
    if (!(this instanceof Repopulate)) return new Repopulate(selector)
    /** @type HTMLSelectElement */
    this.root  =  (typeof selector === "string" )? document.querySelector(selector) : selector;
    this.emitter = EventEmitter.mixin(this);
    if(this.root==null || this.root.tagName.toLowerCase()!=='select'){
        throw new InvalidSelectorException(this.root.tagName.toLowerCase(), 'SELECT');
    }
}

    /**
     * @memberof Repopulate
     * @param {HTMLSelectElement|null} elm
     * @returns this
     */
    Repopulate.prototype.populate = function (elm=null)
    {
        if(!elm) elm= this.root

        let nodeList,
            i=0,
            self = this,
            /**
             * @param {HTMLSelectElement} node
             * @param {HTMLSelectElement} child
             */
            evnt = function(node, child){

                if(node===self.root && node.form){//catch form reset
                    node.form.addEventListener("reset", function(ev){
                        clearSelectors.call(self, child, true);
                        ev.stopPropagation();
                    });
                }

                node.addEventListener("change", function(ev){
                    clearSelectors.call(self, child, true);
                    if (node.options.length===0 ||
                        node.options[node.selectedIndex].value==='' ||
                        (node===self.root && !node.options[node.selectedIndex].value)) return;
                    populateChild.call(self, child, node.options[node.selectedIndex].value);
                    ev.stopPropagation();
                });

                child.addEventListener("change", function(ev){//repopulate before load page if child has one option available
                    if(child.options.length===2 && !child.options[0].value){//has at least one value and first one is empty
                        if(!child.options[child.selectedIndex].value)//selected value is null
                        {
                            clearSelectors.call(self, child);
                            let parent= node.options[node.selectedIndex].value;
                            if(parent) populateChild.call(self, child, node.options[node.selectedIndex].value);
                        }
                    }
                    ev.stopPropagation();
                });
            };
        //context is important!!!
        nodeList= (elm.form||document).querySelectorAll(elm.dataset.child); //static NodeList

        for(; i<nodeList.length; i++)
        {
            evnt(elm, nodeList[i]);
            if ((elm.form||document).querySelectorAll(nodeList[i].dataset.child).length>0) this.populate(nodeList[i]);
        }

        return this;
    }



    window.Repopulate= Repopulate;
}(window, JsonApiManager, EventEmitter));
