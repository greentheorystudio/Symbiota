<?php
include_once(__DIR__ . '/../../config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
?>
<html>
<head>
    <title>Label Content Format Visual Editor</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/labeljsongui.css" type="text/css" rel="stylesheet" />
    <link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
</head>
<body>
<main>
    <div>
        <div id="fields">
            <h4>Fields Available</h4>
            <p style="font-size:0.7em;line-height:1.4em">
                For field definitions please refer to the <a href="https://dwc.tdwg.org/terms/">Darwin Core Quick Reference Guide</a>.
            </p>
            <label for="fields-filter">Filter fields by category:</label>
            <select name="fields-filter" id="fields-filter">
                <option value="all">All</option>
                <option value="specimen">Specimen</option>
                <option value="taxon">Taxon</option>
                <option value="determination">Determination</option>
                <option value="event">Event</option>
                <option value="locality">Locality</option>
            </select>
            <div id="fields-list" class="container"></div>
        </div>
    </div>
    <div>
        <div id="build">
            <div id="build-label">
                <h4 style="color: #212529">Label Content Area</h4>
                <p style="font-size:0.7em;line-height:1.4em">drag, drop & reorder fields here; click to select fields or
                    lines to apply formats (only one item formattable at a time); reorder lines clicking on arrows;
                    remove lines/fields clicking on "x"</p>
                <div id="label-middle">
                    <div class="field-block container" data-delimiter=" ">
                        <span class="material-icons">close</span>
                        <span class="material-icons">keyboard_arrow_up</span><span class="material-icons">keyboard_arrow_down</span>
                    </div>
                </div>
                <button class="btn" onClick="addLine()">Add line</button>
            </div>
        </div>
        <div id="preview">
            <h4>Label preview</h4>
            <p style="font-size:0.7em;line-height:1.4em">content automatically displayed below</p>
            <div id="preview-label"></div>
            <button class="btn" onclick="saveJson()">Set JSON</button>
            <button class="btn" onclick="cancelWindow()">Cancel</button>
            <textarea id="dummy" style="display: none; height: 300px; width: 100%;" data-format-id=""></textarea>
        </div>
    </div>
    <div>
        <div id="controls">
            <div id="field-options" style="display:none;">
                <h4>Field Options</h4>
                <div>
                    <div>
                        <label for="prefix">Prefix:</label>
                        <input type="text" id="fieldPrefix" class="control" data-group="field" disabled>
                    </div>
                    <div>
                        <label for="suffix">Suffix:</label>
                        <input type="text" id="fieldSuffix" class="control" data-group="field" disabled>
                    </div>
                    <div>
                        <label for="font-size">Font Size (px):</label>
                        <input type="text" id="fieldFontSize" class="control" data-group="field" disabled>
                    </div>
                </div>
            </div>
            <div id="field-block-options" style="display:none;">
                <h4>Block Options</h4>
                <div>
                    <label for="line-height">Line Height (px):</label>
                    <input type="text" id="blockLineHeight" class="control" data-group="field-block" disabled>
                </div>
                <div>
                    <label for="space-before">Space Before (px):</label>
                    <input type="text" id="blockSpaceBefore" class="control" data-group="field-block" disabled>
                </div>
                <div>
                    <label for="space-after">Space After (px):</label>
                    <input type="text" id="blockSpaceAfter" class="control" data-group="field-block" disabled>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
<script src="../../js/symb/collections.labeljsongui.js?ver=10"></script>
<script type="text/javascript">
    createFields(fieldProps, fieldListDiv);

    dropdownsArr.forEach((dropObj) => {
        let targetDiv = document.getElementById(`${dropObj.group}-options`);
        let lbl = document.createElement('label');
        lbl.htmlFor = dropObj.id;
        lbl.innerText = dropObj.options[0].text + ':';
        lbl.style = 'display: block;';
        let slct = document.createElement('select');
        slct.dataset.group = dropObj.group;
        slct.classList.add('control');
        slct.id = dropObj.id;
        slct.disabled = true;
        dropObj.options.forEach((choice) => {
            let opt = document.createElement('option');
            opt.value = choice.value;
            opt.innerText = choice.text;
            slct.appendChild(opt);
        });
        targetDiv.appendChild(lbl);
        targetDiv.appendChild(slct);
    });

    formatsArr.forEach((format) => {
        let targetDiv = document.getElementById(`${format.group}-options`);
        let btn = document.createElement('button');
        btn.classList.add('control');
        btn.disabled = true;
        btn.dataset.func = format.func;
        btn.dataset.group = format.group;
        btn.title = format.title;
        if (format.icon !== '') {
            let icon = document.createElement('span');
            icon.classList.add('material-icons');
            icon.innerText = format.icon;
            btn.appendChild(icon);
        }
        else {
            btn.innerText = format.name;
        }
        targetDiv.appendChild(btn);
    });

    refreshLineState();

    fieldsFilter.addEventListener('change', function (e) {
        filterFields(e.target.value);
    });

    draggables.forEach((draggable) => {
        draggable.addEventListener('dragstart', handleDragStart, false);
        draggable.addEventListener('dragover', handleDragOver, false);
        draggable.addEventListener('drop', handleDrop, false);
        draggable.addEventListener('dragend', handleDragEnd, false);
    });

    containers.forEach((container) => {
        container.addEventListener('dragover', (e) => {
            e.preventDefault();
            const dragging = document.querySelector('.dragging');
            dragging !== null ? container.appendChild(dragging) : '';
        });
    });

    labelMid.addEventListener('click', (e) => {
        if (e.target.matches('.material-icons')) {
            if (e.target.innerText === 'keyboard_arrow_up') {
                let first = labelMid.getElementsByClassName('field-block')[0];
                let curr = e.target.parentNode;
                if (curr !== first) {
                    let prev = e.target.parentNode.previousSibling;
                    prev.replaceWith(curr);
                    curr.parentNode.insertBefore(prev, curr.nextSibling);
                }
            }
            else if (e.target.innerText === 'keyboard_arrow_down') {
                let next = e.target.parentNode.nextSibling;
                let curr = e.target.parentNode;
                if (next) {
                    curr.replaceWith(next);
                    next.parentNode.insertBefore(curr, next.nextSibling);
                }
            }
            else if (e.target.innerText === 'close') {
                let line = e.target.parentNode;
                removeLine(line);
            }
            refreshPreview();
        }
        else {
            if (isFormattable(e.target)) {
                let lines = labelMid.querySelectorAll('.field-block');
                lines.forEach((line) => {
                    line.classList.remove('selected');
                });
                let fields = labelMid.querySelectorAll('.draggable');
                fields.forEach((field) => {
                    field.classList.remove('selected');
                });
                e.target.classList.add('selected');
            }
            let selectedItems = build.querySelectorAll('.selected');
            if(selectedItems.length == 1){
                let itemType = '';
                let item = build.querySelector('.selected');
                if (item.matches('.draggable')) {
                    itemType = 'field';
                    activateControls('field-block', false);
                    document.getElementById("field-options").style.display = "block";
                    document.getElementById("field-block-options").style.display = "none";
                }
                else if (item.matches('.field-block')) {
                    itemType = 'field-block';
                    activateControls('field', false);
                    document.getElementById("field-options").style.display = "none";
                    document.getElementById("field-block-options").style.display = "block";
                }
                resetControls();
                activateControls(itemType, true);
                getState(item);
            }
            else {
                return false;
            }
        }
    });

    controlDiv.addEventListener('click', (e) => {
        let formatItems = build.querySelectorAll('.selected');
        let isFormatSelected = toggleSelect(e.target);
        //console.log(isFormatSelected);
        let isButton = e.target.tagName === 'BUTTON';
        let isDropdown = e.target.tagName === 'SELECT';
        if (isButton) {
            toggleStyle(e.target, formatItems, isFormatSelected);
        }
        else if (isDropdown) {
            addReplaceStyle(e.target, formatItems);
        }
    });

    inputs.forEach((input) => {
        input.addEventListener('input', (e) => {
            let formatItem = build.querySelector('.selected');
            updateFieldContent(e.target, formatItem);
            console.log(e.target, formatItem);
            refreshPreview();
        });
    });
</script>
</html>
