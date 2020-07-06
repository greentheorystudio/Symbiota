$(document).ready(function() {
	$("#taxonfilter").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/clsearchsuggest.php", { term: request.term, cl: clid }, response );
		}
	},
	{ minLength: 3 });

	$("#speciestoadd").autocomplete({
		source: function( request, response ) {
			$.getJSON( "rpc/speciessuggest.php", { term: request.term, cl: clid }, response );
		}
	},{ minLength: 4, }
	);

});

function toggleVoucherDiv(tid){
	toggle("voucdiv-"+tid);
	toggle("morevouch-"+tid);
	toggle("lessvouch-"+tid);
	return false;
}

function toggle(target){
	const ele = document.getElementById(target);
	if(ele){
		if(ele.style.display === "none"){
			ele.style.display="";
  		}
	 	else{
	 		ele.style.display="none";
	 	}
	}
	else{
		const divObjs = document.getElementsByTagName("div");
		for (let i = 0; i < divObjs.length; i++) {
			const divObj = divObjs[i];
			if(divObj.getAttribute("class") == target || divObj.getAttribute("className") == target){
				if(divObj.style.display === "none"){
					divObj.style.display="";
				}
			 	else {
			 		divObj.style.display="none";
			 	}
			}
		}
		const spanObjs = document.getElementsByTagName("span");
		for (let i = 0; i < spanObjs.length; i++) {
			const spanObj = spanObjs[i];
			if(spanObj.getAttribute("class") == target || spanObj.getAttribute("className") == target){
				if(spanObj.style.display === "none"){
					spanObj.style.display = "";
				}
			 	else {
			 		spanObj.style.display="none";
			 	}
			}
		}
	}
}

function openIndividualPopup(occid){
	const indUrl = "../collections/individual/index.php?occid=" + occid;
	openPopup(indUrl,"indwindow");
	return false;
}

function openPopup(urlStr,windowName){
	let newWindow = window.open(urlStr, windowName, 'scrollbars=1,toolbar=1,resizable=1,width=1000,height=800,left=400,top=40');
	if (newWindow.opener == null) {
		newWindow.opener = self
	}
	return false;
}
	
function showImagesChecked(f){
	if(f.showimages.checked){
		document.getElementById("wordicondiv").style.display = "none";
		f.showvouchers.checked = false;
		document.getElementById("showvouchersdiv").style.display = "none"; 
		f.showauthors.checked = false;
		document.getElementById("showauthorsdiv").style.display = "none"; 
	}
	else{
		document.getElementById("wordicondiv").style.display = "block";
		document.getElementById("showvouchersdiv").style.display = "block"; 
		document.getElementById("showauthorsdiv").style.display = "block"; 
	}
}

function validateAddSpecies(f){
	const sciName = f.speciestoadd.value;
	if(sciName === ""){
		alert("Enter the scientific name of species you wish to add");
		return false;
	}
	else{
		cseXmlHttp=GetXmlHttpObject();
		if (cseXmlHttp==null){
	  		alert ("Your browser does not support AJAX!");
	  		return false;
	  	}
		let url = "rpc/gettid.php";
		url=url+"?sciname="+sciName;
		url=url+"&sid="+Math.random();
		cseXmlHttp.onreadystatechange=function(){
			if(cseXmlHttp.readyState == 4 && cseXmlHttp.status == 200){
				const testTid = cseXmlHttp.responseText;
				if(testTid === ""){
					alert("ERROR: Scientific name does not exist in database. Did you spell it correctly? If so, contact your data administrator to add this species to the Taxonomic Thesaurus.");
				}
				else{
					f.tidtoadd.value = testTid;
					f.submit();
				}
			}
		};
		cseXmlHttp.open("POST",url,true);
		cseXmlHttp.send(null);
		return false;
	}
}

function changeOptionFormAction(action,target){
	document.optionform.action = action;
	document.optionform.target = target;
}

function GetXmlHttpObject(){
	let xmlHttp = null;
	try{
		xmlHttp=new XMLHttpRequest();
  	}
	catch (e){
  		try{
    		xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    	}
  		catch(e){
    		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    	}
  	}
	return xmlHttp;
}

const timeout = 500;
let closetimer = 0;
let ddmenuitem = 0;

function mopen(id)
{	
	mcancelclosetime();
	if(ddmenuitem) {
		ddmenuitem.style.visibility = 'hidden';
	}
	ddmenuitem = document.getElementById(id);
	ddmenuitem.style.visibility = 'visible';

}

function mclose()
{
	if(ddmenuitem) {
		ddmenuitem.style.visibility = 'hidden';
	}
}

function mclosetime()
{
	closetimer = window.setTimeout(mclose, timeout);
}

function mcancelclosetime()
{
	if(closetimer){
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

document.onclick = mclose;
