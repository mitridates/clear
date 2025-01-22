/**
 * Immediately-Invoked Function Expression (IIFE).
 * @module DummyModule
 *  @return window.{JsonApiManager, JsonApiSpec}
 * @function
 * @param {object} window - Global window object.
 */
(function(window, undefined) {
    'use strict';

    let is_spec= function (s){
        return (typeof s === 'object'
            && s.hasOwnProperty('type')
            && s.hasOwnProperty('attributes')
            && s.hasOwnProperty('id'));
    };

    /**
     * JSON:API Set resource single object
     * @constructor
     * @name JsonApiSpec
     * @param {Object} data JSON:API resource object
     * @see https://jsonapi.org/
     */
    function JsonApiSpec(data){

        this.id = data.id;
        this.type= data.type;
        this.attributes = data.attributes;
        this.meta = data.meta||null;
        this.links = data.links||null;
        this.relationships= data.relationships??null;
        this.toString= function (){
            return this.attributes.hasOwnProperty('name')? this.attributes.name : this.id;
        }
    }

    /**
     * @memberof JsonApiSpec
     * @param {string|null} s
     * @return {string|null} url
     */
    JsonApiSpec.prototype.getLink= function (s){
        return (this.links && this.links.hasOwnProperty(s||'self')) ? this.links[s||'self'] : null;
    }

    /**
     * Get attribute|[attributes]|property by name or function. Function returns compound values
     * @memberof JsonApiSpec
     * @this {JsonApiSpec} Current instance
     * @param {string|*} s
     * @return {*|undefined} undefined if not exists
     */    
    JsonApiSpec.prototype.get= function (s){
        let i, ret={};

        if(Array.isArray(s)){
            for(i=0; i<s.length; i++){
                ret[s[i]]=this.get(s[i])
            }
            return ret
        }

        switch (typeof s){
            case "string":
                if(s==='id'){
                    return this.id
                }else if(s==='_type_') {
                    return this.type
                }else{
                    return this.attributes.hasOwnProperty(s)?  this.attributes[s]: undefined;
                }
            case "function": return  s.call(this, this.attributes);
            default: return undefined
        }
    }

    /**
     * Set attribute
     * @memberof JsonApiSpec
     * @param {string} key
     * @param {string|*} val     
     * @return {JsonApiSpec}
     */       
    JsonApiSpec.prototype.set= function (key, val){
        this.attributes[key]= val;
        return this;
    }
    /**
     * Set meta
     * @memberof JsonApiSpec
     * @param {string} key
     * @param {string|*} val
     * @return {JsonApiSpec}
     */
    JsonApiSpec.prototype.setMeta= function (key, val){
        this.meta[key]= val
        return this;
    }
    /**
     * Get meta
     * @memberof JsonApiSpec
     * @param {string} key
     * @return *|null
     */
    JsonApiSpec.prototype.getMeta= function (key){
        return (this.meta.hasOwnProperty(key))? this.meta[key] : null
    }
    //##### End JsonApiSpec     ####
    //#####    jsonApiManager    ####
   /**
     * Intermediary between XMLHttpRequest response and JsonApiSpec
     * @constructor
     * @name JsonApiManager
     * @param {Array} data - response.data
     * @param {Array|null} included - response.included
     */
    function JsonApiManager(data, included=null) {
        this.data = data;
        this.included = included||null;
        this.ret= [];
        this.length= data.length;
        this.is_parsed= false;
        /**
         * @type undefined|string Document/Collection
         */
        this.resource=undefined;//
    }

    /**
     * Create instance of JsonApiSpec if included
     * @memberof JsonApiManager
     * @param {Object} data - response.included[][id] (JsonApiSpec) found in  JsonApiSpec.relationship{id, type}
     * @param {string} key - JsonApiSpec.relationship[id]
     * @param {JsonApiSpec} spec - Parent JsonApiSpec
     * @return {JsonApiSpec}
     */
    JsonApiManager.prototype.getIncluded= function(data, key, spec){
        let inc;
        if(!data){
            console.warn(`Found null value in relationships for Resource ID:${spec.id} (${spec.type}.relationships.${key}.data===null), 
            so is expected that there is no response.included[][JsonApiSpec]. Did you tried to serialize a null relationship in controller?`);
            return null;
        }
        for (let key in this.included){
            inc= this.included[key]
            if (inc.type===data.type
                && inc.id===data.id) {
                return JsonApiManager.Factory.toJsonApiSpec(inc)
            }
        }
        return null
    }


    /**
     * Parse JsonApiSpec and get relationships
     * @param {JsonApiSpec} spec
     * @memberof JsonApiManager
     * @return JsonApiSpec
     */
    JsonApiManager.prototype.parseSpec = function(spec) {
        let i, l, inc, attr, key;

        for (key in spec.attributes)
        {
            attr= spec.attributes[key]

            //relationship attributes[name] in spec.relationships[key] to JsonApiSpec
            if(spec.relationships && spec.relationships[key])
            {
                attr= spec.attributes[key] = this.getIncluded(spec.relationships[key].data, key, spec)
            }

            //attributes[key] is JsonApiSpec, search for nested relationships
            if(attr instanceof JsonApiSpec &&  attr.relationships)
            {
                for (let i in attr.attributes)
                {
                    if(attr.relationships[i]===undefined) continue
                    //In a compound document, all included resources MUST be represented
                    // as an array of resource objects in a top-level included member
                    spec.attributes[key].attributes[i]= this.getIncluded(attr.relationships[i].data, key, spec)
                }
            }
        }

        //check if meta has [JsonApiSpec]
        if(spec.hasOwnProperty('meta') && spec.meta!==null)
        {

            for(let key in spec.meta)
            {
                if(Array.isArray(spec.meta[key]) && is_spec(spec.meta[key][0]))
                {//PHP: $foo->addMeta('var', $collection->toArray()) -> Javascript: spec.meta[key]===JsonApiSpec[]
                    for(i=0, l=spec.meta[key].length; i<l; i++)
                    {
                        spec.meta[key][i]= new JsonApiManager(spec.meta[key][i]).getParsed()//parse document
                    }
                }else if(spec.meta[key].hasOwnProperty('data') && Array.isArray(spec.meta[key]['data']) && spec.meta[key]['data'][0])
                {
                    // PHP: $foo->addMeta('var', new Document($collection)) -> Javascript: spec.meta[key]===data[JsonApiSpec[]], included[...]]].
                    inc= spec.meta[key].hasOwnProperty('included')? spec.meta[key].included : null
                    spec.meta[key]= new JsonApiManager(spec.meta[key]['data'], inc).getParsed()//parse collection
                }
            }
        }

        return spec
    }

    /**
     * Parse XMLHttpRequest Response
     * @memberof JsonApiManager
     * @return JsonApiManager
     */
    JsonApiManager.prototype.parseResponse = function()
    {
        if(this.is_parsed) return this

        this.resource= typeof this.data ==='object' && this.data.hasOwnProperty('type')? 'document' : 'collection'

        if(this.resource==='document'){
            this.ret.push(this.parseSpec(JsonApiManager.Factory.toJsonApiSpec(this.data)))
        }else{
            for(let i=0, l= this.data.length; i<l;i++){//loop response.data
                this.ret.push(this.parseSpec(JsonApiManager.Factory.toJsonApiSpec(this.data[i])))
            }
        }
        this.length= this.ret.length
        this.is_parsed=true;
        return this;
    }

    /**
     * Search in response by type and filter
     * @memberof JsonApiManager
     * @param {Object} s Search filter {key:val}
     * @return {Array} [JsonApiSpec]
     */
    JsonApiManager.prototype.findBy = function(s)
    {
        let i, el, val,
            ret=[],
            parsed= this.getParsed();

        for(i=0; i<parsed.length;i++){//loop response.data
            el= parsed[i]

            for(let k in s)
            {
                val= el.get(k)
                if(val===false){
                    continue//not found
                }

                if(typeof s[k] === "string" && s[k]===val){//todo regex
                    ret.push(el)
                }else if(typeof s[k] === "function"){//use toString
                    if(s[k].call(el, val)!==false){
                        ret.push(el)
                    }
                }
            }
        }
        return ret;
    }

    /**
     * Search by ID
     * @memberof JsonApiManager
     * @param {string|number} id
     * @return {JsonApiSpec|null}
     */
    JsonApiManager.prototype.findById = function(id)
    {
        let i,
            ret=null,
            parsed= this.getParsed();

        if(!isNaN(id)){
         id= parseInt(id);
        }

        for(i=0; i<parsed.length;i++){//loop response.data
            let pid= parsed[i].id;
            if(!isNaN(pid)){
                pid= parseInt(pid);
            }
            if(pid===id) return parsed[i]
        }
        return ret;
    }

    /**
     * Parsed response to JsonApiSpec
     * @memberof JsonApiManager
     * @return {JsonApiSpec|JsonApiSpec[]} JsonApiSpec document|collection
     */
    JsonApiManager.prototype.getParsed = function()
    {
        if(!this.is_parsed){
            this.parseResponse();
        }
        return (this.resource==='document')?  this.ret[0] : this.ret;
    }
    /**
     *  object literal add and return JsonApiSpec inherit types
     */
    JsonApiManager.Factory = (function(jas, jam)
    {
        let types = {};
        let specs = {};
        return {
            /***
             * Shortcut to addType
             * @param type
             * @param fn
             */
            addSpec: function (type, fn){
                fn.prototype = Object.create(jas.prototype);
                jam.Factory.addType(type, (o)=> new fn(o))
                specs[type]= fn
            },
            /***
             * Add JsonApiSpec Inherited Object
             * @param type
             * @param callBack
             * @example
             *     function Person(data)
             *     {
             *         jas.call(this, data);
             *         this.toString= ()=>  this.attributes['name']+' '+this.attributes['surname'];
             *     }
             *     Person.prototype = Object.create(jas.prototype);
             *     jam.Factory.addType('person', (ob)=> new Person(ob))
             *
             *     let person= jam.Factory.toJsonApiSpec({id:1,type:'person',attributes:{name:'John', surname: 'Doe'}})
             *     console.log(person, person.toString())
             *     //Person{id: 1, type: 'person', attributes: {…}n,…} [[Prototype]]:JsonApiSpec
             *     //'John Doe
             */
            addType: function (type, callBack) {
                types[type] = callBack
            },
            getSpec: function (type) {//check instanceof...
                return specs[type]
            },
            toJsonApiSpec: function (ob) {
                return (types.hasOwnProperty(ob.type)) ? types[ob.type](ob) : new jas(ob)
            },
            exist: function (type) {
                return types.hasOwnProperty(type)
            }
        }
    }
    )(JsonApiSpec, JsonApiManager);

    window.JsonApiManager= JsonApiManager;
    window.JsonApiSpec= JsonApiSpec;
}(window));
