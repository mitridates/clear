(function(w) {
    'use strict';
    /**
     * Json error management
     * @param {XMLHttpRequest} xhr XMLHttpRequest
     * @constructor
     * @name JError
     */
    function JError(xhr) {
        if (!(this instanceof JError)) return new JError(xhr);
        this.errors= getDefaults();
        parse.call(this, xhr);
        return this;
    }

    JError.prototype.show= function ()
    {
        showGlobalErrors.call(this);
        showExceptionErrors.call(this);
        showErrorErrors.call(this);
        showFormErrors.call(this);
        return this;
    }

    /**
     *  loggers
     */
    JError.logger = (function() {
        let loggers= {},
            methods=  ['logGlobals', 'logErrors', 'logExceptions', 'logChildren', 'clearItem', 'clearContext'],
            def= {logger:null},
            ret= {
                get:function(name){return loggers[name];},
                count:()=> Object.keys(loggers).length,
                addLogger:function(name, l){
                    loggers[name]=Object.assign({logger:l}, def, l);
                }
        };
        methods.forEach((m) => {
            def[m]= function(){/**console.info('Method "'+m+'" not implemented in '+ this.logger);*/};
        });
        for(let key in def){
            ret[key]= function(){for(let l in loggers) loggers[l][key].apply({logger:l},arguments);};
        }
        return ret;
    }());

    let getDefaults = function (){
        return{
            formGlobal: [],
            exception: [],
            error:[],
            formChildren:{}
            }
        },
        notJson = function(xhr)
        {
            let w= window.open('', '_blank',  "resizable,scrollbars,width=600,height=300");
            w.document.write(xhr.responseText);
        },
        showExceptionErrors= function()
        {
            let i, arr=[], er= this.errors.exception;
            if(!Object.keys(er).length) return;
            if(JError.logger.count()) return  JError.logger.logExceptions(er);
            for (i in er) arr.push('-'+er[i].title);
            if(!arr.length) return;
            arr.unshift('¡¡Exceptions!!');
            alert(arr.join('\n'));

       },
        showErrorErrors= function()
        {
            let i, arr=[], er= this.errors.error;
            if(!Object.keys(er).length) return;
            if(JError.logger.count()) return  JError.logger['logErrors'](er);
            for (i in er) {arr.push('-'+er[i].title+': '+er[i].detail);}
            if(!arr.length) return;
            arr.unshift('¡¡Errors!!');
            alert(arr.join('\n'));
        },
        /**
         * Show global text errors
         */
        showGlobalErrors= function()
        {
            let i, arr=[], er= this.errors.formGlobal;
            if(!Object.keys(er).length) return

            if(JError.logger.count()) return  JError.logger['logGlobals'](er);
            for (i in er) {arr.push('-'+er[i].title+': '+er[i].detail);}
            if(!arr.length) return;
            arr.unshift('¡¡Global Errors!!');
            alert(arr.join('\n'));
        },

        /**
         * Show Form elements errors
         */
        showFormErrors= function()
        {
            if(!Object.keys(this.errors.formChildren).length) return;

            let children= this.errors.formChildren;

            for(let id in children)//[field_error, ...]
            {
                let child = children[id];
                JError.logger.logChildren(document.getElementById(id), child, id);
            }//end children loop

            // if(first) first.focus();
        };

    /**
     * Ocultar error icons, borders && popovers
     * @param {HTMLFormElement|null} context
     */
    JError.clear = function (context=null){
        let elms;

        if(!sessionStorage.getItem('hasPreviousErrors')) return;

        if(context && context.tagName.toUpperCase()==='FORM'){
            elms= context.elements;
        }else{
            context = context||document;
            elms= context.getElementsByTagName("input");
        }
        if(context) JError.logger['clearContext'](context);

        for (let i = 0; i < elms.length; i++) {
            JError.logger['clearItem'](elms[i]);
        }
        sessionStorage.removeItem('hasPreviousErrors')
        this.errors= getDefaults();
        return this;
    };

    function parse(xhr) {
        let
            resp= xhr.response,
            errors
        ;

        if(!resp){
            resp= xhr.responseText;
        }

        //XMLHttpRequest.responseType='json' return status & statusText. response is null
        if(!resp){
            this.errors.formGlobal.push({detail: `Error ${xhr.status} : ${xhr.statusText}`})
            return;
        }else{
            try {//Not JSON
                if(resp.hasOwnProperty('errors')){
                    errors= resp.errors;
                }else{
                    errors = JSON.parse(resp).errors; // parse or catch error
                }
            } catch(e) {
                notJson(xhr);
                return;
            }
        }

        sessionStorage.setItem('hasPreviousErrors', errors.length);

        errors.forEach(function (e){
            if(this.errors.hasOwnProperty(e.meta.type)){

                if(e.meta.type==='formChildren'){//agrupamos los errores por id
                    this.errors.formChildren.hasOwnProperty(e.meta.id)? this.errors.formChildren[e.meta.id].push(e) : this.errors.formChildren[e.meta.id]=[e];

                }else{
                    this.errors[e.meta.type].push(e);
                }
            }else{
                alert(e.meta.type+' No registrado como tipo de error. Ver consola');
                console.log(e);
            }

            // switch (e.meta.type){
            //     case 'formChildren':
            //         this.errors.formChildren.push(e);
            //         break;
            //     case 'formGlobal':
            //         this.errors.formGlobal.push(e);
            //         break;
            //     case 'error':
            //         this.errors.error.push(e);
            //         break;
            //     case 'exception':
            //         this.errors.exception.push(e);
            //         break;
            //     default:
            //         this.errors.formGlobal.push({detail: `Error ${e.title} : ${e.detail}`})
            //         break;
            // }
        },this);
    }
    w.JError = JError;
}(window));

