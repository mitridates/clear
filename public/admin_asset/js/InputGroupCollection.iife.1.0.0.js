/**
 * Immediately-Invoked Function Expression (IIFE).
 * @function
 * @param {object} window - Global window object.
 */
(function(window, undefined) {
    'use strict';

    /**
     * Serialize This collection to json
     * @param {HTMLElement} elm
     * @returns {object}
     */
    function inputGroupToJson(elm)
    {
        let json={};

        elm.querySelectorAll('input, select').forEach(/** HTMLElement*/el =>
        {
            if(el.tagName.toLowerCase()==='select')
            {
                json[el.dataset.name]= el.options[el.selectedIndex].value||null;
            }else{
                json[el.dataset.name]= el.value||null;
            }
        });
        return json;
    }

    /**
     * Serialize inputs to json
     * @param {HTMLElement} container container
     */
    function serializeCollection(container)
    {
        let out= [];
        Array.from(container.children).forEach((elm , i)=>{
            out.push(inputGroupToJson(elm))
        });
        return JSON.stringify(out);
    }

    /**
     * Default function to add a text string to the control bar.
     * @param {Object} json Serialized form
     * @param {HTMLElement} wrapper
     */
    function jsonToNavbar(json, wrapper)
    {
        let
            out=[],
            navTxt= wrapper.querySelector('.fic-navbar_txt')
        ;

        for(let i in json)
        {
            if(json[i] && i!=='__type'){
                out.push(json[i]);
            }
        }
        navTxt.title= navTxt.innerHTML= out.length? out.join('. ') : '';
    }

    /**
     * Serialize This collection to json
     * @param {HTMLElement} elm
     * @returns {object}
     */
    function collectionToJson(elm)
    {
        let json={};

        elm.querySelectorAll('input, select').forEach(/**@param HTMLElement el*/el =>
        {
            if(el.tagName.toLowerCase()==='select')
            {
                json[el.dataset.name]= el.options[el.selectedIndex].value||null;
            }else{
                json[el.dataset.name]= el.value||null;
            }
        });
        return json;
    }

    /**
     * Serialize inputs to json
     * @param {Object} config
     */
    function updateHiddenInput(config)
    {
        let
            input =config.wrapper.querySelector('input[name="'+config.prefix+'"]'),
            container= config.container
        ;
        if(container.children.length===0){
            input.parentNode.removeChild(input)
        }else{
            input.value= (config.container.children.length)? serializeCollection(config.container) : '';
        }
    }

    /**
     * Add events to input group fields
     * @param {HTMLElement} elm Current node in container
     * @param {Function|undefined} callback Create a navbar string
     * @param {Object} config
     */
    function addFieldsEvents(elm, callback, config)
    {
        elm.querySelectorAll('input, select').forEach(/**@param {HTMLInputElement|HTMLSelectElement} el*/el => {
            let evType= el.tagName.toLowerCase()==='select' ? 'change': 'input';
            el.id = `${el.dataset.name}::${(Math.random() + 1).toString(36).substring(7)}`;
            el.addEventListener(evType, (ev)=>{

                if(callback){
                    callback(collectionToJson(elm), elm);
                }else{
                    jsonToNavbar(collectionToJson(elm), elm);
                }
                if(['json', 'jform'].indexOf(config.mode)!==-1) updateHiddenInput(config);
            });
        })
    }

    /**
     * Rename all input/select to create an input array with different index for each node in container.
     * @param {Object} config
     */
    function addInputNames(config)
    {
        Array.from(config.container.children).forEach((child , i)=>{
            child.querySelectorAll('input, select').forEach(elm => {
                elm.name = `${config.prefix}[${i}][${elm.dataset.name}]`;
                // elm.id = `${elm.name}::${(Math.random() + 1).toString(36).substring(7)}`;

            })
        })
    }

    /**
     * Parse container to show/hide arrows used to reorder the forms
     * @param {HTMLElement} container
     */
    function showArrows(container)
    {
        let length = container.children.length;

        Array.from(container.children).forEach((child , i)=>{
            let show= (function (l, i) {
                if(l===1){
                    return null;
                }else{
                    if(i===0){
                        return 'down';
                    }else if(i>0 && (i+1)<l){
                        return 'both';
                    }else{
                        return 'up';
                    }
                }
            })(length, i);

            child.querySelectorAll('[data-arrow]').forEach(el => {

                if(show==='both'){
                    el.style.display='inline';
                    return;
                }

                if(el.dataset.arrow!==show){
                    el.style.display='none';
                }else{
                    el.style.display='inline';
                }
            });
        });
    }

    /**
     * Add events to icons in control bar
     * @param {HTMLElement} elm
     * @param {Object} config FormCollection config
     */
    function addButtonEvents(elm, config)
    {
        let
            remover= function(ev)
            {
                let container= config.container;
                container.removeChild(this.elm);
                showArrows(container);
                if(['json', 'jform'].indexOf(config.mode)!==-1){
                    updateHiddenInput(config)
                }else{
                    addInputNames(config);
                }
            },
            toggler= elm.querySelector('.fic-navbar_toggle'),
            trash= elm.querySelector('.fic-navbar_trash'),
            up= elm.querySelector('.fic-navbar_up'),
            down= elm.querySelector('.fic-navbar_down'),
            txt= elm.querySelector('.fic-navbar_txt'),
            form= elm.querySelector('.fic-group'),
            rotate= 'fa-rotate-180';
        ;

        trash.addEventListener('click', remover.bind({elm: elm}));

        toggler.addEventListener('click', (ev)=>{

            let angle= ev.currentTarget.querySelector('.fa-angle-down');

            if(angle.classList.contains(rotate)){
                angle.classList.remove(rotate)
                form.style.display='inline'
            }else{
                angle.classList.add(rotate)
                form.style.display='none'
            }
        });

        txt.addEventListener('click', (ev)=>{
            toggler.click();
        });

        up.addEventListener('click', (ev)=>{
            moveElment(elm, false, config);
        });
        down.addEventListener('click', (ev)=>{
            moveElment(elm, true, config);
        });
    }

    function moveElment(elm, to, config)
    {
        var arr = [].slice.call(elm.parentNode.children);
        var i= arr.indexOf(elm);
        var ii= to ? i+1 : i-1;
        if(ii>i){
            elm.parentNode.insertBefore(arr[ii], elm);
        }else{
            elm.parentNode.insertBefore(elm,arr[ii]);
        }
        showArrows(elm.parentNode);

        if(['json', 'jform'].indexOf(config.mode)!==-1){
            updateHiddenInput(config)
        }else{
            addInputNames(config);
        }
    }

    /**
     * Clone template and add a new node to container
     * @param {Object} config
     * @returns {HTMLElement}
     */
    function createNodeFromTemplate(config, btn)
    {
        let
            tplNode=  btn.template.content.firstElementChild.cloneNode(true),
            tplNavbar=  config.navbar
        ;
        if(!tplNavbar){//navbar in form template?
            if(!btn.template.content.querySelector('.fic-navbar')){
                throw Error(`navbar not found for ${config.prefix}.`)
            }
        }else{
            tplNavbar= config.navbar.content.firstElementChild.cloneNode(true)
            tplNode.insertBefore(tplNavbar, tplNode.firstChild);//add control bar to node
        }
        return tplNode;
    }

    /**
     * Create a new input group from template
     * @param {Object} btn Current button config
     * @param {Object} config
     */
    function createInputGroup(btn, config)
    {
        let newGroup= createNodeFromTemplate(config, btn);
        config.container.appendChild(newGroup)
        addButtonEvents(newGroup, config);
        showArrows(config.container);
        if(['json', 'jform'].indexOf(config.mode)!==-1) {
            let input =config.wrapper.querySelector('input[name="'+config.prefix+'"]');
            if(!input){
                input = document.createElement('input');
                input.name = config.prefix;
                // elm.id = `${elm.name}::${(Math.random() + 1).toString(36).substring(7)}`;

                input.style.width= '100%';
                input.style.display= config.debug? 'inline' : 'none';
                config.wrapper.appendChild(input);
            }
        }else{
            //todo esta función se ha quedado colgando??? por???
            addInputNames(config);
        }

        addFieldsEvents(newGroup, btn.jsonToNavbar, config);

        return newGroup;
    }

    function getConfig(sel, params)
    {
        let wrapper= document.querySelector(sel);

        if(!wrapper) throw new Error(`Wrapper not found in : ${sel}`);

        let
            config= {
                wrapper: wrapper,
                buttons:[],
                navbar: null,
                template:null,//common template for buttons
                mode:'prefix',//<input name="prefix[0][name]" value="fieldValue">
                //mode:'json'// <input type="hidden" name="myPrefix" value="[JsonSerializedFields]">
                //mode:jform,//<input type="hidden" name="myFormName[myPrefix]" value="[JsonSerializedFields]">
            },
            data = Object.assign(Object.assign({}, wrapper.dataset), params||{}),
            buttons, container
        ;


        if(!data.prefix) throw new Error(`Prefix is null for ${sel}`);

        for (let key in data)
        {
            if(!['navbar', 'container', 'buttons', 'template'].includes(key)) config[key]= data[key];;

        }

        if(config.mode==='jform')
        {
            let form= wrapper.closest('form');

            if(!form) throw new Error(`Form not found for ${sel}`);
            config.prefix= `${form.name}[${config.prefix}]`
        }


        //navbar in dataset, find navbar in document
        if(data.hasOwnProperty('navbar'))
        {
            config.navbar= document.querySelector(data.navbar);
        }else{
            //config.navbar= wrapper.querySelector('.fic-navbar');
            config.navbar= document.querySelector('template.fic-navbar');//fallback
        }

        config.container= ((sel, fallback)=>{
            let c = wrapper.querySelector(sel||fallback) ;
            if(c) return c;
            c= document.createElement('div')
            wrapper.insertBefore(c, wrapper.firstChild);//add container dynamically
            return c;
        })(data.container, ".fic-container");

        if(data.hasOwnProperty('buttons'))
        {
            buttons=  (typeof data.buttons==="string")? wrapper.querySelectorAll(data.buttons) : data.buttons;//array
        }else{
            buttons= wrapper.querySelectorAll('.fic-button');
        }

        //template in dataset, find template in document
        if(data.hasOwnProperty('template'))
        {
            config.template= document.querySelector(data.template);
        }
        if(!buttons.length) throw new Error(`No buttons for ${sel}`);

        buttons.forEach((b)=>
        {
            let bttn= {
                node: (b.nodeType)? b : config.wrapper.querySelector(b.node),
                __type: (b.nodeType)? b.dataset.__type : b.__type,
                template: ((nt, str)=>
                {
                    let tpl;

                    if(nt && b.dataset.template)
                    {
                        tpl= document.querySelector(b.dataset.template)//template in button dataset
                    }else if(!nt && b.hasOwnProperty(str))
                    {
                        tpl= document.querySelector(b[str]);//template in json property
                    }else if(config.template)
                    {
                        tpl= config.template;//template in json/wrapper dataset
                    }

                    if(!tpl)throw new Error(`No template for button in ${sel}`);

                    //navbar in template?
                    if(!config.navbar)
                    {
                        let cloned= tpl.content.firstElementChild.cloneNode(true);
                        if(!cloned.querySelector('.fic-navbar')){
                            throw new Error(`Navbar not found for button in ${sel}`);
                        }
                        cloned=null;
                    };
                    return tpl;
                })(b.nodeType, 'template'),

                jsonToNavbar: ((nt, str)=>{
                    if(!nt && b.hasOwnProperty(str)) return b[str];
                    if(data.hasOwnProperty(str)) return data[str];
                    return jsonToNavbar;
                })(b.nodeType, 'jsonToNavbar')
            };

            if(buttons.length>1 && !bttn.__type) console.warn(`More than one button without __type ${sel}. The serializer will search for this property!`);

            config.buttons.push(bttn);
        })
        if(!config.buttons.length) throw new Error(`No buttons for ${sel}`);

        return config;
    }

    /**
     * @constructor
     * @name InputGroupCollection
     * @param {string} w Wrapper selector ID|class
     * @param {Object|undefined} params
     */
    function InputGroupCollection(w, params) {

        let config = this.config= getConfig(w, params);
        config.buttons.forEach((b)=>{
            b.node.addEventListener('click', (ev) => {
                let newForm= createInputGroup(b, config)
                Array.from(config.container.children).forEach((elm , i)=>{
                    if(elm!==newForm){
                        if(elm.querySelector('.fic-group').style.display!=='none') elm.querySelector('.fic-navbar_toggle').click();
                    }
                });
            });
        });
    }


    /**
     * Serialize form to json
     * @returns {Object}
     */
    InputGroupCollection.prototype.serialize= function (){
        return serializeCollection(this.config.container);
    }

    /**
     * Populate container with some data
     * @param {Object[]} arr
     */
    InputGroupCollection.prototype.populate= function (arr){

        arr.forEach((data , i)=>{
            if(!data || Array.isArray(data)) return;

            let button=((buttons)=>{
                let ret= buttons[0];
                if(buttons.length>1)
                {
                    for(let i in buttons)
                    {
                        //different templates (forms) por each button with specific data for each form
                        if(buttons[i].hasOwnProperty('__type') && data.hasOwnProperty('__type'))
                        {
                            if(buttons[i]['__type']===data['__type']){
                                return buttons[i];
                            }
                        }
                    }
                }
                return ret;
            })(this.config.buttons);

            if(!button){
                console.error(`No config button for ${this.config.prefix}`)
                return;
            }

            let newForm= createInputGroup(button, this.config);//create HTMLElement from template

            newForm.querySelector('.fic-navbar_toggle').click();//Toggle (close)

            newForm.querySelectorAll('input, select').forEach(/**@param {HTMLElement} el*/el => {//set form values
                let name= el.dataset.name
                el.id = `${el.dataset.name}::${(Math.random() + 1).toString(36).substring(7)}`;

                if(name in data) el.value= data[name];
            });
            button.jsonToNavbar(data, newForm);//Values to string in buttons bar
        });

        if(['json', 'jform'].indexOf(this.config.mode)!==-1) updateHiddenInput(this.config)

    }

    /**
     * Used to copy/paste in edit/new pages
     */
    InputGroupCollection.prototype.copyToLocalStorage= function ()
    {
        localStorage.setItem(`${this.config.prefix}Storage`, this.serialize());
    }

    /**
     * Used to copy/paste in edit/new pages
     */
    InputGroupCollection.prototype.pasteFromLocalStorage= function ()
    {
        this.populate(JSON.parse(localStorage.getItem(`${this.config.prefix}Storage`)||[]));
    }

    window.InputGroupCollection= InputGroupCollection;
}(window, undefined));