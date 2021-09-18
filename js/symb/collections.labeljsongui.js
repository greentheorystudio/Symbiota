const fieldProps = [
    {
        block: 'labelBlock',
        name: 'Occurrence ID',
        id: 'occurrenceid',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Catalog Number',
        id: 'catalognumber',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Other Catalog Numbers',
        id: 'othercatalognumbers',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Family',
        id: 'family',
        group: 'taxon'
    },
    {
        block: 'labelBlock',
        name: 'Scientific Name',
        id: 'sciname',
        group: 'taxon',
    },
    {
        block: 'labelBlock',
        name: 'Taxon Rank',
        id: 'taxonrank',
        group: 'taxon'
    },
    {
        block: 'labelBlock',
        name: 'Infraspecific Epithet',
        id: 'infraspecificepithet',
        group: 'taxon',
    },
    {
        block: 'labelBlock',
        name: 'Scientific Name Authorship',
        id: 'scientificnameauthorship',
        group: 'taxon',
    },
    {
        block: 'labelBlock',
        name: 'Parent Author',
        id: 'parentauthor',
        group: 'taxon',
    },
    {
        block: 'labelBlock',
        name: 'Identified By',
        id: 'identifiedby',
        group: 'determination',
    },
    {
        block: 'labelBlock',
        name: 'Date Identified',
        id: 'dateidentified',
        group: 'determination',
    },
    {
        block: 'labelBlock',
        name: 'Identification References',
        id: 'identificationreferences',
        group: 'determination',
    },
    {
        block: 'labelBlock',
        name: 'Identification Remarks',
        id: 'identificationremarks',
        group: 'determination',
    },
    {
        block: 'labelBlock',
        name: 'Taxon Remarks',
        id: 'taxonremarks',
        group: 'determination',
    },
    {
        block: 'labelBlock',
        name: 'Identification Qualifier',
        id: 'identificationqualifier',
        group: 'determination',
    },
    {
        block: 'labelBlock',
        name: 'Type Status',
        id: 'typestatus',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Recorded By',
        id: 'recordedby',
        group: 'event',
    },
    {
        block: 'labelBlock',
        name: 'Record Number',
        id: 'recordnumber',
        group: 'event',
    },
    {
        block: 'labelBlock',
        name: 'Associated Collectors',
        id: 'associatedcollectors',
        group: 'event',
    },
    {
        block: 'labelBlock',
        name: 'Event Date',
        id: 'eventdate',
        group: 'event'
    },
    {
        block: 'labelBlock',
        name: 'Year',
        id: 'year',
        group: 'event'
    },
    {
        block: 'labelBlock',
        name: 'Month',
        id: 'month',
        group: 'event'
    },
    {
        block: 'labelBlock',
        name: 'Month Name',
        id: 'monthname',
        group: 'event'
    },
    {
        block: 'labelBlock',
        name: 'Day',
        id: 'day',
        group: 'event'
    },
    {
        block: 'labelBlock',
        name: 'Verbatim Event Date',
        id: 'verbatimeventdate',
        group: 'event',
    },
    {
        block: 'labelBlock',
        name: 'Habitat',
        id: 'habitat',
        group: 'event'
    },
    {
        block: 'labelBlock',
        name: 'Substrate',
        id: 'substrate',
        group: 'event'
    },
    {
        block: 'labelBlock',
        name: 'Occurrence Remarks',
        id: 'occurrenceremarks',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Associated Taxa',
        id: 'associatedtaxa',
        group: 'taxon',
    },
    {
        block: 'labelBlock',
        name: 'Dynamic Properties',
        id: 'dynamicproperties',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Verbatim Attributes',
        id: 'verbatimattributes',
        group: 'event',
    },
    {
        block: 'labelBlock',
        name: 'Behavior',
        id: 'behavior',
        group: 'specimen'
    },
    {
        block: 'labelBlock',
        name: 'Reproductive Condition',
        id: 'reproductivecondition',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Cultivation Status',
        id: 'cultivationstatus',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Establishment Means',
        id: 'establishmentmeans',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Life Stage',
        id: 'lifeStage',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Sex',
        id: 'sex',
        group: 'specimen'
    },
    {
        block: 'labelBlock',
        name: 'Individual Count',
        id: 'individualcount',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Sampling Protocol',
        id: 'samplingprotocol',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Preparations',
        id: 'preparations',
        group: 'specimen',
    },
    {
        block: 'labelBlock',
        name: 'Country',
        id: 'country',
        group: 'locality'
    },
    {
        block: 'labelBlock',
        name: 'State/Province',
        id: 'stateprovince',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'County',
        id: 'county',
        group: 'locality'
    },
    {
        block: 'labelBlock',
        name: 'Municipality',
        id: 'municipality',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Locality',
        id: 'locality',
        group: 'locality'
    },
    {
        block: 'labelBlock',
        name: 'Decimal Latitude',
        id: 'decimallatitude',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Decimal Longitude',
        id: 'decimallongitude',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Geodetic Datum',
        id: 'geodeticdatum',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Coordinate Uncertainty In Meters',
        id: 'coordinateuncertaintyinmeters',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Verbatim Coordinates',
        id: 'verbatimcoordinates',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Elevation In Meters',
        id: 'elevationinmeters',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Verbatim Elevation',
        id: 'verbatimelevation',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Minimum Depth In Meters',
        id: 'minimumdepthinmeters',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Maximum Depth In Meters',
        id: 'maximumdepthinmeters',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Verbatim Depth',
        id: 'verbatimdepth',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Disposition',
        id: 'disposition',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Storage Location',
        id: 'storagelocation',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Duplicate Quantity',
        id: 'duplicatequantity',
        group: 'locality',
    },
    {
        block: 'labelBlock',
        name: 'Date Last Modified',
        id: 'datelastmodified',
        group: 'event',
    },
];

