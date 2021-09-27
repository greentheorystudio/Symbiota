<?php
include_once(__DIR__ . '/../../config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Label Content Format Visual Editor</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/bootstrap.css" type="text/css" rel="stylesheet" />
    <link href="../../css/jquery.mobile-1.4.0.min.css?ver=20210817" type="text/css" rel="stylesheet" />
    <link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <style>
        #fields-filter {
            margin-bottom: .25em;
            width: 100%;
        }

        #field-options > div {
            display: inline-block;
            margin: 1em 0;
        }

        #field-list {
            overflow-y: scroll;
        }

        #field-list .block-icons {
            display: none;
        }

        #build-label {
            background-color: #C8C8C8;
            grid-column: 1/2;
            padding: .5em;
            margin-top: 1em;
        }

        #build-label li .block-icons {
            font-size: 12px;
            padding-left: 6px;
        }

        #build-label > li span.block-icons:hover {
            background-color: none !important;
        }

        #build-label .delimiter {
            height: 1em;
        }

        #label-middle {
            margin-top: 10px;
        }

        #label-middle > .field-block {
            border: 2px dashed black;
            min-height: 2em;
            line-height: 10px;
            text-align: left !important;
            margin: 10px 0;
            padding: 5px;
        }

        #label-middle .draggable {
            font-family: inherit !important;
            font-size: normal !important;
            font-weight: 400 !important;
            font-style: normal !important;
            text-transform: none !important;
            float: none !important;
        }

        .field-block.container.selected {
            background-color: #99ffcc;
        }

        #preview-label {
            border: 1px solid gray;
            min-height: 100px;
            padding: .5em;
        }

        #preview-label .field-block {
            line-height: 1.1rem;
            overflow: auto;
        }

        #preview-label > .field-block > div {
            display: inline;
        }

        #field-block-options {
            margin-top: 2em;
        }

        li {
            list-style-type: none;
            display: inline-block;
            color: #fff;
            font-weight: 600;
            font-size: .8rem !important;
            border-radius: 2px;
            font: inherit;
            line-height: 1;
            margin: .5em;
            padding: .25em .5em;
        }

        button.btn:hover {
            background-color: #a8a7a7;
        }

        button.btn:focus {
            outline: 0;
        }

        .field-block > span.block-icons.disabled {
            color: gray;
            cursor: not-allowed;
        }

        span.block-icons {
            cursor: pointer;
        }

        .draggable.selected {
            background-color: #99ffcc;
            color: black;
            border: 1px solid #fff;
        }

        .drag-icon {
            background-color: #fff;
            cursor: move;
        }

        [data-category=record-level] {
            background: #d5512d;
            border: 2px solid #d5512d;
        }

        [data-category=occurrence] {
            background: #0da827;
            border: 2px solid #0da827;
        }

        [data-category=event] {
            background: #077eb6;
            border: 2px solid #077eb6;
        }

        [data-category=location] {
            background: #1c4eda;
            border: 2px solid #1c4eda;
        }

        [data-category=identification] {
            background: #ee7bc8;
            border: 2px solid #ee7bc8;
        }

        [data-category=taxon] {
            background: #952ed1;
            border: 2px solid #952ed1;
        }

        .draggable.dragging {
            opacity: 1;
        }
    </style>
    <link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <script src="../../js/all.min.js" type="text/javascript"></script>
    <script src="../../js/jquery.js" type="text/javascript"></script>
    <script src="../../js/jquery-ui.js" type="text/javascript"></script>
    <script src="../../js/jquery.popupoverlay.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#fieldoptions').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
            $('#blockoptions').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
            $('#barcodeoptions').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
            $('#qrcodeoptions').popup({
                transition: 'all 0.3s',
                scrolllock: true,
                blur: false
            });
            addLine();
        });

        function closePopup(id) {
            let lines = labelMid.querySelectorAll('.field-block');
            lines.forEach((line) => {
                line.classList.remove('selected');
            });
            let fields = labelMid.querySelectorAll('.draggable');
            fields.forEach((field) => {
                field.classList.remove('selected');
            });
            currentEditId = null;
            clearBlockOptionsForm();
            clearFieldOptionsForm();
            clearBarcodeOptionsForm();
            clearQRCodeOptionsForm();
            $('#'+ id).popup('hide');
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div id="innertext">
    <div style="display:flex;justify-content:space-between;">
        <div id="fields" style="width:48%;">
            <h2>Fields Available</h2>
            <div>For field definitions please refer to the <a href="https://dwc.tdwg.org/terms/">Darwin Core Quick Reference Guide</a>.</div>
            <div style="margin-top:5px;">
                <label for="fields-filter">Filter fields by category:</label>
                <select name="fields-filter" id="fields-filter" onchange="filterFields(this.value);">
                    <option value="all">All</option>
                    <option value="record-level">Record-level</option>
                    <option value="occurrence">Occurrence</option>
                    <option value="event">Event</option>
                    <option value="location">Location</option>
                    <option value="identification">Identification</option>
                    <option value="taxon">Taxon</option>
                </select>
            </div>
            <div id="field-list"></div>
        </div>
        <div id="build-label" style="width:48%;">
            <h2>Label Content Blocks</h2>
            <div>
                Drag and drop the colored content components from the left into the dashed-line content blocks below to add and arrange
                content within the label. Clicking on the Add Content Block button below will add content blocks to the label. Clicking
                on the x within any content block will delete that block. Clicking on the arrows within any clock will move
                that block up and down in the label arrangement. Click within any content block to customize that block. Click on any
                content component within a content block to customize that component.
            </div>
            <div id="label-middle"></div>
            <button class="btn" onClick="addLine()">Add Content Block</button>
        </div>
    </div>
    <div id="preview">
        <h2>Label preview:</h2>
        <div id="preview-label"></div>
    </div>
    <div style="margin-top:10px;">
        <button class="btn" onclick="saveJson()">Set JSON</button>
        <button class="btn" onclick="cancelWindow()">Cancel</button>
        <textarea id="guijson" style="display:none;height:300px;width:100%;"></textarea>
        <input type="hidden" id="formid" value="" />
    </div>
</div>
<div id="fieldoptions" data-role="popup" class="well" style="width:500px;height:420px;font-size:14px;">
    <h2>Field Options</h2>
    <fieldset class="fieldset-block">
        <legend>Prefix</legend>
        <div class="field-block">
            <span class="label">Prefix:</span>
            <span class="field-elem">
                <input id="fieldPrefix" type="text" value="" onchange="processFieldOptionsFormChange()" />
            </span>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline">
                    <input id="fieldPrefixBold" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Bold</span>
                </span>
                <span class="field-inline">
                    <input id="fieldPrefixItalic" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Italic</span>
                </span>
                <span class="field-inline">
                    <input id="fieldPrefixUnderline" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Underline</span>
                </span>
                <span class="field-inline">
                    <input id="fieldPrefixUppercase" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Uppercase</span>
                </span>
            </div>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label-inline">Font:</span>
                    <select id="fieldPrefixFont" onchange="processFieldOptionsFormChange()">
                        <option value="">Select a Font</option>
                        <option value="Arial">Arial (sans-serif)</option>
                        <option value="Brush Script MT">Brush Script MT (cursive)</option>
                        <option value="Courier New">Courier New (monospace)</option>
                        <option value="Garamond">Garamond (serif)</option>
                        <option value="Georgia">Georgia (serif)</option>
                        <option value="Helvetica">Helvetica (sans-serif)</option>
                        <option value="Tahoma">Tahoma (sans-serif)</option>
                        <option value="Times New Roman">Times New Roman (serif)</option>
                        <option value="Trebuchet">Trebuchet (sans-serif)</option>
                        <option value="Verdana">Verdana (sans-serif)</option>
                    </select>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label">Font Size (px):</span>
                    <span class="field-elem"><input id="fieldPrefixFontSize" type="text" style="width:40px;" value="" onchange="processFieldOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset-block">
        <legend>Suffix</legend>
        <div class="field-block">
            <span class="label">Suffix:</span>
            <span class="field-elem"><input id="fieldSuffix" type="text" value="" onchange="processFieldOptionsFormChange()" /></span>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline">
                    <input id="fieldSuffixBold" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Bold</span>
                </span>
                <span class="field-inline">
                    <input id="fieldSuffixItalic" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Italic</span>
                </span>
                <span class="field-inline">
                    <input id="fieldSuffixUnderline" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Underline</span>
                </span>
                <span class="field-inline">
                    <input id="fieldSuffixUppercase" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Uppercase</span>
                </span>
            </div>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label-inline">Font:</span>
                    <select id="fieldSuffixFont" onchange="processFieldOptionsFormChange()">
                        <option value="">Select a Font</option>
                        <option value="Arial">Arial (sans-serif)</option>
                        <option value="Brush Script MT">Brush Script MT (cursive)</option>
                        <option value="Courier New">Courier New (monospace)</option>
                        <option value="Garamond">Garamond (serif)</option>
                        <option value="Georgia">Georgia (serif)</option>
                        <option value="Helvetica">Helvetica (sans-serif)</option>
                        <option value="Tahoma">Tahoma (sans-serif)</option>
                        <option value="Times New Roman">Times New Roman (serif)</option>
                        <option value="Trebuchet">Trebuchet (sans-serif)</option>
                        <option value="Verdana">Verdana (sans-serif)</option>
                    </select>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label">Font Size (px):</span>
                    <span class="field-elem"><input id="fieldSuffixFontSize" type="text" style="width:40px;" value="" onchange="processFieldOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset-block">
        <legend>Data</legend>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline">
                    <input id="fieldBold" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Bold</span>
                </span>
                <span class="field-inline">
                    <input id="fieldItalic" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Italic</span>
                </span>
                <span class="field-inline">
                    <input id="fieldUnderline" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Underline</span>
                </span>
                <span class="field-inline">
                    <input id="fieldUppercase" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="label-inline">Uppercase</span>
                </span>
            </div>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label-inline">Font:</span>
                    <select id="fieldFont" onchange="processFieldOptionsFormChange()">
                        <option value="">Select a Font</option>
                        <option value="Arial">Arial (sans-serif)</option>
                        <option value="Brush Script MT">Brush Script MT (cursive)</option>
                        <option value="Courier New">Courier New (monospace)</option>
                        <option value="Garamond">Garamond (serif)</option>
                        <option value="Georgia">Georgia (serif)</option>
                        <option value="Helvetica">Helvetica (sans-serif)</option>
                        <option value="Tahoma">Tahoma (sans-serif)</option>
                        <option value="Times New Roman">Times New Roman (serif)</option>
                        <option value="Trebuchet">Trebuchet (sans-serif)</option>
                        <option value="Verdana">Verdana (sans-serif)</option>
                    </select>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label">Font Size (px):</span>
                    <span class="field-elem"><input id="fieldFontSize" type="text" style="width:40px;" value="" onchange="processFieldOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <div style="margin-top:15px;">
        <button onclick="closePopup('fieldoptions');">Close</button>
    </div>
</div>
<div id="blockoptions" data-role="popup" class="well" style="width:500px;height:250px;font-size:14px;">
    <h2>Block Options</h2>
    <fieldset class="fieldset-block">
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline">
                    <span class="label-inline">Text Alignment:</span>
                    <select id="blockTextAlign" onchange="processBlockOptionsFormChange()">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label">Text Line Height (px):</span>
                    <span class="field-elem"><input id="blockLineHeight" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
        <div class="field-block" style="margin-top:5px;">
            <div class="field-elem">
                <span class="field-inline">
                    <span class="label">Margin Before (px):</span>
                    <span class="field-elem"><input id="blockSpaceBefore" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label">Margin After (px):</span>
                    <span class="field-elem"><input id="blockSpaceAfter" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset-block" id="blockLineOptions" style="display:none;">
        <legend>Line</legend>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline">
                    <input id="blockDisplayLine" type="checkbox" value="1" onchange="setBlockLineDisplay();processBlockOptionsFormChange();" />
                    <span class="label-inline">Display Horizontal Line <b>(Note: Fields cannot be added to blocks set to this display)</b></span>
                </span>
            </div>
        </div>
        <div class="field-block" style="margin-top:5px;">
            <div class="field-elem">
                <span class="field-inline">
                    <span class="label-inline">Line Style:</span>
                    <select id="blockDisplayLineStyle" onchange="processBlockOptionsFormChange();">
                        <option value="solid">Solid</option>
                        <option value="dash">Dashed</option>
                        <option value="dot">Dotted</option>
                    </select>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label">Line Height (px):</span>
                    <span class="field-elem"><input id="blockDisplayLineHeight" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <div style="margin-top:15px;">
        <button onclick="closePopup('blockoptions');">Close</button>
    </div>
</div>
<div id="barcodeoptions" data-role="popup" class="well" style="width:500px;height:250px;font-size:14px;">
    <h2>Barcode Options</h2>
    <fieldset class="fieldset-block">
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label">Barcode Height (px):</span>
                    <span class="field-elem"><input id="barcodeHeight" type="text" style="width:40px;" value="" onchange="processBarcodeOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <div style="margin-top:15px;">
        <button onclick="closePopup('barcodeoptions');">Close</button>
    </div>
</div>
<div id="qrcodeoptions" data-role="popup" class="well" style="width:500px;height:250px;font-size:14px;">
    <h2>QR Code Options</h2>
    <fieldset class="fieldset-block">
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline" style="margin-left:5px;">
                    <span class="label">QR Code Size (px):</span>
                    <span class="field-elem"><input id="qrcodeSize" type="text" style="width:40px;" value="" onchange="processQRCodeOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <div style="margin-top:15px;">
        <button onclick="closePopup('qrcodeoptions');">Close</button>
    </div>
</div>
<script src="../../js/symb/collections.labeljsongui.js?ver=23"></script>
<script type="text/javascript">
    createFields(fieldProps, fieldListDiv);
    refreshLineState();
</script>
<?php
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
