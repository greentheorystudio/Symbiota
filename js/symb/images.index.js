$('html').hide();

$(document).ready(function() {
	$('html').show();
});


jQuery(document).ready(function($) {
	$('#taxa').manifest({
		marcoPolo: {
			url: 'rpc/imagesearchautofill.php',
			data: {
				t: 'taxa'
			},
			formatItem: function (data) {
				return data.name;
			},
			onSelect: function (){
				$("#imagedisplay").val("thumbnail");
			}
		}
	});
	
	$('#common').manifest({
		marcoPolo: {
			url: 'rpc/imagesearchautofill.php',
			data: {
				t: 'common'
			},
			formatItem: function (data) {
				return data.name;
			}
		}
	});
	
	$('#country').manifest({
		marcoPolo: {
			url: 'rpc/imagesearchautofill.php',
			data: {
				t: 'country'
			},
			formatItem: function (data) {
				return data.name;
			}
		}
	});
	
	$('#state').manifest({
		marcoPolo: {
			url: 'rpc/imagesearchautofill.php',
			data: {
				t: 'state'
			},
			formatItem: function (data) {
				return data.name;
			}
		}
	});
	
	$('#keywords').manifest({
		marcoPolo: {
			url: 'rpc/imagesearchautofill.php',
			data: {
				t: 'keywords'
			},
			formatItem: function (data) {
				return data.name;
			}
		}
	});
});

function submitImageForm(){
	let taxastr;
	const taxavals = $('#taxa').manifest('values');
	const commonvals = $('#common').manifest('values');
	const countryvals = $('#country').manifest('values');
	const statevals = $('#state').manifest('values');
	const keywordsvals = $('#keywords').manifest('values');
	if(taxavals.length > 0){
		taxastr = taxavals.join();
		document.getElementById('taxastr').value = taxastr;
	}
	else if(commonvals.length > 0){
		taxastr = commonvals.join();
		document.getElementById('taxastr').value = taxastr;
	}
	else{
		document.getElementById('taxastr').value = '';
	}
	if(countryvals.length > 0){
		document.getElementById('countrystr').value = countryvals.join();
	}
	else{
		document.getElementById('countrystr').value = '';
	}
	if(statevals.length > 0){
		document.getElementById('statestr').value = statevals.join();
	}
	else{
		document.getElementById('statestr').value = '';
	}
	if(keywordsvals.length > 0){
		document.getElementById('keywordstr').value = keywordsvals.join();
	}
	else{
		document.getElementById('keywordstr').value = '';
	}
	if(phArr.length > 0){
		const phids = [];
		for(let i = 0; i < phArr.length; i++){
			phids.push(phArr[i].id);
		}
		document.getElementById('phuidstr').value = phids.join();
		document.getElementById('phjson').value = JSON.stringify(phArr);
	}
	else{
		document.getElementById('phuidstr').value = '';
		document.getElementById('phjson').value = '';
	}
	return verifyCollForm(document.getElementById('imagesearchform'));
}

function imageDisplayChanged(f){
	if(f.imagedisplay.value === "taxalist" && $('#taxa').manifest('values') !== ""){
		f.imagedisplay.value = "thumbnail";
		alert("Only the thumbnail display is allowed when searching for a scientific name");
	}
}

function toggle(target){
	const ele = document.getElementById(target);
	if(ele){
		if(ele.style.display === "none"){
			if(ele.id.substring(0,5) === "minus" || ele.id.substring(0,4) === "plus"){
				ele.style.display = "inline";
	  		}
			else{
				ele.style.display = "block";
			}
  		}
	 	else {
	 		ele.style.display="none";
	 	}
	}
}

function checkTaxonType(){
	let vals;
	const newtaxontype = document.getElementById('taxontype').value;
	const oldtaxontype = document.getElementById('taxtp').value;
	if(newtaxontype == 1||newtaxontype == 2){
		if(oldtaxontype == 3){
			vals = $('#common').manifest('values');
			for (let i = 0; i < vals.length; i++) {
				$('#common').manifest('remove', i);
			}
			document.getElementById('thesdiv').style.display = "block";
			document.getElementById('commonbox').style.display = "none";
			document.getElementById('taxabox').style.display = "block";
			document.getElementById('taxtp').value = newtaxontype;
		}
	
	}
	if(newtaxontype == 3){
		if(oldtaxontype == 1||oldtaxontype == 2){
			vals = $('#taxa').manifest('values');
			for (let i = 0; i < vals.length; i++) {
				$('#taxa').manifest('remove', i);
			}
			document.getElementById('commonbox').style.display = "block";
			document.getElementById('taxabox').style.display = "none";
			document.getElementById('thesdiv').style.display = "none";
			document.getElementById('thes').checked = false;
			document.getElementById('taxtp').value = newtaxontype;
		}
	
	}
}

function toggleCat(catid){
	toggle("minus-"+catid);
	toggle("plus-"+catid);
	toggle("cat-"+catid);
}