const formatsArr = [
    {
        group: 'field',
        func: 'bold',
        icon: 'format_bold',
        title: 'Bold'
    },
    {
        group: 'field',
        func: 'italic',
        icon: 'format_italic',
        title: 'Italic'
    },
    {
        group: 'field',
        func: 'underline',
        icon: 'format_underline',
        title: 'Underline',
    },
    {
        group: 'field',
        func: 'uppercase',
        icon: 'format_uppercase',
        title: 'Uppercase',
    }
];

const dropdownsArr = [
    {
        id: 'font-family',
        name: 'font-family',
        group: 'field',
        options: [
            {value: 'Arial', text: 'Arial (sans-serif)'},
            {value: 'Brush Script MT', text: 'Brush Script MT (cursive)'},
            {value: 'Courier New', text: 'Courier New (monospace)'},
            {value: 'Garamond', text: 'Garamond (serif)'},
            {value: 'Georgia', text: 'Georgia (serif)'},
            {value: 'Helvetica', text: 'Helvetica (sans-serif)'},
            {value: 'Tahoma', text: 'Tahoma (sans-serif)'},
            {value: 'Times New Roman', text: 'Times New Roman (serif)'},
            {value: 'Trebuchet', text: 'Trebuchet (sans-serif)'},
            {value: 'Verdana', text: 'Verdana (sans-serif)'},
        ],
    },
    {
        id: 'text-align',
        name: 'text-align',
        group: 'field-block',
        options: [
            {value: 'left', text: 'Left'},
            {value: 'center', text: 'Center'},
            {value: 'right', text: 'Right'},
        ],
    }
];

const dummy = document.getElementById('dummy');
const fieldDiv = document.getElementById('fields');
const fieldListDiv = document.getElementById('fields-list');
const controlDiv = document.getElementById('controls');
const fieldsFilter = document.getElementById('fields-filter');
const labelMid = document.getElementById('label-middle');
const containers = document.querySelectorAll('.container');
const draggables = document.querySelectorAll('.draggable');
const build = document.getElementById('build-label');
const preview = document.getElementById('preview-label');
const controls = document.querySelectorAll('.control');
const inputs = document.querySelectorAll('input');
const overlay = document.getElementById('instructions');
let dragSrcEl = null;

