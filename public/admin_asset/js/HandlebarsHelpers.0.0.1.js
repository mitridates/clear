(function(window,Handlebars) {
    'use strict';
    //{{var "greeting" "Hello World!"}} New handlebars var
    Handlebars.registerHelper('var',function(name, value){
        if(this[name]) console.warn(`Re-assign ${name} to ${value}`)
        this[name] = value;
    });


    //{{coalesce a b c d }} return the first not false||null
    // Handlebars.registerHelper('coalesce', (...args) => args.find(i => !!i));
    Handlebars.registerHelper('coalesce', function() {
        for (let i = 0; i < arguments.length - 1; i++) { // - 1 because last should be handlebars options var
            if (arguments[i]) {
                return arguments[i];
            }
        }
        return null;
    });

    //{{run this.proto 'method' arg1, ...}}
    Handlebars.registerHelper('run', function(proto, method)
    {
        return proto[method].apply(null,Array.prototype.slice.call(arguments, 2,-1));
    });

    //{{#ifIsset a b}}{{a+b}}{{/ifIsset}}  Checkea valores normalmente cadenas...
    //no es un isset exacto
    Handlebars.registerHelper('ifIsset', function() {
        let
            val= false,
            opt= arguments[arguments.length - 1]
        ;
        for (let i = 0; i < arguments.length - 1; i++) { // - 1 because last should be handlebars options var
            if (arguments[i]) val= true
        }
        return val ? opt.fn(this) : opt.inverse(this);
    });

    Handlebars.registerHelper('split', function(text, char, index) {
        return text.indexOf(char) !== -1 ? text.split(char)[index] : text
    });

    Handlebars.registerHelper('countryCodeToLocale', function(code) {
        let locale = new Intl.DisplayNames([window.navigator.language],{type:'region'});
        return locale.of(code.toUpperCase())
    });

    Handlebars.registerHelper('textContent', function(html) {
        let div= document.createElement('div');
        div.innerHTML=html;
        return div.textContent;
    });

    Handlebars.registerHelper('join', function()
    {
        let separator= arguments[0];
        let toJoin= Array.prototype.slice.call(arguments, 1, -1)
        if(toJoin.length===1){//join array
            return toJoin[0].filter((e)=>e!=null && e!=='').join(separator)
        }else{//join strings
            return toJoin.filter((e)=>e!=null && e!=='').join(separator)
        }
    });

    //{{#ifNotEquals created updated}}{{updated}}{{/ifNotEquals}} compare strings
    Handlebars.registerHelper('ifNotEquals', function(arg1, arg2, options) {
        return !(arg1 === arg2) ? options.fn(this) : options.inverse(this);
    });
    //{{#ifEquals created updated}}{{updated}}{{/ifEquals}} compare strings
    Handlebars.registerHelper('ifEquals', function(arg1, arg2, options) {
        return (arg1 === arg2) ? options.fn(this) : options.inverse(this);
    });

    //{{#contains c array|string }}...{{/contains}}
    Handlebars.registerHelper('contains', function(needle, haystack, not) {
        if(!haystack) return false;
        let contains = haystack.indexOf(needle) > -1;
        if(typeof not !== 'undefined') return !contains;

    });
    //{{parseFloat n }}
    Handlebars.registerHelper('parseFloat', function(num) {
        return  num ? parseFloat(num) : '';
    });

    Handlebars.registerHelper('formatbytes', function(bytes,  decimals = 2) {
        if (!+bytes) return '0 Bytes'

        const k = 1024
        const dm = decimals < 0 ? 0 : decimals
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
        const i = Math.floor(Math.log(bytes) / Math.log(k))
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
    });

    // {{#times 10}}
    // <span> {{@first}} {{@index}} {{@last}}</span>
    // {{/times}}
    Handlebars.registerHelper('times', function(n, block) {
        var accum = '';
        for(var i = 0; i < n; ++i) {
            block.data.index = i;
            block.data.first = i === 0;
            block.data.last = i === (n - 1);
            accum += block.fn(this);
        }
        return accum;
    });

    function compare(operand_1, operator, operand_2){
        let operators = {                     //  {{#when <operand1> 'eq' <operand2>}}
            'eq': (l,r) => l === r,              //  {{/when}}
            'noteq': (l,r) => l !== r,
            'gt': (l,r) => (+l) > (+r),                        // {{#when var1 'eq' var2}}
            'gteq': (l,r) => ((+l) > (+r)) || (l === r),        //               eq
            'lt': (l,r) => (+l) < (+r),                        // {{else when var1 'gt' var2}}
            'lteq': (l,r) => ((+l) < (+r)) || (l === r),        //               gt
            'or': (l,r) => l || r,                             // {{else}}
            'and': (l,r) => l && r,                            //               lt
            '%': (l,r) => (l % r) === 0                        // {{/when}}
        }
        return operators[operator](operand_1,operand_2);
    }
    Handlebars.registerHelper('compare', function(operand_1, operator, operand_2) {
        return compare(operand_1, operator, operand_2)
    });

    Handlebars.registerHelper("when", (operand_1, operator, operand_2, options) => {
        let result = compare(operand_1, operator, operand_2);
        if(result) return options.fn(this);
        return options.inverse(this);
    });
}(window, Handlebars));