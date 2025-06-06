/**
 * Libera código de la página Edit para pasarlo a este script
 */
'use strict';
(function(window) {

    const MTO_SELECT=  document.querySelector('#choices-tab');

    /**
     * Función para iniciar los los nested select
     * @param {HTMLFormElement} form
     */
    function _fnRepopulate(form)
    {
        form.querySelectorAll('[data-repopulate]').forEach(function (elm){
            Repopulate(elm).populate();
        })
    }
    /**
     * Función para iniciar los los suggest
     * @param {HTMLFormElement} form
     */
    function _fnSuggest(form)
    {
        form.querySelectorAll('.jsuggest-select').forEach(function (elm){
            JSuggest(elm)
        })
    }

    //###### FUNCIONES PARA EL SELECTOR MANYTOONE ######
    /**
     * Mostramos los div ocultos con formularios ManyToOne mediante un <SELECT>.
     * @param {HTMLOptionElement} option
     * @param {boolean} display
     * @private
     */
    function _fnSowOption(option, display )
    {
        let
            name = option.dataset.name,
            loaded = option.dataset.loaded,
            container= document.querySelector(option.value);

        //Para no sobrecargar el inicio, cargamos los script y los datos si aún estan vacíos.

        if(display && loaded==='0')
        {
            if(!container.dataset.action) return;

            JXhrSubmit(null, {action: container.dataset.action, silent:true})
                .on('jxhr.request.success', function (response){
                container.innerHTML=response

                FieldDefinitionGetter(container.querySelectorAll("[data-fdget='popover']"), FIELDEFINITION_PATH_JSON, REQUEST_LOCALE)
                    .on('fdget.success.request', FielddefinitionFunctionCallback)

                let p= new Pagination('#js-'+name+'Table', null, {withNavbar:false})

                let mto= new ManyToOne(name, p)

                if(name==='discovery')//algunos formularios contienen scripts adicionales
                {
                    JSimas('input[name=discoveryorg]', mto.form.getForm()).toggleRadioByDataset();
                    JSimas('input[name=discoveryperson]', mto.form.getForm()).toggleRadioByDataset();
                    JSimas('input[name=discoveryorg]', mto.editForm.getForm()).toggleRadioByDataset();
                    JSimas('input[name=discoveryperson]', mto.editForm.getForm()).toggleRadioByDataset();
                }else
                if(name==='specie')
                {
                    JSimas('input[name=specieadd]', mto.editForm.getForm()).toggleRadioByDataset();
                    JSimas('input[name=specieadd]', mto.form.getForm()).toggleRadioByDataset();
                }else
                if(name==='widestmap')
                {
                    JSimas('input[name=widestmap-radio]', mto.editForm.getForm()).toggleRadioByDataset();
                    JSimas('input[name=widestmap-radio]', mto.form.getForm()).toggleRadioByDataset();
                }else
                if(name==='reference')
                {
                    JSimas('input[name=reference-radio]', mto.editForm.getForm()).toggleRadioByDataset();
                    JSimas('input[name=reference-radio]', mto.form.getForm()).toggleRadioByDataset();
                }else
                if(name==='type'){
                    mto.onUpdate(//Update cave types in home
                        /**
                         * @param {string} name
                         * @param {JsonApiManager} jam
                         * @param {HTMLElement} node - Tbody/div paginator container
                         * @param {ManyToOne} mto
                         */
                        function (name, jam, node, mto){
                        document.querySelectorAll('.js-rtype').forEach(function (el){
                            let
                                rmsg= el.dataset.tpl,
                                out= rmsg,
                                tpl= el.querySelector('.js-tpl'),
                                matches= rmsg.match(/\{.+?\}/g),
                                body= el.querySelector('.js-body'),
                                found, clon
                                body.innerHTML=''

                            jam.getParsed().forEach(function (spec){
                                clon= tpl.cloneNode()
                                clon.classList.remove('hidden')
                                out= rmsg
                                matches.forEach(function (w){
                                    found= spec.get(w.slice(1, -1))
                                    out= out.replace(w, found)
                                })
                                clon.innerHTML= out
                                body.appendChild(clon)
                            })


                        }, jam)
                    })
                }
                option.dataset.loaded='1'
            }).submit()

        }
        container.style.display= display? 'inline' : 'none'
    }

    function _fnSelectorChangeByName(n)
    {
        for(let i=0,l= MTO_SELECT.length; i<l; i++)
        {
            if(MTO_SELECT[i].dataset.name===n){
                _fnSelectorChangeListener(MTO_SELECT, i)
                return;
            }
        }
    }
    /**
     * Carga de formularios MayToOne mediante función o Listener.<br>
     * @param {Event|HTMLSelectElement} e
     * @param {number|null} i Option index a cargar
     * @private
     */
    function _fnSelectorChangeListener(e, i)
    {
        let select,option,val,hidden, parent, id, selected, name;

        select= (e instanceof Event)?  e.target : e;

        if(i && select.selectedIndex!==i) select.options[i].selected=true;

        selected= select.options[select.selectedIndex]
        val= selected.value
        name= selected.dataset.name

        if(val==='0') return//Opción desabilitada se resetea al cambiar de tab

        for(i = 1; i < select.length; i++)//loop <OPTION> para mostrar los ID
        {
            option= select[i]
            id = option.value

            if(!isNaN(id)) continue//Sólo actuamos sobre ID string

            if(!isNaN(val))//Si val es number actuamos sobre grupos de divs
            {
                if(val==='1')//Valor 1= mostrar todos
                {
                    _fnSowOption(option, true)
                }
                else if(val==='2') //Valor 2= mostrar manytoone con datos
                {
                    _fnSowOption(option, (!isNaN(option.dataset.count) && option.dataset.count>0))

                }else if(val==='3')//mostrar manytoone sin datos
                {
                    _fnSowOption(option, (isNaN(option.dataset.count) || !option.dataset.count))

                }else{
                    console.log('Opción sin codificar')
                    return;
                }
            }else{//Selecionado ID string

                if(val===id)//mueve el div arriba, apareciendo el primero en posteriores listados
                {
                    hidden= document.querySelector(val)
                    parent= hidden.parentNode
                    parent.insertBefore(hidden, parent.firstChild)
                }
                _fnSowOption(option, (val===id))
            }
        }

        //actualizamos el hash, mostramos el tab, ...
        window.location.hash = select.dataset.target+'@'+ name
        try{
            bootstrap.Tab.getOrCreateInstance(select).show()
        }catch (e) {}
        document.body.scroll(0,0)
    }

    function _fnLoadTabPartial(tab, pane)
    {
        if(!pane || !pane.dataset.action) return;

        JXhrSubmit(null, {action: pane.dataset.action, silent:true}).on('jxhr.request.success', function (response)
        {
            pane.innerHTML=response

            _fnBindPartial(pane.id)

            FieldDefinitionGetter(pane.querySelectorAll("[data-fdget='popover']"), FIELDEFINITION_PATH_JSON, REQUEST_LOCALE)
                .on('fdget.success.request', FielddefinitionFunctionCallback)

            tab.dataset.loaded='true'

        }).submit()

    }

    function _fnBindPartial(name)
    {
        let id=  '#cave' + (name === 'cave' ? '' : name[0].toUpperCase()+name.substring(1)) + 'Form',
            form= document.querySelector(id),
            deleteBttn, xhr;

            _fnSuggest(form)
            _fnRepopulate(form)

        if(name==='cave')//Map tiene su botón de borrado a parte
        {
            JXhrSubmit(form)
            return
        }

        JXhrSubmit(form)
            .on('jxhr.request.success', function ()
            {
                document.querySelector('.'+name+'-js-badge').classList.add('check-js-badge')
            });

        deleteBttn= form.querySelector('.js-xhrdelete')

        xhr= JXhrSubmit(deleteBttn)
            .setParameter('method', 'DELETE')
            // .setParameter('silent', true)
            .on('jxhr.request.success', function ()
            {
                JSimas(form).clear()
                document.querySelector('.'+name+'-js-badge').classList.remove('check-js-badge')
            })

        deleteBttn.addEventListener('click', function (ev){
            if (!JSimas.inOn(ev.currentTarget)) return
            xhr.submit()
        })


        //management events
        if(name==='management')
        {
            let
                clasifier= form.querySelector('[field_id="44"]'),
                category= form.querySelector('[field_id="45"]')
            ;
            JSimas('input[name=management-radio]', form).toggleRadioByDataset();

            if(category.options[category.selectedIndex].value==='') clasifier.disabled = true;
            category.addEventListener('change', function (e){
                let
                    el = e.target,
                    val= el.options[el.selectedIndex].value
                ;
                if(val==='') clasifier.value= ''
                clasifier.disabled= (val==='')
            })
        }
    }

    window.addEventListener('DOMContentLoaded', e =>
    {
        let toggle= document.querySelector('a[data-js-toggle]')

        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (elm)
        {
            if(!IS_MOBILE_DEVICE) new JTooltip(elm.parentNode)

            if(elm.id==='mapimages-tab')
            {
                elm.addEventListener('shown.bs.tab', function () {
                        if(!elm.dataset.loaded) {

                            let badge= elm.querySelector('.mapimages-js-badge')

                            Pagination('#js-imageTable', null, {withNavbar:false})
                                .on('jpager.show.response', function (jam){
                                    if(jam.length){
                                        badge.dataset.count=jam.length
                                    }else{
                                        delete badge.dataset.count
                                    }
                                })
                                .submit()
                            elm.dataset.loaded='true'
                        }
                    })
            }

            elm.addEventListener('shown.bs.tab', function ()
            {
                window.location.hash = elm.dataset.bsTarget
                elm.querySelector('.badge-js-title').style.display='inline';

                if(!elm.dataset.loaded){
                    _fnLoadTabPartial(elm, document.querySelector(elm.dataset.bsTarget))
                    document.body.scroll(0,0)
                }
            })
            elm.addEventListener('hidden.bs.tab', function ()
            {
                if(toggle.dataset.jsToggle==='1') elm.querySelector('.badge-js-title').style.display='none';
            })
        })

    //Listeners para el selector
    let optionIndex=2,
         hash, arr;

    MTO_SELECT.addEventListener('click', function (e) //prevent propagation to tab
    {
        e.preventDefault()
        e.stopPropagation();
    })

    MTO_SELECT.addEventListener('change', _fnSelectorChangeListener)

    //reset selector on hide
    MTO_SELECT.addEventListener('hide.bs.tab', function ()
    {
        MTO_SELECT.selectedIndex=0
    })

    //Show tab on page load
    if (location.hash)
    {
        hash= location.hash

        if(hash.indexOf('@')===-1)
        {
            try{
                bootstrap.Tab.getOrCreateInstance(document.querySelector('a[data-bs-target="'+location.hash+'"]')).show()
            }catch (e) {}

            if(location.hash===MTO_SELECT.dataset.target)
            {
                setTimeout(()=>_fnSelectorChangeListener(MTO_SELECT, optionIndex), 500)
            }

        }else{
            arr= hash.split('@')
            try{
                bootstrap.Tab.getOrCreateInstance(document.querySelector('a[data-bs-target="'+arr[0]+'"]')).show()
            }catch (e) {}
            setTimeout(()=>_fnSelectorChangeByName(arr[1]), 100)
        }
    }
        _fnBindPartial('cave')

        document.querySelector('a[data-js-toggle]').addEventListener('click', function ()
        {
            let
                tog=['fa-toggle-off','fa-toggle-on'],
                disp= ['none','inline'],
                cl= toggle.querySelector('i').classList,
                to = parseInt(toggle.dataset.jsToggle)
            ;
            cl.add(tog[to])
            cl.remove(tog[+!to])

            document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (elm)
            {
                if(elm.classList.contains('active')) return
                elm.querySelector('.badge-js-title').style.display=disp[to];
            })
            toggle.dataset.jsToggle= to?'0':'1'
        })

    });

    //El formulario principal ya viene en el request
    // Evitar que los dropdown se cierren
    // Normalmente no lo hace, pero suggest & dropdown(toggle) se llevan mal
    // document.querySelectorAll('.dropdown-menu').forEach(function (el){
    //     el.addEventListener('click', function (ev) {
    //         ev.stopPropagation()
    //     })
    // })
//###### FIN INICIALIZAR SCRIPTS ######


    window.selectorChangeByName= _fnSelectorChangeByName
})(window);
