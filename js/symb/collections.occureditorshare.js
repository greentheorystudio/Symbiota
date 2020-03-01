function submitQueryForm(qryIndex){
	if(verifyLeaveForm()){
		const f = document.queryform;
		if(qryIndex) {
			f.occindex.value = qryIndex;
		}
		if(verifyQueryForm()) {
			f.submit();
		}
	}
	return false;
}

function verifyLeaveForm(){
	if(document.fullform && document.fullform.submitaction.disabled === false && document.fullform.submitaction.type === "submit"){
		return confirm("It appears that you didn't save your changes. Are you sure you want to leave without saving?"); 
	}
	return true;
}

function submitQueryEditor(f){
	f.action = "occurrenceeditor.php";
	if(verifyQueryForm(f)) {
		f.submit();
	}
	return true;
}

function submitQueryTable(f){
	f.action = "occurrencetabledisplay.php";
	if(verifyQueryForm(f)) {
		f.submit();
	}
	return true;
}
function detectBatchUpdateField(){
	const fieldSelected = document.getElementById('bufieldname').value;
	if(fieldSelected === "processingstatus"){
		let buNewValue = '<select name="bunewvalue">';
		buNewValue += '<option value="unprocessed">Unprocessed</option>';
		buNewValue += '<option value="unprocessed/nlp">Unprocessed/NLP</option>';
		buNewValue += '<option value="stage 1">Stage 1</option>';
		buNewValue += '<option value="stage 2">Stage 2</option>';
		buNewValue += '<option value="stage 3">Stage 3</option>';
		buNewValue += '<option value="pending review-nfn">Pending Review-NfN</option>';
		buNewValue += '<option value="pending review">Pending Review</option>';
		buNewValue += '<option value="expert required">Expert Required</option>';
		buNewValue += '<option value="reviewed">Reviewed</option>';
		buNewValue += '<option value="closed">Closed</option>';
		buNewValue += '<option value="">No Set Status</option>';
		buNewValue += '</select>';
		document.getElementById("bunewvaluediv").innerHTML = buNewValue;
	}
	else if(!$("input[name='bunewvalue']").val()){
		document.getElementById("bunewvaluediv").innerHTML = '<input name="bunewvalue" type="text" value="" />';
	}
}

