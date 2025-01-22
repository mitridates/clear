((L, w)=>{
	/**
	 * @param {JsonApiSpec|null} depth cathegory as Fieldvaluecode
	 * @param {JsonApiSpec|null} length cathegory as Fieldvaluecode
	 */
	function colorByCategory(depth, length){

		let
			color='grey',
			dcode, lcode
		;

		if(!depth || !length) return 'grey';
		if(!depth && length) return 'black';
		if(depth && !length) return 'black';

		dcode= depth.get('code')*1;
		lcode= length.get('code')*1;

		if(dcode===1){
			switch (true){
				case lcode<=1:
					color= 'black';
					break;
				default: color= 'blue';
			}
			return color;
		}else
		if(dcode===2){
			switch (true){
				case lcode<=2:
					color= 'green';
					break;
				default: color= 'orange';
			}
		}else
		if(dcode===3){
			switch (true){
				case lcode<=2:
					color= 'gold';
					break;
				default: color= 'red';
			}
		}else
		if(dcode>3){
			switch (true){
				case lcode<=1:
					color= 'gold';
					break;
				default: color= 'red';
			}
		}
		return color;
	}
	let icon={
		Hex: {//ex color
			'grey':['#7B7B7B', '#6B6B6B'],
			'black':['#3D3D3D', '#313131'],
			'green':['#2AAD27', '#31882A'],
			'blue':['#2A81CB', '#3274A3'],
			'yellow':['#CAC428', '#988F2E'],
			'orange':['#CB8427', '#98652E'],
			'red':['#CB2B3E', '#982E40'],
			'gold':['#FFD326', '#C1A32D'],
			'violet':['#9C2BCB', '#742E98'],
		},
		xl: {
			iconSize: [25, 41],
			iconAnchor: [12, 41],
			popupAnchor: [1, -34],
			shadowSize: [41, 41]
		},
		md: {
			iconSize: [18.5, 27.3],
			iconAnchor: [8, 27.3],
			popupAnchor: [0.7, -22.5],
			shadowSize: [27.3, 27.3]
		},
		getXl: (path, color)=>{
			let props= icon.xl;
			props['iconUrl']= `${path}marker-icon-2x-${color}.png`;
			props['shadowUrl']= `${path}marker-shadow.png`;
			return props;
		},
		getMd: (path, color)=>{
			let props= icon.md;
			props['iconUrl']= `${path}marker-icon-${color}.png`;
			props['shadowUrl']= `${path}marker-shadow.png`;
			return props;
		}

	}

	function colorMarker(path, L){
		this.path=path;
		this.L=L;
		this.color=null;
	}
	colorMarker.prototype={
		getXl: function (color){
			return new this.L.Icon(icon.getXl(this.path, color));
		},
		getMd(color){
			return new this.L.Icon(icon.getMd(this.path, color));
		},
		/**
		 *
		 * @param {JsonApiSpec|null} depthcategory
		 * @param {JsonApiSpec|null} lengthCategory
		 * @param {string|null} size
		 */
		getMarkerByCathegory: function (depthCategory, lengthCategory, size='md'){
			this.color = colorByCategory(depthCategory, lengthCategory);
			return size==='md'? this.getMd(this.color): this.getXl(this.color);
		},
		/**
		 * color Hex array
		 * @return Array
		 */
		getHex: function (){
			return icon.Hex[this.color];
		}
	}


	w.colorMarker= colorMarker;
})(L, window)