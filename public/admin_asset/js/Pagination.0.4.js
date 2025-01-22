(function(window, EventEmitter, Handlebars, JError,  undefined) {
    'use strict';

    let
        _req={},//track  request to abort
        _defaults={
            footCallback:false,//callback(response, JsonApiManager)
            withNavbar: true,
            withMsg: true,
            withSorting: true,
            withNavbarItems: true,
            classSort: 'fa-bars',
            classAsc: 'fa-sort-asc',
            classDesc: 'fa-sort-desc',
            classSpin: 'fa-spin',//reload data
            selectorLink: '.page-link',//link selector
            selectorSync: '.js-sync',//update selector
            rresult: "Results: {totalRows}",
            rpager: "Page {currentPage}. Display {currentPageRows} from {totalRows}.",
            rnoresultfound: "No results found",
            pagerSize:15

        };

    /**
     * Set values before request
     * @private
     */
    function _fnPresetBeforeRequest (clearCache= false, page= undefined)
    {
        let currPage=   this.params.extra.get('page');//buscamos en los parametros;

        if(!currPage) currPage= !this.tplNavbar ? 0 : 1//sin navBar solo hay pagina 1

        if(clearCache || page===0) this.cache= {page: {},response: {}}

        if(page===undefined){
            page = currPage
        }else if (page>0){
            //...
        }else if (page<0){//if negative, clear cache and reload page
            page = Math.abs(page)
            try{
                delete this.cache.page[page]
                delete this.cache.response[page]
            }catch (e) {}
        }else if (page===0){//all results in one page
            delete this.cache.page[page]
            delete this.cache.response[page]
            console.log(page)
        }

        this.params.extra.set('page', page);
        // this.params.last.delete('page');
        _fnGetRequest.call(this)
    }

    /**
     * @private
     * @this Pagination
     */
    function _fnGetRequest()
    {
        let fd= (function (l,e){
            let key, value, d= new FormData();
            for ([key, value] of l.entries())  d.set(key, value.toString());
            for ([key, value] of e.entries()) d.set(key, value.toString());
            return d;
        })(this.params.last, this.params.extra);

        if(this.staticFilter){
            this.emit('jpager.before.request', this)
            _fnShowResponse.call(this, this.staticFilter(fd));
            return;
        }

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

        //use cache
        if(this.cache.page.hasOwnProperty(page))
        {
                this.emit('jpager.before.request', this);
                _fnShowResponse.call(this, this.cache.response[page]);
                this.emit('jpager.success.request', this.cache.response[page], Tbody);
                return;
        }

        //check request and abort if necessary
        try {_req[reqId].abort(); } catch(e){}
        _req[reqId] = xhr =  new XMLHttpRequest()

        this.emit('jpager.before.request', this, Tbody)

        animate(true)
        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                //para debug/prod comentar/descomentar la línea superior/inferior y descomentar/comentar otra línea
                //res= xhr.response
                res= xhr.response.hasOwnProperty('data')? xhr.response : JSON.parse(xhr.response)
                let jam =(res.hasOwnProperty('data')) ?(new JsonApiManager(res.data, res.included||null)).parseResponse() : null
                self.cache.page[page]= fd
                self.cache.response[page]= res
                self.emit('jpager.success.request', res, Tbody, jam)
                _fnShowResponse.call(self, res)
            } else {
                self.emit('jpager.request.error', xhr)
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
            self.emit('jpager.complete.request', xhr);

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
     * @this Pagination
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
     * @this Pagination
     */
    function _fnShowResponse(response)
    {
        let jam= (response instanceof JsonApiManager)? response : new JsonApiManager(response['data'], response['included']??null),
            body= this.domElms.getBody(),
            foot= this.domElms.getFoot(),
            /**
             * Pagination data
             * @type {{
             *  currentPage: int,
             *  currentPageRows: int,
             *  itemsPerPage: int,
             *  limit: int,
             *  limits: [],
             *  offset: int,
             *  totalPages: int,
             *  totalRows: int
             *  }}
             */
            pagination= response['meta']['pagination']
        ;
        this.length=jam.length

        body.innerHTML=this.tplBody({
            data: jam.getParsed(),
        });

        if(this.opt.footCallback && typeof this.opt.footCallback=== "function"){
            foot.innerHTML=this.tplFoot({
                data: this.opt.footCallback(response, jam)
            });
        }

        if(this.tplNavbar)
        {
            //loop navBar
            this.domElms.getNav().forEach(function (nav)
            {
                nav.innerHTML= this.tplNavbar.compiled(
                    {
                        currentPage: pagination.currentPage,
                        totalPages : pagination.totalPages,
                        parameters: this.tplNavbar.options,
                        data: pagination
                    })

                //loop navBar links
                nav.querySelectorAll(this.opt.selectorLink).forEach(function (link)
                {

                    link.pageNumber= link.dataset.page ?? parseInt(link.value)

                    if(!link.classList.contains('active'))
                    {
                        link.addEventListener("click",function(ev)
                        {
                            ev.stopPropagation();
                            ev.preventDefault()
                            _fnPresetBeforeRequest.call(this, false, parseInt(ev.currentTarget.dataset.page))
                        }.bind(this));
                    }
                }, this)

                //Add listener to sync icon after each pagination navBar refresh (if exists)
                nav.querySelectorAll(this.opt.selectorSync).forEach(function (sync){
                    sync.addEventListener("click", _fnReloadEventHandler.bind(this));
                }, this)

                //Add listener to sync icon after each pagination navBar refresh (if exists)


                nav.querySelectorAll('.pager-size').forEach(//select Items per page
                    /**@type HTMLSelectElement*/
                    function (elm){
                        let
                            limit= this.params.extra.get('limit'),
                            l= elm.options.length,
                            i, v
                        ;
                        if(!limit) limit= pagination.limit;

                        for(i=l-1; i>0; i--){//set infinite value to total rows
                            if(elm.options[i].value==='0'){
                                elm.options[i].value= pagination.totalRows;
                                break;
                            }
                        }

                        for(i=0; i<l; i++){//set selected if match
                            v= elm.options[i].value;
                            if(v && v===limit.toString()){
                                elm.options[i].selected=true;
                                break;
                            }
                        }

                        elm.addEventListener('change', function (e){//event change limit
                            let
                                s= e.currentTarget,
                                i= s.selectedIndex,
                                v= s[i].value
                            ;
                            if(v) this.params.extra.set('limit', v)
                        }.bind(this))
                }, this)


            }, this)
        }

        this.domElms.getMsg().forEach(function (elm)
        {
            if(!this.opt.withMsg){
                elm.innerHTML=''
                return
            }

            let
                rmsg= this.tplNavbar?  'rpager' : 'rresult',
                m= {
                    'rresult': this.opt.rresult,
                    'rpager': this.opt.rpager,
                    'rnoresultfound': this.opt.rnoresultfound
                },
                out= 'This is a test message',
                d

            //search translation in dataset

            for(d in elm.dataset){
                if(m.hasOwnProperty(d)){
                    m[d]= elm.dataset[d]
                }
            }

            if(pagination.totalRows===0){
                out = m.rnoresultfound
            }else{
                out= m[rmsg]
                m[rmsg].match(/\{.+?\}/g).forEach(function (w){
                    w.slice(1, -1)
                    out= out.replace(w, pagination[w.slice(1, -1)])
                })
            }
            elm.innerHTML= out;
        }, this)

        this.emit('jpager.show.response', jam, body, this)
    }


    /**
     * @constructor
     * @name Pagination
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
     *         Pagination('#paginatorWrapper', params, mod)
     *             .setPager(pager)
     *             .submit();
     *     </script>
     *     </body>
     *     </html>
     *   </code>
     */
    function Pagination (wrapper,  params=undefined, mod=undefined)
    {
        if (!(this instanceof Pagination)) return new Pagination(wrapper, params, mod)

        let
            w= document.querySelector(wrapper),
            sort, i, icon, toggle, selected, j, k
        ;
        EventEmitter.mixin(this);
        this.cache= {page: {},response: {}};
        this.length=0;//current result length
        /**@type function|undefined*/
        this.staticFilter= undefined;
        this.params= {
            /**@type {Map}*/
            last: new Map(),//last serialized data (form)
            /** @type {Map} */
            extra: new Map(),//Query data for pagination(sort, page, etc)
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

        /** Paginator dom elements */
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
        this.tplFoot= Handlebars.compile(this.domElms.getFootScript().innerHTML);
        this.tplNavbar= (function (opt, elms){
            return !opt.withNavbar ? undefined : {
                compiled: Handlebars.compile(elms.getNavbarScript().innerHTML),
                options: {"size": opt.pagerSize}
            }
        })(this.opt, this.domElms);

        sort= this.opt.withSorting? w.querySelectorAll('[data-orderby]') : {};

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

        //Add events to any Element with [data-orderby]
        for (i = 0; i < sort.length; ++i)
        {
            //Add sort icon to Elements with [data-orderby]
            icon= document.createElement('i')
            icon.classList.add('fa',  this.opt.classSort)
            sort[i].appendChild(document.createTextNode(' '))
            sort[i].appendChild(icon)
            sort[i].classList.add('cursor-pointer')
            sort[i].addEventListener("click", function (ev)
            {

                if(this.length<2){
                    //console.warn('Nothing to sort')
                    return
                }
                selected= ev.currentTarget
                toggle= selected.getAttribute('data-sort')==='DESC' ? 'ASC' : 'DESC';
                selected.setAttribute('data-sort', toggle);
                this.setParameter('sort', toggle)
                    .setParameter('orderby', selected.getAttribute('data-orderby'))
                    .setParameter('page', this.tplNavbar? 1 : 0);

                (function(toggle, sel, p){//toggle sort icon
                    let
                        opt= p.opt,
                        add= toggle==='ASC'? opt.classAsc : opt.classDesc,
                        rem= toggle==='ASC'? opt.classDesc : opt.classAsc,
                        none= opt.classSort,
                        icon
                    ;
                    //change sort icon
                    sort.forEach(function (elm)
                    {
                        icon= elm.querySelector('i')
                        if(elm===sel){
                            icon.classList.remove(rem, none)
                            icon.classList.add(add);
                        }else{
                            elm.setAttribute('data-sort', '');
                            icon.classList.remove(rem, add)
                            icon.classList.add(none);
                        }
                    });
                })(toggle, selected, this)

                _fnPresetBeforeRequest.call(this, true)

            }.bind(this));
        }
    }


    Pagination.prototype=
        {
            /**
             * Remove cache and send request with last parameters
             * @return {Pagination}
             */
            update: function () {
                _fnPresetBeforeRequest.call(this, true)
                return this;
            },
            /**
             * Remove current page cache and send request with last parameters
             * @return {Pagination}
             */
            reload: function () {
                let p= this.params.extra.get('page');
                p= (p)? -Math.abs(p) : undefined//todo
                _fnPresetBeforeRequest.call(this, false, p)
                return this
            },
            /**
             * Send request
             * @return {Pagination}
             */
            submit: function () {
                if (this.requestSource.is_form)
                { //submit form para serializado eventos relacionados y serializado de
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
             * @return Pagination
             */
            setParameter: function (name, value)
            {
                this.params.extra.set(name, value);
                return this
            },
            /***
             * Function con datos que retorna JsonApiSpec[paginación]
             * @param c{function} filter
             */
            addStaticFilter: function (filter){
                this.staticFilter= filter;
                return this;
            }
        }

    window.Pagination = Pagination;
}(window, EventEmitter , Handlebars, JError));

