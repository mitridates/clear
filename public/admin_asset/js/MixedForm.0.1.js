
(function(window, undefined) {
    'use strict';

   /**
     * @constructor
     */
    function MixedForm(ID){
        let form = document.querySelector(ID)
        this.id= ID
        this.xhr= new JXhrSubmit(form)
        form.querySelectorAll('.jsuggest-select').forEach(function (elm){
            JSuggest(elm)
        })
        form.querySelectorAll('[data-repopulate]').forEach(function (elm){
            Repopulate(elm).populate();
        })
    }

    MixedForm.prototype= {
        getForm: function (){
            return document.querySelector(this.id)
        }
    }
    window.MixedForm = MixedForm;
}(window));
