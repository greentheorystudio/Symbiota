const parentChild = false;

$(document).ready(function() {
	if(!navigator.cookieEnabled){
		alert("Your browser cookies are disabled. To be able to login and access your profile, they must be enabled for this domain.");
	}

	$('#tabs').tabs({
		beforeLoad: function( event, ui ) {
			$(ui.panel).html("<p>Loading...</p>");
		}
	});
	
	function split( val ) {
		return val.split( /,\s*/ );
	}
	function extractLast( term ) {
		return split( term ).pop();
	}

	$( "#addauthorsearch" )
		.bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				$.getJSON( "rpc/authorlist.php", {
					term: extractLast( request.term ), t: function() {
						return document.authorform.addauthorsearch.value;
					}
				}, response );
			},
			search: function() {
				const term = extractLast(this.value);
				if ( term.length < 3 ) {
					return false;
				}
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				const terms = split(this.value);
				terms.pop();
				terms.push( ui.item.label );
				document.getElementById('refauthorid').value = ui.item.value;
				this.value = terms;
				addAuthorToRef();
				return false;
			},
			change: function (event, ui) {
				if (!ui.item) {
					this.value = '';
					if (confirm("Would you like to add a new author to the database?")) {
						openNewAuthorWindow();
					}
				}
			}
		},{});
		
	if(parentChild){
		let url = '';
		if(document.getElementById("ReferenceTypeId").value === 2 || document.getElementById("ReferenceTypeId").value === 4 || document.getElementById("ReferenceTypeId").value === 7 || document.getElementById("ReferenceTypeId").value === 8){
			url = 'rpc/parenttitlelist.php';
		}
		if(document.getElementById("ReferenceTypeId").value === 3 || document.getElementById("ReferenceTypeId").value === 6){
			url = 'rpc/seriestitlelist.php';
		}
		$( "#secondarytitle" )
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				source: function( request, response ) {
					$.getJSON( url, {
						term: extractLast( request.term ), t: function() {
							return document.referenceeditform.secondarytitle.value;
						}
					}, response );
				},
				search: function() {
					const term = extractLast(this.value);
					if ( term.length < 3 ) {
						return false;
					}
				},
				focus: function() {
					return false;
				},
				select: function( event, ui ) {
					const terms = split(this.value);
					terms.pop();
					terms.push( ui.item.label );
					getParentInfo(ui.item.value);
					return false;
				}
			},{});
	}
	
	if(document.getElementById("ReferenceTypeId").value === 4){
		$( "#tertiarytitle" )
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				source: function( request, response ) {
					$.getJSON( "rpc/seriestitlelist.php", {
						term: extractLast( request.term ), t: function() {
							return document.referenceeditform.tertiarytitle.value;
						}
					}, response );
				},
				search: function() {
					const term = extractLast(this.value);
					if ( term.length < 3 ) {
						return false;
					}
				},
				focus: function() {
					return false;
				},
				select: function( event, ui ) {
					const terms = split(this.value);
					terms.pop();
					terms.push( ui.item.label );
					getParentInfo(ui.item.value);
					return false;
				}
			},{});
	}
});

