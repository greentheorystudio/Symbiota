function enableProtectedEditing(id){
    const disabled = document.getElementById(id).disabled;
    if(disabled){
        document.getElementById(id).disabled = false;
    }
    else{
        document.getElementById(id).disabled = true;
    }
}

function formatPath(path){
    if(path.charAt(path.length - 1) === '/'){
        path = path.substring(0, path.length - 1);
    }
    if(path.charAt(0) !== '/'){
        path = '/' + path;
    }
    return path;
}

function processAddConfiguration(){
    const name = document.getElementById('newConfName').value;
    const value = document.getElementById('newConfValue').value;
    if(name && value){
        sendAPIRequest("add",name,value);
    }
    else{
        alert('Please enter both a valid configuration name and a configuration value to add a new configuration.');
    }
}

function processCheckConfigurationChange(configname){
    const checked = document.getElementById(configname).checked;
    if(checked){
        sendAPIRequest("add",configname,1);
    }
    else{
        sendAPIRequest("delete",configname,"");
    }
}

function processClientPathConfigurationChange(configname,oldValue){
    document.getElementById(configname).value = formatPath(document.getElementById(configname).value);
    const configvalue = document.getElementById(configname).value;
    if(configvalue !== ''){
        const http = new XMLHttpRequest();
        const url = "../../api/configurations/configurationValidationController.php";
        let params = 'action=validateClientPath&value='+configvalue;
        console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                if(http.responseText){
                    if(confirm("Do you want to save and activate this change?")){
                        if(oldValue){
                            sendAPIRequest("update",configname,configvalue);
                        }
                        else{
                            sendAPIRequest("add",configname,configvalue);
                        }
                    }
                    else{
                        document.getElementById(configname).value = oldValue;
                    }
                }
                else{
                    alert('The path entered is not a valid URL path to a portal.');
                    document.getElementById(configname).value = oldValue;
                }
            }
        };
        http.send(params);
    }
    else{
        sendAPIRequest("delete",configname,"");
    }
}

function processIntConfigurationChange(configname,oldValue,required){
    const configvalue = Number(document.getElementById(configname).value);
    if(configvalue !== 0){
        if(Number.isInteger(configvalue)){
            if(confirm("Do you want to save and activate this change?")){
                if(oldValue){
                    sendAPIRequest("update",configname,configvalue);
                }
                else{
                    sendAPIRequest("add",configname,configvalue);
                }
            }
            else{
                document.getElementById(configname).value = oldValue;
            }
        }
        else{
            alert('Value can only be whole numbers.');
            document.getElementById(configname).value = oldValue;
        }
    }
    else{
        if(required){
            alert('This value is required.');
            document.getElementById(configname).value = oldValue;
        }
        else{
            if(confirm("Do you want to remove this configuration?")){
                sendAPIRequest("delete",configname,configvalue);
            }
            else{
                document.getElementById(configname).value = oldValue;
            }
        }
    }
}

function processNewConfNameChange(){
    const http = new XMLHttpRequest();
    const url = "../../api/configurations/configurationValidationController.php";
    let newNameValue = document.getElementById('newConfName').value;
    newNameValue = newNameValue.replace(/ /g, "_");
    newNameValue = newNameValue.toUpperCase();
    document.getElementById('newConfName').value = newNameValue;
    let params = 'action=validateNameCore&value='+newNameValue;
    //console.log(url+'?'+params);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            if(http.responseText){
                alert('That Configuration Name is used internally within the software and cannot be set as an additional configuration name. Please enter a different name.');
                document.getElementById('newConfName').value = '';
            }
            else{
                params = 'action=validateNameExisting&value='+newNameValue;
                //console.log(url+'?'+params);
                http.open("POST", url, true);
                http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                http.onreadystatechange = function() {
                    if(http.readyState === 4 && http.status === 200) {
                        if(http.responseText > 0){
                            alert('That Configuration Name is already set and in use within the portal. Please enter a different name.');
                            document.getElementById('newConfName').value = '';
                        }
                    }
                };
                http.send(params);
            }
        }
    };
    http.send(params);
}

function processServerPathConfigurationChange(configname,oldValue){
    document.getElementById(configname).value = formatPath(document.getElementById(configname).value);
    const configvalue = document.getElementById(configname).value;
    if(configvalue !== ''){
        const http = new XMLHttpRequest();
        const url = "../../api/configurations/configurationValidationController.php";
        let params = 'action=validateServerPath&value='+configvalue;
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                if(http.responseText){
                    if(confirm("Do you want to save and activate this change?")){
                        sendAPIRequest("update",configname,configvalue);
                    }
                    else{
                        document.getElementById(configname).value = oldValue;
                    }
                }
                else{
                    alert('The path entered is not a valid path to a portal installation on the server.');
                    document.getElementById(configname).value = oldValue;
                }
            }
        };
        http.send(params);
    }
    else{
        alert('This value is required.');
        document.getElementById(configname).value = oldValue;
    }
}

