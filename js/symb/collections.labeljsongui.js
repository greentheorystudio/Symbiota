const fieldProps = [
    {name: 'Dynamic Properties', id: 'dynamicproperties', group: 'record-level'},
    {name: 'Associated Collectors', id: 'associatedcollectors', group: 'occurrence'},
    {name: 'Associated Taxa', id: 'associatedtaxa', group: 'occurrence'},
    {name: 'BARCODE [Catalog Number]', id: 'barcode-catalognumber', group: 'occurrence'},
    {name: 'BARCODE [Occurrence ID]', id: 'barcode-occurrenceid', group: 'occurrence'},
    {name: 'BARCODE [Other Catalog Numbers]', id: 'barcode-othercatalognumbers', group: 'occurrence'},
    {name: 'Behavior', id: 'behavior', group: 'occurrence'},
    {name: 'Catalog Number', id: 'catalognumber', group: 'occurrence'},
    {name: 'Cultivation Status', id: 'cultivationstatus', group: 'occurrence'},
    {name: 'Disposition', id: 'disposition', group: 'occurrence'},
    {name: 'Duplicate Quantity', id: 'duplicatequantity', group: 'occurrence'},
    {name: 'Establishment Means', id: 'establishmentmeans', group: 'occurrence'},
    {name: 'Individual Count', id: 'individualcount', group: 'occurrence'},
    {name: 'Label Project', id: 'labelproject', group: 'occurrence'},
    {name: 'Life Stage', id: 'lifeStage', group: 'occurrence'},
    {name: 'Occurrence ID', id: 'occurrenceid', group: 'occurrence'},
    {name: 'Occurrence Remarks', id: 'occurrenceremarks', group: 'occurrence'},
    {name: 'Other Catalog Numbers', id: 'othercatalognumbers', group: 'occurrence'},
    {name: 'Preparations', id: 'preparations', group: 'occurrence'},
    {name: 'QR CODE', id: 'qr-code', group: 'occurrence'},
    {name: 'Record Number', id: 'recordnumber', group: 'occurrence'},
    {name: 'Recorded By', id: 'recordedby', group: 'occurrence'},
    {name: 'Reproductive Condition', id: 'reproductivecondition', group: 'occurrence'},
    {name: 'Sex', id: 'sex', group: 'occurrence'},
    {name: 'Storage Location', id: 'storagelocation', group: 'occurrence'},
    {name: 'Substrate', id: 'substrate', group: 'occurrence'},
    {name: 'Verbatim Attributes', id: 'verbatimattributes', group: 'occurrence'},
    {name: 'Day', id: 'day', group: 'event'},
    {name: 'Event Date', id: 'eventdate', group: 'event'},
    {name: 'Field Notes', id: 'fieldnotes', group: 'event'},
    {name: 'Field Number', id: 'fieldnumber', group: 'event'},
    {name: 'Habitat', id: 'habitat', group: 'event'},
    {name: 'Month', id: 'month', group: 'event'},
    {name: 'Month Name', id: 'monthname', group: 'event'},
    {name: 'Sampling Effort', id: 'samplingeffort', group: 'event'},
    {name: 'Sampling Protocol', id: 'samplingprotocol', group: 'event'},
    {name: 'Verbatim Event Date', id: 'verbatimeventdate', group: 'event'},
    {name: 'Year', id: 'year', group: 'event'},
    {name: 'Coordinate Uncertainty In Meters', id: 'coordinateuncertaintyinmeters', group: 'location'},
    {name: 'Country', id: 'country', group: 'location'},
    {name: 'County', id: 'county', group: 'location'},
    {name: 'Decimal Latitude', id: 'decimallatitude', group: 'location'},
    {name: 'Decimal Longitude', id: 'decimallongitude', group: 'location'},
    {name: 'Geodetic Datum', id: 'geodeticdatum', group: 'location'},
    {name: 'Georeference Protocol', id: 'georeferenceprotocol', group: 'location'},
    {name: 'Georeference Remarks', id: 'georeferenceremarks', group: 'location'},
    {name: 'Georeference Sources', id: 'georeferencesources', group: 'location'},
    {name: 'Georeferenced By', id: 'georeferencedby', group: 'location'},
    {name: 'Locality', id: 'locality', group: 'location'},
    {name: 'Location ID', id: 'locationid', group: 'location'},
    {name: 'Location Remarks', id: 'locationremarks', group: 'location'},
    {name: 'Maximum Depth In Meters', id: 'maximumdepthinmeters', group: 'location'},
    {name: 'Minimum Depth In Meters', id: 'minimumdepthinmeters', group: 'location'},
    {name: 'Maximum Elevation In Meters', id: 'maximumelevationinmeters', group: 'location'},
    {name: 'Minimum Elevation In Meters', id: 'minimumelevationinmeters', group: 'location'},
    {name: 'Municipality', id: 'municipality', group: 'location'},
    {name: 'State/Province', id: 'stateprovince', group: 'location'},
    {name: 'Verbatim Coordinates', id: 'verbatimcoordinates', group: 'location'},
    {name: 'Verbatim Depth', id: 'verbatimdepth', group: 'location'},
    {name: 'Verbatim Elevation', id: 'verbatimelevation', group: 'location'},
    {name: 'Water Body', id: 'waterbody', group: 'location'},
    {name: 'Date Identified', id: 'dateidentified', group: 'identification'},
    {name: 'Identification Qualifier', id: 'identificationqualifier', group: 'identification'},
    {name: 'Identification References', id: 'identificationreferences', group: 'identification'},
    {name: 'Identification Remarks', id: 'identificationremarks', group: 'identification'},
    {name: 'Identified By', id: 'identifiedby', group: 'identification'},
    {name: 'Type Status', id: 'typestatus', group: 'identification'},
    {name: 'Family', id: 'family', group: 'taxon'},
    {name: 'Genus', id: 'genus', group: 'taxon'},
    {name: 'Infraspecific Epithet', id: 'infraspecificepithet', group: 'taxon'},
    {name: 'Infraspecific Epithet Authorship', id: 'infraspecificepithetauthorship', group: 'taxon'},
    {name: 'Scientific Name', id: 'sciname', group: 'taxon'},
    {name: 'Scientific Name Authorship', id: 'scientificnameauthorship', group: 'taxon'},
    {name: 'Specific Epithet', id: 'specificepithet', group: 'taxon'},
    {name: 'Specific Epithet Authorship', id: 'specificepithetauthorship', group: 'taxon'},
    {name: 'Taxon Rank', id: 'taxonrank', group: 'taxon'},
    {name: 'Taxon Remarks', id: 'taxonremarks', group: 'taxon'}
];

