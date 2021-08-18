function addVoucherToCl(occidIn,clidIn,tidIn){
    $.ajax({
        type: "POST",
        url: "rpc/addvoucher.php",
        data: { occid: occidIn, clid: clidIn, tid: tidIn }
    }).done(function( msg ) {
        if(msg === "1"){
            alert("Success! Voucher added to checklist.");
        }
        else{
            alert(msg);
        }
    });
}

function openIndPU(occId,clid){
    let wWidth = 900;
    if(document.getElementById('innertext')){
        wWidth = document.getElementById('innertext').offsetWidth*1.05;
    }
    else if(document.body.offsetWidth){
        wWidth = document.body.offsetWidth*0.9;
    }
    if(wWidth > 1000) {
        wWidth = 1000;
    }
    let newWindow = window.open('individual/index.php?occid=' + occId + '&clid=' + clid, 'indspec' + occId, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=700,left=20,top=20');
    if (newWindow.opener == null) {
        newWindow.opener = self;
    }
    return false;
}

function toggleFieldBox(target){
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

function selectAllDataset(cbElem){
    let boxesChecked = true;
    if(!cbElem.checked) {
        boxesChecked = false;
    }
    const f = cbElem.form;
    for(let i = 0;i<f.length;i++){
        if(f.elements[i].name === "occid[]") {
            f.elements[i].checked = boxesChecked;
        }
    }
}

function hasSelectedOccid(f){
    let isSelected = false;
    for(let h = 0;h<f.length;h++){
        if(f.elements[h].name === "occid[]" && f.elements[h].checked){
            isSelected = true;
            break;
        }
    }
    if(!isSelected){
        alert('Please select at least one occurrence to be added to the dataset');
        return false;
    }
    return true;
}

function displayDatasetTools(){
    $('.dataset-div').toggle();
    document.getElementById("dataset-tools").scrollIntoView({behavior: 'smooth'});
}

function validateOccurListForm(f){
    if(f.targetdatasetid.value === ""){
        alert('Please select a dataset to append occurrences, or select Create New Dataset');
        return false;
    }
    document.getElementById("dsstarrjson").value = JSON.stringify(stArr);
    return true;
}