function getParentInfo(refid){
	const sutXmlHttp = GetXmlHttpObject();
	if (sutXmlHttp == null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	const refType = document.getElementById("ReferenceTypeId").value;

	const url = "rpc/parentdetails.php?refid=" + refid + "&reftype=" + refType;

	sutXmlHttp.onreadystatechange=function(){
		let parentArr;
		if (sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200) {
			parentArr = JSON.parse(sutXmlHttp.responseText);
			document.getElementById("parentRefId").value = parentArr['parentRefId'];
			document.getElementById("parentRefId2").value = parentArr['parentRefId2'];
			if(document.getElementById("secondarytitle")){
				document.getElementById("secondarytitle").value = parentArr['secondarytitle'];
			}
			if(document.getElementById("tertiarytitle")){
				document.getElementById("tertiarytitle").value = parentArr['tertiarytitle'];
			}
			if(document.getElementById("shorttitle")){
				document.getElementById("shorttitle").value = parentArr['shorttitle'];
			}
			if(document.getElementById("alternativetitle")){
				document.getElementById("alternativetitle").value = parentArr['alternativetitle'];
			}
			if(document.getElementById("pubdate")){
				document.getElementById("pubdate").value = parentArr['pubdate'];
			}
			if(document.getElementById("edition")){
				document.getElementById("edition").value = parentArr['edition'];
			}
			if(document.getElementById("volume")){
				document.getElementById("volume").value = parentArr['volume'];
			}
			if(document.getElementById("number")){
				document.getElementById("number").value = parentArr['number'];
			}
			if(document.getElementById("placeofpublication")){
				document.getElementById("placeofpublication").value = parentArr['placeofpublication'];
			}
			if(document.getElementById("publisher")){
				document.getElementById("publisher").value = parentArr['publisher'];
			}
			if(document.getElementById("isbn_issn")){
				document.getElementById("isbn_issn").value = parentArr['isbn_issn'];
			}
			if(document.getElementById("numbervolumnes")){
				document.getElementById("numbervolumnes").value = parentArr['numbervolumnes'];
			}
		}
	};
	sutXmlHttp.open("POST",url,false);
	sutXmlHttp.send(null);
}

function addAuthorToRef(){
	const refauthid = document.getElementById('refauthorid').value;
	const sutXmlHttp = GetXmlHttpObject();
	if (sutXmlHttp == null){
		alert ("Your browser does not support AJAX!");
		return;
	}

	const url = "rpc/authormanager.php?refid=" + refid + "&action=addauthor&refauthid=" + refauthid;

	let authorList = '';
	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200){
			authorList = sutXmlHttp.responseText;
			document.getElementById("authorlistdiv").innerHTML = authorList;
			document.getElementById("addauthorsearch").value = '';
			document.getElementById("refauthorid").value = '';
		}
	};
	sutXmlHttp.open("POST",url,false);
	sutXmlHttp.send(null);
}

function deleteRefAuthor(refauthid){
	if (confirm("Are you sure you would like to remove this author from this reference?")) {
		const sutXmlHttp = GetXmlHttpObject();
		if (sutXmlHttp == null){
			alert ("Your browser does not support AJAX!");
			return;
		}

		const url = "rpc/authormanager.php?refid=" + refid + "&action=deleterefauthor&refauthid=" + refauthid;

		let authorList = '';
		sutXmlHttp.onreadystatechange=function(){
			if(sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200){
				authorList = sutXmlHttp.responseText;
				document.getElementById("authorlistdiv").innerHTML = authorList;
				document.getElementById("addauthorsearch").value = '';
				document.getElementById("refauthorid").value = '';
			}
		};
		sutXmlHttp.open("POST",url,false);
		sutXmlHttp.send(null);
	}
}

function deleteRefLink(table,field,type,id){
	if (confirm("Are you sure you would like to remove this link from this reference?")) {
		const sutXmlHttp = GetXmlHttpObject();
		if (sutXmlHttp == null){
			alert ("Your browser does not support AJAX!");
			return;
		}

		const url = "rpc/authormanager.php?refid=" + refid + "&action=deletereflink&table=" + table + "&field=" + field + "&id=" + id + "&type=" + type;

		let authorList = '';
		sutXmlHttp.onreadystatechange=function(){
			if(sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200){
				authorList = sutXmlHttp.responseText;
				document.getElementById(table).innerHTML = authorList;
			}
		};
		sutXmlHttp.open("POST",url,false);
		sutXmlHttp.send(null);
	}
}

function openNewAuthorWindow(){
	const urlStr = 'authoreditor.php?refid=' + refid + '&addauth=1';
	let newWindow = window.open(urlStr, 'popup', 'scrollbars=1,toolbar=1,resizable=1,width=470,height=300');
	if (newWindow.opener == null) {
		newWindow.opener = self;
	}
	return false;
}

function processNewAuthor(f){
	const firstName = f.firstname.value;
	const middleName = f.middlename.value;
	const lastName = f.lastname.value;
	if(firstName === "" || lastName === ""){
		alert("Please enter the first and last name of the author.");
		return false;
	}
	const sutXmlHttp = GetXmlHttpObject();
	if (sutXmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}

	const url = "rpc/authormanager.php?refid=" + refid + "&action=createauthor&firstname=" + firstName + "&midname=" + middleName + "&lastname=" + lastName;

	let authorList = '';

	sutXmlHttp.onreadystatechange=function(){
		if(sutXmlHttp.readyState === 4 && sutXmlHttp.status === 200){
			authorList = sutXmlHttp.responseText;
			opener.document.getElementById("authorlistdiv").innerHTML = authorList;
			opener.document.getElementById("addauthorsearch").value = '';
			opener.document.getElementById("refauthorid").value = '';
			self.close();
		}
	};
	sutXmlHttp.open("POST",url,false);
	sutXmlHttp.send(null);
}

