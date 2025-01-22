	let iconProperties={
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
			let props= iconProperties.xl;
			props['iconUrl']= `${path}marker-icon-2x-${color}.png`;
			props['shadowUrl']= `${path}marker-shadow.png`;
			return props;
		},
		getMd: (path, color)=>{
			let props= iconProperties.md;
			props['iconUrl']= `${path}marker-icon-${color}.png`;
			props['shadowUrl']= `${path}marker-shadow.png`;
			return props;
		}

	}

	let colorMarker= {
		getXl: function (color, path, L){
			return new L.Icon(iconProperties.getXl(path, color));
		},
		getMd: function (color, path, L){
			return new L.Icon(iconProperties.getMd(path, color));
		},
		/**
		 * color Hex array
		 * @return Array
		 */
		getHex: function (color){
			return iconProperties.Hex[color];
		}
	}

	export {colorMarker, iconProperties}