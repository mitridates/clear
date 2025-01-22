(function(window){
    let wrapper=undefined

    /**
     * Get wrapper (create one if not exists)
     * @returns {HTMLElement}
     */
    function getWrapper(){
        wrapper= document.querySelector('.jtoast_wrapper')
        if(wrapper == null){
            wrapper = document.createElement("div");
            wrapper.classList.add("jtoast_wrapper");
            document.querySelector('body').prepend(wrapper);
        }
        return wrapper
    }


    function JToast(title, description, type = 'info', userOptions = {})
    {
        if (!(this instanceof JToast)) return new JToast(title, description, type, userOptions)

        this.title = title;
        this.description = description;
        this.type = type==='error'? 'danger' : type;
        this.userOptions = userOptions;
        this.options= setOptions(userOptions);

        if(description===undefined && title===undefined){
            throw Error('JToast require title or/and description')
        }

        this.elms= createElements(this.type);       

        setPossition(this.options, this.userOptions);
        
        addTextContent.call(this)
        
        addOptionEvents.call(this);

    }

    /**
     *
     * @param uOpt
     * @return {{xAlign: string, waitOnHover: boolean, closeOnCrossClick: boolean, yAlign: string, autoCloseDuration: number, autoClose: boolean, closeOnClick: boolean}}
     */
    function setOptions(uOpt){
        let opt = {
            yAlign: 'top',
            xAlign: 'center',
            autoClose: true,
            waitOnHover:true,
            autoCloseDuration: 4000,
            closeOnCrossClick: true,
            closeOnClick: false,
            };
        
        if(uOpt.hasOwnProperty('yAlign')){
            let yAlignArr = ['top', 'bottom'];
            if(uOpt.yAlign !== opt.yAlign && yAlignArr.includes(uOpt.yAlign)){
                opt.yAlign = uOpt.yAlign;
            }
        }
        if(uOpt.hasOwnProperty('xAlign')){
            let xAlignArr = ['left', 'right', 'center'];
            if(uOpt.xAlign !== opt.xAlign && xAlignArr.includes(uOpt.xAlign)){
                opt.xAlign = uOpt.xAlign;
            }
        }
        if(uOpt.hasOwnProperty('autoClose')){
            if(uOpt.autoClose !== opt.autoClose && typeof uOpt.autoClose == "boolean"){
                opt.autoClose = uOpt.autoClose;
            }
        }
        if(uOpt.hasOwnProperty('autoCloseDuration')){
            if(uOpt.autoCloseDuration !== opt.autoCloseDuration){
                opt.autoCloseDuration = uOpt.autoCloseDuration;
            }
        }
        if(uOpt.hasOwnProperty('closeOnCrossClick')){
            if(uOpt.closeOnCrossClick !== opt.closeOnCrossClick && typeof uOpt.closeOnCrossClick == "boolean"){
                opt.closeOnCrossClick = uOpt.closeOnCrossClick;
            }
        }
        if(uOpt.hasOwnProperty('closeOnClick')){
            if(uOpt.closeOnClick !== opt.closeOnClick && typeof uOpt.closeOnClick == "boolean"){
                opt.closeOnClick = uOpt.closeOnClick;
            }
        }
        return opt;
    }

    /***
     *
     * @param type
     * @return {{container: HTMLDivElement, button: HTMLButtonElement, header: HTMLHeadingElement, description: HTMLDivElement, float: HTMLSpanElement}}
     */
    function createElements(type){
        let co,de, he, bt,float;
        
        co = document.createElement("div");
        co.classList.add("jtoast");

        bt = document.createElement("button");
        bt.classList.add('button', 'button-'+type);

        float= document.createElement('span');
        float.classList.add('float')
        float.appendChild(bt)

        he = document.createElement("h3");
        he.classList.add('header')
        he.appendChild(float)
        co.appendChild(he);

        de = document.createElement("div");
        de.classList.add("description", type);

        co.appendChild(de);
        
        getWrapper().appendChild(co);

        return {
            container: co,
            header: he,
            float: float,
            button: bt,
            description:de
        }
    }

    function setPossition(opt, uopt){

        if(wrapper.getAttribute('class').indexOf('x')===-1 || uopt.hasOwnProperty('xAlign'))
        {
            if(opt.xAlign === 'left') {
                wrapper.classList.add('xleft');
                wrapper.classList.remove('xright', 'xcenter');
            }else if(opt.xAlign === 'center') {
                wrapper.classList.add('xcenter');
                wrapper.classList.remove('xright', 'xleft');
            }
            else{
                wrapper.classList.add('xright');
                wrapper.classList.remove('xleft', 'xcenter');
            }
        }

        if(wrapper.getAttribute('class').indexOf('y')===-1  || uopt.hasOwnProperty('yAlign'))
        {
            if(opt.yAlign === 'bottom'){
                wrapper.classList.add('ybottom');
                wrapper.classList.remove('ytop');
            }
            else{
                wrapper.classList.add('ytop');
                wrapper.classList.remove('ybottom');
            }
        }
    }


    function addTextContent(){
        let 
            de = this.elms.description,
            he= this.elms.header
            ;
            if(this.title){
                he.insertAdjacentHTML( 'beforeend', this.title );
                he.classList.add('jtoast-'+this.type)  
            }else{
                de.classList.add('jtoast-'+this.type, 'bubble')  
                he.remove()
            }

            if(this.description){
                de.innerHTML = this.description;
                if(!this.title) de.prepend(this.elms.float)

            }else{
                he.classList.add('snack')
                de.remove()
            }
            
    }

    function addOptionEvents(){
        let
            timeout, timer_on,
            co = this.elms.container,
            button= this.elms.button,
            that = this,
            opt = this.options,
            startTimeOut= function(){
                timer_on=1
                timeout= setTimeout(() => {
                    removeToast.call(that);
                }, opt.autoCloseDuration);
            },
            stopTimeOut= function(){
                clearTimeout(timeout);
                timer_on = 0;
            }
            ;
        
            if(opt.autoClose){
                startTimeOut()
            }

        co.classList.add(opt.yAlign==='bottom'? 'frombottom': 'fromtop')
        
        if(opt.autoClose && opt.waitOnHover){
            co.addEventListener('mouseover', function(){
                stopTimeOut()
            })
            co.addEventListener('mouseout', function(){
                startTimeOut()
            })
            co.addEventListener('touchstart', function(){
                stopTimeOut()
            },{passive: true})
            co.addEventListener('touchend', function(){
                startTimeOut()
            },{passive: true})
        }



        if(!opt.closeOnCrossClick){           
            button.remove();
        }

        if(opt.closeOnClick){
            co.addEventListener('click', () => {
                removeToast.call(that);
            })                       
        }

        button.addEventListener("click", function(){
            removeToast.call(that);
        });
    }

    function removeToast(){
        // JToast.clear()//TODO eliminar instancia y eventos
        this.elms.container.remove()
    }

    JToast.bubble= function(description, type = 'info', userOptions = {}){
        return new JToast(null, description, type, userOptions)
    }
    JToast.snack= function(title, type = 'info', userOptions = {}){
        return new JToast(title, null, type, userOptions)
    }
    JToast.clear= function(){
        let w= getWrapper();
        while (w.firstChild){
            w.removeChild(w.lastChild);
        }
    }
window.JToast = JToast
})(window)