function processServerWritePathConfigurationChange(configname,oldValue){
    document.getElementById(configname).value = formatPath(document.getElementById(configname).value);
    const configvalue = document.getElementById(configname).value;
    if(configvalue !== ''){
        const http = new XMLHttpRequest();
        const url = "../../api/configurations/configurationValidationController.php";
        let params = 'action=validateServerWritePath&value='+configvalue;
        //console.log(url+'?'+params);
        http.open("POST", url, true);
        http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        http.onreadystatechange = function() {
            if(http.readyState === 4 && http.status === 200) {
                if(http.responseText){
                    if(confirm("Do you want to save and activate this change?")){
                        sendAPIRequest("update",configname,configvalue);
                    }
                    else{
                        document.getElementById(configname).value = oldValue;
                    }
                }
                else{
                    alert('The path entered is not writable on the server.');
                    document.getElementById(configname).value = oldValue;
                }
            }
        };
        http.send(params);
    }
    else{
        alert('This value is required.');
        document.getElementById(configname).value = oldValue;
    }
}

function processTaxonomyRankCheckChange(action){
    const rankArr = [];
    const checkBoxes = document.getElementsByClassName('taxonomy-checkbox');
    for(let i in checkBoxes){
        if(checkBoxes.hasOwnProperty(i) && checkBoxes[i].checked === true){
            rankArr.push(Number(checkBoxes[i].value));
        }
    }
    if(action === 'add'){
        sendAPIRequest('add','TAXONOMIC_RANKS',JSON.stringify(rankArr));
    }
    else if(action === 'update'){
        sendAPIRequest('update','TAXONOMIC_RANKS',JSON.stringify(rankArr),false);
    }
}

function processTextConfigurationChange(configname,oldValue,required){
    const configvalue = document.getElementById(configname).value;
    if(configvalue !== ""){
        if(confirm("Do you want to save and activate this change?")){
            if(oldValue){
                sendAPIRequest("update",configname,configvalue);
            }
            else{
                sendAPIRequest("add",configname,configvalue);
            }
        }
        else{
            document.getElementById(configname).value = oldValue;
        }
    }
    else{
        if(required){
            alert('This value is required.');
            document.getElementById(configname).value = oldValue;
        }
        else{
            if(confirm("Do you want to remove this configuration?")){
                sendAPIRequest("delete",configname,configvalue);
            }
            else{
                document.getElementById(configname).value = oldValue;
            }
        }
    }
}

function processUpdateCss(){
    const http = new XMLHttpRequest();
    const url = "../../api/configurations/configurationModelController.php";
    const params = 'action=updateCss';
    console.log(url+'?'+params);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            location.reload();
        }
    };
    http.send(params);
}

function processUploadFilesizeConfigurationChange(configname,oldValue){
    const configvalue = Number(document.getElementById(configname).value);
    if(configvalue !== 0){
        if(Number.isInteger(configvalue) && configvalue <= maxPostSize && configvalue <= maxUploadSize){
            if(confirm("Do you want to save and activate this change?")){
                sendAPIRequest("update",configname,configvalue);
            }
            else{
                document.getElementById(configname).value = oldValue;
            }
        }
        else{
            alert('Value can only be whole numbers and it must be less than or equal to the upload_max_filesize and post_max_size php settings on the server. The upload_max_filesize setting is currently set to '+maxUploadSize+'M, and the post_max_size setting is currently set to '+maxPostSize+'M on the server.');
            document.getElementById(configname).value = oldValue;
        }
    }
    else{
        alert('This value is required.');
        document.getElementById(configname).value = oldValue;
    }
}

function sendAPIRequest(action,configname,configvalue,reload = true){
    configvalue = configvalue.toString().replaceAll('+','%2B');
    const data = {};
    const http = new XMLHttpRequest();
    const url = "../../api/configurations/configurationModelController.php";
    data[configname] = configvalue;
    const jsonData = JSON.stringify(data);
    const params = 'action='+action+'&data='+jsonData;
    //console.log(url+'?'+params);
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function() {
        if(http.readyState === 4 && http.status === 200) {
            if(reload){
                location.reload();
            }
        }
    };
    http.send(params);
}

function showPassword(id){
    document.getElementById(id).type = 'text';
}
