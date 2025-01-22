(function() {
    'use strict';

    /**
     * Init nested select
     * @param {HTMLFormElement} form
     */
    function _fnRepopulate(form)
    {
        form.querySelectorAll('[data-repopulate]').forEach(function (elm){
            Repopulate(elm).populate();
        })
    }
    /**
     * Init suggest
     * @param {HTMLFormElement} form
     */
    function _fnSuggest(form)
    {
        form.querySelectorAll('.jsuggest-select').forEach(function (elm){
            JSuggest(elm)
        })
    }

// ###### INIT MAP && ONETOONE SCRIPTS ######
    ['map', 'controller', 'details', 'comment', 'publicationtext', 'specialmapsheet'].forEach(function (name)
    {
        let id=  '#map' + (name === 'map' ? '' : name[0].toUpperCase()+name.substring(1)) + 'Form',
            form= document.querySelector(id),
            deleteBttn, xhr;

        _fnSuggest(form)
        _fnRepopulate(form)

        if(name==='map')//Map tiene su botón de borrado a parte
        {
            JXhrSubmit(form)
            JSimas('input[name=mapsource-radio]', form).toggleRadioByDataset()
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
            .on('jxhr.request.success', function ()
            {
                JSimas(form).clear()
                document.querySelector('.'+name+'-js-badge').classList.remove('check-js-badge')
            })

        deleteBttn.addEventListener('click', function (ev){
            if (!JSimas.inOn(ev.currentTarget)) return
            xhr.submit()
        })

        // Usabilidad
        if (name === 'controller') {
            JSimas('input[name=controller-radio]', form).toggleRadioByDataset();
        } else if (name === 'details') {
            JSimas('input[name=details-org-radio]', form).toggleRadioByDataset();
            JSimas('input[name=details-person-radio]', form).toggleRadioByDataset();
        }
    });

    ['image', 'surveyor','drafter', 'citation', 'furtherpc', 'furthergc', 'cave', 'link'].forEach(function (name)
    {
        let
            p= new Pagination('#js-'+name+'Table', null, {withNavbar:false}),
            mto=  new ManyToOne(name, p)
        ;

        if(name==='surveyor')
        {
            JSimas('input[name=surveyor-radio]', mto.form.getForm()).toggleRadioByDataset();
            JSimas('input[name=surveyor-radio]', mto.editForm.getForm()).toggleRadioByDataset();

        }else
            if(name==='drafter')
        {
            JSimas('input[name=drafter-radio]', mto.form.getForm()).toggleRadioByDataset();
            JSimas('input[name=drafter-radio]', mto.editForm.getForm()).toggleRadioByDataset();

        }
        else if(name==='image')
        {
            // _fnBsFileInput(mto.form.getForm())
            // _fnBsFileInput(mto.editForm.getForm())
        }
    })

    window.addEventListener('DOMContentLoaded', e => {

        let toggle= document.querySelector('a[data-js-toggle]')

        document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (elm)
        {
            if(!IS_MOBILE_DEVICE) new JTooltip(elm.parentNode)
            elm.addEventListener('show.bs.tab', function ()
            {
                document.body.scroll(0,0)
                window.location.hash = elm.dataset.bsTarget
                elm.querySelector('.badge-js-title').style.display='inline';
            })
            elm.addEventListener('hidden.bs.tab', function ()
            {
                if(toggle.dataset.jsToggle==='1') elm.querySelector('.badge-js-title').style.display='none';
            })
        })
        //Show tab on page load
        if (location.hash) {
            try{
            bootstrap.Tab.getOrCreateInstance(document.querySelector('a[data-bs-target="'+location.hash+'"]')).show()
            }catch (e) {}

        }
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

            document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(function (elm){
                if(elm.classList.contains('active')) return
                elm.querySelector('.badge-js-title').style.display=disp[to];
            })
            toggle.dataset.jsToggle= to?'0':'1'
        })
    });
})()