function translateJson(source) {
    let srcLines = source;
    if(!srcLines){
        preview.innerText = 'ERROR: Your label format is not translatable. Please adjust your JSON definition and try again, or create a new format from scratch using this visual builder.';
    }
    let lineCount = srcLines.length;
    for (i = 0; i < lineCount - 1; i++) {
        addLine();
    }
    let lbBlocks = labelMid.querySelectorAll('.field-block');
    srcLines.forEach((srcLine, i) => {
        console.log(i);
        let lbBlock = lbBlocks[i];
        /*srcLine.delimiter !== undefined
            ? (lbBlock.dataset.delimiter = srcLine.delimiter)
            : '';
        srcLine.className !== undefined
            ? (lbBlock.className = lbBlock.className + ' ' + srcLine.className)
            : '';*/
        let fieldsArr = srcLine.fields;
        if (fieldsArr !== undefined) {
            let propsArr = [];
            fieldsArr.forEach(({field, className}) => {
                let props = fieldProps.find((obj) => obj.id === field);
                propsArr.push(props);
            });
            createFields(propsArr, lbBlocks[i]);
        }
        else {
            preview.innerText = 'Error';
        }
        let createdLis = lbBlocks[i].querySelectorAll('.draggable');
        createdLis.forEach((li, j) => {
            let srcFieldsArr = srcLines[i].fields;
            let srcPropsArr = srcFieldsArr[j];
            let fieldId = srcPropsArr.field;
            let prefix = srcPropsArr.fieldPrefix;
            let suffix = srcPropsArr.fieldSuffix;
            if (li.id === fieldId) {
                prefix !== undefined ? (li.dataset.fieldPrefix = prefix) : '';
                suffix !== undefined ? (li.dataset.fieldSuffix = suffix) : '';
            }
        });
    });
    refreshAvailFields();
    refreshPreview();
}

function openOverlay() {
    overlay.classList.remove('hidden');
}

function closeOverlay() {
    overlay.classList.add('hidden');
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
    if (usedFields.length > 0) {
        usedFields.forEach((usedField) => {
            currFields = removeObject(currFields, {id: usedField.id});
        });
    }
    return currFields;
}

function filterFields(value) {
    let filteredFields = '';
    value === 'all'
        ? (filteredFields = getCurrFields())
        : (filteredFields = filterObject(getCurrFields(), {group: value}));
    fieldListDiv.innerHTML = '';
    createFields(filteredFields, fieldListDiv);
}

function refreshAvailFields() {
    let available = getCurrFields();
    fieldListDiv.innerHTML = '';
    let selectedFilter = fieldsFilter.value;
    selectedFilter != 'all'
        ? filterFields(selectedFilter)
        : createFields(available, fieldListDiv);
}

function createFields(arr, target) {
    arr.forEach((field) => {
        let li = document.createElement('li');
        li.innerHTML = field.name;
        li.id = field.id;
        if (field.block === 'labelBlock') {
            let closeBtn = document.createElement('span');
            closeBtn.classList.add('material-icons');
            closeBtn.innerText = 'cancel';
            closeBtn.addEventListener('click', removeField, false);
            li.appendChild(closeBtn);
            li.draggable = 'true';
            li.classList.add('draggable');
            li.dataset.category = field.group;
            li.addEventListener('dragstart', handleDragStart, false);
            li.addEventListener('dragover', handleDragOver, false);
            li.addEventListener('drop', handleDrop, false);
            li.addEventListener('dragend', handleDragEnd, false);
            target.appendChild(li);
        }
    });
}