function selectAll(cb){
	let boxesChecked = cb.checked;
	const dbElements = document.getElementsByName("occid[]");
	for(let i = 0; i < dbElements.length; i++){
		const dbElement = dbElements[i];
		dbElement.checked = boxesChecked;
	}
}

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

function verifyNewRefForm(){
	if(document.getElementById("newreftitle").value === ""){
		alert("Please enter the title of the reference.");
		return false;
	}
	if(document.getElementById("newreftype").selectedIndex < 2){
		alert("Please select the type of reference.");
		return false;
	}
	return true;
}

function verifyNewAuthForm(){
	if(document.getElementById("firstname").value === "" || document.getElementById("lastname").value === ""){
		alert("Please enter the first and last name of the author.");
		return false;
	}
	return true;
}

function verifyEditRefForm(){
	if(document.getElementById("title")){
		if(document.getElementById("title").value === ""){
			alert("Please enter the title of the reference.");
			return false;
		}
	}
	if(document.getElementById("ReferenceTypeId").selectedIndex < 2){
		alert("Please select the type of reference.");
		return false;
	}
	if(document.getElementById("ReferenceTypeId").value === 4){
		if(document.getElementById("secondarytitle").value === '' && document.getElementById("tertiarytitle").value === ''){
			alert("Please enter either a book title or book series title.");
			return false;
		}
		if(document.getElementById("tertiarytitle").value !== '' && document.getElementById("volume").value === '' && document.getElementById("number").value === ''){
			alert("Please enter either the volume or number in the series.");
			return false;
		}
	}
	if(document.getElementById("ReferenceTypeId").value === 2 || document.getElementById("ReferenceTypeId").value === 7){
		if(document.getElementById("secondarytitle").value === ''){
			alert("Please enter a periodical title.");
			return false;
		}
		if(document.getElementById("volume").value === '' && document.getElementById("number").value === ''){
			alert("Please enter a volume or number for the periodical.");
			return false;
		}
	}
	if(document.getElementById("ReferenceTypeId").value === 8){
		if(document.getElementById("secondarytitle").value === ''){
			alert("Please enter a periodical title.");
			return false;
		}
		if(document.getElementById("edition").value === '' || document.getElementById("pubdate").value === ''){
			alert("Please enter the date or edition for the periodical.");
			return false;
		}
	}
	if(document.getElementById("ReferenceTypeId").value === 3 || document.getElementById("ReferenceTypeId").value === 6){
		if(document.getElementById("secondarytitle").value !== '' && document.getElementById("volume").value === '' && document.getElementById("number").value === ''){
			alert("Please enter either the volume or number in the series.");
			return false;
		}
	}
	return true;
}

function verifyRefTypeChange(){
	if(document.getElementById("ReferenceTypeId").selectedIndex > 1){
		if (confirm("Are you sure you would like to change the reference type?")) {
			const refTypeVal = document.getElementById("ReferenceTypeId").value;
			if(refTypeVal === 4 || refTypeVal === 3 || refTypeVal === 6 || refTypeVal === 27){
				document.getElementById("refGroup").value = 1;
			}
			else if(refTypeVal === 2 || refTypeVal === 7 || refTypeVal === 8 || refTypeVal === 30){
				document.getElementById("refGroup").value = 2;
			}
			else{
				document.getElementById("refGroup").value = 3;
			}
			document.getElementById("dynamicInput").innerHTML = '<input name="formsubmit" type="hidden" value="Edit Reference" />';
			document.getElementById("referenceeditform").submit();
		}
	}
}

function updateIspublished(){
	if(document.getElementById("ispublishedcheck").checked === true){
		document.getElementById("ispublished").value = "1";
	}
	else{
		document.getElementById("ispublished").value = "0";
	}
}

function GetXmlHttpObject(){
	let xmlHttp;
	try{
		xmlHttp = new XMLHttpRequest();
	}
	catch (e){
		try{
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e){
			xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
