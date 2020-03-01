function toggleAll(){
	toggleChars("dynam");
	toggleChars("dynamControl");
}

function toggleChars(name){
	const chars = document.getElementsByTagName("div");
	for (let i = 0; i < chars.length; i++) {
		const obj = chars[i];
		if(obj.className === name){
			if(obj.style.display === "none"){
				obj.style.display="block";
				setCookie("all");
			}
		 	else {
		 		obj.style.display="none";
				setCookie("limited");
		 	}
		}
  }
}

function setCookie(status){
	document.cookie = "showchars=" + status;		
}

function getCookie(name){
	const pos = document.cookie.indexOf(name + "=");
	if(pos === -1){
		return null;
	} else {
		const pos2 = document.cookie.indexOf(";", pos);
		if(pos2 === -1){
			return unescape(document.cookie.substring(pos + name.length + 1));
		}
		else{
			return unescape(document.cookie.substring(pos + name.length + 1, pos2));
		}
	}
}

function setDisplayStatus(){
	const showStatus = getCookie("showchars");
	if(showStatus === "all"){
		toggleAll();
	}
	else {
		if(allClosed()) {
			toggleAll();
		}
	}
}

function allClosed(){
	const objs = document.getElementsByTagName("div");
	for (let i = 0; i < objs.length; i++) {
		const obj = objs[i];
		if(obj.id !== "showall" && obj.style.display !== "none"){
			return false;
		}
	}
	return true;
}

function setLang(list){
	const langName = list.options[list.selectedIndex].value;
	const objs = document.getElementsByTagName("span");
	for (let i = 0; i < objs.length; i++) {
		const obj = objs[i];
		if(obj.lang === langName){
			obj.style.display="";
		}
		else if(obj.lang !== ""){
	 		obj.style.display="none";
		}
	}
}