JError.logger.addLogger('FormErrorDivLogger',{
    clearItem:function (){},
    logExceptions:function (){},
    /**
     * Para elementos sin popup. Requiere un campo class= form-error error-+id
     * @param {HTMLFormElement} elm
     * @param {Array} errors
     * @param {string} id
     */
    logChildren:function(elm, errors, id)
    {
        let div= document.getElementsByClassName('error-'+id)[0];
        if(!div) return;
        while (div.firstChild) {
            div.removeChild(div.lastChild);
        }
        errors.forEach(e=>{
            let d= document.createElement('div');
            d.classList.add("color-danger");
            d.innerHTML= e.detail;
            div.appendChild(d);
        });

    },
    clearContext:function (context){
        if(!context) return;
        let list = document.getElementsByClassName('form-error')
        for (var i = 0; i < list.length; i++) {
            while (list[i].firstChild) {
                list[i].removeChild(list[i].lastChild);
            }
        }
    },
    // logExceptions: function (){
    //     console.log(this, arguments)
    //     let w= window.open(
    //         '',
    //         '_blank',
    //         "resizable,scrollbars,width=600,height=300"
    //     );
    //     w.document.write(xhr.responseText);
    // }
});

JError.logger.addLogger('ColorErrorLogger',{
    logExceptions:function (){},
    clearContext:function (){},
    /**
     * @param {HTMLFormElement} elm
     * @param {array} errors
     * @param {string} id
     */
    logChildren:function(elm, errors, id)
        {
            let form= document.getElementById(errors[0]['meta']['form']['id']);
            // console.log(form, elm.form )
            let suggest= elm.dataset.jsuggest && JSuggest!=="undefined"? JSuggest.cache.getInstance(elm) : false;
            if(suggest){
                suggest.elms.falseInput.classList.add('border-danger');
            }else{
                elm.classList.add('border-danger');
            }
        },
        clearItem:function (elm){
            let suggest= elm.dataset.jsuggest && JSuggest!=="undefined"? JSuggest.cache.getInstance(elm) : false;
            if(suggest){
                suggest.elms.falseInput.classList.remove('border-danger');
            }else{
                elm.classList.remove('border-danger');
            }
        }
});
//
// JError.logger.addLogger('WindowOpenErrorLogger',(function () {
//         /**
//          * @return {WindowProxy}
//          * @private
//          */
//         function _open (){
//             return this._window= window.open(
//                 '',
//                 'WindowOpenExceptionLogger',
//                 "resizable,scrollbars,width=600,height=300"
//             );
//         }
//         /**
//          * @param {HTMLFormElement} elm
//          * @param {array} errors
//          * @param {string} id
//          */
//     function logChildren(elm, errors, id)
//         {
//             let w= _open();
//
//             errors.forEach(e=>{
//                 let
//                     title= document.createElement('b'),
//                     detail= document.createElement('i'),
//                     separator= document.createElement('span'),
//                     container= document.createElement('div');
//                 ;
//                 separator.innerText= ': ';
//                 title.innerHTML=e.title;
//                 detail.innerHTML=e.detail;
//                 container.appendChild(title);
//                 container.appendChild(separator);
//                 container.appendChild(detail)
//                 w.document.body.appendChild(container);
//             });
//             w.focus();
//         }
//         /**
//          * @param {Array} errors
//          */
//     function logExceptions (errors){
//
//             errors.forEach(e=>{
//                 let w= window.open(
//                     '',
//                     'WindowOpenExceptionLogger',
//                     "resizable,scrollbars,width=600,height=300"
//                 );
//
//                 let title= document.createElement('h4');
//                 title.innerHTML=e.title;
//                 // w.document.write(title);
//                 w.document.body.appendChild(title);
//             });
//
//         }
//     return {
//         logExceptions: logExceptions,
//         logChildren: logChildren,
//     }
//
// })());

