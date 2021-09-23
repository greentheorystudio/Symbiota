const fieldProps = [
    {name: 'Dynamic Properties', id: 'dynamicproperties', group: 'record-level'},
    {name: 'Associated Collectors', id: 'associatedcollectors', group: 'occurrence'},
    {name: 'Associated Taxa', id: 'associatedtaxa', group: 'occurrence'},
    {name: 'Behavior', id: 'behavior', group: 'occurrence'},
    {name: 'Catalog Number', id: 'catalognumber', group: 'occurrence'},
    {name: 'Cultivation Status', id: 'cultivationstatus', group: 'occurrence'},
    {name: 'Disposition', id: 'disposition', group: 'occurrence'},
    {name: 'Duplicate Quantity', id: 'duplicatequantity', group: 'occurrence'},
    {name: 'Establishment Means', id: 'establishmentmeans', group: 'occurrence'},
    {name: 'Individual Count', id: 'individualcount', group: 'occurrence'},
    {name: 'Life Stage', id: 'lifeStage', group: 'occurrence'},
    {name: 'Occurrence ID', id: 'occurrenceid', group: 'occurrence'},
    {name: 'Occurrence Remarks', id: 'occurrenceremarks', group: 'occurrence'},
    {name: 'Other Catalog Numbers', id: 'othercatalognumbers', group: 'occurrence'},
    {name: 'Preparations', id: 'preparations', group: 'occurrence'},
    {name: 'Record Number', id: 'recordnumber', group: 'occurrence'},
    {name: 'Recorded By', id: 'recordedby', group: 'occurrence'},
    {name: 'Reproductive Condition', id: 'reproductivecondition', group: 'occurrence'},
    {name: 'Sex', id: 'sex', group: 'occurrence'},
    {name: 'Storage Location', id: 'storagelocation', group: 'occurrence'},
    {name: 'Substrate', id: 'substrate', group: 'occurrence'},
    {name: 'Verbatim Attributes', id: 'verbatimattributes', group: 'occurrence'},
    {name: 'Day', id: 'day', group: 'event'},
    {name: 'Event Date', id: 'eventdate', group: 'event'},
    {name: 'Habitat', id: 'habitat', group: 'event'},
    {name: 'Month', id: 'month', group: 'event'},
    {name: 'Month Name', id: 'monthname', group: 'event'},
    {name: 'Sampling Protocol', id: 'samplingprotocol', group: 'event'},
    {name: 'Verbatim Event Date', id: 'verbatimeventdate', group: 'event'},
    {name: 'Year', id: 'year', group: 'event'},
    {name: 'Coordinate Uncertainty In Meters', id: 'coordinateuncertaintyinmeters', group: 'location'},
    {name: 'Country', id: 'country', group: 'location'},
    {name: 'County', id: 'county', group: 'location'},
    {name: 'Decimal Latitude', id: 'decimallatitude', group: 'location'},
    {name: 'Decimal Longitude', id: 'decimallongitude', group: 'location'},
    {name: 'Geodetic Datum', id: 'geodeticdatum', group: 'location'},
    {name: 'Locality', id: 'locality', group: 'location'},
    {name: 'Maximum Depth In Meters', id: 'maximumdepthinmeters', group: 'location'},
    {name: 'Minimum Depth In Meters', id: 'minimumdepthinmeters', group: 'location'},
    {name: 'Minimum Elevation In Meters', id: 'minimumelevationinmeters', group: 'location'},
    {name: 'Municipality', id: 'municipality', group: 'location'},
    {name: 'State/Province', id: 'stateprovince', group: 'location'},
    {name: 'Verbatim Coordinates', id: 'verbatimcoordinates', group: 'location'},
    {name: 'Verbatim Depth', id: 'verbatimdepth', group: 'location'},
    {name: 'Verbatim Elevation', id: 'verbatimelevation', group: 'location'},
    {name: 'Date Identified', id: 'dateidentified', group: 'identification'},
    {name: 'Identification Qualifier', id: 'identificationqualifier', group: 'identification'},
    {name: 'Identification References', id: 'identificationreferences', group: 'identification'},
    {name: 'Identification Remarks', id: 'identificationremarks', group: 'identification'},
    {name: 'Identified By', id: 'identifiedby', group: 'identification'},
    {name: 'Type Status', id: 'typestatus', group: 'identification'},
    {name: 'Family', id: 'family', group: 'taxon'},
    {name: 'Infraspecific Epithet', id: 'infraspecificepithet', group: 'taxon'},
    {name: 'Parent Author', id: 'parentauthor', group: 'taxon'},
    {name: 'Scientific Name', id: 'sciname', group: 'taxon'},
    {name: 'Scientific Name Authorship', id: 'scientificnameauthorship', group: 'taxon'},
    {name: 'Taxon Rank', id: 'taxonrank', group: 'taxon'},
    {name: 'Taxon Remarks', id: 'taxonremarks', group: 'taxon'}
];