function addLine() {
    let line = document.createElement('div');
    line.classList.add('field-block', 'container');
    let midBlocks = document.querySelectorAll('#label-middle > .field-block');
    let close = document.createElement('span');
    close.classList.add('material-icons');
    close.innerText = 'close';
    line.appendChild(close);
    let up = document.createElement('span');
    up.classList.add('material-icons');
    up.innerText = 'keyboard_arrow_up';
    line.appendChild(up);
    let down = document.createElement('span');
    down.classList.add('material-icons');
    down.innerText = 'keyboard_arrow_down';
    line.appendChild(down);
    let lastBlock = midBlocks[midBlocks.length - 1];
    lastBlock.parentNode.insertBefore(line, lastBlock.nextSibling);
    line.addEventListener('dragover', (e) => {
        e.preventDefault();
        const dragging = document.querySelector('.dragging');
        dragging !== null ? line.appendChild(dragging) : '';
    });
    refreshLineState();
}

function refreshLineState() {
    let lines = labelMid.querySelectorAll('.field-block');
    let icons = lines[0].querySelectorAll('.material-icons');
    let isSingleLine = lines.length == 1;
    icons.forEach((icon) => {
        isSingleLine
            ? icon.classList.add('disabled')
            : icon.classList.remove('disabled');
    });
}

function removeLine(line) {
    let lineCount = labelMid.querySelectorAll('.field-block').length;
    lineCount > 1 ? line.remove() : false;
    refreshLineState();
    refreshAvailFields();
}

function removeField(field) {
    field.target.parentNode.remove();
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
            itemObj.field = item.id;
            itemObj.className = className;
            itemObj.prefix = item.dataset.prefix;
            itemObj.suffix = item.dataset.suffix;
            itemsArr.push(itemObj);
        });
        labelList.push(itemsArr);
        let fieldBlockStyles = Array.from(block.classList).filter(isPrintStyle);
        fieldBlockStyles ? (itemsArr.className = fieldBlockStyles) : '';
        let fieldBlockDelim = block.dataset.delimiter;
        fieldBlockDelim == undefined
            ? (itemsArr.delimiter = ' ')
            : (itemsArr.delimiter = fieldBlockDelim);
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