function togglePid(pid){
	toggle("minus-pid-"+pid);
	toggle("plus-pid-"+pid);
	toggle("pid-"+pid);
}

function selectAll(cb){
	let boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
	}
	const f = cb.form;
	for(let i=0; i<f.length; i++){
		if(f.elements[i].name === "db[]" || f.elements[i].name === "cat[]") {
			f.elements[i].checked = boxesChecked;
		}
	}
}

function uncheckAll(){
	if(document.getElementById('dballcb')){
		document.getElementById('dballcb').checked = false;
	}
	if(document.getElementById('dballspeccb')){
		document.getElementById('dballspeccb').checked = false;
	}
	if(document.getElementById('dballobscb')){
		document.getElementById('dballobscb').checked = false;
	}
}

function selectAllCat(cb,target){
	let boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
		uncheckAll();
	}
	const inputObjs = document.getElementsByTagName("input");
	for (let i = 0; i < inputObjs.length; i++) {
		const inputObj = inputObjs[i];
		if(inputObj.getAttribute("class") == target || inputObj.getAttribute("className") == target){
  			inputObj.checked = boxesChecked;
  		}
  	}
}

function unselectCat(catTarget){
	const catObj = document.getElementById(catTarget);
	catObj.checked = false;
	uncheckAll();
}

function selectAllPid(cb){
	let boxesChecked = true;
	if(!cb.checked){
		boxesChecked = false;
	}
	const target = "pid-" + cb.value;
	const inputObjs = document.getElementsByTagName("input");
	for (let i = 0; i < inputObjs.length; i++) {
		const inputObj = inputObjs[i];
		if(inputObj.getAttribute("class") == target || inputObj.getAttribute("className") == target){
  			inputObj.checked = boxesChecked;
  		}
  	}
}

function verifyCollForm(f){
	let formVerified = false;
	for(let h=0; h<f.length; h++){
		if(f.elements[h].name === "db[]" && f.elements[h].checked){
			formVerified = true;
		}
		else{
            document.getElementById("dballcb").checked = false;
		}
		if(f.elements[h].name === "cat[]" && f.elements[h].checked){
			formVerified = true;
		}
	}
	if(!formVerified){
		alert("Please choose at least one collection!");
		return false;
	}
	else{
		for(let i=0; i<f.length; i++){
			if(f.elements[i].name === "cat[]" && f.elements[i].checked){
				const childrenEle = document.getElementById('cat-' + f.elements[i].value).children;
				for(let j=0; j<childrenEle.length; j++){
					if(childrenEle[j].tagName === "DIV"){
						const divChildren = childrenEle[j].children;
						for(let k=0; k<divChildren.length; k++){
							const divChildren2 = divChildren[k].children;
							for(let l=0; l<divChildren2.length; l++){
								if(divChildren2[l].tagName === "INPUT"){
									divChildren2[l].checked = false;
								}
							}
						}
					}
				}
			}
		}
	}
  	return formVerified;
}

function openIndPU(occId,clid){
	let wWidth = 900;
	if(document.getElementById('innertext').offsetWidth){
		wWidth = document.getElementById('innertext').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	if(wWidth > 1000) {
		wWidth = 1000;
	}
	let newWindow = window.open('../collections/individual/index.php?occid=' + occId, 'indspec' + occId, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
}

function openTaxonPopup(tid){
	let wWidth = 900;
	if(document.getElementById('innertext').offsetWidth){
		wWidth = document.getElementById('innertext').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	if(wWidth > 1000) {
		wWidth = 1000;
	}
	let newWindow = window.open("../taxa/index.php?taxon=" + tid, 'taxon' + tid, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=700,left=20,top=20');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
}

function openImagePopup(imageId){
	let wWidth = 900;
	if(document.getElementById('innertext').offsetWidth){
		wWidth = document.getElementById('innertext').offsetWidth*1.05;
	}
	else if(document.body.offsetWidth){
		wWidth = document.body.offsetWidth*0.9;
	}
	if(wWidth > 1000) {
		wWidth = 1000;
	}
	let newWindow = window.open("imgdetails.php?imgid=" + imageId, 'image' + imageId, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
}

function changeImagePage(taxonIn,viewIn,starrIn,pageIn){
	document.getElementById("imagebox").innerHTML = "<p>Loading...</p>";
	$.ajax( {
		url: "rpc/changeimagepage.php",
		method: "POST",
		data: { 
			starr: starrIn, 
			page: pageIn, 
			view: viewIn,
			taxon: taxonIn
		},
		success: function( data ) {
			document.getElementById("imagebox").innerHTML = JSON.parse(data);
			if(viewIn === 'thumb'){
				document.getElementById("imagetab").innerHTML = 'Images';
			}
			else{
				document.getElementById("imagetab").innerHTML = 'Taxa List';
			}
        }
	});

}

function changeFamily(taxon){
	selectedFamily = taxon;
}