const fieldListDiv = document.getElementById('field-list');
const labelMid = document.getElementById('label-middle');
const preview = document.getElementById('preview-label');
let settingArr = {};
let jsonArr = null;
let dragSrcEl = null;
let currentEditId = null;
let blockID = 0;
let fieldID = 0;

function translateJson(source) {
    let srcLines = source['labelBlocks'];
    if(!srcLines){
        preview.innerText = 'ERROR: Your label format is not translatable. Please adjust your JSON definition and try again, or create a new format from scratch using this visual builder.';
    }
    if(srcLines.length > 0){
        let lineCount = srcLines.length;
        for(i = 0; i < lineCount; i++){
            const keys = Object.keys(srcLines[i]);
            const idStr = 'block-' + blockID;
            for(let k in keys){
                if(keys.hasOwnProperty(k)){
                    if(keys[k] !== 'fields'){
                        if(!settingArr.hasOwnProperty(idStr)){
                            settingArr[idStr] = {};
                        }
                        settingArr[idStr][keys[k]] = srcLines[i][keys[k]];
                    }
                }
            }
            addLine();
        }
    }
    let lbBlocks = labelMid.querySelectorAll('.field-block');
    srcLines.forEach((srcLine, i) => {
        //console.log(i);
        let lbBlock = lbBlocks[i];
        if(lbBlocks.hasOwnProperty(i) && srcLine.hasOwnProperty('fields')){
            let fieldsArr = srcLine.fields;
            if(fieldsArr !== undefined){
                let propsArr = [];
                fieldsArr.forEach((item) => {
                    let props = fieldProps.find((obj) => obj.id === item.field);
                    propsArr.push(props);
                });
                createFields(propsArr, lbBlocks[i]);
                let createdLis = lbBlocks[i].querySelectorAll('.draggable');
                createdLis.forEach((li, j) => {
                    const settings = fieldsArr.find((obj) => obj.field === li.title);
                    const keys = Object.keys(settings);
                    for(let k in keys){
                        if(keys.hasOwnProperty(k)){
                            if(keys[k] !== 'field'){
                                if(!settingArr.hasOwnProperty(li.id)){
                                    settingArr[li.id] = {};
                                }
                                settingArr[li.id][keys[k]] = settings[keys[k]];
                            }
                        }
                    }
                });
            }
        }
    });
    refreshAvailFields();
    refreshPreview();
}

function filterObject(arr, criteria) {
    return arr.filter(function (obj) {
        return Object.keys(criteria).every(function (c) {
            return obj[c] == criteria[c];
        });
    });
}

function removeObject(arr, criteria) {
    return arr.filter(function (obj) {
        return Object.keys(criteria).every(function (c) {
            return obj[c] !== criteria[c];
        });
    });
}

function getCurrFields() {
    let currFields = fieldProps;
    let usedFields = document.querySelectorAll('#label-middle .draggable');
    if(usedFields.length > 0){
        usedFields.forEach((usedField) => {
            currFields = removeObject(currFields, {id: usedField.title});
        });
    }
    return currFields;
}