JError.logger.addLogger('FormErrorJToastLogger',{

    /**
     * Show global text errors
     */
    logGlobals:function(errors)
    {
        let e, i;
        for (i in errors) {
            e= errors[i]
            JToast.bubble(e.detail, 'danger', {autoClose: true})
        }
    },
    logExceptions: function (errors){
        let e, i;
        for (i in errors) {
            e= errors[i]
            JToast(e.title, e.content, 'danger', {autoClose: true})
        }
    },
    logErrors: function (errors){
            let e, i, type;
            for (i in errors) {
                e= errors[i]
                type= e.meta.level ?? 'danger'
                JToast(e.title, e.detail, type, {autoClose: true});
            }
    },
    logChildren: function (elm, errors, id)
    {
        let e, i, type;
        for (i in errors){
            e= errors[i];
            type= 'danger';
            JToast(e.title, e.detail, type, {autoClose: true});

        }
    }

});


JError.logger.addLogger('PopoverErrorLoger',{
    /**
     * Para elementos sin popup. Requiere un campo class= form-error error-+id
     * @param {HTMLFormElement} elm
     * @param {Array} errors
     * @param {string} id
     */
    logChildren:function(elm, errors, id)
    {
        let form, errElm, pop, out=[];
        form= document.getElementById(errors[0].meta.form.id);
        errElm= (form??document).querySelector('.js-error-id-'+id);
        pop= bootstrap.Popover.getOrCreateInstance(errElm);//popover
        console.log(bootstrap.Popover)
        if(!errElm || !pop) return;

        errors.forEach(e=>{
            out.push('<li>'+e.detail+'</li>')
        });
        out.unshift('<ul class=\'list-unstyled\'>');
        out.push('</ul>');
        pop.disable()
        errElm.dataset.bsContent= out.join('\n');
        errElm.dataset.html= 'true';
        errElm.style.display= 'inline';
        errElm.title=(function (el)
        {//Popover modify title
            if(el.dataset.bsTitle) return el.dataset.bsTitle;
            else if(el.title && el.title!=='') return el.title;
            else return '<span class="text-danger">Error</span>';
        })(errElm);
        pop= new bootstrap.Popover(errElm);//popover
        pop.show();
        setTimeout(function() {
            pop.hide();
        },3500);

    },
    clearContext:function (context){
        context.querySelectorAll('[data-toggle="popover"]').forEach(function (elm){
            let inst= bootstrap.Popover.getInstance(elm);
            if(inst) inst.hide();
        });

        context.querySelectorAll('.js-error').forEach(function (el)
        {//loop over icon danger, hide & remove input red border
            el.title=null;
            el.dataset.originalTitle=null;
            el.dataset.bsContent=null;
            el.style.display='none'; //hide icon
        });
    }
});