(function(window) {
    "use strict"
    let cache = new Map()


    function JTooltip(selector){

        let elm=(
            function(s){
                if(NodeList.prototype.isPrototypeOf(s)) return s;
                else if (s.nodeType)return s;
                else if(typeof s === 'string' || s instanceof String) return document.querySelectorAll(s)
                else return null
            }
        )(selector)


        if(NodeList.prototype.isPrototypeOf(elm)){
            elm.forEach(function(el) {
                new JTooltip(el)
            });
            return;
        }

        if(!elm.nodeType) return;

        let tt= createTooltip(elm);

        setPosition(elm, tt);

        elm.addEventListener("mouseleave", hideListener.bind({tt:tt, elm:elm}));
        elm.addEventListener("mouseenter", showListener.bind({tt:tt, elm:elm}));
        window.addEventListener("resize", possListener.bind({elm: elm, tt: tt}));

    }

    function createTooltip(elm)
    {
        let
            tit= elm.getAttribute("title"),
            tt =  document.createElement("div"),
            rand= randId();
        ;

        tt.style.visibility= "hidden";
        elm.dataset.jtRandid=rand;
        tt.id=rand;
        if(!tit || tit === "") tit= 'Default title';
        elm.removeAttribute("title");
        tt.classList.add('jtooltip')
        tt.innerHTML = tit;
        document.body.appendChild(tt);
        return tt;
    }


    /**
     *
     * @param {HTMLElement} elm
     * @param {HTMLElement} tt
     */
    function setPosition(elm, tt)
    {
        let pos_left, pos_top,
            pos= getOffset(elm);

        pos_left = pos.left + (elm.offsetWidth / 2) - (tt.offsetWidth / 2);
        pos_top  = pos.top - tt.offsetHeight - 10;

        if( pos_left < 0 )
        {
            pos_left = pos.left + elm.offsetWidth / 2 - 20;
            tt.classList.add("left");
        }
        else
            tt.classList.remove("left");

        if( pos_left + tt.offsetWidth > window.innerWidth )
        {
            pos_left = pos.left - tt.offsetWidth + elm.offsetWidth / 2 + 20;
            tt.classList.add("right");
        }
        else tt.classList.remove("right");

        if( pos_top < 0 )
        {
            pos_top  = pos.top + elm.offsetHeight + 10;
            tt.classList.add("top");
        }
        else tt.classList.remove("top");

        tt.style.left = pos_left + "px";
        tt.style.top = pos_top + "px";

        setMaxWidth(tt);
    }

    function setMaxWidth(tt){
        // set width of tooltip to half of window width
        if(window.innerWidth < tt.offsetWidth * 1.5)
            tt.style.maxWidth = window.innerWidth / 2+'px';
        else
            tt.style.maxWidth = 340+'px';
    }

    function getOffset(elm) {
        let rect = elm.getBoundingClientRect();

        return {
            left: rect.left + window.scrollX,
            top: rect.top + window.scrollY
        };
        //let offsetLeft = 0, offsetTop = 0;
        // do {
        //   if ( !isNaN( elem.offsetLeft ) )
        //   {
        //     offsetLeft += elem.offsetLeft;
        //     offsetTop += elem.offsetTop;
        //   }
        // } while( elem = elem.offsetParent );
        // return {left: offsetLeft, top: offsetTop};
    }


    /**
     *
     * @this {tt:HTMLElement, elm: HTMLElement}
     */
    function possListener(ev){
        setPosition(this.elm, this.tt)
    }
    /**
     *
     * @this {tt:HTMLElement, elm: HTMLElement}
     */
    function hideListener(ev){
        this.tt.style.visibility  = 'hidden';
        //  this.tt.style.display  = 'none';

    }
    /**
     *
     * @this {tt:HTMLElement, elm: HTMLElement}
     */
    function showListener(ev){

        setPosition(this.elm, this.tt);
        //this.tt.style.display  = 'inline';
        this.tt.style.visibility  = 'visible';
    }

    function randId (){
        let s;
        for (s=''; s.length < 4; s += 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.charAt(Math.random()*62|0));
        return s;
    }


    JTooltip.remove= function (elm){
        let tt =  document.getElementById(elm.dataset.jtRandid);
        if(!tt) return;
        elm.setAttribute("title", any.innerHTML );
        elm.removeEventListener("mouseleave", hideListener.bind({tt:tt, elm:elm}));
        elm.removeEventListener("mouseenter", showListener.bind({tt:tt, elm:elm}));
        window.removeEventListener("resize", possListener.bind({tt: tt, elm: elm}));
        tt.parentNode.removeChild(tt)
        elm.removeAttribute('jt-randid');
    }

    window.JTooltip = JTooltip
})(window);