function filterFields(value) {
    let filteredFields = '';
    if(value === 'all'){
        filteredFields = getCurrFields();
    }
    else{
        filteredFields = filterObject(getCurrFields(), {group: value});
    }
    fieldListDiv.innerHTML = '';
    createFields(filteredFields, fieldListDiv);
}

function refreshAvailFields() {
    let available = getCurrFields();
    fieldListDiv.innerHTML = '';
    let selectedFilter = document.getElementById('fields-filter').value;
    if(selectedFilter === 'all'){
        createFields(available, fieldListDiv);
    }
    else{
        filterFields(selectedFilter);
    }
}

function createFields(arr, target) {
    arr.forEach((field) => {
        const idStr = 'field-' + fieldID;
        let li = document.createElement('li');
        li.innerHTML = field.name;
        li.title = field.id;
        li.id = idStr;
        let closeBtn = document.createElement('span');
        closeBtn.classList.add('block-icons');
        let closeIcon = document.createElement('i');
        closeIcon.setAttribute("style","width:15px;height:15px;");
        closeIcon.setAttribute("class","fas fa-times");
        closeIcon.setAttribute('onclick', 'removeField("'+idStr+'");');
        closeBtn.appendChild(closeIcon);
        li.appendChild(closeBtn);
        li.draggable = 'true';
        li.classList.add('draggable');
        li.dataset.category = field.group;
        li.addEventListener('dragstart', handleDragStart, false);
        li.addEventListener('dragover', handleDragOver, false);
        li.addEventListener('drop', handleDrop, false);
        li.addEventListener('dragend', handleDragEnd, false);
        li.addEventListener('click', (e) => {
            if(e.target.id && e.target.id === idStr && e.target.parentNode.id !== 'field-list'){
                openFieldOptions(idStr);
            }
        });
        target.appendChild(li);
        fieldID++;
    });
}

function addLine() {
    const idStr = 'block-' + blockID;
    let line = document.createElement('div');
    line.setAttribute("id", idStr);
    line.classList.add('field-block', 'container');
    let midBlocks = document.querySelectorAll('#label-middle > .field-block');
    let close = document.createElement('span');
    close.classList.add('block-icons');
    let closeIcon = document.createElement('i');
    closeIcon.setAttribute("style","width:20px;height:20px;");
    closeIcon.setAttribute("class","fas fa-times");
    closeIcon.setAttribute('onclick', 'handleBlockClose("'+idStr+'");');
    close.appendChild(closeIcon);
    line.appendChild(close);
    let up = document.createElement('span');
    up.classList.add('block-icons');
    let upIcon = document.createElement('i');
    upIcon.setAttribute("style","width:20px;height:20px;");
    upIcon.setAttribute("class","fas fa-chevron-up");
    upIcon.setAttribute('onclick', 'handleBlockUp("'+idStr+'");');
    up.appendChild(upIcon);
    line.appendChild(up);
    let down = document.createElement('span');
    down.classList.add('block-icons');
    let downIcon = document.createElement('i');
    downIcon.setAttribute("style","width:20px;height:20px;");
    downIcon.setAttribute("class","fas fa-chevron-down");
    downIcon.setAttribute('onclick', 'handleBlockDown("'+idStr+'");');
    down.appendChild(downIcon);
    line.appendChild(down);
    if(midBlocks.length > 0){
        let lastBlock = midBlocks[midBlocks.length - 1];
        lastBlock.parentNode.insertBefore(line, lastBlock.nextSibling);
    }
    else{
        document.getElementById("label-middle").appendChild(line);
    }
    line.addEventListener('click', (e) => {
        if(e.target.id && e.target.id === idStr){
            openBlockOptions(idStr);
        }
    });
    line.addEventListener('dragover', (e) => {
        e.preventDefault();
        const dragging = document.querySelector('.dragging');
        if(dragging){
            line.appendChild(dragging);
        }
    });
    refreshLineState();
    blockID++;
}

function refreshLineState() {
    let lines = labelMid.querySelectorAll('.field-block');
    if(lines.length > 0){
        let icons = lines[0].querySelectorAll('.block-icons');
        let isSingleLine = lines.length == 1;
        icons.forEach((icon) => {
            if(isSingleLine){
                icon.classList.add('disabled');
            }
            else{
                icon.classList.remove('disabled');
            }
        });
    }
}