const cssFontFamilies = {};
cssFontFamilies['Arial'] = 'Arial, Helvetica, sans-serif';
cssFontFamilies['Brush Script MT'] = '"Brush Script MT", cursive';
cssFontFamilies['Courier New'] = '"Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace';
cssFontFamilies['Garamond'] = 'Garamond, Baskerville, "Baskerville Old Face", "Hoefler Text", "Times New Roman", serif';
cssFontFamilies['Georgia'] = 'Georgia, Times, "Times New Roman", serif';
cssFontFamilies['Helvetica'] = '"Helvetica Neue", Helvetica, Arial, sans-serif';
cssFontFamilies['Tahoma'] = 'Tahoma, Verdana, Segoe, sans-serif';
cssFontFamilies['Times New Roman'] = '"Times New Roman", Times, serif';
cssFontFamilies['Trebuchet'] = '"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Tahoma, sans-serif';
cssFontFamilies['Verdana'] = 'Verdana, Geneva, sans-serif';

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
        document.getElementById("label-middle").innerHTML = '';
        let lineCount = srcLines.length;
        for(i = 0; i < lineCount; i++){
            const keys = Object.keys(srcLines[i]);
            const idStr = 'block-' + blockID;
            let displayLine = false;
            for(let k in keys){
                if(keys.hasOwnProperty(k)){
                    if(keys[k] === 'blockDisplayLine'){
                        displayLine = true;
                    }
                    if(keys[k] !== 'fields'){
                        if(!settingArr.hasOwnProperty(idStr)){
                            settingArr[idStr] = {};
                        }
                        settingArr[idStr][keys[k]] = srcLines[i][keys[k]];
                    }
                }
            }
            addLine(displayLine);
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
        if(field.id.startsWith('barcode-')){
            li.addEventListener('click', (e) => {
                if(e.target.id && e.target.id === idStr && e.target.parentNode.id !== 'field-list'){
                    openBarcodeOptions(idStr);
                }
            });
        }
        else if(field.id.startsWith('qr-')){
            li.addEventListener('click', (e) => {
                if(e.target.id && e.target.id === idStr && e.target.parentNode.id !== 'field-list'){
                    openQRCodeOptions(idStr);
                }
            });
        }
        else{
            li.addEventListener('click', (e) => {
                if(e.target.id && e.target.id === idStr && e.target.parentNode.id !== 'field-list'){
                    openFieldOptions(idStr);
                }
            });
        }
        target.appendChild(li);
        fieldID++;
    });
}

