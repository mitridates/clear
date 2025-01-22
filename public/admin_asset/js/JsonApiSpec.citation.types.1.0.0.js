/**
 * @see ./JsonApiSpec.app.types.1.0.0.js
 * @function
 * @requires JsonApiManager
 * @requires JsonApiSpec
 */
(function(jam, jas) {
    'use strict';

    let getString= {
        'publicationDate': function(){
            let d= getDate.apply(this, arguments);
            return d? `(${d}). ` : null;
            },
        'title': function (){
            let t= this.get('title'),
                s= this.get('subtitle');
            return s? `<i>${t}: ${s}</i>` : `<i>${t}</i>`
        },
        'contributor': function(){
                return getContributors.apply(this, arguments);
        },
        'page': function(){

            let p=this.getJsonData('page'),
                pe=this.getJsonData('page_end');
            if(!p) return null;
            return pe? ` (pp. ${p}-${pe})`: ` (p. ${p})`;
        },
        'issue': function(){return this.getJsonData('issue');},
        // /**
        //  * @param {string} s string value to search
        //  * @param {string} f return format
        //  */
        // 'jprintf': function(s, f){
        //     let r=  this.getJsonData(s);
        //     return r? String.format(f, r) : null;
        // },
        // /**
        //  * @param {string} s string value to search
        //  * @param {string} f return format
        //  */
        // 'sprintf': function(s, f){
        //     let r=  this.get(s);
        //     return r? String.format(f, r) : null;
        // },
        'link': function(){
            let ret=[], l, dPref= 'http://doi.org/', t=['url', 'pdf', 'doi'];
            for (let i = 0; i < t.length; i++) {
                l= this.getJsonData(t[i]);
                if(l){
                    l=  t[i]==='doi'? dPref+l : l;
                    if(ret.length===0){
                        ret.push('. ');
                    }else{
                        ret.push(', ');
                    }
                    ret.push(`<a href="${l}" title="${t[i].toUpperCase()}" target="_blank">${l}</a>`)
                }
            }
                return ret.length? ret.join('') : null;
        }

    };

    /**
     *
     * @param {string|null} y
     * @param {string|null} m
     * @param {string|null} d
     * @this Citation
     */
    function getDate(y,m,d)
    {

        let ret=[], date, locale= REQUEST_LOCALE|| 'es';
        y= y||this.getJsonData('year');
        m= m||this.getJsonData('month');
        d= d||this.getJsonData('day');

        if(!y) return 'n.d.';
        if(!m) return y;

        if(!isNaN(m)){
            date = new Date(y, m, d);
            let year = new Intl.DateTimeFormat(locale, { year: 'numeric' }).format(date);
            let month = new Intl.DateTimeFormat(locale, { month: 'long' }).format(date);
            ret.push(`${year}, ${month.charAt(0).toUpperCase()+ month.slice(1) }`);
            if(d) ret.push(` ${d}`);
        }else{
            ret.push(`${y}, ${m.charAt(0).toUpperCase()+ m.slice(1).toLowerCase() }`);
            if(d) ret.push(` ${d}`);
        }
        return ret.join('');
    }

   


   

    /**
     * @param {number} rule Citation.CATALOGING_RULE
     * @this Citation
     * @returns {string}
     */
    function getContributors(rule)
    {
        let contributors, test, c,             
            maxLength= Citation.AUTHORS_LENGTH[rule],
            arr=[],
        /**
         * @param {JSON} o
         * @param {string} type Reference type
         * @return string
         */
        orgToString= function (o , type)
        {
            let prop=['name', 'screenname']
            for(let i=0; i<prop.length; i++){
                let p= prop[i];
                if(o.hasOwnProperty(p)){
                    return o[p];
                }
            }
            return null;
        },
            /**
             * @param {JSON} c Contributor
             * @return string
             */
         personToString= function (c)
        {
            let ret=[];
            let prop=['infix', 'lastname', 'firstname'];
            for(let i=0; i<prop.length; i++){
                let p= prop[i];
                if(c.hasOwnProperty(p)){
                    let val= c[p];
                    if(!val) continue;
                    if(p==='lastname'){
                        ret.push(val+',')
                    }else if(p==='firstname'){
                        ret.push(val.slice(0,1).toUpperCase()+'.')
                    }
                }
            }
            return ret.join(' ')
        };
        
        try{contributors= JSON.parse(this.get('contributor'));}catch(e){}
        if(!Array.isArray(contributors)) return null;

        for(let i=0;i<contributors.length;i++) {
            test = contributors[i];
            switch (test['__type']) {
                case 'org':
                    c= orgToString(test);
                    break;
                case 'person':
                    c= personToString(test);
                    break;
                default: c=null;
            }
            if(c) arr.push(c);
        }

        //todo Ver reglas catalogación e idioma.
        if(maxLength>arr.length && arr.length+1===maxLength)//más autores
        {
            return `${arr.join(', ')} et al.`;
        }

        if(maxLength<= arr.length && arr.length+1===arr.length)//menos autores
        {
            let last = arr.pop();
            return `${arr.join(', ')} & ${last}`;
        }
        //No alcanzable.
        return arr.join(', ')
    }

    /**
     * @this Citation
     * @returns {string}
     */
    function getReference(rule)
    {
        let res, ret=[], i=0,
            r = rule||Citation.CATALOGING_RULE.APA6;
        let items= (function (spec, type){
            switch (type){
                case 0:
                case 1: return ['contributor', 'publicationDate', 'title', 'issue', 'page', 'link'];//book
                case 2: return ['contributor', 'publicationDate', 'title', 'issue', 'page', 'link'];//todo book_carpet
                case 3: return ['contributor', 'publicationDate', 'title', 'issue', 'page', 'link']//JOURNAL_ARTICLE_TYPE article
                case 4: return ['contributor', 'publicationDate', 'title', 'issue', 'page', 'link'];//WEBPAGE_TYPE
                case 5: return ['contributor', 'publicationDate', 'title', 'issue', 'page', 'link'];//WEBSITE_TYPE
                case 6: return ['contributor', 'publicationDate', 'title', 'issue', 'page', 'link'];//todo ONLINE_ARTICLE_TYPE
                case 7: return ['contributor', 'publicationDate', 'title', 'issue', 'page', 'link'];//todo ONLINE_MAGAZINE_ARTICLE_TYPE
                default:
                    throw new Error(`No hay caso "${spec.get('type')}" para "${spec.get('typeToString')}"... TODO`);;
            }
        })(this, this.get('type'));

        this.rule= rule||Citation.CATALOGING_RULE.APA6;
        while (i < items.length) {
            res= getString[items[i]].call(this);
            if(res) ret.push(res)
            i++;
        }
        return ret.join('');
    }

    /**
     * @this Citation
     * @returns {string}
     */
    function getListItem(rule)
    {
        let ret= [], t;
        ret.push(this.get('title'));
        if(t= this.get('subtitle'))  ret.push(': '+t);
        return ret.join('');
    }
    /**
     * @this Citation
     * @returns {string}
     */
    function getAbbrType()
    {
        let s= this.get('cType');
        return s.match(/([^_|^-]+)/g).map(function(i){
            return Array.from(i.toUpperCase())[0];
        }).join('');

    }

    /**
     *
     * @param {JSON} d Json data
     * @constructor
     */
    function Citation(d)
    {
        jas.call(this, d);
        this.getJsonData= function (attr){
            let jd= this.get('jsondata');
            return jd.hasOwnProperty(attr) ? jd[attr] : null
        }
        //functions para los listados
        this.getReference=getReference.bind(this);
        this.getListItem=getListItem.bind(this);
        this.getAbbrType=getAbbrType.bind(this);
        this.toString= function (){
            return this.getListItem();
        }
    }


    Citation.CATALOGING_RULE= {
        ND:0,
        APA6: 6,
        APA7: 7,
        // /**
        //  * @param {int} rule
        //  * @returns {number}
        //  */
        // ruleExists:function (rule)
        // {
        //     return this[rule]||6
        // }
    };
    /**
     * Relationship with Citation.CATALOGING_RULE
     * @type {{0: number, 6: number, 7: number}}
     */
    Citation.AUTHORS_LENGTH={
        0:1,
        6:7,
        7:20
    };
    /**
     * Las citas en el texto pueden ser de dos tipos.
     * Las referencias a estas citas van al final del texto.
     * @type {{PARENTHETICAL: number, NARRATIVE: number}}
     */
    Citation.CITATION_FORMAT={
        /**
         * in parenthetical citations, the author name and publication date appear in parentheses.
         */
        PARENTHETICAL:0,
        /**
         * In narrative citations, the author name is incorporated into the text as part of the sentence and the year follows in parentheses.
         */
        NARRATIVE:1,
    };
    jam.Factory.addSpec('citation', Citation)

}(JsonApiManager, JsonApiSpec));