function removeLine(line) {
    let lineCount = labelMid.querySelectorAll('.field-block').length;
    lineCount > 1 ? line.remove() : false;
    refreshLineState();
    refreshAvailFields();
}

function removeField(fieldId) {
    document.getElementById(fieldId).remove();
    refreshAvailFields();
}

function refreshPreview() {
    let labelList = [];
    let fieldBlocks = document.querySelectorAll('#build-label .field-block');
    fieldBlocks.forEach((block) => {
        let itemsArr = [];
        let items = block.querySelectorAll('li');
        items.forEach((item) => {
            let itemObj = {};
            let className = Array.from(item.classList).filter(isPrintStyle);
            itemObj.field = item.title;
            itemObj.className = className;
            itemObj.prefix = item.dataset.prefix;
            itemObj.suffix = item.dataset.suffix;
            itemsArr.push(itemObj);
        });
        labelList.push(itemsArr);
        let fieldBlockStyles = Array.from(block.classList).filter(isPrintStyle);
        fieldBlockStyles ? (itemsArr.className = fieldBlockStyles) : '';
    });
    preview.innerHTML = '';
    labelList.forEach((labelItem, blockIdx) => {
        let blockLen = labelItem.length;
        let fieldBlock = document.createElement('div');
        fieldBlock.classList.add('field-block');
        let labelItemStyles = labelItem.className;
        labelItemStyles.forEach((style) => {
            fieldBlock.classList.add(style);
        });
        preview.appendChild(fieldBlock);
        labelItem.forEach((field, fieldIdx) => {
            createPreviewEl(field, fieldBlock);
            let isLast = fieldIdx == blockLen - 1;
            if (!isLast) {
                let preview = document.getElementsByClassName(field.field);
                let delim = document.createElement('span');
                delim.innerText = labelItem.delimiter;
                preview[0].after(delim);
            }
        });
    });

    return labelList;
}

function createPreviewEl(element, parent) {
    let fieldInfo = fieldProps[fieldProps.findIndex((x) => x.id === element.field)];
    let div = document.createElement('div');
    div.innerHTML = fieldInfo.name.split(' ').join('');
    div.classList.add(fieldInfo.id);
    div.classList.add(...element.className);
    parent.appendChild(div);
    let hasPrefix = element.prefix != undefined;
    let hasSuffix = element.suffix != undefined;
    if (hasPrefix) {
        let currText = div.innerText;
        let prefSpan = `<span>${element.prefix}</span>`;
        div.innerHTML = prefSpan + currText;
    }
    if (hasSuffix) {
        let sufSpan = document.createElement('span');
        sufSpan.innerText = element.suffix;
        div.appendChild(sufSpan);
    }
}

function isPrintStyle(className) {
    const functionalStyles = [
        'draggable',
        'selected',
        'field-block',
        'container',
    ];
    return !functionalStyles.includes(className);
}

function loadJson(){
    let currBlocks = labelMid.querySelectorAll('.field-block');
    document.getElementById("label-middle").innerHTML = '';
    blockID = 0;
    let firstBlock = currBlocks[0];
    let currFields = firstBlock.querySelectorAll('.draggable');
    currFields.forEach((currField) => {
        currField.remove();
    });
    let sourceStr = document.getElementById("guijson").value;
    jsonArr = null;
    try{
        jsonArr = JSON.parse(sourceStr);
    }catch(error){
        //console.log(error);
        alert('There is an issue with your JSON format. If your label format is very customized, that could interfere with its correct display.');
    }
    if(jsonArr){
        translateJson(jsonArr);
        refreshLineState();
    }
    else{
        preview.innerText = '';
    }
}

