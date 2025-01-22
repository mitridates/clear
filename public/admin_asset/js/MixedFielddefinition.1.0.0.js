'use strict';
function languagePagination(wrapperID, formID) {

    let pager= Pagination(wrapperID, null, {
        withNavbar:false,
        /** @param {Response} response*/
        footCallback: (response)=>{
            return response['meta'].pending
        }
    })
        .on( 'jpager.show.response', function (jam, body)
        {//Add popover
            FieldDefinitionGetter(body.querySelectorAll("[data-fdget='popover']"), FielddefinitionFunctionCallback[0])
                .on('fdget.success.request', FielddefinitionFunctionCallback)
        })
        .on('jpager.show.response', function (jam, body)
        {
            let remove= function (el, spec, paginator)
            {
                el.addEventListener('click', function (ev)
                {
                    let elm= ev.currentTarget;
                    if (!JSimas.inOn(elm)) return
                    if(!confirm(elm.dataset.deleteMessage)) return
                    JXhrSubmit(null, {
                        snack: false,
                        method:'DELETE',
                        action: spec.getLink('DELETE')
                    }).on('jxhr.request.success', function (){
                        paginator.update()
                    }).submit();
                    el.removeEventListener(ev.type, this, false);
                })
            }

            body.querySelectorAll('.js-delete').forEach(function (el){
                /**
                 * @type {JsonApiSpec}
                 */
                let spec= jam.findById(el.getAttribute('data-id'))
                remove(el, spec, this)
            }, this)

        })
        .submit();

    JXhrSubmit(document.getElementById(formID))
        .on('jxhr.request.success', function (){
            pager.update()
        })
}