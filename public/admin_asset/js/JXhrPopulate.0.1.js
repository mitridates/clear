(function(window, EventEmitter, Handlebars, JError,  undefined) {
    'use strict';

    let
        _req={},//track  request to abort
        _defaults={
            classSpin: 'fa-spin',//reload data
            selectorSync: '.js-sync',//update selector
            rnoresultfound: "No results found",

        };

    /**
     * Set values before request
     * @private
     */
    function _fnPresetBeforeRequest (clearCache= false, page= undefined)
    {
        _fnGetRequest.call(this)
    }

    /**
     * @private
     * @this JXhrPopulate
     */
    function _fnGetRequest()
    {
        let fd= (function (l,e){
            let key, value, d= new FormData();
            for ([key, value] of l.entries())  d.set(key, value.toString());
            for ([key, value] of e.entries()) d.set(key, value.toString());
            return d;
        })(this.params.last, this.params.extra);



        let
            /** small id generator */
            randId= function (){
                let s;
                for (s=''; s.length < 4; s += 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.charAt(Math.random()*62|0));
                return s;
            },
            reqId= (function (e){
                let id;
                if(e.lastrequest) return e.lastrequest;
                e.lastrequest= id = randId();
                return id;
            })(this.domElms.getWrapper().dataset),
            self = this,
            method= this.requestSource.method.toUpperCase(),
            url= this.requestSource.action,
           open= function (xhr,method,url){
                xhr.open(method, url)
                //xhr.responseType= 'json';//descomentar en produccion
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            }.bind(this),
            page= fd.get('page'),
            Tbody= this.domElms.getBody(),
            animate= function (on) {
                this.domElms.getWrapper().querySelectorAll(this.opt.selectorSync).forEach(function (sync){
                    on? sync.classList.add('fa-spin') : sync.classList.remove('fa-spin')
                }, on)
            }.bind(this),
            xhr, qstr, res
        ;

        //check request and abort if necessary
        try {_req[reqId].abort(); } catch(e){}
        _req[reqId] = xhr =  new XMLHttpRequest()

        this.emit('populate.before.request', this, Tbody)

        animate(true)
        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                //para debug/prod comentar/descomentar la línea superior/inferior y descomentar/comentar otra línea
                //res= xhr.response
                res= xhr.response.hasOwnProperty('data')? xhr.response : JSON.parse(xhr.response)
                let jam =(res.hasOwnProperty('data')) ?(new JsonApiManager(res.data, res.included||null)).parseResponse() : null
                self.emit('populate.success.request', res, Tbody, jam)
                _fnShowResponse.call(self, res)
            } else {
                self.emit('populate.request.error', xhr)
                //para prod, dejar el alert
                if(window.JError){
                    JError(xhr).show()
                }else{
                    alert(`Error ${xhr.status} : ${xhr.statusText}`)
                }
            }
        };

        xhr.onloadend = () => {
            animate(false);
            delete _req[reqId];
            self.emit('populate.complete.request', xhr);

        }

        if(method === 'GET') //set queryString
        {
            //query string para GET
            qstr = [...fd.entries()].map(x => `${encodeURIComponent(x[0])}=${encodeURIComponent(x[1])}`).join('&')
            let arr = url.split('?');
            if (arr.length > 1) {
                url = arr[0] + (arr[1]!== ''? arr[1]+'&'+qstr : qstr)
            }else{
                url = url +'?'+qstr
            }
            open(xhr, 'GET', url)
            xhr.send();

        }else{
            open(xhr, 'POST', url)
            xhr.send(fd);
        }


    }
    /**
     * @param {Event} ev
     * @this JXhrPopulate
     */
    function _fnReloadEventHandler (ev)
    {
        let p= this.params.extra.get('page');
        ev.stopPropagation();
        ev.preventDefault()
        _fnPresetBeforeRequest.call(this, false, (p===null ? 1 : parseInt(p))*-1)
    }

    /**
     * @param {XMLHttpRequest.response} response
     * @this JXhrPopulate
     */
    function _fnShowResponse(response)
    {
        let jam= (response instanceof JsonApiManager)? response : new JsonApiManager(response['data'], response['included']??null),
            body= this.domElms.getBody(),
            foot= this.domElms.getFoot(),
            /**
             * JXhrPopulate data
             * @type {JSON|null}
             */
            meta= response['meta']
        ;
        this.length=jam.length

        body.innerHTML=this.tplBody({
            data: jam.getParsed(),
        });


        if(this.tplNavbar)
        {
            //loop navBar
            this.domElms.getNav().forEach(function (nav)
            {
                //Add listener to sync icon after each pagination navBar refresh (if exists)
                nav.querySelectorAll(this.opt.selectorSync).forEach(function (sync){
                    sync.addEventListener("click", _fnReloadEventHandler.bind(this));
                }, this)
            }, this)
        }
        this.emit('populate.show.response', jam, body, this)
    }


    /**
     * @constructor
     * @name JXhrPopulate
     * @param {HTMLElement|string} wrapper
     * @param {Object|undefined} params - Add first default Request params.
     * @param {Object|undefined} mod - Change default (_def) values
     * @mixes EventEmitter
     * @example
     * Paginando con un formulario
     *  <code>
     *      <html>
     *      <body>
     *      ...
     *     {% embed '@Dashboard/object/pager/paginator_handlebars.html.twig' with{
     *         id: 'paginatorWrapper',
     *         source: '#'~form.vars.attr.id,
     *     } %}
     *     {% block thead %}
     *         <tr>
     *         <th data-sort data-orderby="name">Name</th>
     *         ...
     *         </tr>
     *     {% endblock thead %}
     *         {% block script %}
     *             {% verbatim %}
     *             <tr>
     *                 <td><a href="{{this.links.self}}">{{this.attributes.name}}</a></td>
     *                 ...
     *             </tr>
     *             {% endverbatim %}
     *         {% endblock script%}
     *     {% endembed %}
     *     ...
     *
     *      <script>
     *        let
     *        params= {//request parameters
     *             sort:'ASC',
     *             orderby: 'name'
     *             limit:5//Max items per page
     *         },
     *             mod= {
     *             classSort: 'fa-bars',//yes, is fontawesome...
     *             pagerSize:3//if links > this value, use arrows
     *         }
     *         ;
     *         JXhrPopulate('#paginatorWrapper', params, mod)
     *             .setPager(pager)
     *             .submit();
     *     </script>
     *     </body>
     *     </html>
     *   </code>
     */
    function JXhrPopulate (wrapper,  params=undefined, mod=undefined)
    {
        if (!(this instanceof JXhrPopulate)) return new JXhrPopulate(wrapper, params, mod)

        let
            w= document.querySelector(wrapper),
            sort, i, icon, toggle, selected, j, k
        ;
        EventEmitter.mixin(this);
        this.length=0;//current result length
        /**@type Object|undefined*/
        this.params= {
            /**@type {Map}*/
            last: new Map(),//last serialized data (form)
            /** @type {Map} */
            extra: new Map(),//Query extra parameters
        };
        for (k in params||{}){
            this.params.extra.set(k, params[k])
        }
        this.opt= (function (m, d)
        {
            for (j in d){
                if(m.hasOwnProperty(j)){
                        d[j]= m[j]
                }
            }
            return d
        })(mod||{}, _defaults);

        /**dom elements */
        this.domElms= {
            getWrapper: ()=> w,
            getNav: ()=> w.querySelectorAll('.js-navbar'),
            getMsg: ()=> w.querySelectorAll('.js-message'),
            getHead: ()=>w.querySelector('.js-head'),
            getBody: ()=>w.querySelector('.js-body'),
            getFoot: ()=>w.querySelector('.js-foot'),
            getBodyScript: ()=> w.querySelector('script.body'),
            getFootScript: ()=> w.querySelector('script.foot'),
            getNavbarScript: ()=> document.querySelector('script.script-navbar' )
        }

        this.tplBody= Handlebars.compile(this.domElms.getBodyScript().innerHTML);

        /** Div/Form which provide the url*/
        this.requestSource= (function (elm, w, p) {//request by url o form
            let
                selector= elm.dataset.source ?? w,
                node= selector===w? elm : document.querySelector(selector),
                is_form= (node.nodeType && node.nodeName.toLowerCase()==='form'),
                action= [p.extra.get('action'), elm.dataset.action, node.action];
            return {
                is_form: is_form,
                method: is_form && node.method? node.method : (elm.dataset.method ?? 'POST'),
                action: action.find(el => el !== undefined),
                getNode: ()=>document.querySelector(selector)
            }
        })(w, wrapper, this.params);

        if(this.requestSource.is_form)
        {
            this.requestSource.getNode().addEventListener("submit", function(ev)
            {
                ev.stopPropagation();
                ev.preventDefault();
                this.params.last = new FormData(this.requestSource.getNode());
                _fnPresetBeforeRequest.call(this, true, 1)
            }.bind(this));
        }

        //update pager content on click sync icon
        w.querySelectorAll(this.opt.selectorSync).forEach(function (sync){
            sync.addEventListener("click", _fnReloadEventHandler.bind(this));
        }, this)


    }


    JXhrPopulate.prototype=
        {
            /**
             * Remove cache and send request with last parameters
             * @return {JXhrPopulate}
             */
            update: function () {
                _fnPresetBeforeRequest.call(this, true)
                return this;
            },

            /**
             * Send request
             * @return {JXhrPopulate}
             */
            submit: function () {
                if (this.requestSource.is_form)
                { //submit form
                    this.requestSource.getNode().dispatchEvent(new Event('submit', {cancelable: true}));
                } else {
                    _fnPresetBeforeRequest.call(this, true, 1)
                }
                return this;
            },
            /**
             * Set extra parameters (page, orderBy, sort...)
             * @param {string} name
             * @param {string|number} value
             * @return JXhrPopulate
             */
            setParameter: function (name, value)
            {
                this.params.extra.set(name, value);
                return this
            }
        }

    window.JXhrPopulate = JXhrPopulate;
}(window, EventEmitter , Handlebars, JError));

