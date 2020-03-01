function toggle(target){
	const objDiv = document.getElementById(target);
	if(objDiv){
		if(objDiv.style.display === "none"){
			objDiv.style.display = "block";
		}
		else{
			objDiv.style.display = "none";
		}
	}
	else{
		const divs = document.getElementsByTagName("div");
		for (let h = 0; h < divs.length; h++) {
			const divObj = divs[h];
			if(divObj.className === target){
				if(divObj.style.display === "none"){
					divObj.style.display="block";
				}
			 	else {
			 		divObj.style.display="none";
			 	}
			}
		}
	}
}
