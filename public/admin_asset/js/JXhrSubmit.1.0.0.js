(
    /**
     * @requires EventEmitter
     * @requires JError
     */
    function(window, EventEmitter, JError) {
    'use strict';
    let
        pendingRequest= (()=>{
            let pending={},
                /**
                 * @param {XMLHttpRequest} req
                 */
                abort=(req)=>{
                    try {req.abort(); } catch(e){}
                }
                ;

            return {
                add:(url,req)=>{
                    pending[url]=req;
                },
                remove:(url)=>{
                    if(pending.hasOwnProperty(url)){
                        abort(pending[url]);
                        delete pending[url];
                    }
                }
            }
        })(),
        formatBytes= function(a,b=2){if(!+a)return"0 Bytes";const c=0>b?0:b,d=Math.floor(Math.log(a)/Math.log(1024));return`${parseFloat((a/Math.pow(1024,d)).toFixed(c))} ${["Bytes","KB","MB","GB","TB","PB","EB","ZB","YB"][d]}`},
        normalizeSize=function (size){
            let factor={
                'k' : 1000,
                'ki' : 1 << 10,
                'm' : 1000 * 1000,
                'mi' : 1 << 20,
                'g' : 1000 * 1000 * 1000,
                'gi' : 1 << 30,
                },
                rSize= /^(\d+)(k|ki|m|mi|g|gi)$/i,
                matches= size.match(rSize),
                bytes, unit
            ;

            if(!isNaN(size)){
                bytes=size;
            }else if (matches){
                bytes= matches[1] * factor[unit = matches[2].toLowerCase()]
            }else{
                throw new Error(`${size} !invalid size`)
            }
            return {
                size:size,
                bytes: bytes,
                unit: unit||'bytes',
                format: formatBytes(bytes)
            }

        },
        /**
         * @this JXhrSubmit
         * @return undefined
         */
        getRequest = function()
        {
            let
                /**Files vars*/
                match, name,/**@type File[] */ files,
                fileCounter=0,
                elmsTypeFile= this.node? this.node.querySelectorAll('input[type=file]') : null,
                fileErrors=[],
                totalSize=0,//TODO Global var to check this???
                checkFile= function (elm, f){
                    let norm,
                        ret= true;
                    if(elm.dataset.maxSize){
                        norm= normalizeSize(elm.dataset.maxSize)
                        if(norm.bytes<f.size){
                            fileErrors.push(`File ${f.name} "${formatBytes(f.size)}" exceed max size "${norm.format}"`)
                            ret=false;
                        }
                    }
                    fileCounter++;
                    totalSize= f.size+totalSize;
                    return ret;
                },
                self= this,
                method= this.parameters.method,
                url= this.parameters.action,
                open= function (x,m,u){
                    x.open(m, u)
                    // x.responseType= 'json';
                    x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                }.bind(this),

                xhr, msg, queryStr
            ;

            this.emit('jxhr.request.preSetData', this);

            this.onceData.forEach(function (value, key){
                this.currentData.set(key, value)
            }, this)
            this.customData.forEach(function (value, key){
                this.currentData.set(key, value)
            },this)


            if(elmsTypeFile && elmsTypeFile.length)//add files
            {
                method='POST';

                elmsTypeFile.forEach(function (elm)
                {
                    match= elm.name.match(/\[(.*?)\]/)
                    name= match ? match[1] : elm.name

                    files= (function (e){
                        let ret=[];
                        for(let i=0;i<e.files.length;i++){ ret.push(elm.files[i]) }
                        return ret;
                    })(elm);

                    if(!files.length) return false

                    if(files.length===1 && !elm.multiple) //Single file, so assign file to input
                    {
                        if(checkFile(elm, files[0])) this.currentData.append(elm.name , files[0], files[0].name)
                    }
                    else{//FormData doesn't support a FileList object
                        files.forEach(function (f, i)
                            {//assign file/s to "uploaded[name][file][i]" array & validate in controller
                                if(!checkFile(elm, f)) return;
                                //controller->  $request->files->get(uploaded)[name][files][UploadedFile...]
                                this.currentData.append('uploaded['+name+'][length]' ,(length).toString());
                                this.currentData.append('uploaded['+name+'][data]['+i+'][type]' ,f.type.toString());
                                this.currentData.append('uploaded['+name+'][data]['+i+'][size]' ,f.size.toString());
                                this.currentData.append('uploaded['+name+'][data]['+i+'][name]' ,f.name.toString());
                                if(elm.multiple) this.currentData.append('uploaded['+name+'][file]['+i+']' ,f, f.name);
                            }, this)
                    }
                }, this);
           }

            if(fileErrors.length){
                if(window.JToast) fileErrors.forEach(function(e){JToast.snack(e, 'danger')});
                else alert(fileErrors.join('\n'));
                return;
            }

            //check request and abort if necessary
            pendingRequest.remove(url)

            xhr =  new XMLHttpRequest();
            pendingRequest.add(url, xhr)

            this.emit('jxhr.request.beforeSend', this.currentData)


            xhr.onload = () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    self.emit('jxhr.request.success', xhr.response, self.currentData)

                    if(window.JError){
                        JError.clear(this.node);
                    }

                    if(self.parameters.silent) return

                    if(method==='DELETE') msg= self.parameters.msgDelete
                    else msg= self.parameters.msgComplete;
                    if(window.JToast) JToast.apply(null, msg);
                    // else console.info('Success');
                } else {
                    self.emit('jxhr.request.error', xhr.response, self.currentData)
                    try{
                        JError.clear(this.node);
                        JError(xhr).show();
                    }catch (e) {
                        alert(`Error ${xhr.status} : ${xhr.statusText}`)
                    }
                }
            };

            // if ( xhr.upload && fileCounter) {
            //     xhr.upload.onprogress = function(e) {
            //         let done = e.position || e.loaded, total = e.totalSize || e.total;
            //         console.log('xhr.upload progress: ' + done + ' / ' + total + ' = ' + (Math.floor(done/total*1000)/10) + '%');
            //     };
            // }

            xhr.onloadend = () => {
                pendingRequest.remove(url)
                this.currentData= new FormData();
                self.onceData= new Map();
            }

            if(method === 'GET') //set queryString
            {
                //query string para GET
                queryStr = [...this.currentData.entries()].map(x => `${encodeURIComponent(x[0])}=${encodeURIComponent(x[1])}`).join('&');
                let arr = url.split('?');
                if (arr.length > 1) {
                    url = arr[0] + (arr[1]!== ''? arr[1]+'&'+queryStr : queryStr)
                }else{
                    url = url +'?'+queryStr
                }
                open(xhr, method, url)
                // xhr.open('GET', url)
                xhr.send();
            }else{
                open(xhr, method, url)
                xhr.send(this.currentData);
            }

        };

    /**
     *
     * @param selector
     * @param options
     * @param allEvents
     * @return {JXhrSubmit}
     * @mixes EventEmitter
     * @constructor
     */
    function JXhrSubmit(selector, options=null, allEvents= true)
    {
        if (!(this instanceof JXhrSubmit)) return new JXhrSubmit(selector, options, allEvents)

        this.node= (function(s, ty){
            if(!s) return null
            else if (s && s.nodeType) return s
            else if (ty==="string") return document.querySelector(s);
            else return null
        })(selector, typeof selector)

        if(!this.node && !options){
            throw new Error(['Wrong selector type "', typeof selector, '". Expected: string|nodeType'].join(''))
        }

        this.parameters= (function (elm, opt)
        {
            let ret= {
                responseType: 'json',//default responseType
                action:null,//in dataset.action||form.action|||options.action
                method: 'POST',
                is_form:null,
                has_input_file: false,
                node: this,
                validator: false,
                silent: false,
                msgComplete: ['<i class="fa fa-thumbs-up" aria-hidden="true"></i>', null , 'success'],
                msgDelete: ['<i class="fa fa-trash" aria-hidden="true"></i>', null ,'warning']
            };
            if(elm && elm.nodeType)
            {
                ret.is_form= elm.nodeName.toLowerCase()==='form'
                if(elm.getAttribute('data-action')){
                    ret.action= elm.getAttribute('data-action')
                }
                else if(elm.nodeName.toLowerCase()==='form'){
                    ret.action= elm.action
                }
            }
            if(opt){
                for(let i in ret){
                    if(opt.hasOwnProperty(i)){
                        ret[i]=opt[i]
                    }
                }
            }
            return  ret
        })(this.node, options)

        this.nodes= [];//[node, type] add click event in external buttons
        /**
         * Data to send in the current request.<br>
         * Serialize Form (if any) and merge data from customData/onceData (if any)
         * @type FormData 
         */
        this.currentData= new FormData();
        /**@type Map */
        this.onceData= new Map();//Add this data  only once
        /**@type Map */
        this.customData= new Map();//Extra data to send in all request

        EventEmitter.mixin(this)
        let that= this

        //Add event to submit if FORM
        if(this.parameters.is_form){
            this.node.addEventListener("submit", function(ev)
            {
                ev.stopPropagation();
                ev.preventDefault();
                that.currentData =  new FormData(that.node);
                getRequest.call(this)
            }.bind(this));
        }
        return this;
    }

    /**
     * @param {string} name
     * @param {string|number|boolean} value
     */
    JXhrSubmit.prototype.setParameter = function (name, value)
    {
        this.parameters[name]= value
        return this;
    }

    /**
     * Set post data
     * @param {string} name
     * @param {string|number} value
     */
    JXhrSubmit.prototype.set = function (name, value)
    {
        this.customData.set(name, value);
        return this;
    }

    /**
     * Set post data only for the next request
     * @param {string} name
     * @param {string|number} value
     */
    JXhrSubmit.prototype.setOnce = function (name, value)
    {
        this.onceData.set(name, value);
        return this;
    }

    /**
     * Add buttons/node/s to trigger submit
     * @param {string|number} ev Event type
     * @param {Node|NodeList} node Button/s...
     * @return {JXhrSubmit}
     */
    JXhrSubmit.prototype.addTrigger = function (ev, node)
    {
        let addEv= (ev, elm) => elm.addEventListener(ev, this.ev.forSubmit);
        //Add event to node/s
        if(node.length){//es NodeList
            for(let i = 0, l= node.length; i<l; i++){
                this.nodes.push({node: node[i], type: ev})
                addEv(ev, node[i]);
            }
        }else{
            this.nodes.push({node: node, type: ev})
            addEv.call(this, ev, node);
        }
        return this;
    }

    /**
     * Get XHR
     */
    JXhrSubmit.prototype.submit = function ()
    {
        if(this.parameters.is_form)//listener set a new FormData
        {
            this.node.dispatchEvent(new Event('submit', {cancelable: true}));
        }else{
            getRequest.call(this)
        }
        return this;
    }

    JXhrSubmit.prototype.remove = function ()
    {
        let i;
        if(this.node) this.node.removeEventListener("submit", this.ev.forSubmit);
        for(i = 0; i<this.nodes.length; i++){
            this.nodes[i].node.removeEventListener(this.nodes[i].type, this.ev.forSubmit)
        }
        // this.listeners={}
        return this;
    }

    window.JXhrSubmit = JXhrSubmit;
}(window, EventEmitter, JError));
