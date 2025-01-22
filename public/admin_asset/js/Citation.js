(function(w){
    
    /**
     * @param {HTMLFormElement} form
     * @returns {void}
     */
function bindPage(form)
{
    let fields={
            vol:form.querySelector('.js-page'),
            end: form.querySelector('.js-page-end'),
            range:form.querySelector('.js-page-range'),
            container:form.querySelector('.js-pend-container')

        };
        if(!fields.vol || !fields.end || !fields.range|| !fields.container) return;

        if(fields.end.value!=='') fields.range.checked=true;
        
        let listener= _pageListener.bind(fields);      

        listener();
	fields.range.addEventListener("change", listener);
        
}

    /**
     * Cambiar el tipo de cita desde un select
     * @param {HTMLFormElement} form
     */
     function bindToggleSelector(form)
    {
        let sel= document.getElementById('toggleCitationType');
        if(!sel) return;
        sel.addEventListener('change', function (){
            let path= sel.options[sel.selectedIndex].value;
            if(form){
                let inp= document.createElement('input');
                inp.name='toggle';
                inp.value=form.dataset.type;
                form.appendChild(inp);
                form.action=path;
                form.noValidate=true;
                form.submit();
            }else{
                window.open(path,'_self');
            }
        });
    }

function _pageListener(ev)
{
        this.container.style.display= (this.range.checked)? 'inline':'none';
        this.end.disabled= !this.range.checked;
}    
    
    
    /**
     * @param {HTMLFormElement} form
     * @returns {void}
     */
function bindVolume(form)
{
    let fields={
            vol:form.querySelector('.js-volume'),
            end: form.querySelector('.js-volume-end'),
            range:form.querySelector('.js-volume-range'),
            container:form.querySelector('.js-vend-container')

        };
        if(!fields.vol || !fields.end || !fields.range|| !fields.container) return;

        if(fields.end.value!=='') fields.range.checked=true;
        
        let listener= _volumeListener.bind(fields);      

        listener();
	fields.range.addEventListener("change", listener);
        
}

    function bindSubtitle(form)
    {
        let check=form.querySelector('.js-showsubtitle'),
            cont=form.querySelector('.js-subtitle-container'),
            txt
        ;
        if(!check || !cont) return;
        txt= cont.querySelector('input[type=text]')
        if(txt.value===''){
            check.checked=false;
            cont.style.display='none';
        }
        check.addEventListener('change', function(ev){
            let c= ev.currentTarget.checked
            cont.style.display= c ? 'inline': 'none';
            if(!c){
                txt.value='';
            }
        });
    }


    function _volumeListener(ev)
    {
        this.container.style.display= (this.range.checked)? 'inline':'none';
        this.end.disabled= !this.range.checked;
    }
    function bindComments(elms)
    {
        [].forEach.call(elms, function(container)
        {
            let elm={};
            ['button', 'textarea'].forEach(function (n, i){
                let l= n.charAt(0);
                elm[l]=container.querySelector(n)
                elm[l+'c']= container.querySelector('.js-'+n+'-container');
            });
            let listener= _commentListener.bind(elm);
            listener(false);
            elm.b.addEventListener('click', listener);
        });
    }
    function _commentListener(ev)
    {
        let hide= (ev===false)? this.t.value==="" : (this.b.dataset.isopen==='true');
        this.b.innerHTML= this.b.dataset[hide ? 'on':'off'];
        this.tc.style.display= hide? 'none':'inline';
        this.b.dataset.isopen= hide? 'false':'true';
        if(hide){
            this.t.value='';
            this.b.blur();
        }else{
            if(ev) this.t.focus();
        }
    }


    /**
     * @param {HTMLElement|boolean} container
     * @returns {void}
     */
function bindDate(container){
    if(!container) return;
    	let todayElm= container.querySelector('.js-today'),
            todayEv= function(ev) {
                let dateObj = new Date(),
                    y= dateObj.getUTCFullYear(),
                    m= dateObj.getUTCMonth() + 1
                ;

                    this.year.value= y;
                    this.month.disabled=false;
                    this.month.selectedIndex= m;
                    this.day.disabled= false;
                    fillDaysOptions(this.day, daysInMonth(y,m), dateObj.getUTCDate());
                },
            fields={
            year:container.querySelector('.js-year'),
            month: container.querySelector('.js-month'),
            day:container.querySelector('.js-day')
        };

    if(!fields.year) return;
        
	let listener= _dateListener.bind(fields);
	listener(false);
	fields.month.addEventListener("change", listener);
    fields.year.addEventListener("change", listener);


    if(todayElm){
        let todayListener= todayEv.bind(fields);
        todayElm.addEventListener('click', todayListener);
    }
}

function daysInMonth (y, m){
    return new Date(y, m, 0).getDate();
}

    /**
     * @param {HTMLSelectElement} dayElm
     * @param {number} numDays
     * @param {number|boolean} d
     */
    function fillDaysOptions(dayElm, numDays, d)
{
    let opt;
    for (let i = 1; i <= numDays; i++) {
        opt = document.createElement('option');
        opt.title = opt.text = opt.value = i;
        if (d && i === d) opt.selected = true;
        dayElm.add(opt);
    }

}

function _dateListener(ev)
	{
       let
            m= this.month.options[this.month.selectedIndex].value,
            y= this.year.value,
            d= this.day.options[this.day.selectedIndex].value,

            opt ;

        if(y===''){
            this.day.disabled=true;
            this.day.length=1;
            this.month.disabled=true;
            this.month.selectedIndex='';
            return;
        }else{
            this.month.disabled=false
            if(ev) this.month.focus();
        }

        if (m === '' || isNaN(m) || y === '') {
            this.day.length = 1;
            this.day.disabled = true;

        } else {
            if (!ev && this.year.value && m && this.day.options.length >= 29 && d) return
            this.day.length = 1;
            this.day.disabled = false;
            fillDaysOptions(this.day, daysInMonth(y, m), d)
        }
    }

    /**
     *
     * @param {string|null} formID
     * @constructor
     */
    function Citation (formID)
    {
        if(typeof formID==='string'){
            let form= document.getElementById(formID);
            bindDate(form.querySelector('.js-publication-date'));
            bindDate(form.querySelector('.js-access-date'));
            bindComments(form.querySelectorAll('.js-comment-container'));
            bindSubtitle(form);
            bindVolume(form);
            bindPage(form);
            bindToggleSelector(form);
        }else{
            bindToggleSelector(null);
        }

    }
    w.Citation= Citation;
    
})(window);
