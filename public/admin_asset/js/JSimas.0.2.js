'use strict';
/**
 * Toolbox para formularios
 *
 */
(function(window, undefined) {

    let rinputs = /^(?:input|select|textarea|button)$/i,
        rpreventInputTypes = /^(?:reset|button|image|submit)$/i,
        serializableTypesSelector= 'textarea, select, input:not([type="reset"]):not([type="button"]):not([type="submit"]):not([type="image"])',
        rcheckableType = /^(?:checkbox|radio)$/i,
        rselectable= /^(?:select|select-one|select-multiple)$/i,
        // ,
        // rnoInnerhtml = /<(?:script|style|link)/i,
        // rxhtmlTag = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,
        // Support: IE<10
        // For `typeof xmlNode.method` instead of `xmlNode.method !== undefined`
        // core_strundefined = typeof undefined,
        // Used for splitting on whitespace
        // core_rnotwhite = /\S+/g,
        // rclass = /[\t\r\n\f]/g,
        // isPlainObject= function( obj ) {
        //     let key;
        // // Must be an Object.
        // if ( !obj || typeof obj !== "object" || obj.nodeType || obj == obj.window ) {
        //         return false;
        //     }
        // // Own properties are enumerated firstly, so to speed up,
        // // if last one is own, then all properties are own.
        //     for ( key in obj ) {}
        //
        //     return key === undefined || obj.hasOwnProperty(key);
        // }
        //simple extend
        extend = function ( defaults, options ) {
            let prop, extended = {};
             ;
            for (prop in defaults) {
                if (Object.prototype.hasOwnProperty.call(defaults, prop)) {
                    extended[prop] = defaults[prop];
                }
            }
            for (prop in options) {
                if (Object.prototype.hasOwnProperty.call(options, prop)) {
                    extended[prop] = options[prop];
                }
            }
            return extended;
        }
        ;


    function _fnParseSquareBracketInputTags(form)
    {
        let ret= {
                names: [],
                matches:{},
                getMatch: function (name){
                    return this.matches.hasOwnProperty(name)? this.matches[name] : null
                },
                getElement: function (name){
                    let m= this.getMatch(name);
                    return m? form.querySelector('[name="'+m+'"]') : null
                }
            },
            match;

        for(let i=0,elms=form.elements, l=form.length; i<l; i++)
        {
            match= elms[i].name.match(/\[(.*?)\]/)
            if(!match) continue
            ret.names.push(match[1])
            ret.matches[match[1]]= elms[i].name
        }


        ret.getElement.bind(ret)
        ret.getMatch.bind(ret)
        return ret
    }


    /**
     * @constructor
     * @name JSimas
     * @param {String|Object} selector
     * @param {Object} [context]
     */
    function JSimas (selector, context) {
        //return instance
        if (!(this instanceof JSimas)) return new JSimas(selector, context)

        this.context = context && context.nodeType ? context : window.document;

        if ( !selector ) {
            this.length=0
            return this;
        } else if (typeof selector === 'string'){

            let arr = Array.prototype.slice.call(this.context.querySelectorAll(selector))

            for(let i = 0; i< arr.length; i++){
                this[i] = arr[i]
            }

            this.length= arr.length

        }else if ( selector.nodeType ){
            this.length = 1
            this[0]= selector
        }
    }


    /**
     * Spec attributes & php class properties & input.name[name] are related.
     *
     * @param {JsonApiSpec} spec
     * @param {HTMLFormElement} form
     */
    JSimas.specToForm= function(spec, form)
    {
        let finder = _fnParseSquareBracketInputTags(form),
            attr, elm
        ;
        finder.names.forEach(function (n, i)
        {
            attr= spec.get(n)
            if(attr===undefined) return
            elm= finder.getElement(n)
            JSimas.populateHtmlFormElement(elm, attr)
        })

    }


    /**
     *
     * @param {HTMLFormElement} elm
     * @param {JsonApiSpec|*} val
     */
    JSimas.populateHtmlFormElement= function (elm, val)
    {
        let
            type= elm.type.toLowerCase(),
            nodeName= elm.nodeName.toLowerCase(),
            opt, j
        ;

        if(!elm.name || (nodeName==='input' && rpreventInputTypes.test(type))) return;

        if(elm.disabled && val!==null) elm.disabled=false;

        if (rcheckableType.test(type))
        {
            elm.checked= val===true;
            return
        }

        if (rselectable.test(type))
        {
            if(!elm.options.length || elm.dataset.jsuggest || elm.dataset.populatable)//dismiss options
            {
                if(elm.dataset.jsuggest)
                {
                    try{//window.JSuggest???
                        j= JSuggest.cache.get(elm).clearValues()
                        if(val) j.setValue(val)
                        return;
                    }catch (e) {
                        console.warn(e)
                    }
                }else{
                    elm.length=0
                }

                if( val===null || !(val instanceof JsonApiSpec)) return//populate select needs id & value

                    opt = document.createElement('option');
                    opt.value= val.id
                    opt.innerHTML= val.toString()
                    opt.selected= true
                    elm.appendChild(opt)

            }else{

                for (let n = 0, l= elm.options.length; n < l; n++)
                {
                    if(typeof val==="string"){
                        if (elm.options[n].value===val){
                            elm.options[n].selected=true;
                            return
                        }
                    }
                    if(val instanceof JsonApiSpec)
                    {//data repopulate first node / fieldvaluecodes
                        if (elm.options[n].value===val.id){
                            elm.options[n].selected=true;
                            return
                        }
                    }else{
                        if (elm.options[n].value===val){
                            elm.options[n].selected=true;
                            return
                        }
                    }

                }
            }

            return
        }

        elm.value= val;
    }

    JSimas.isHidden= function (el) {
        let style = window.getComputedStyle(el);
        return (style.display === 'none')
    }

    JSimas.inOn= function (elm, prop='color') {
        let on= 'rgb(240, 173, 78)';

        if(elm.style[prop]!==on){
            elm.style[prop]=on
            setTimeout( function () {
                elm.style[prop]=''
            },2000);
            return false
        }
        elm.style[prop]=''
        return true;
    }

    JSimas.isMobileDevice =  function (){
        return /Android|webOS|iPhone|iPad|BlackBerry|Windows Phone|Opera Mini|IEMobile|Mobile/i.test(navigator.userAgent)
    }

    JSimas.extend= extend
    /**
     * Find form elements in Form|div|.selector|... and return array of elements
     * @return {[]}
     */
    JSimas.prototype.formElements = function (){
        let n,el,
            i = 0,
            arr= [],
            elems = (arguments.length)? arguments : this;

        for(; i<elems.length; i++)
        {
            el = elems[i];

            if(!el.nodeType) continue

            if(el.nodeName.toLowerCase()==='form'){//is FORM
                for(n = 0; n<el.length; n++) arr.push(el[n])

            }else{
                if(rinputs.test(el.nodeName.toLowerCase())) arr.push(el)//form element
                else arr = arr.concat(JSimas(el).formElements(el.querySelectorAll(serializableTypesSelector)))//dom node
            }
        }
        return arr;
    }
    /**
     * Disable|enable|toggle
     * @param {boolean|undefined} bool Enable/disable elements. Default toggle
     * @param {boolean|undefined} [allTypes] Disable also prevented types. Default false.
     * @return JSimas
     */
    JSimas.prototype.disable = function(bool=undefined, allTypes=false){
        this.formElements().map(function (el) {
            if(rpreventInputTypes.test(el.type.toLowerCase()) && !allTypes) return
            else el.disabled = typeof bool === "boolean"? bool :  !el.disabled
        })
        return this;
    }


    /**
     * Copy form values
     * @param {HTMLFormElement|HTMLElement|undefined} form
     */
    JSimas.copy = function(form){
        [...form.elements].forEach(function (elm){
            if(elm.name==='' || elm.name.indexOf('_token')!==-1) return;
            localStorage.setItem(elm.name, elm.value);
            if(elm.dataset.jsuggest){
               JSuggest.copy(elm)
            }
        })
    }

    /**
     * paste form values
     * @param {HTMLFormElement} form
     * @param {boolean|null} form
     */
    JSimas.paste = function(form, clear){
        let val;
        [...form.elements].forEach(function (elm){
            if(elm.name==='' || elm.name.indexOf('_token')!==-1) return;
            val= localStorage.getItem(elm.name);
            if(val){
                if(elm.dataset.jsuggest){
                    JSuggest.paste(elm)
                }else{
                    elm.value= val;
                }
            }
        })
        if(clear) localStorage.clear()
    }

    /**
     * removes all child nodes and content from the selected elements*
     * @returns {JSimas}
     */
    JSimas.prototype.empty= function(){
        let elem, nodeName,
            i = 0;

        for ( ; (elem = this[i]) != null; i++ ) {
            nodeName = elem.nodeName.toLowerCase();
            if(rinputs.test(nodeName)){
               JSimas(elem).clear()
                if ( elem.options && nodeName === "select") {
                    elem.options.length = 0;
                }
            }else{
                // Remove any remaining nodes
                while ( elem.firstChild ) {
                    elem.removeChild( elem.firstChild );
                }
            }


        }
        return this;
    };

    /**
     * Clear form fields
     * @param {Object|undefined} o - Clear options
     * @returns {JSimas}
     */
    JSimas.prototype.clear= function(o={}){
        o= extend({
            hidden:false,
            notRegexType:false,
            notClass:false
        }, o||{})


        this.formElements().map(function (el) {
            let type = el.type.toLowerCase(),
                isHidden= function (el) {
                    let style = window.getComputedStyle(el);
                    return (style.display === 'none')
                }
            ;

            if(rpreventInputTypes.test(type)) return
            if(o.notRegexType && o.notRegexType.test(type)) return
            if(o.notClass && (el.getAttribute('class') && el.getAttribute('class').indexOf(o.notClass)!==-1)) return
            if(!o.hidden && (el.type==='hidden' || isHidden(el))) return


            if(el.classList.contains('jsuggest-false-input')){
                try{//window.JSuggest???
                    JSuggest.cache.get(el).clearValues()
                    return;
                }catch (e) {
                    console.warn(e)
                }
            }

            if(rcheckableType.test(type)){
                el.checked = false
            }else if(rselectable.test(type)){
                for (let n = 0; n < el.options.length; n++) {
                    el.options[n].selected= false;
                }
            }else{
                el.value = ''
                el.setAttribute('value', '')
            }

        })
        return this;
    };

    /**
     * Toggle enable/disable form elements by dataset.selector="selector/s"
     * @param {?Object} options - {
     *          callback: function Callback on click event with params (nodeList , radio, bool is_clicked),
     *          active: int Set default active radio button if fields are empty
     *          }
     * @example
     *      <input type="radio" name="surveyor" data-selector="#{{ form.surveyorid.vars.id }}">
     *      {{ form_row(form.surveyorid)}}
     *      <input type="radio" name="surveyor" data-selector="#{{ form.surveyor.vars.id }}">
     *      {{ form_row(form.surveyor) }}
     *      <script>
     *          JSimas('input[name=surveyor]').toggleRadioByDataset();
     *      </script>
     * @returns {JSimas}
     */
    JSimas.prototype.toggleRadioByDataset= function(options={}){
        let i, ii, n, radioListener, nodes, clickListener,
            active=null, //active radio item, default first
            checked= null,
            context= this.context,
            elems= [],
            radio = [],
            elm;
        //force options
        options = extend({callback: null, active: null}, options )

        //loop radio buttons
        this.formElements().forEach(function (item, i)
        {
            //Is input:radio|input:checkbox && has attr data-selector=".selector #id ..." ?
            if(!rcheckableType.test(item.type) || !item.dataset.selector) return;
            if(item.checked) checked=i //find checked
            /**
             * Find selectors to toggle by data-selector attr
             * @type {NodeListOf<Element>}
             */
            nodes = item.form.querySelectorAll(item.dataset.selector)

            //find not null data in fields to set active radio programatically???
            for(ii = 0; ii < nodes.length; ii++)
            {
                elm= nodes[ii]

                //si el select tiene un valor, debería ser el checheado por defecto
                if(elm.nodeName.toLowerCase()==='select' && elm.value)
                {
                    active = i;
                    break;
                }else
                    if(elm.value!==''){
                    active = i;
                    break;
                }
            }

            radio.push(item)
            elems.push(nodes)
        });

        if(!active){
            active= checked ?? 0;
        }

        //eventListener
        radioListener= {
            handleEvent: function (event) {
                for(i=0 ; i<radio.length; i++){
                    for(n = 0; n < elems[i].length; n++){
                            JSimas(elems[i][n]).clear({'hidden':true}).disable( event.target !== radio[i])
                    }
                }
            }
        }

        // set active
        if(options.active && options.active<radio.length) active = options.active
        //add event listener
        for(i=0; i<radio.length; i++)
        {
            if(i === active){
                radio[i].checked = true
            }else{
                for(n = 0; n < elems[i].length; n++){
                    // JSimas(elems[i][n]).clear().disable()
                    JSimas(elems[i][n]).disable()
                }
            }
            radio[i].addEventListener('click',radioListener);
        }
        return this
    };

    /**
     * paste form values
     * @param {HTMLFormElement|HTMLElement} form
     * @param {Object|null} opt
     */
    JSimas.initForm = function(form, opt){

        let n=[],ret={form: form, xhr: null, repopulate: [], suggest: []};
        opt= opt||{};

        if(!form.tagName.toLowerCase()==='form')
        {
            let collectionOf= form.getElementsByTagName('form');
            if(collectionOf.length){
                ret.form= collectionOf[0];
            }else{
                return null;
            }
        }

        //toggleRadioByDataset (todos tienen el mismo elm.name)
        ret.form.querySelectorAll('input[type="radio"][data-selector]').forEach(function (elm)
        {
            if(n.indexOf(elm.name)===-1){
                n.push(elm.name);
                JSimas('input[name='+elm.name+']').toggleRadioByDataset();
            }
        });

        //data-repopulate para el primer elm
        form.querySelectorAll('[data-repopulate]').forEach(function (elm, k)
        {
            ret['repopulate'][elm.getAttribute('id')]=(new Repopulate(elm)).populate();
        });


        form.querySelectorAll('.jsuggest-select').forEach(function (elm, k){
            ret['suggest'][elm.getAttribute('id')]= new JSuggest(elm);
        });

        //XMLHttpRequest si hay .js-submit
        if(form.querySelector('.js-submit')){
            ret['xhr'] = new JXhrSubmit(form);
        }

        return ret;
    }

    window.JSimas = JSimas;
}(window));