function isFormattable(element) {
    if(element.classList.contains('field-block') || element.classList.contains('draggable')){
        return true;
    }
    else {
        return false;
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

function generateJson(list) {
    let wrapper = {
        labelBlocks: []
    };
    let labelBlocks = [];
    Object.keys(list).forEach((index) => {
        let fieldBlockObj = {};
        let fieldItem = list[index];
        fieldItem.map((prop) => {
            prop.className.length > 0
                ? (prop.className = prop.className.join(' '))
                : delete prop.className;
        });
        fieldBlockObj.fieldBlock = fieldItem;
        let fieldBlockDelim = fieldItem.delimiter;
        fieldBlockDelim !== undefined
            ? (fieldBlockObj.delimiter = fieldBlockDelim)
            : '';
        let fieldBlockStyles = fieldItem.className;
        fieldBlockStyles.length > 0
            ? (fieldBlockObj.className = fieldItem.className.join(' '))
            : delete fieldBlockObj.className;
        labelBlocks.push(fieldBlockObj);
    });
    wrapper.labelBlocks = labelBlocks;
    let json = JSON.stringify(wrapper, null, 2);
    return json;
}

function loadJson(){
    let currBlocks = labelMid.querySelectorAll('.field-block');
    let numBlocks = currBlocks.length;
    if(numBlocks > 1){
        for(i = 1; i < numBlocks; i++){
            removeLine(currBlocks[i]);
        }
    }
    let firstBlock = currBlocks[0];
    let currFields = firstBlock.querySelectorAll('.draggable');
    currFields.forEach((currField) => {
        currField.remove();
    });
    let sourceStr = dummy.value.replace(/'/g, '"');
    sourceJson = false;
    try{
        sourceJson = JSON.parse(sourceStr);
    }catch(error){
        //console.log(error);
        window.alert(
            'There is an issue with your JSON format. If your label format is very customized, that could interfere with its correct display.'
        );
    }
    console.log(sourceJson);
    if(sourceJson){
        translateJson(sourceJson);
        refreshLineState();
    }
    else{
        preview.innerText = '';
    }
}

function toggleSelect(element) {
    element.classList.toggle('selected');
    let isSelected = element.classList.contains('selected');
    return isSelected;
}

function activateControls(filter, bool) {
    let filtered = document.querySelectorAll(`[data-group=${filter}]`);
    filtered.forEach((control) => {
        bool ? (control.disabled = false) : (control.disabled = true);
    });
}

function deactivateControls() {
    controls.forEach((control) => {
        control.disabled = true;
    });
}

function getState(item) {
    let delimiter = item.dataset.delimiter;
    if (delimiter) {
        let delimiterInput = document.getElementById('delimiter');
        delimiterInput.value = delimiter;
    }
    let formatList = Array.from(item.classList);
    printableList = formatList.filter(isPrintStyle);
    if (printableList.length > 0) {
        printableList.forEach((formatItem) => {
            let strArr = formatItem.split('-');
            let str = '';
            if(strArr.length == 3){
                str = strArr[0] + '-' + strArr[1];
            }
            else{
                str = strArr[0];
            }
            dropdownsArr.forEach((dropdown) => {
                let isDropdownStyle = str === dropdown.id;
                if (isDropdownStyle) {
                    let selDropdown = document.getElementById(str);
                    selDropdown.value = formatItem;
                }
            });
            controls.forEach((control) => {
                if (formatItem === control.dataset.func) {
                    control.classList.add('selected');
                }
            });
        });
    }

    let hasPrefix = item.dataset.prefix != null;
    let prefixInput = document.getElementById('prefix');
    hasPrefix ? (prefixInput.value = item.dataset.prefix) : '';
    let hasSuffix = item.dataset.suffix != null;
    let suffixInput = document.getElementById('suffix');
    hasSuffix ? (suffixInput.value = item.dataset.suffix) : '';
}

function toggleStyle(control, selectedItems, bool) {
    selectedItems.forEach((item) => {
        if (item.classList.contains('selected')) {
            if(bool){
                item.classList.add(control.dataset.func);
            }
            else{
                item.classList.remove(control.dataset.func);
            }
        }
        else {
            return false;
        }
        refreshPreview();
    });
}

function addReplaceStyle(dropdown, selectedItems) {
    dropdown.addEventListener('input', function () {
        selectedItems.forEach((item) => {
            let option = dropdown.value;
            if (option !== '') {
                let group = new RegExp(`${dropdown.id}-*`);
                let hasGroup = item.className.split(' ').some(function (c) {
                    return group.test(c);
                });
                if (item.classList.contains('selected')) {
                    if (!hasGroup) {
                        item.classList.add(option);
                        console.log(`added ${option} to ${item.id}`);
                    }
                    else {
                        item.classList.forEach((className) => {
                            if (className.startsWith(dropdown.id)) {
                                item.classList.remove(className);
                            }
                        });
                        item.classList.add(option);
                    }
                }
            }
        });
    });
    refreshPreview();
}

function resetControls() {
    controls.forEach((control) => {
        let isDropdown = control.tagName === 'SELECT';
        isDropdown ? (control.value = '') : '';
        control.classList.remove('selected');
        let isInput = control.tagName === 'INPUT';
        isInput ? (control.value = '') : '';
    });
}

function updateFieldContent(content, item) {
    let option = content.id;
    item.setAttribute('data-' + option, content.value);
    console.log(content, item);
}

function handleDragStart(e) {
    dragSrcEl = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    if (dragSrcEl != this) {
        this.parentNode.insertBefore(dragSrcEl, this);
    }
    return false;
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    refreshPreview();
    return false;
}

function saveJson(){
    let formatId = dummy.dataset.formatId;
    let formatTextArea = window.opener.document.querySelector(formatId);
    let list = refreshPreview();
    let isEmpty = list[0].length == 0;
    let message = '';
    if(isEmpty){
        alert(
            'Label format is empty! Please drag some items to the build area before trying again'
        );
    } else {
        let json = generateJson(refreshPreview());
        dummy.value = json;
        formatTextArea.value = json;
        window.close();
    }
}

function cancelWindow() {
    window.close();
}
