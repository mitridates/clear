/**
 * Add JsonApiSpec inherit objects to its factory.
 * @function
 * @requires JsonApiManager
 * @requires JsonApiSpec
 * @example
 *      //Create the object and add to factory.
 *     function Person(data) {
 *         jas.call(this, data);
 *         this.toString= function (){
 *             return this.attributes['name']+' '+this.attributes['surname'];
 *         }
 *     }
 *     Person.prototype = Object.create(jas.prototype);
 *     jam.Factory.addType('person', (ob)=> new Person(ob))
 *
 *     //OR use a shortcut JsonApiManager.Factory.addSpec()
 *    jam.Factory.addSpec('person', function Person(d)
 *     {
 *         jas.call(this, d);
 *         this.toString= function (){
 *              return this.attributes['name']+' '+this.attributes['surname'];
 *         }
 *     })
 */
(function(jam, jas) {
    'use strict';

    /**
     * @constructor
     * @param {JsonApiSpec} spec
     * @param {[]} aloc
     * @private
     */
    function _fnLocation(spec, aloc){
        this.spec= spec
        this.names= []
        this.toString= function (glue=' > '){
            return this.names.join(glue)
        }
        this.closest= function (){
            return this.names.length? this.names[this.names.length - 1] : '';
        }.bind(this)

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

    let a= 'attributes';

    const CODING={
        I: 'International coding',
        U: 'Uncoded',
        L : 'Locally coded'
    };

    const VALUETYPE={
        S: 'Single-valued',
        M: 'Multi-valued'
    };
    const DATATYPE={
        'A' : 'Alphanumeric up to 255 chars long (A1-A255)',
        'N' : 'Numeric; A "real" number allowing decimal places and many significant digits, typically around 15.',
        'D' : 'Date. Accepts only full valid dates',
        'L' : 'Logical. True or False',
        'I' : 'Integer (sort); A whole number in the range -32,767 to +32,768.',
        'S' : 'Short integer up to 32768',
        'M' : 'Memo ;variable length free text',
        'B' : 'BLOB; A Binary Large Object e.g. a photo image'
    }
    const ENTITY={
        AR:  'Article in a publication',
        AT:  'Field or attribute',
        AV:  'Field value',
        CA:  'Cave or karst feature',
        CI:  'Citation',
        EN:  'Entity',
        OR:  'Organisation',
        PA:  'Land parcel',
        PB:  'Publication',
        PE:  'Person',
        PH:  'Photograph',
        PL:  'Plan or map',
        PM:  'Marker (Permanent mark)',
        PS:  'Map series',
        RE:  'Region or area',
        RL:  'Role',
        RP:  'Report',
        SM:  'Specimen',
        SP:  'Species',
        ST:  'Site',
        SU:  'Subject',
        SV:  'Survey',
        SY:  'System field',
        XK:  'A key-in batch',
        XL:  'An upload batch',
        XU:  'An update batch'
    }

    //##### Fieldvaluecode    ####
    /**
     * Fieldvaluecode class.
     * @name Fieldvaluecode
     * @constructor
     * @augments JsonApiSpec
     * @throws ExceptionInvalidType
     */
    function Fieldvaluecode(data) {
        jas.call(this, data);
        this.toString= function (){
            return this[a].value
        }
        this.set('s_coding', CODING[this.get('coding')] ?? this.get('coding'))
    }
    jam.Factory.addSpec('fieldvaluecode', Fieldvaluecode)

//##### Fielddefinition    ####
    /**
     * Fielddefinition class.
     * @name Fielddefinition
     * @constructor
     * @augments JsonApiSpec
     * @throws ExceptionInvalidType
     */
    function Fielddefinition(data) {
        jas.call(this, data);
        this.set('s_coding', CODING[this.get('coding')] ?? this.get('coding'))
        this.set('s_singlemultivalued', VALUETYPE[this.get('singlemultivalued')] ?? this.get('singlemultivalued'))
        this.set('s_datatype', DATATYPE[this.get('datatype')] ?? this.get('datatype'))
        this.set('s_entity', ENTITY[this.get('entity')] ?? this.get('entity'))
    }
    jam.Factory.addSpec('fielddefinition', Fielddefinition)

//##### Fielddefinitionlang    ####
    jam.Factory.addSpec('fielddefinitionlang', function Fielddefinitionlang(d) {
        Fielddefinition.call(this, d);
    })


    function Person(d)
    {
        jas.call(this, d);
        this.toString= ()=>  this[a]['name']+' '+this[a]['surname'];
    }
    jam.Factory.addSpec('person', Person)

    jam.Factory.addSpec('mapsurveyor', function Mapsurveyor(d)
    {
        jas.call(this, d);
        this.toString= function (){
            if(this[a]['surveyorid'] instanceof Person){//person
                return this[a]['surveyorid'].toString()
            }else{
                return this[a]['surveyor']
            }
        }
    })


    function Cave(d) {
        jas.call(this, d);
        this.set('_location', ()=>{return new _fnLocation(this, ['country', 'admin1', 'admin2', 'admin3'])})
    }
    jam.Factory.addSpec('cave', Cave)


    function Article(d)
    {
        jas.call(this, d);
        let self= this;
        this.getReference= ()=>{
            let out=[];
            out.push(self.get('author'));
            if(self.get('publicationyear')){
                out.push(' ('+self.get('publicationyear') +(self.get('publicationyearsuffix')??'')+')');
            }
            if(self.get('articlename')){
                out.push('. '+self.get('articlename'));
            }
            if(self.get('publicationname')){
                out.push('. '+self.get('publicationname'));
            }
            if(self.get('volumenumber')){
                out.push(', '+self.get('volumenumber'));
            }
            if(self.get('issuenumber')){
                out.push(' ('+self.get('issuenumber')+')');
            }
            if(self.get('pagerange')){
                out.push(', '+self.get('pagerange')+'p');
            }
            if(self.get('isbn')){
                out.push('. ISBN '+self.get('isbn'));
            }
            if(self.get('issn')){
                out.push('. ISSN '+self.get('issn'));
            }
            if(self.get('quantityofmaps')){
                out.push(' ('+self.get('quantityofmaps')+' mapas)');
            }
            return out.join('')
        }

        this.toString= function (){
            let out=[];
            if(self.get('articlename')){
                out.push(self.get('articlename'));
            }
            if(self.get('publicationname')){
                if(self.get('articlename')) out.push('. ');
                out.push(self.get('publicationname'));
            }
            if(self.get('volumenumber')){
                out.push(', '+self.get('volumenumber'));
            }
            if(self.get('publicationyear')){
                out.push(' ('+self.get('publicationyear') +(self.get('publicationyearsuffix')??'')+')');
            }
            return out.join('')
        }

    }
    jam.Factory.addSpec('article', Article)

    jam.Factory.addSpec('Mapsurveyor', function Mapsurveyor(d)
    {
        jas.call(this, d);
        this.toString= function (){
            if(this[a]['surveyorid'] instanceof Person){//person
                return this[a]['surveyorid'].toString()
            }else{
                return this[a]['surveyor']
            }
        }
    })

    jam.Factory.addSpec('mapdrafter', function Mapdrafter(d)
    {
        jas.call(this, d);
        this.toString= function (){
            if(this[a]['drafterid'] instanceof Person){//person
                return this[a]['drafterid'].toString()
            }else{
                return this[a]['drafter']
            }
        }
    })

    jam.Factory.addSpec('mapcitation', function Mapcitation(d){
        jas.call(this, d);
        this.toString= function (){//?????????
            if(this[a]['article'] instanceof Article)
            {
                return this[a]['article'].toString()
            }else{
                return this[a]['article']
            }
        }
    })

    jam.Factory.addSpec('specie', function Specie(d){
        jas.call(this, d);
        this.toString= function (){
            let
                n = this[a]['name'],
                c = this[a]['commonname'];
            return  c ? c + (n? ' ('+n+')': '') : n
        }
    })
    jam.Factory.addSpec('link', function Link(d){
        jas.call(this, d);
        this.toString= function (){
            return  this[a].title
        }
    })


}(JsonApiManager, JsonApiSpec));