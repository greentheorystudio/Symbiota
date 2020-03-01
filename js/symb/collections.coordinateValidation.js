function verifyCoordinates(f){
	const lngValue = f.decimallongitude.value;
	const latValue = f.decimallatitude.value;
	if(latValue && lngValue){
		
		$.ajax({
			type: "GET",
			url: "//maps.googleapis.com/maps/api/geocode/json?sensor=false",
			dataType: "json",
			data: { latlng: latValue+","+lngValue }
		}).done(function( data ) {
			if(data){
				if(data.status !== "ZERO_RESULTS"){
					const result = data.results[0];
					if(result.address_components){
						const compArr = result.address_components;
						let coordCountry = "";
						let coordState = "";
						let coordCounty = "";
						for (const p1 in compArr) {
							if(compArr.hasOwnProperty(p1)){
								const compObj = compArr[p1];
								if(compObj.long_name && compObj.types){
									const longName = compObj.long_name;
									const types = compObj.types;
									if(types[0] === "country"){
										coordCountry = longName;
									}
									else if(types[0] === "administrative_area_level_1"){
										coordState = longName;
									}
									else if(types[0] === "administrative_area_level_2"){
										coordCounty = longName;
									}
								}
							}
						}
						let coordValid = true;
						if(f.country.value === "" && coordCountry !== ""){
							f.country.value = coordCountry;
						}
						if(coordState !== ""){
							if(f.stateprovince.value !== ""){
								if(f.stateprovince.value.toLowerCase().indexOf(coordState.toLowerCase()) === -1) coordValid = false;
							}
							else{
								f.stateprovince.value = coordState;
							}
						}
						if(coordCounty !== ""){
							let coordCountyIn = coordCounty.replace(" County", "");
							coordCountyIn = coordCountyIn.replace(" Parish","");
							if(f.county.value !== ""){
								if(f.county.value.toLowerCase().indexOf(coordCountyIn.toLowerCase()) === -1){
									if(f.county.value.toLowerCase() !== coordCountyIn.toLowerCase()){
										coordValid = false;
									}
								}
							}
							else{
								f.county.value = coordCountyIn;
							}
						}
						if(!coordValid){
							let msg = "Are coordinates accurate? They currently map to: " + coordCountry + ", " + coordState;
							if(coordCounty) msg = msg + ", " + coordCounty;
							msg = msg + ", which differs from what is in the form. Click globe symbol to display coordinates in map.";
							alert(msg);
						}
					}
				}
				else{
					if(f.country.value !== ""){
						alert("Unable to identify country! Are coordinates accurate? Click globe symbol to display coordinates in map.");
					}
				}
			}
		});
	}
}
