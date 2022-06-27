<?php
include_once(__DIR__ . '/../../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Label Content Format Visual Editor</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../../css/bootstrap.min.css?ver=20220225" type="text/css" rel="stylesheet" />
    <link href="../../css/jquery.mobile-1.4.0.min.css?ver=20210817" type="text/css" rel="stylesheet" />
    <link href="../../css/jsongui.css?ver=20220617" type="text/css" rel="stylesheet" />
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
        <button class="btn" onclick="saveJson();">Set JSON</button>
        <button class="btn" onclick="cancelWindow();">Cancel</button>
        <textarea id="guijson" style="display:none;height:300px;width:100%;"></textarea>
        <input type="hidden" id="formid" value="" />
    </div>
</div>
<div id="fieldoptions" data-role="popup" class="well" style="width:500px;height:420px;font-size:14px;">
    <h2>Field Options</h2>
    <fieldset class="fieldset-block">
        <legend>Prefix</legend>
        <div class="field-block">
            <span class="labelFormat">Prefix:</span>
            <span class="field-elem">
                <input id="fieldPrefix" type="text" value="" onchange="processFieldOptionsFormChange()" />
            </span>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline">
                    <input id="fieldPrefixBold" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Bold</span>
                </span>
                <span class="field-inline">
                    <input id="fieldPrefixItalic" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Italic</span>
                </span>
                <span class="field-inline">
                    <input id="fieldPrefixUnderline" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Underline</span>
                </span>
                <span class="field-inline">
                    <input id="fieldPrefixUppercase" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Uppercase</span>
                </span>
            </div>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline" style="margin-left:5px;">
                    <span class="labelFormat">Font:</span>
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
                    <span class="labelFormat">Font Size (px):</span>
                    <span class="field-elem"><input id="fieldPrefixFontSize" type="text" style="width:40px;" value="" onchange="processFieldOptionsFormChange()" /></span>
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
                    <span class="labelFormat">Bold</span>
                </span>
                <span class="field-inline">
                    <input id="fieldItalic" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Italic</span>
                </span>
                <span class="field-inline">
                    <input id="fieldUnderline" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Underline</span>
                </span>
                <span class="field-inline">
                    <input id="fieldUppercase" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Uppercase</span>
                </span>
            </div>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline" style="margin-left:5px;">
                    <span class="labelFormat">Font:</span>
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
                    <span class="labelFormat">Font Size (px):</span>
                    <span class="field-elem"><input id="fieldFontSize" type="text" style="width:40px;" value="" onchange="processFieldOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <fieldset class="fieldset-block">
        <legend>Suffix</legend>
        <div class="field-block">
            <span class="labelFormat">Suffix:</span>
            <span class="field-elem"><input id="fieldSuffix" type="text" value="" onchange="processFieldOptionsFormChange()" /></span>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline">
                    <input id="fieldSuffixBold" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Bold</span>
                </span>
                <span class="field-inline">
                    <input id="fieldSuffixItalic" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Italic</span>
                </span>
                <span class="field-inline">
                    <input id="fieldSuffixUnderline" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Underline</span>
                </span>
                <span class="field-inline">
                    <input id="fieldSuffixUppercase" type="checkbox" value="1" onchange="processFieldOptionsFormChange()" />
                    <span class="labelFormat">Uppercase</span>
                </span>
            </div>
        </div>
        <div class="field-block">
            <div class="field-elem">
                <span class="field-inline" style="margin-left:5px;">
                    <span class="labelFormat">Font:</span>
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
                    <span class="labelFormat">Font Size (px):</span>
                    <span class="field-elem"><input id="fieldSuffixFontSize" type="text" style="width:40px;" value="" onchange="processFieldOptionsFormChange()" /></span>
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
                    <span class="labelFormat">Text Alignment:</span>
                    <select id="blockTextAlign" onchange="processBlockOptionsFormChange()">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="labelFormat">Text Line Height (px):</span>
                    <span class="field-elem"><input id="blockLineHeight" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
        <div class="field-block" style="margin-top:5px;">
            <div class="field-elem">
                <span class="field-inline">
                    <span class="labelFormat">Top Margin (px):</span>
                    <span class="field-elem"><input id="blockTopMargin" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="labelFormat">Bottom Margin (px):</span>
                    <span class="field-elem"><input id="blockBottomMargin" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
        <div class="field-block" style="margin-top:5px;">
            <div class="field-elem">
                <span class="field-inline">
                    <span class="labelFormat">Left Margin (px):</span>
                    <span class="field-elem"><input id="blockLeftMargin" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="labelFormat">Right Margin (px):</span>
                    <span class="field-elem"><input id="blockRightMargin" type="text" style="width:40px;" value="" onchange="processBlockOptionsFormChange()" /></span>
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
                    <span class="labelFormat">Display Horizontal Line <b>(Note: Fields cannot be added to blocks set to this display)</b></span>
                </span>
            </div>
        </div>
        <div class="field-block" style="margin-top:5px;">
            <div class="field-elem">
                <span class="field-inline">
                    <span class="labelFormat">Line Style:</span>
                    <select id="blockDisplayLineStyle" onchange="processBlockOptionsFormChange();">
                        <option value="solid">Solid</option>
                        <option value="dash">Dashed</option>
                        <option value="dot">Dotted</option>
                    </select>
                </span>
                <span class="field-inline" style="margin-left:5px;">
                    <span class="labelFormat">Line Height (px):</span>
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
                    <span class="labelFormat">Barcode Height (px):</span>
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
                    <span class="labelFormat">QR Code Size (px):</span>
                    <span class="field-elem"><input id="qrcodeSize" type="text" style="width:40px;" value="" onchange="processQRCodeOptionsFormChange()" /></span>
                </span>
            </div>
        </div>
    </fieldset>
    <div style="margin-top:15px;">
        <button onclick="closePopup('qrcodeoptions');">Close</button>
    </div>
</div>
<script src="../../js/symb/collections.labeljsongui.js?ver=26"></script>
<script type="text/javascript">
    createFields(fieldProps, fieldListDiv);
    refreshLineState();
</script>
<?php
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>
