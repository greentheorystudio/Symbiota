function verifyCoordinates(f){
	const lngValue = f.decimallongitude.value;
	const latValue = f.decimallatitude.value;
	if(latValue && lngValue){
		const http = new XMLHttpRequest();
		const url = "https://nominatim.openstreetmap.org/reverse?lat="+latValue+"&lon="+lngValue+"&format=json";
		http.open("GET", url, true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.onreadystatechange = function() {
			if(http.readyState === 4 && http.status === 200) {
				if(http.responseText){
					const data = JSON.parse(http.responseText);
					if(data.hasOwnProperty('address')){
						const addressArr = data.address;
						let coordCountry = addressArr.country;
						let coordState = addressArr.state;
						let coordCounty = addressArr.county;
						let coordValid = true;
						if(f.country.value === "" && coordCountry !== ""){
							f.country.value = coordCountry;
						}
						if(f.country.value !== "" && (f.country.value.toLowerCase() !== coordCountry.toLowerCase())){
							if(f.country.value.toLowerCase() !== "usa" && f.country.value.toLowerCase() !== "united states of america" && coordCountry.toLowerCase() !== "united states"){
								coordValid = false;
							}
						}
						if(coordState !== ""){
							if(f.stateprovince.value !== "" && (f.stateprovince.value.toLowerCase() !== coordState.toLowerCase())){
								coordValid = false;
							}
							else{
								f.stateprovince.value = coordState;
							}
						}
						if(coordCounty !== ""){
							let coordCountyIn = coordCounty.replace(" County", "");
							coordCountyIn = coordCountyIn.replace(" Parish","");
							if(f.county.value !== "" && (f.county.value.toLowerCase() !== coordCountyIn.toLowerCase())){
								coordValid = false;
							}
							else{
								f.county.value = coordCountyIn;
							}
						}
						if(!coordValid){
							let msg = "Are those coordinates accurate? They currently map to: " + coordCountry + ", " + coordState;
							if(coordCounty) {
								msg = msg + ", " + coordCounty;
							}
							msg = msg + ", which differs from what you have entered. Click the globe icon to set the coordinates in a map.";
							alert(msg);
						}
					}
					else{
						alert("Unable to identify a country from the coordinates entered! Are they accurate? Click the globe icon to set the coordinates in a map.");
					}
				}
			}
		};
		http.send();
	}
}
