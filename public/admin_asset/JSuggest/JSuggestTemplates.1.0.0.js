(function(JSuggest, JsonApiSpec) {
    'use strict';

    /**
     * @constructor
     * @param {JsonApiSpec} spec
     * @param {string[]} aloc Available administrative divisions in THIS spec [country, admin1, admin2...]
     * @private
     */
    function _fnLocation(spec, aloc){
        this.spec= spec
        this.names= []
        this.toString= function (glue=' > '){
            return this.names.join(glue)
        }
        this.closest= function (){
            return this.names.length? this.names[this.names.length - 1] : null;
        }

        let l=aloc.length,
            i,s
            ;

        for(i=0;i<l;i++){
            s = spec.get(aloc[i])
            if(!s) continue
            if(s instanceof JsonApiSpec){

                if(this.names.indexOf(s.get('name'))!==-1) continue//prevent same state/province name

                this.names.push(s.get('name'))
            }else{
                this.names.push(s)
            }
        }
    }

    let _defaultProtoWithLocation= {
        /***
         * Return a JSuggest list item
         * @param {HTMLElement} wrap JSuggest list item wrapper
         * @param {number} index JSuggest list item number
         * @return {HTMLElement}
         */
        getItem: function (wrap, index)
        {
            let inner= document.createElement('div')
            inner.innerHTML= 'ID: '+this.spec.id + '. '+ this.loc.toString()
            wrap.appendChild(document.createTextNode(this.spec.toString() ))
            wrap.appendChild(inner)
            return wrap
        },
        setInput: function (elm){
            let
                str= this.spec.toString(),
                loc= this.loc.toString(' > '),
                closest= this.loc.closest(),
                id= '. ID: '+this.spec.id
            ;
            elm.value= str + (closest? '. '+closest : '')
            elm.title= str+ id + (loc!==''? '. '+loc : '')
        }
    }

    /**
     * @constructor
     * @class
     * @name JSuggestFormatter
     * @param {JsonApiSpec} spec
     * @see JSuggest
     */
    function JSuggestFormatter(spec)
    {
        this.spec= spec;
        this.bag={};//set bag on getItem
        this.toString= function (){
            return this.spec.toString();
        }
    }

    /**
     * Render option item to overwrite
     * @param {HTMLDivElement} wrap Suggestion wrapper
     * @param {number} index Suggestion index
     * @memberof JSuggestFormatter
     * @return {HTMLDivElement}
     */
    JSuggestFormatter.prototype.getItem= function (wrap, index){
        wrap.appendChild(document.createTextNode(this.spec.toString() ))//example
        return wrap;
        // throw Error('You must implement abstractMethod "getItem"');
    }

    /**
     * Set false input properties
     * Ojo. No modificar elm.value
     * @param {HTMLDivElement} elm False input element
     * @memberof JSuggestFormatter
     * @example
     *   elm.value: spec.toString() //no modificar
     *   title: spec.toString() + '. ' + spec.id,
     *   idx: spec.id
     */
    JSuggestFormatter.prototype.setInput= function (elm){
        // throw Error('You must implement abstractMethod "getInput"');
    }

    /**
     * Cave.
     * @name Cave
     * @class
     * @augments JSuggestFormatter
     */
    function Cave(spec) {
        JSuggestFormatter.call(this, spec);
        /**
         * @type _fnLocation
         */
        this.loc= new _fnLocation(spec, ['country', 'admin1', 'admin2', 'admin3'])
    }
    Cave.prototype = Object.create(JSuggestFormatter.prototype);
    Cave.prototype.getItem= _defaultProtoWithLocation.getItem
    Cave.prototype.setInput= _defaultProtoWithLocation.setInput
    JSuggest.cache.addTemplate('cave', Cave)

    /**
     * @name Organisation
     * @class
     * @augments JSuggestFormatter
     */
    function Organisation(spec) {
        JSuggestFormatter.call(this, spec);
        this.loc=new _fnLocation(spec, ['country', 'admin1', 'admin2', 'admin3'])
    }
    Organisation.prototype = Object.create(JSuggestFormatter.prototype);
    Organisation.prototype.getItem= _defaultProtoWithLocation.getItem
    Organisation.prototype.setInput= _defaultProtoWithLocation.setInput
    JSuggest.cache.addTemplate('organisation', Organisation)


    /**
     * @name person
     * @class
     * @augments JSuggestFormatter
     */
    function Person(spec) {
        JSuggestFormatter.call(this, spec);
        this.loc=new _fnLocation(spec, ['country', 'admin1', 'admin2', 'admin3', 'cityorsuburb'])
    }
    Person.prototype = Object.create(JSuggestFormatter.prototype);
    Person.prototype.getItem= _defaultProtoWithLocation.getItem
    Person.prototype.setInput= _defaultProtoWithLocation.setInput
    JSuggest.cache.addTemplate('person', Person)

    /**
     * @name article
     * @class
     * @augments JSuggestFormatter
     */
    function Article(spec) {
        JSuggestFormatter.call(this, spec);
        this.loc=new _fnLocation(spec, ['country', 'admin1'])
        this.getSuffix= function (spec, loc){
            let out= (loc.closest())? [loc.closest()] : [];
                ['isbn', 'issn', 'legaldepositnumber'].forEach(function (s){
                if(spec.get(s)){
                    if(s!=='legaldepositnumber') out.push(`${s}: ${spec.get(s)}`)
                    else out.push(spec.get(s))
                }
            })
            return out.length? out.join('; ') : `ID: ${spec.id}`;
        }
    }
    Article.prototype = Object.create(JSuggestFormatter.prototype);
    Article.prototype.getItem= function (wrap, index){
        let inner= document.createElement('div'),
            suffix= this.getSuffix(this.spec, this.loc);

        inner.innerHTML=suffix;
        wrap.appendChild(document.createTextNode(this.spec.toString() ))
        wrap.appendChild(inner)
        return wrap
    }

    Article.prototype.setInput= function (elm){
        let
            suffix= this.getSuffix(this.spec, this.loc),
            str= this.spec.toString(),
            closest= this.loc.closest()
        ;
        elm.value= str + (closest? '. '+closest : '')
        elm.title= str+'. '+suffix
    }
    JSuggest.cache.addTemplate('article', Article)


    /**
     * @name Link
     * @class
     * @augments JSuggestFormatter
     */
    function Link(spec) {
        JSuggestFormatter.call(this, spec);

        this.getAuthor= function (raw){
            let arr= ['author', 'authorname'];
            for (let i = 0; i < arr.length; i++) {
                let found= this.spec.get(arr[i]);
                if(found){
                    if(raw){
                        return `${found}`;
                    }else{
                        let
                            i= document.createElement("i"),
                            span= document.createElement("span")
                        ;
                        i.style.fontFamily='FontAwesome';
                        i.style.fontStyle="normal";
                        i.setAttribute('class', 'fa fa-2x') ;
                        i.innerHTML= "&#xf007;"//fa-user
                        span.appendChild(i);
                        span.appendChild(document.createTextNode(' ' + found));
                        span.style.fontWeight='bold';
                        return span;
                    }
                    break;
                }
            }
            return null
        }

        this.getOrg= function (raw=true)
        {
            let arr= ['organisation', 'organisationname'];
            for (let i = 0; i < arr.length; i++) {
                let found= this.spec.get(arr[i]);
                if(found){
                    if(raw){
                        return `${found}`;
                    }else{
                        let
                            i= document.createElement("i"),
                            span= document.createElement("span")
                        ;
                        i.style.fontFamily='FontAwesome';
                        i.style.fontStyle="normal";
                        i.setAttribute('class', 'fa fa-2x') ;
                        i.innerHTML= "&#xf509;"//fa-users-gear
                        span.appendChild(i);
                        span.appendChild(document.createTextNode(' ' + found));
                        span.style.fontWeight='bold';
                        return span;
                    }
                    break;
                }
            }
                return null
        }

        this.getInfo= function (raw= true){
            let arr=[], val;
            if(val=this.getOrg(raw)) arr.push(val);
            if(val=this.getAuthor(raw)) arr.push(val);
            if(arr.length){
                if(raw){
                    return arr.join(', ')
                }else{
                    let s= document.createElement("span");
                    arr.forEach(function (/**HTMLElement*/elm){
                        let d= document.createElement('div');
                        d.appendChild(elm)
                        s.appendChild(d)
                    })
                    return s;

                }
            }else{
                return null;
            }

        }
    }
    Link.prototype = Object.create(JSuggestFormatter.prototype);
    Link.prototype.getItem= function (wrap, index){
        let
            inner= document.createElement('div'),
            info= this.getInfo(false);
        ;


        wrap.appendChild(document.createTextNode(this.spec.toString() ))
        if(info){
            inner.appendChild(info);
            wrap.appendChild(inner)
        }
        return wrap
    }

    Link.prototype.setInput= function (elm){
        let
            str= this.spec.toString()
        ;
        elm.value= elm.title= str+'. '+ this.getInfo(true);
    }
    JSuggest.cache.addTemplate('link', Link)
}(JSuggest, JsonApiSpec));