function verifyQueryForm(f){
	if(!verifyLeaveForm()) {
		return false;
	}

	const validformat1 = /^\s*[<>]?\s?\d{4}-\d{2}-\d{2}\s*$/;
	const validformat2 = /^\s*\d{4}-\d{2}-\d{2}\s{1,3}-\s{1,3}\d{4}-\d{2}-\d{2}\s*$/;
	const validformat3 = /^\s*\d{4}-\d{2}-\d{2}\s{1,3}to\s{1,3}\d{4}-\d{2}-\d{2}\s*$/;
	const validformat4 = /^\s*>\s?\d{4}-\d{2}-\d{2}\s{1,3}AND\s{1,3}<\s?\d{4}-\d{2}-\d{2}\s*$/i;

	if(f.q_eventdate){
		const edDateStr = f.q_eventdate.value;
		if(edDateStr){
			try{
				if(!validformat1.test(edDateStr) && !validformat2.test(edDateStr) && !validformat3.test(edDateStr) && !validformat4.test(edDateStr)){
					alert("Event date must follow formats: YYYY-MM-DD, YYYY-MM-DD - YYYY-MM-DD, YYYY-MM-DD to YYYY-MM-DD, >YYYY-MM-DD, <YYYY-MM-DD, >YYYY-MM-DD AND <YYYY-MM-DD");
					return false;
				}
			}
			catch(ex){}
		}
	}
	
	if(f.q_datelastmodified){
		const modDateStr = f.q_datelastmodified.value;
		if(modDateStr){
			try{
				if(!validformat1.test(modDateStr) && !validformat2.test(modDateStr) && !validformat3.test(modDateStr) && !validformat4.test(edDateStr)){
					alert("Date last modified must follow formats: YYYY-MM-DD, YYYY-MM-DD - YYYY-MM-DD, YYYY-MM-DD to YYYY-MM-DD, >YYYY-MM-DD, <YYYY-MM-DD, >YYYY-MM-DD AND <YYYY-MM-DD");
					return false;
				}
			}
			catch(ex){}
		}
	}
	if(f.q_dateentered){
		const dateEnteredStr = f.q_dateentered.value;
		if(dateEnteredStr){
			try{
				if(!validformat1.test(dateEnteredStr) && !validformat2.test(dateEnteredStr) && !validformat3.test(dateEnteredStr) && !validformat4.test(edDateStr)){
					alert("Date entered must follow formats: YYYY-MM-DD, YYYY-MM-DD - YYYY-MM-DD, YYYY-MM-DD to YYYY-MM-DD, >YYYY-MM-DD, <YYYY-MM-DD, >YYYY-MM-DD AND <YYYY-MM-DD");
					return false;
				}
			}
			catch(ex){
			}
		}
	}
    if(f.q_customopenparen1){
		let open = 0;
		let closed = 0;
		if(f.q_customopenparen1.value === '(') {
			open++;
		}
		if(f.q_customcloseparen1.value === ')'){
            closed++;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 1 that does not have a corresponding selected open parenthesis.");
                return false;
			}
		}
        if(f.q_customopenparen2.value === '(') {
        	open++;
        }
        if(f.q_customcloseparen2.value === ')'){
            closed++;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 2 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(f.q_customopenparen3.value === '(') {
        	open++;
        }
        if(f.q_customcloseparen3.value === ')'){
            closed++;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 3 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(f.q_customopenparen4.value === '(') {
        	open++;
        }
        if(f.q_customcloseparen4.value === ')'){
            closed++;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 4 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(f.q_customopenparen5.value === '(') {
        	open++;
        }
        if(f.q_customcloseparen5.value === ')'){
            closed++;
            if(closed > open){
                alert("You have selected a closed parenthesis in Custom Field 5 that does not have a corresponding selected open parenthesis.");
                return false;
            }
        }
        if(open > closed){
            alert("You have selected open parenthesis that do not have corresponding selected closed parenthesis in the Custom Fields.");
            return false;
		}
    }

	return true;
}

function resetQueryForm(f){
	f.q_catalognumber.value = "";
	f.q_othercatalognumbers.value = "";
	f.q_recordedby.value = "";
	f.q_recordnumber.value = "";
	f.q_eventdate.value = "";
	f.q_recordenteredby.value = "";
	f.q_dateentered.value = "";
	f.q_datelastmodified.value = "";
	f.q_processingstatus.value = "";
	if(document.getElementById("q_exsiccatiid")){
		f.q_exsiccatiid.value = "";
	}
	f.q_customopenparen1.options[0].selected = true;
    f.q_customfield1.options[0].selected = true;
	f.q_customtype1.options[0].selected = true;
	f.q_customvalue1.value = "";
    f.q_customcloseparen1.options[0].selected = true;
    f.q_customandor2.options[0].selected = true;
    f.q_customopenparen2.options[0].selected = true;
	f.q_customfield2.options[0].selected = true;
	f.q_customtype2.options[0].selected = true;
	f.q_customvalue2.value = "";
    f.q_customcloseparen2.options[0].selected = true;
    f.q_customandor3.options[0].selected = true;
    f.q_customopenparen3.options[0].selected = true;
	f.q_customfield3.options[0].selected = true;
	f.q_customtype3.options[0].selected = true;
	f.q_customvalue3.value = "";
    f.q_customcloseparen3.options[0].selected = true;
    f.q_customandor4.options[0].selected = true;
    f.q_customopenparen4.options[0].selected = true;
    f.q_customfield4.options[0].selected = true;
    f.q_customtype4.options[0].selected = true;
    f.q_customvalue4.value = "";
    f.q_customcloseparen4.options[0].selected = true;
    f.q_customandor5.options[0].selected = true;
    f.q_customopenparen5.options[0].selected = true;
    f.q_customfield5.options[0].selected = true;
    f.q_customtype5.options[0].selected = true;
    f.q_customvalue5.value = "";
    f.q_customcloseparen5.options[0].selected = true;
	f.q_imgonly.checked = false;
	f.q_withoutimg.checked = false;
	f.orderby.value = "";
	f.orderbydir.value = "ASC";
}

function submitBatchUpdate(f){
	const fieldName = f.bufieldname.options[f.bufieldname.selectedIndex].value;
	const oldValue = f.buoldvalue.value;
	const newValue = f.bunewvalue.value;
	let buMatch = 0;
	if(f.bumatch[1].checked) {
		buMatch = 1;
	}
	if(!fieldName){
		alert("Please select a target field name");
		return false;
	}
	if(!oldValue && !newValue){
		alert("Please enter a value in the current or new value fields");
		return false;
	}
	if(oldValue === newValue){
		alert("The values within current and new fields cannot be equal to one another");
		return false;
	}

	$.ajax({
		type: "POST",
		url: "rpc/batchupdateverify.php",
		dataType: "json",
		data: { collid: f.collid.value, fieldname: fieldName, oldvalue: oldValue, bumatch: buMatch, ouid: f.ouid.value }
	}).done(function( retCnt ) {
		if(confirm("You are about to update "+retCnt+" records.\nNote that you won't be able to undo this Replace operation!\nDo you want to continue?")){
			f.submit();
		}
	});
}

function customSelectChanged(targetSelect){
	let sourceObj = document.queryform.q_customfield1;
	let targetObj = document.queryform.q_customtype1;
	if(targetSelect === 2){
		sourceObj = document.queryform.q_customfield2;
		targetObj = document.queryform.q_customtype2;
	}
	else if(targetSelect === 3){
		sourceObj = document.queryform.q_customfield3;
		targetObj = document.queryform.q_customtype3;
	}
    else if(targetSelect === 4){
        sourceObj = document.queryform.q_customfield4;
        targetObj = document.queryform.q_customtype4;
    }
    else if(targetSelect === 5){
        sourceObj = document.queryform.q_customfield5;
        targetObj = document.queryform.q_customtype5;
    }
	if(sourceObj.value === "ocrFragment"){
		targetObj.value = "LIKE";
	}
}

function toggleCustomDiv2(){
	const f = document.queryform;
	f.q_customandor2.options[0].selected = true;
    f.q_customopenparen2.options[0].selected = true;
    f.q_customfield2.options[0].selected = true;
	f.q_customtype2.options[0].selected = true;
	f.q_customvalue2.value = "";
    f.q_customcloseparen2.options[0].selected = true;
    f.q_customandor3.options[0].selected = true;
    f.q_customopenparen3.options[0].selected = true;
	f.q_customfield3.options[0].selected = true;
	f.q_customtype3.options[0].selected = true;
	f.q_customvalue3.value = "";
    f.q_customcloseparen3.options[0].selected = true;
	document.getElementById('customdiv3').style.display = "none";
	toggle('customdiv2');
}

function toggleCustomDiv3(){
	const f = document.queryform;
	f.q_customandor3.options[0].selected = true;
    f.q_customopenparen3.options[0].selected = true;
    f.q_customfield3.options[0].selected = true;
    f.q_customtype3.options[0].selected = true;
    f.q_customvalue3.value = "";
    f.q_customcloseparen3.options[0].selected = true;
    f.q_customandor4.options[0].selected = true;
    f.q_customopenparen4.options[0].selected = true;
    f.q_customfield4.options[0].selected = true;
    f.q_customtype4.options[0].selected = true;
    f.q_customvalue4.value = "";
    f.q_customcloseparen4.options[0].selected = true;
    document.getElementById('customdiv4').style.display = "none";
    toggle('customdiv3');
}

function toggleCustomDiv4(){
	const f = document.queryform;
	f.q_customandor4.options[0].selected = true;
    f.q_customopenparen4.options[0].selected = true;
    f.q_customfield4.options[0].selected = true;
    f.q_customtype4.options[0].selected = true;
    f.q_customvalue4.value = "";
    f.q_customcloseparen4.options[0].selected = true;
    f.q_customandor5.options[0].selected = true;
    f.q_customopenparen5.options[0].selected = true;
    f.q_customfield5.options[0].selected = true;
    f.q_customtype5.options[0].selected = true;
    f.q_customvalue5.value = "";
    f.q_customcloseparen5.options[0].selected = true;
    document.getElementById('customdiv5').style.display = "none";
    toggle('customdiv4');
}

function toggleCustomDiv5(){
	const f = document.queryform;
	f.q_customandor5.options[0].selected = true;
    f.q_customopenparen5.options[0].selected = true;
	f.q_customfield5.options[0].selected = true;
	f.q_customtype5.options[0].selected = true;
	f.q_customvalue5.value = "";
    f.q_customcloseparen5.options[0].selected = true;
	toggle('customdiv5');
}

function toggle(target){
	const ele = document.getElementById(target);
	if(ele){
		if(ele.style.display === "none" || ele.style.display === ""){
			ele.style.display="block";
  		}
	 	else {
	 		ele.style.display="none";
	 	}
	}
	else{
		const divObjs = document.getElementsByTagName("div");
		for (let i = 0; i < divObjs.length; i++) {
			const divObj = divObjs[i];
			if(divObj.getAttribute("class") === target || divObj.getAttribute("className") === target){
				if(divObj.style.display === "none"){
					divObj.style.display="";
				}
			 	else {
			 		divObj.style.display="none";
			 	}
			}
		}
	}
}

function toggleSearch(){
	if(document.getElementById("batchupdatediv")) {
		document.getElementById("batchupdatediv").style.display = "none";
	}
	toggle("querydiv");
}

function toggleBatchUpdate(){
	document.getElementById("querydiv").style.display = "none";
	toggle("batchupdatediv");
}
