(function() {
    'use strict';
    /**
     * @param {string} selector
     * @param {number|null} n
     */
    function _fnUpdateCounter(selector, n){

        document.querySelectorAll(selector).forEach(function (elm){
            if(n>0){
                elm.setAttribute('data-count', n)
            }else{
                elm.removeAttribute('data-count')
            }
            if(elm.tagName.toLowerCase()==='option')
            {
                // el.setAttribute('data-count', n)
                if(!elm.getAttribute('data-rlabel')){
                    elm.setAttribute('data-rlabel', elm.label)
                }
                elm.label= elm.getAttribute('data-rlabel') + ((n!==0)? " ("+n+")" : '')
            }
        })

    }

    /**
     *
     * @param {string} name
     * @param {JsonApiManager} jam
     * @param {HTMLElement} node - Container element
     * @param {mto} mto
     * @private
     */
    function _fnOnShowMtoResponse(name, jam, node, mto){

        _fnUpdateCounter('.'+name+'-js-badge', jam.length)//Contador en el menú
        //Listeners para el borrado de filas
        node.querySelectorAll('.js-delete').forEach(function (el){
            /** @type {JsonApiSpec}*/
            let spec= jam.findById(el.getAttribute('data-id'))
            _fnAddOnClickDeleteListener(mto, el, spec)
        })
        //Listeners para la edición borrado de filas
        node.querySelectorAll('.js-edit').forEach(function (el){
            /**
             * @type {JsonApiSpec}
             */
            let spec= jam.findById(el.getAttribute('data-id'))
            _fnAddOnClickEditListener(mto, el, spec)
        })

    }


    /**
     * @param {mto} mto
     * @param {HTMLElement} el - Any clickable element
     * @param {JsonApiSpec} spec - JSON data
     * @private
     */
    function _fnAddOnClickEditListener(mto, el, spec){
        let listener= function (ev)
        {
            let
                row = el.closest('tr'),
                once= function (){
                    if(row) row.classList.remove('bg-warning','text-white')//desmarcamos la fila
                    mto.editForm.xhr.off('jxhr.request.success')
                    mto.editForm.getForm().querySelectorAll('input[type=file]').forEach(function (el){
                        el.value=''
                    });
                    mto.modal.removeEventListener('hidden.bs.modal', once);
                }
            ;
            //Puede ser paginación por div
            if(row) row.classList.add('bg-warning','text-white')//marcamos la fila

            mto.editForm.xhr.setParameter('action', spec.getLink('self'))
            bootstrap.Modal.getOrCreateInstance(mto.modal).show();
            mto.editForm.getForm().elements[0].focus();
            JSimas.specToForm(spec, mto.editForm.getForm())
            mto.editForm.xhr
                .once('jxhr.request.success', function (){
                    ev.target.removeEventListener(ev.type, listener, false);//quitamos este listener
                })
            mto.modal.addEventListener('hidden.bs.modal', once)
        };
        el.addEventListener('click', listener)
    }


    /**
     * @param {mto} mto
     * @param {HTMLElement} el - Any clickable element
     * @param {JsonApiSpec} spec - JSON data
     * @private
     */
    function _fnAddOnClickDeleteListener(mto, el, spec)
    {
        let listener= function (ev)
        {

            if (!JSimas.inOn(ev.currentTarget)) return

            JXhrSubmit(null, {
                snack: false,
                method:'DELETE',
                action: spec.getLink('DELETE')
            }).on('jxhr.request.success', function (response){
                mto.deleteEvents.forEach(function (cb){
                    cb(response)
                })
                ev.target.removeEventListener(ev.type, listener, false);
                mto.pager.update()
            }).submit();
        };
        el.addEventListener('click', listener.bind(mto))
    }

    /**
     * ManyToOne
     * @param {string} name
     * @param {Object|null} paginaton
     * @return {{form: MixedForm, pager: Pagination, editForm: MixedForm, dropdown: jQuery}}
     */
    function mto(name, paginaton){
        if(!document.querySelector('#'+name+'Form')){
            throw Error(`Form "#${name}Form" not found `)
        }
        this.name= name
        /**
         * Envío mediante XMLHttpRequest y un par de métodos de ayuda.
         * @type MixedForm
         */
        this.form= new MixedForm('#'+name+'Form')

        /**
         * Envío mediante XMLHttpRequest y un par de métodos de ayuda.
         * @type MixedForm
         */
        this.editForm= new MixedForm('#'+name+'EditForm')

        this.dropdown=(function (elm){
            return {
                elm: elm,
                getInstance: ()=> bootstrap.Dropdown.getInstance(elm),
                getWrapper: ()=> elm.parentNode,
                getJquery: ()=> bootstrap.Dropdown.getInstance(elm.parentNode)
            }
        })(document.querySelector('#'+name+'FormDropdown'))

        /**
         * @type {jQuery} Boostrap modal
         */
        this.modal= document.querySelector('#js-'+name+'-modal')
        /**
         * Permite paginar y mostrar los datos mediante handlebars
         * @type Pagination
         */
        this.pager= (paginaton instanceof Pagination)? paginaton :  Pagination('#js-'+name+'Table')

        this.deleteEvents=[]
        this.updateEvents=[]
        const self= this

        this.form.xhr.on('jxhr.request.success', function ()
        {
            //Ya no se cierra al utilizar suggest
            let checkbox= self.dropdown.getWrapper().querySelector('.js-checkbox')
            if(checkbox && !checkbox.checked)
            {
                self.dropdown.getInstance().toggle()
            }
            self.pager.update()
        })

        /**
         * Tasks on dropdown show
         */
        // this.dropdown.addEventListener('show.bs.dropdown',
        //     function(){
        //         JSimas(self.form.getForm()).clear({ notRegexType:/^(?:radio)$/i})
        //     }
        // )


        this.pager.on('jpager.show.response',
            /**
             * Tareas después de mostrar el contenido
             * @param {JsonApiManager} jam
             * @param {HTMLElement} node - Tbody/div container
             */
            function (jam, node){
            _fnOnShowMtoResponse( name, jam, node, self)
                self.updateEvents.forEach(function (cb){
                    cb(name, jam, node, self)
                })
        }).submit()

        this.editForm.xhr.on('jxhr.request.success', function (){
            // JSimas(self.editForm.getForm()).clear({notClass: 'js-checkbox'});
            bootstrap.Modal.getInstance(self.modal).hide();
            // $(self.modal).modal('hide')
            self.pager.update()
        })

        let  closeBttn= this.editForm.getForm().parentNode.querySelector('.js-close')
        if(closeBttn){
            let closeBttnEventListener= function (){
                bootstrap.Modal.getInstance(self.modal).hide();
            }
            closeBttn.addEventListener('click', closeBttnEventListener)
        }

    }

    mto.prototype={
        onDelete: function (callback){
            this.deleteEvents.push(callback)
        },
        /**
         *
         * @param callback  cb(name, jam, node, ManyToOne)
         */
        onUpdate:function (callback){
            this.updateEvents.push(callback)
        }
    }


    window.ManyToOne = mto

})();