function handleDragStart(e) {
    dragSrcEl = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragOver(e) {
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDrop(e) {
    if(dragSrcEl != this){
        this.parentNode.insertBefore(dragSrcEl, this);
    }
    return false;
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    refreshPreview();
    return false;
}

function openBlockOptions(blockId) {
    currentEditId = blockId;
    document.getElementById(blockId).classList.add('selected');
    setBlockOptionsForm(blockId);
    $('#blockoptions').popup('show');
}

function setBlockOptionsForm(blockId) {
    if(settingArr.hasOwnProperty(blockId)){
        const settings = settingArr[blockId];
        if(settings.hasOwnProperty('blockTextAlign')){
            document.getElementById('blockTextAlign').value = settings['blockTextAlign'];
        }
        if(settings.hasOwnProperty('blockLineHeight')){
            document.getElementById('blockLineHeight').value = settings['blockLineHeight'];
        }
        if(settings.hasOwnProperty('blockSpaceBefore')){
            document.getElementById('blockSpaceBefore').value = settings['blockSpaceBefore'];
        }
        if(settings.hasOwnProperty('blockSpaceAfter')){
            document.getElementById('blockSpaceAfter').value = settings['blockSpaceAfter'];
        }
    }
}

function processBlockOptionsFormChange() {
    const newSettings = {};
    if(document.getElementById('blockTextAlign').value){
        newSettings['blockTextAlign'] = document.getElementById('blockTextAlign').value;
    }
    if(document.getElementById('blockLineHeight').value){
        newSettings['blockLineHeight'] = document.getElementById('blockLineHeight').value;
    }
    if(document.getElementById('blockSpaceBefore').value){
        newSettings['blockSpaceBefore'] = document.getElementById('blockSpaceBefore').value;
    }
    if(document.getElementById('blockSpaceAfter').value){
        newSettings['blockSpaceAfter'] = document.getElementById('blockSpaceAfter').value;
    }
    settingArr[currentEditId] = newSettings;
}

function clearBlockOptionsForm() {
    document.getElementById('blockTextAlign').value = 'left';
    document.getElementById('blockLineHeight').value = '';
    document.getElementById('blockSpaceBefore').value = '';
    document.getElementById('blockSpaceAfter').value = '';
}

function openFieldOptions(fieldId) {
    currentEditId = fieldId;
    document.getElementById(fieldId).classList.add('selected');
    setFieldOptionsForm(fieldId);
    $('#fieldoptions').popup('show');
}

function setFieldOptionsForm(fieldId) {
    if(settingArr.hasOwnProperty(fieldId)){
        const settings = settingArr[fieldId];
        if(settings.hasOwnProperty('fieldPrefix')){
            document.getElementById('fieldPrefix').value = settings['fieldPrefix'];
        }
        if(settings.hasOwnProperty('fieldPrefixBold')){
            document.getElementById('fieldPrefixBold').checked = settings['fieldPrefixBold'];
        }
        if(settings.hasOwnProperty('fieldPrefixItalic')){
            document.getElementById('fieldPrefixItalic').checked = settings['fieldPrefixItalic'];
        }
        if(settings.hasOwnProperty('fieldPrefixUnderline')){
            document.getElementById('fieldPrefixUnderline').checked = settings['fieldPrefixUnderline'];
        }
        if(settings.hasOwnProperty('fieldPrefixUppercase')){
            document.getElementById('fieldPrefixUppercase').checked = settings['fieldPrefixUppercase'];
        }
        if(settings.hasOwnProperty('fieldPrefixFont')){
            document.getElementById('fieldPrefixFont').value = settings['fieldPrefixFont'];
        }
        if(settings.hasOwnProperty('fieldPrefixFontSize')){
            document.getElementById('fieldPrefixFontSize').value = settings['fieldPrefixFontSize'];
        }
        if(settings.hasOwnProperty('fieldSuffix')){
            document.getElementById('fieldSuffix').value = settings['fieldSuffix'];
        }
        if(settings.hasOwnProperty('fieldSuffixBold')){
            document.getElementById('fieldSuffixBold').checked = settings['fieldSuffixBold'];
        }
        if(settings.hasOwnProperty('fieldSuffixItalic')){
            document.getElementById('fieldSuffixItalic').checked = settings['fieldSuffixItalic'];
        }
        if(settings.hasOwnProperty('fieldSuffixUnderline')){
            document.getElementById('fieldSuffixUnderline').checked = settings['fieldSuffixUnderline'];
        }
        if(settings.hasOwnProperty('fieldSuffixUppercase')){
            document.getElementById('fieldSuffixUppercase').checked = settings['fieldSuffixUppercase'];
        }
        if(settings.hasOwnProperty('fieldSuffixFont')){
            document.getElementById('fieldSuffixFont').value = settings['fieldSuffixFont'];
        }
        if(settings.hasOwnProperty('fieldSuffixFontSize')){
            document.getElementById('fieldSuffixFontSize').value = settings['fieldSuffixFontSize'];
        }
        if(settings.hasOwnProperty('fieldBold')){
            document.getElementById('fieldBold').checked = settings['fieldBold'];
        }
        if(settings.hasOwnProperty('fieldItalic')){
            document.getElementById('fieldItalic').checked = settings['fieldItalic'];
        }
        if(settings.hasOwnProperty('fieldUnderline')){
            document.getElementById('fieldUnderline').checked = settings['fieldUnderline'];
        }
        if(settings.hasOwnProperty('fieldUppercase')){
            document.getElementById('fieldUppercase').checked = settings['fieldUppercase'];
        }
        if(settings.hasOwnProperty('fieldFont')){
            document.getElementById('fieldFont').value = settings['fieldFont'];
        }
        if(settings.hasOwnProperty('fieldFontSize')){
            document.getElementById('fieldFontSize').value = settings['fieldFontSize'];
        }
    }
}

function processFieldOptionsFormChange() {
    const newSettings = {};
    if(document.getElementById('fieldPrefix').value){
        newSettings['fieldPrefix'] = document.getElementById('fieldPrefix').value;
        if(document.getElementById('fieldPrefixBold').checked === true){
            newSettings['fieldPrefixBold'] = true;
        }
        if(document.getElementById('fieldPrefixItalic').checked === true){
            newSettings['fieldPrefixItalic'] = true;
        }
        if(document.getElementById('fieldPrefixUnderline').checked === true){
            newSettings['fieldPrefixUnderline'] = true;
        }
        if(document.getElementById('fieldPrefixUppercase').checked === true){
            newSettings['fieldPrefixUppercase'] = true;
        }
        if(document.getElementById('fieldPrefixFont').value) {
            newSettings['fieldPrefixFont'] = document.getElementById('fieldPrefixFont').value;
        }
        if(document.getElementById('fieldPrefixFontSize').value) {
            newSettings['fieldPrefixFontSize'] = document.getElementById('fieldPrefixFontSize').value;
        }
    }
    if(document.getElementById('fieldSuffix').value) {
        newSettings['fieldSuffix'] = document.getElementById('fieldSuffix').value;
        if(document.getElementById('fieldSuffixBold').checked === true){
            newSettings['fieldSuffixBold'] = true;
        }
        if(document.getElementById('fieldSuffixItalic').checked === true){
            newSettings['fieldSuffixItalic'] = true;
        }
        if(document.getElementById('fieldSuffixUnderline').checked === true){
            newSettings['fieldSuffixUnderline'] = true;
        }
        if(document.getElementById('fieldSuffixUppercase').checked === true){
            newSettings['fieldSuffixUppercase'] = true;
        }
        if(document.getElementById('fieldSuffixFont').value) {
            newSettings['fieldSuffixFont'] = document.getElementById('fieldSuffixFont').value;
        }
        if(document.getElementById('fieldSuffixFontSize').value) {
            newSettings['fieldSuffixFontSize'] = document.getElementById('fieldSuffixFontSize').value;
        }
    }
    if(document.getElementById('fieldBold').checked === true){
        newSettings['fieldBold'] = true;
    }
    if(document.getElementById('fieldItalic').checked === true){
        newSettings['fieldItalic'] = true;
    }
    if(document.getElementById('fieldUnderline').checked === true){
        newSettings['fieldUnderline'] = true;
    }
    if(document.getElementById('fieldUppercase').checked === true){
        newSettings['fieldUppercase'] = true;
    }
    if(document.getElementById('fieldFont').value) {
        newSettings['fieldFont'] = document.getElementById('fieldFont').value;
    }
    if(document.getElementById('fieldFontSize').value) {
        newSettings['fieldFontSize'] = document.getElementById('fieldFontSize').value;
    }
    settingArr[currentEditId] = newSettings;
}

function clearFieldOptionsForm() {
    document.getElementById('fieldPrefix').value = '';
    document.getElementById('fieldPrefixBold').checked = false;
    document.getElementById('fieldPrefixItalic').checked = false;
    document.getElementById('fieldPrefixUnderline').checked = false;
    document.getElementById('fieldPrefixUppercase').checked = false;
    document.getElementById('fieldPrefixFont').value = 'Arial';
    document.getElementById('fieldPrefixFontSize').value = '';
    document.getElementById('fieldSuffix').value = '';
    document.getElementById('fieldSuffixBold').checked = false;
    document.getElementById('fieldSuffixItalic').checked = false;
    document.getElementById('fieldSuffixUnderline').checked = false;
    document.getElementById('fieldSuffixUppercase').checked = false;
    document.getElementById('fieldSuffixFont').value = 'Arial';
    document.getElementById('fieldSuffixFontSize').value = '';
    document.getElementById('fieldBold').checked = false;
    document.getElementById('fieldItalic').checked = false;
    document.getElementById('fieldUnderline').checked = false;
    document.getElementById('fieldUppercase').checked = false;
    document.getElementById('fieldFont').value = 'Arial';
    document.getElementById('fieldFontSize').value = '';
}

function handleBlockClose(blockId) {
    const line = document.getElementById(blockId);
    removeLine(line);
    refreshPreview();
}

function handleBlockUp(blockId) {
    const first = labelMid.getElementsByClassName('field-block')[0];
    const curr = document.getElementById(blockId);
    if(curr !== first){
        const prev = curr.previousSibling;
        prev.replaceWith(curr);
        curr.parentNode.insertBefore(prev, curr.nextSibling);
    }
    refreshPreview();
}

function handleBlockDown(blockId) {
    const curr = document.getElementById(blockId);
    const next = curr.nextSibling;
    if(next){
        curr.replaceWith(next);
        next.parentNode.insertBefore(curr, next.nextSibling);
    }
    refreshPreview();
}

function saveJson(){
    let formId = document.getElementById('formid').value;
    let list = refreshPreview();
    if(list[0].length === 0){
        alert('Label format is empty! Please drag some items to the build area before trying again');
    }
    else {
        const newBlockArr = [];
        let fieldBlocks = labelMid.querySelectorAll('.field-block');
        console.log(fieldBlocks.length);
        fieldBlocks.forEach((block) => {
            const newBlockObj = {};
            const newFieldsArr = [];
            const blockId = block.id;
            if(settingArr.hasOwnProperty(blockId)){
                const keys = Object.keys(settingArr[blockId]);
                for(let k in keys){
                    if(keys.hasOwnProperty(k)){
                        newBlockObj[keys[k]] = settingArr[blockId][keys[k]];
                    }
                }
            }

            let items = block.querySelectorAll('li');
            items.forEach((item) => {
                const newItemObj = {};
                const field = item.title;
                const fieldId = item.id;
                if(settingArr.hasOwnProperty(fieldId)){
                    const keys = Object.keys(settingArr[fieldId]);
                    for(let k in keys){
                        if(keys.hasOwnProperty(k)){
                            newItemObj[keys[k]] = settingArr[fieldId][keys[k]];
                        }
                    }
                }
                newItemObj['field'] = field;
                newFieldsArr.push(newItemObj);
            });
            if(newFieldsArr.length > 0){
                newBlockObj['fields'] = newFieldsArr;
            }
            newBlockArr.push(newBlockObj);
        });
        jsonArr['labelBlocks'] = newBlockArr;
        const f = window.opener.document.getElementById(formId);
        f.json.value = JSON.stringify(jsonArr, null, 4);
        window.close();
    }
}

function cancelWindow() {
    window.close();
}
