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
        sendAPIRequest("addConfigurationArr",name,value);
    }
    else{
        alert('Please enter both a valid configuration name and a configuration value to add a new configuration.');
    }
}

function processCheckConfigurationChange(configname){
    const checked = document.getElementById(configname).checked;
    if(checked){
        sendAPIRequest("addConfigurationArr",configname,1);
    }
    else{
        sendAPIRequest("deleteConfigurationArr",configname,"");
    }
}

function processClientPathConfigurationChange(configname,oldValue){
    document.getElementById(configname).value = formatPath(document.getElementById(configname).value);
    const configvalue = document.getElementById(configname).value;
    if(configvalue !== ''){
        const formData = new FormData();
        formData.append('value', configvalue);
        formData.append('action', 'validateClientPath');
        fetch(configurationsApiUrl, {
            method: 'POST',
            body: formData
        })
        .then((response) => {
            return response.ok ? response.text() : null;
        })
        .then((res) => {
            if(Number(res) === 1){
                if(confirm("Do you want to save and activate this change?")){
                    if(oldValue){
                        sendAPIRequest("updateConfigurationValueArr",configname,configvalue);
                    }
                    else{
                        sendAPIRequest("addConfigurationArr",configname,configvalue);
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
        });
    }
    else{
        sendAPIRequest("deleteConfigurationArr",configname,"");
    }
}

function processIntConfigurationChange(configname,oldValue,required){
    const configvalue = Number(document.getElementById(configname).value);
    if(configvalue !== 0){
        if(Number.isInteger(configvalue)){
            if(confirm("Do you want to save and activate this change?")){
                if(oldValue){
                    sendAPIRequest("updateConfigurationValueArr",configname,configvalue);
                }
                else{
                    sendAPIRequest("addConfigurationArr",configname,configvalue);
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
                sendAPIRequest("deleteConfigurationArr",configname,configvalue);
            }
            else{
                document.getElementById(configname).value = oldValue;
            }
        }
    }
}

function processNewConfNameChange(){
    let newNameValue = document.getElementById('newConfName').value;
    newNameValue = newNameValue.replace(/ /g, "_");
    newNameValue = newNameValue.toUpperCase();
    document.getElementById('newConfName').value = newNameValue;
    const formData = new FormData();
    formData.append('value', newNameValue);
    formData.append('action', 'validateNameCore');
    fetch(configurationsApiUrl, {
        method: 'POST',
        body: formData
    })
    .then((response) => {
        return response.ok ? response.text() : null;
    })
    .then((res) => {
        if(Number(res) === 1){
            alert('That Configuration Name is used internally within the software and cannot be set as an additional configuration name. Please enter a different name.');
            document.getElementById('newConfName').value = '';
        }
        else{
            const formData = new FormData();
            formData.append('value', newNameValue);
            formData.append('action', 'validateNameExisting');
            fetch(configurationsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) > 0){
                    alert('That Configuration Name is already set and in use within the portal. Please enter a different name.');
                    document.getElementById('newConfName').value = '';
                }
            });
        }
    });
}

function processServerPathConfigurationChange(configname,oldValue){
    document.getElementById(configname).value = formatPath(document.getElementById(configname).value);
    const configvalue = document.getElementById(configname).value;
    if(configvalue !== ''){
        const formData = new FormData();
        formData.append('value', configvalue);
        formData.append('action', 'validateServerPath');
        fetch(configurationsApiUrl, {
            method: 'POST',
            body: formData
        })
        .then((response) => {
            return response.ok ? response.text() : null;
        })
        .then((res) => {
            if(Number(res) === 1){
                if(confirm("Do you want to save and activate this change?")){
                    sendAPIRequest("updateConfigurationValueArr",configname,configvalue);
                }
                else{
                    document.getElementById(configname).value = oldValue;
                }
            }
            else{
                alert('The path entered is not a valid path to a portal installation on the server.');
                document.getElementById(configname).value = oldValue;
            }
        });
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
        const formData = new FormData();
        formData.append('value', configvalue);
        formData.append('action', 'validateServerWritePath');
        fetch(configurationsApiUrl, {
            method: 'POST',
            body: formData
        })
        .then((response) => {
            return response.ok ? response.text() : null;
        })
        .then((res) => {
            if(Number(res) === 1){
                if(confirm("Do you want to save and activate this change?")){
                    sendAPIRequest("updateConfigurationValueArr",configname,configvalue);
                }
                else{
                    document.getElementById(configname).value = oldValue;
                }
            }
            else{
                alert('The path entered is not writable on the server.');
                document.getElementById(configname).value = oldValue;
            }
        });
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
        sendAPIRequest('addConfigurationArr','TAXONOMIC_RANKS',JSON.stringify(rankArr));
    }
    else if(action === 'update'){
        sendAPIRequest('updateConfigurationValueArr','TAXONOMIC_RANKS',JSON.stringify(rankArr),false);
    }
}

function processTextConfigurationChange(configname,oldValue,required){
    const configvalue = document.getElementById(configname).value;
    if(configvalue !== ""){
        if(confirm("Do you want to save and activate this change?")){
            if(oldValue){
                sendAPIRequest("updateConfigurationValueArr",configname,configvalue);
            }
            else{
                sendAPIRequest("addConfigurationArr",configname,configvalue);
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
                sendAPIRequest("deleteConfigurationArr",configname,configvalue);
            }
            else{
                document.getElementById(configname).value = oldValue;
            }
        }
    }
}

function processUpdateCss(){
    const formData = new FormData();
    formData.append('action', 'updateCss');
    fetch(configurationsApiUrl, {
        method: 'POST',
        body: formData
    })
    .then((response) => {
        return response.ok ? response.text() : null;
    })
    .then((res) => {
        if(Number(res) === 1){
            location.reload();
        }
    });
}

function processUploadFilesizeConfigurationChange(configname,oldValue){
    const configvalue = Number(document.getElementById(configname).value);
    if(configvalue !== 0){
        if(Number.isInteger(configvalue) && configvalue <= maxPostSize && configvalue <= maxUploadSize){
            if(confirm("Do you want to save and activate this change?")){
                sendAPIRequest("updateConfigurationValueArr",configname,configvalue);
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
    data[configname] = configvalue;
    const jsonData = JSON.stringify(data);
    const formData = new FormData();
    formData.append('data', jsonData);
    formData.append('action', action);
    fetch(configurationsApiUrl, {
        method: 'POST',
        body: formData
    })
    .then((response) => {
        return response.ok ? response.text() : null;
    })
    .then((res) => {
        if(Number(res) === 1 && reload){
            location.reload();
        }
    });
}

function showPassword(id){
    document.getElementById(id).type = 'text';
}