function addLine(displayLine = false) {
    const idStr = 'block-' + blockID;
    let line = document.createElement('div');
    line.setAttribute("id", idStr);
    line.classList.add('field-block');
    if(!displayLine){
        line.classList.add('container');
    }
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
    if(displayLine){
        const hrId = 'block' + idStr + 'hr';
        const hrElement = document.createElement('hr');
        hrElement.setAttribute("style","width:80%;height:4px;float:right;");
        hrElement.setAttribute("id",hrId);
        line.appendChild(hrElement);
    }
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
        if(line.classList.contains('container')){
            e.preventDefault();
            const dragging = document.querySelector('.dragging');
            if(dragging){
                line.appendChild(dragging);
            }
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
    preview.innerHTML = '';
    const layout = jsonArr['pageLayout'];
    const defaultFont = jsonArr.hasOwnProperty('defaultFont') ? cssFontFamilies[jsonArr['defaultFont']] : 'Arial, Helvetica, sans-serif';
    const defaultFontSize = jsonArr.hasOwnProperty('defaultFontSize') ? Number(jsonArr['defaultFontSize']) : 12;
    let labelWidthText = '';
    if(layout === 'packet'){
        labelWidthText = 'width:520px;';
    }
    if(Number(layout) === 1){
        labelWidthText = 'width:720px;';
    }
    if(Number(layout) === 2){
        labelWidthText = 'width:348px;';
    }
    if(Number(layout) === 3){
        labelWidthText = 'width:182px;';
    }
    if(Number(layout) === 4){
        labelWidthText = 'width:84px;';
    }
    const labelDiv = document.createElement('div');
    labelDiv.setAttribute("style",labelWidthText + "margin:15px auto;");
    if(layout === 'packet'){
        const packetFirstHr = document.createElement('hr');
        packetFirstHr.setAttribute("style","border-top: 1px dotted black;margin-top:285px;width:500px;");
        labelDiv.appendChild(packetFirstHr);
        const packetSecondHr = document.createElement('hr');
        packetSecondHr.setAttribute("style","border-top: 1px dotted black;margin-top:355px;margin-bottom:10px;width:500px;");
        labelDiv.appendChild(packetSecondHr);
    }
    if(jsonArr.hasOwnProperty('headerPrefix') || jsonArr.hasOwnProperty('headerMidText') || jsonArr.hasOwnProperty('headerSuffix')){
        let headerStr = '';
        let headerStyleStr = labelWidthText + "clear:both;";
        const headerMidTextVal = jsonArr.hasOwnProperty('headerMidText') ? Number(jsonArr['headerMidText']) : 0;
        headerStr += jsonArr.hasOwnProperty('headerPrefix') ? jsonArr['headerPrefix'] : '';
        if(headerMidTextVal === 1){
            headerStr += '[Country]';
        }
        if(headerMidTextVal === 2){
            headerStr += '[State/Province]';
        }
        if(headerMidTextVal === 3){
            headerStr += '[County]';
        }
        if(headerMidTextVal === 4){
            headerStr += '[Family]';
        }
        headerStr += jsonArr.hasOwnProperty('headerSuffix') ? jsonArr['headerSuffix'] : '';
        headerStyleStr += jsonArr.hasOwnProperty('headerBold') ? 'font-weight:bold;' : '';
        headerStyleStr += jsonArr.hasOwnProperty('headerItalic') ? 'font-style:italic;' : '';
        headerStyleStr += jsonArr.hasOwnProperty('headerUnderline') ? 'text-decoration:underline;' : '';
        headerStyleStr += jsonArr.hasOwnProperty('headerUppercase') ? 'text-transform:uppercase;' : '';
        headerStyleStr += jsonArr.hasOwnProperty('headerTextAlign') ? 'text-align:' + jsonArr['headerTextAlign'] + ';' : '';
        headerStyleStr += 'font-family:' + (jsonArr.hasOwnProperty('headerFont') ? cssFontFamilies[jsonArr['headerFont']] : defaultFont) + ';';
        headerStyleStr += 'font-size:' + (jsonArr.hasOwnProperty('headerFontSize') ? jsonArr['headerFontSize'] : defaultFontSize) + ';';
        const headerDiv = document.createElement('div');
        headerDiv.setAttribute("style",headerStyleStr);
        headerDiv.innerHTML = headerStr;
        labelDiv.appendChild(headerDiv);
        if(jsonArr.hasOwnProperty('headerBottomMargin')){
            const headerBottomDiv = document.createElement('div');
            headerBottomDiv.setAttribute("style",labelWidthText + "height:" + jsonArr['headerBottomMargin'] + 'px;clear:both;');
            labelDiv.appendChild(headerBottomDiv);
        }
    }
    let fieldBlocks = labelMid.querySelectorAll('.field-block');
    fieldBlocks.forEach((block) => {
        const blockId = block.id;
        let items = block.querySelectorAll('li');
        if((settingArr.hasOwnProperty(blockId) && settingArr[blockId].hasOwnProperty('blockDisplayLine')) || items.length > 0){
            const blockSettings = settingArr.hasOwnProperty(blockId) ? settingArr[blockId] : {};
            let blockStyleStr = labelWidthText + "clear:both;";
            if(blockSettings.hasOwnProperty('blockTopMargin')){
                const blockTopMarginDiv = document.createElement('div');
                blockTopMarginDiv.setAttribute("style",labelWidthText + "height:" + blockSettings['blockTopMargin'] + 'px;clear:both;');
                labelDiv.appendChild(blockTopMarginDiv);
            }
            if(blockSettings.hasOwnProperty('blockDisplayLine')){
                let topBorderStr = '';
                topBorderStr += blockSettings.hasOwnProperty('blockDisplayLineHeight') ? ' ' + blockSettings['blockDisplayLineHeight'] + 'px' : ' 1px';
                if(blockSettings.hasOwnProperty('blockDisplayLineStyle') && blockSettings['blockDisplayLineHeight'] === 'dash'){
                    topBorderStr += ' dashed';
                }
                else if(blockSettings.hasOwnProperty('blockDisplayLineStyle') && blockSettings['blockDisplayLineHeight'] === 'dot'){
                    topBorderStr += ' dotted';
                }
                else{
                    topBorderStr += ' solid';
                }
                const lineStyleStr = labelWidthText + 'border-top:' + topBorderStr + ' black;';
                const blockDiv = document.createElement('div');
                blockDiv.setAttribute("style",blockStyleStr);
                const hrElement = document.createElement('hr');
                hrElement.setAttribute("style",lineStyleStr);
                blockDiv.appendChild(hrElement);
                labelDiv.appendChild(blockDiv);
            }
            else{
                blockStyleStr += "display:flex;flex-wrap:wrap;";
                blockStyleStr += blockSettings['blockTextAlign'] === 'left' ? 'justify-content:flex-start;text-align:left;' : '';
                blockStyleStr += blockSettings['blockTextAlign'] === 'center' ? 'justify-content:center;text-align:center;' : '';
                blockStyleStr += blockSettings['blockTextAlign'] === 'right' ? 'justify-content:flex-end;text-align:right;' : '';
                blockStyleStr += blockSettings.hasOwnProperty('blockLineHeight') ? 'line-height:' + blockSettings['blockLineHeight'] + 'px;' : '';
                blockStyleStr += blockSettings.hasOwnProperty('blockLeftMargin') ? 'margin-left:' + blockSettings['blockLeftMargin'] + 'px;' : '';
                blockStyleStr += blockSettings.hasOwnProperty('blockRightMargin') ? 'margin-right:' + blockSettings['blockRightMargin'] + 'px;' : '';
                const blockDiv = document.createElement('div');
                blockDiv.setAttribute("style",blockStyleStr);
                items.forEach((item) => {
                    const field = item.title;
                    const fieldId = item.id;
                    const prop = fieldProps.find((obj) => obj.id === field);
                    const fieldSettings = settingArr.hasOwnProperty(fieldId) ? settingArr[fieldId] : {};
                    if(field.startsWith("barcode-") || field.startsWith("qr-")){
                        const barcodeSpan = document.createElement('span');
                        barcodeSpan.innerHTML = '[' + prop.name + ']';
                        blockDiv.appendChild(barcodeSpan);
                    }
                    else{
                        if(fieldSettings.hasOwnProperty('fieldPrefix')){
                            let prefixStyleStr = '';
                            prefixStyleStr += fieldSettings.hasOwnProperty('fieldPrefixBold') ? 'font-weight:bold;' : '';
                            prefixStyleStr += fieldSettings.hasOwnProperty('fieldPrefixItalic') ? 'font-style:italic;' : '';
                            prefixStyleStr += fieldSettings.hasOwnProperty('fieldPrefixUnderline') ? 'text-decoration:underline;' : '';
                            prefixStyleStr += fieldSettings.hasOwnProperty('fieldPrefixUppercase') ? 'text-transform:uppercase;' : '';
                            prefixStyleStr += 'font-family:' + (fieldSettings.hasOwnProperty('fieldPrefixFont') ? cssFontFamilies[fieldSettings['fieldPrefixFont']] : defaultFont) + ';';
                            prefixStyleStr += 'font-size:' + (fieldSettings.hasOwnProperty('fieldPrefixFontSize') ? fieldSettings['fieldPrefixFontSize'] : defaultFontSize) + ';';
                            const prefixSpan = document.createElement('span');
                            prefixSpan.setAttribute("style",prefixStyleStr);
                            prefixSpan.innerHTML = fieldSettings['fieldPrefix'].replace(" ", "&nbsp;");
                            blockDiv.appendChild(prefixSpan);
                        }
                        let fieldStyleStr = '';
                        fieldStyleStr += fieldSettings.hasOwnProperty('fieldBold') ? 'font-weight:bold;' : '';
                        fieldStyleStr += fieldSettings.hasOwnProperty('fieldItalic') ? 'font-style:italic;' : '';
                        fieldStyleStr += fieldSettings.hasOwnProperty('fieldUnderline') ? 'text-decoration:underline;' : '';
                        fieldStyleStr += fieldSettings.hasOwnProperty('fieldUppercase') ? 'text-transform:uppercase;' : '';
                        fieldStyleStr += 'font-family:' + (fieldSettings.hasOwnProperty('fieldFont') ? cssFontFamilies[fieldSettings['fieldFont']] : defaultFont) + ';';
                        fieldStyleStr += 'font-size:' + (fieldSettings.hasOwnProperty('fieldFontSize') ? fieldSettings['fieldFontSize'] : defaultFontSize) + ';';
                        const fieldSpan = document.createElement('span');
                        fieldSpan.setAttribute("style",fieldStyleStr);
                        fieldSpan.innerHTML = '[' + prop.name + ']';
                        blockDiv.appendChild(fieldSpan);
                        if(fieldSettings.hasOwnProperty('fieldSuffix')){
                            let suffixStyleStr = '';
                            suffixStyleStr += fieldSettings.hasOwnProperty('fieldSuffixBold') ? 'font-weight:bold;' : '';
                            suffixStyleStr += fieldSettings.hasOwnProperty('fieldSuffixItalic') ? 'font-style:italic;' : '';
                            suffixStyleStr += fieldSettings.hasOwnProperty('fieldSuffixUnderline') ? 'text-decoration:underline;' : '';
                            suffixStyleStr += fieldSettings.hasOwnProperty('fieldSuffixUppercase') ? 'text-transform:uppercase;' : '';
                            suffixStyleStr += 'font-family:' + (fieldSettings.hasOwnProperty('fieldSuffixFont') ? cssFontFamilies[fieldSettings['fieldSuffixFont']] : defaultFont) + ';';
                            suffixStyleStr += 'font-size:' + (fieldSettings.hasOwnProperty('fieldSuffixFontSize') ? fieldSettings['fieldSuffixFontSize'] : defaultFontSize) + ';';
                            const suffixSpan = document.createElement('span');
                            suffixSpan.setAttribute("style",suffixStyleStr);
                            suffixSpan.innerHTML = fieldSettings['fieldSuffix'].replace(" ", "&nbsp;");
                            blockDiv.appendChild(suffixSpan);
                        }
                    }
                });
                labelDiv.appendChild(blockDiv);
                if(blockSettings.hasOwnProperty('blockBottomMargin')){
                    const blockBottomMarginDiv = document.createElement('div');
                    blockBottomMarginDiv.setAttribute("style",labelWidthText + "height:" + blockSettings['blockBottomMargin'] + 'px;clear:both;');
                    labelDiv.appendChild(blockBottomMarginDiv);
                }
            }
        }
    });
    if(jsonArr.hasOwnProperty('footerText')){
        if(jsonArr.hasOwnProperty('footerTopMargin')){
            const footerTopDiv = document.createElement('div');
            footerTopDiv.setAttribute("style",labelWidthText + "height:" + jsonArr['footerTopMargin'] + 'px;clear:both;');
            labelDiv.appendChild(footerTopDiv);
        }
        let footerStyleStr = labelWidthText + "clear:both;";
        footerStyleStr += jsonArr.hasOwnProperty('footerBold') ? 'font-weight:bold;' : '';
        footerStyleStr += jsonArr.hasOwnProperty('footerItalic') ? 'font-style:italic;' : '';
        footerStyleStr += jsonArr.hasOwnProperty('footerUnderline') ? 'text-decoration:underline;' : '';
        footerStyleStr += jsonArr.hasOwnProperty('footerUppercase') ? 'text-transform:uppercase;' : '';
        footerStyleStr += jsonArr.hasOwnProperty('footerTextAlign') ? 'text-align:' + jsonArr['headerTextAlign'] + ';' : '';
        footerStyleStr += 'font-family:' + (jsonArr.hasOwnProperty('footerFont') ? cssFontFamilies[jsonArr['footerFont']] : defaultFont) + ';';
        footerStyleStr += 'font-size:' + (jsonArr.hasOwnProperty('footerFontSize') ? jsonArr['footerFontSize'] : defaultFontSize) + ';';
        const footerDiv = document.createElement('div');
        footerDiv.setAttribute("style",footerStyleStr);
        footerDiv.innerHTML = jsonArr['footerText'];
        labelDiv.appendChild(footerDiv);
    }
    preview.appendChild(labelDiv);
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
}

function handleDrop(e) {
    if(dragSrcEl != this){
        this.parentNode.insertBefore(dragSrcEl, this);
    }
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    refreshPreview();
}

function openBlockOptions(blockId) {
    currentEditId = blockId;
    if(!blockFieldCheck(blockId)){
        document.getElementById('blockoptions').style.height = '320px';
        document.getElementById('blockLineOptions').style.display = 'block';
    }
    document.getElementById(blockId).classList.add('selected');
    setBlockOptionsForm(blockId);
    $('#blockoptions').popup('show');
}

function setBlockLineDisplay() {
    const blockId = currentEditId;
    const displayLine = document.getElementById('blockDisplayLine').checked;
    const hrId = 'block' + blockId + 'hr';
    if(displayLine){
        document.getElementById(blockId).classList.remove('container');
        const hrElement = document.createElement('hr');
        hrElement.setAttribute("style","width:80%;height:4px;float:right;");
        hrElement.setAttribute("id",hrId);
        document.getElementById(blockId).appendChild(hrElement);
    }
    else{
        document.getElementById(blockId).classList.add('container');
        const hrElement = document.getElementById(hrId);
        hrElement.parentNode.removeChild(hrElement);
    }
}

function blockFieldCheck(blockId) {
    let items = document.getElementById(blockId).querySelectorAll('li');
    return items.length > 0;
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
        if(settings.hasOwnProperty('blockTopMargin')){
            document.getElementById('blockTopMargin').value = settings['blockTopMargin'];
        }
        if(settings.hasOwnProperty('blockBottomMargin')){
            document.getElementById('blockBottomMargin').value = settings['blockBottomMargin'];
        }
        if(settings.hasOwnProperty('blockLeftMargin')){
            document.getElementById('blockLeftMargin').value = settings['blockLeftMargin'];
        }
        if(settings.hasOwnProperty('blockRightMargin')){
            document.getElementById('blockRightMargin').value = settings['blockRightMargin'];
        }
        if(settings.hasOwnProperty('blockDisplayLine')){
            document.getElementById('blockDisplayLine').checked = settings['blockDisplayLine'];
        }
        if(settings.hasOwnProperty('blockDisplayLineStyle')){
            document.getElementById('blockDisplayLineStyle').value = settings['blockDisplayLineStyle'];
        }
        if(settings.hasOwnProperty('blockDisplayLineHeight')){
            document.getElementById('blockDisplayLineHeight').value = settings['blockDisplayLineHeight'];
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
    if(document.getElementById('blockTopMargin').value){
        newSettings['blockTopMargin'] = document.getElementById('blockTopMargin').value;
    }
    if(document.getElementById('blockBottomMargin').value){
        newSettings['blockBottomMargin'] = document.getElementById('blockBottomMargin').value;
    }
    if(document.getElementById('blockLeftMargin').value){
        newSettings['blockLeftMargin'] = document.getElementById('blockLeftMargin').value;
    }
    if(document.getElementById('blockRightMargin').value){
        newSettings['blockRightMargin'] = document.getElementById('blockRightMargin').value;
    }
    if(document.getElementById('blockDisplayLine').checked === true){
        newSettings['blockDisplayLine'] = true;
        if(document.getElementById('blockDisplayLineStyle').value){
            newSettings['blockDisplayLineStyle'] = document.getElementById('blockDisplayLineStyle').value;
        }
        if(document.getElementById('blockDisplayLineHeight').value){
            newSettings['blockDisplayLineHeight'] = document.getElementById('blockDisplayLineHeight').value;
        }
    }
    settingArr[currentEditId] = newSettings;
    refreshPreview();
}

function clearBlockOptionsForm() {
    document.getElementById('blockTextAlign').value = 'left';
    document.getElementById('blockLineHeight').value = '';
    document.getElementById('blockTopMargin').value = '';
    document.getElementById('blockBottomMargin').value = '';
    document.getElementById('blockLeftMargin').value = '';
    document.getElementById('blockRightMargin').value = '';
    document.getElementById('blockDisplayLine').checked = false;
    document.getElementById('blockDisplayLineStyle').value = 'solid';
    document.getElementById('blockDisplayLineHeight').value = '';
    document.getElementById('blockoptions').style.height = '250px';
    document.getElementById('blockLineOptions').style.display = 'none';
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
    refreshPreview();
}

function clearFieldOptionsForm() {
    document.getElementById('fieldPrefix').value = '';
    document.getElementById('fieldPrefixBold').checked = false;
    document.getElementById('fieldPrefixItalic').checked = false;
    document.getElementById('fieldPrefixUnderline').checked = false;
    document.getElementById('fieldPrefixUppercase').checked = false;
    document.getElementById('fieldPrefixFont').value = '';
    document.getElementById('fieldPrefixFontSize').value = '';
    document.getElementById('fieldSuffix').value = '';
    document.getElementById('fieldSuffixBold').checked = false;
    document.getElementById('fieldSuffixItalic').checked = false;
    document.getElementById('fieldSuffixUnderline').checked = false;
    document.getElementById('fieldSuffixUppercase').checked = false;
    document.getElementById('fieldSuffixFont').value = '';
    document.getElementById('fieldSuffixFontSize').value = '';
    document.getElementById('fieldBold').checked = false;
    document.getElementById('fieldItalic').checked = false;
    document.getElementById('fieldUnderline').checked = false;
    document.getElementById('fieldUppercase').checked = false;
    document.getElementById('fieldFont').value = '';
    document.getElementById('fieldFontSize').value = '';
}

function openBarcodeOptions(fieldId) {
    currentEditId = fieldId;
    document.getElementById(fieldId).classList.add('selected');
    setBarcodeOptionsForm(fieldId);
    $('#barcodeoptions').popup('show');
}

function setBarcodeOptionsForm(blockId) {
    if(settingArr.hasOwnProperty(blockId)){
        const settings = settingArr[blockId];
        if(settings.hasOwnProperty('barcodeHeight')){
            document.getElementById('barcodeHeight').value = settings['barcodeHeight'];
        }
    }
}

function processBarcodeOptionsFormChange() {
    const newSettings = {};
    if(document.getElementById('barcodeHeight').value){
        newSettings['barcodeHeight'] = document.getElementById('barcodeHeight').value;
    }
    settingArr[currentEditId] = newSettings;
    refreshPreview();
}

function clearBarcodeOptionsForm() {
    document.getElementById('barcodeHeight').value = '';
}

function openQRCodeOptions(fieldId) {
    currentEditId = fieldId;
    document.getElementById(fieldId).classList.add('selected');
    setQRCodeOptionsForm(fieldId);
    $('#qrcodeoptions').popup('show');
}

function setQRCodeOptionsForm(blockId) {
    if(settingArr.hasOwnProperty(blockId)){
        const settings = settingArr[blockId];
        if(settings.hasOwnProperty('qrcodeSize')){
            document.getElementById('qrcodeSize').value = settings['qrcodeSize'];
        }
    }
}

function processQRCodeOptionsFormChange() {
    const newSettings = {};
    if(document.getElementById('qrcodeSize').value){
        newSettings['qrcodeSize'] = document.getElementById('qrcodeSize').value;
    }
    settingArr[currentEditId] = newSettings;
    refreshPreview();
}

function clearQRCodeOptionsForm() {
    document.getElementById('qrcodeSize').style.display = '';
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

function cleanContentBlocks(){
    let fieldBlocks = labelMid.querySelectorAll('.field-block');
    fieldBlocks.forEach((block) => {
        const blockId = block.id;
        let items = block.querySelectorAll('li');
        if(items.length === 0 && (!settingArr.hasOwnProperty(blockId) || !settingArr[blockId].hasOwnProperty('blockDisplayLine'))){
            handleBlockClose(blockId);
        }
    });
}

function saveJson(){
    cleanContentBlocks();
    let formId = document.getElementById('formid').value;
    let testList = labelMid.querySelectorAll('li');
    if(testList.length === 0){
        alert('Label format is empty! Please drag some items to the build area before trying again');
    }
    else {
        const newBlockArr = [];
        let fieldBlocks = labelMid.querySelectorAll('.field-block');
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
