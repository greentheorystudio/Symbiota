<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
require_once __DIR__ . '/../../vendor/autoload.php';

$collid = (int)$_POST['collid'];
$labelformatindex = htmlspecialchars($_POST['labelformatindex']);
$action = htmlspecialchars($_POST['submitaction']);

$columnStyle = '';
$labelWidth = '';
$labelStyle = '';
$formatFields = array();
$cssFontFamilies = array(
    'Arial' => 'Arial, Helvetica, sans-serif',
    'Brush Script MT' => '"Brush Script MT", cursive',
    'Courier New' => '"Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace',
    'Garamond' => 'Garamond, Baskerville, "Baskerville Old Face", "Hoefler Text", "Times New Roman", serif',
    'Georgia' => 'Georgia, Times, "Times New Roman", serif',
    'Helvetica' => '"Helvetica Neue", Helvetica, Arial, sans-serif',
    'Tahoma' => 'Tahoma, Verdana, Segoe, sans-serif',
    'Times New Roman' => '"Times New Roman", Times, serif',
    'Trebuchet' => '"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Tahoma, sans-serif',
    'Verdana' => 'Verdana, Geneva, sans-serif'
);

$scope = $labelformatindex[0];
$labelIndex = substr($labelformatindex,2);
if(!is_numeric($labelIndex)) {
    $labelIndex = '';
}

use Endroid\QrCode\QrCode;
$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);
$formatArr = ($scope && is_numeric($labelIndex)) ? $labelManager->getLabelFormatByID($scope,$labelIndex) : array();
if($formatArr){
    $defaultFont = isset($formatArr['defaultFont']) ? $cssFontFamilies[$formatArr['defaultFont']] : 'Arial, Helvetica, sans-serif';
    $defaultFontSize = isset($formatArr['defaultFontSize']) ? (int)$formatArr['defaultFontSize'] : 12;
    $formatFields = $formatArr['labelBlocks'];
    $columnCount = $formatArr['pageLayout'];
    if(!in_array($columnCount, array('1', '2', '3', '4', 'packet'), true)) {
        $columnCount = 2;
    }
    if($columnCount === 'packet'){
        $labelWidth = 'width:500px;';
    }
    elseif((int)$columnCount === 1){
        $labelWidth = 'width:700px;';
    }
    elseif((int)$columnCount === 2){
        $labelWidth = 'width:328px;';
    }
    elseif((int)$columnCount === 3){
        $labelWidth = 'width:162px;';
    }
    elseif((int)$columnCount === 4){
        $labelWidth = 'width:64px;';
    }
    $columnStyle = 'display:flex;flex-wrap:nowrap;clear:both;';
    $labelStyle = $labelWidth . 'margin:15px;page-break-before:auto;page-break-inside:avoid;';

    if($action === 'Export to CSV'){
        $labelManager->exportLabelCsvFile($_POST);
    }
    else{
        header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
    }
    ?>
    <html>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Labels</title>
        <style type="text/css">
            .foldMarks1 { clear:both;padding-top:285px; }
            .foldMarks1 span { margin-left:77px; margin-right:80px; }
            .foldMarks2 { clear:both;padding-top:355px;padding-bottom:10px; }
            .foldMarks2 span { margin-left:77px; margin-right:80px; }
            @media print { .controls { display: none; } }
        </style>
    </head>
    <body style="background-color:white;">
    <div style="width:816px;margin:0 auto;padding-bottom:15px;display:flex;justify-content:space-evenly;">
        <button id="edit" style="font-weight:bold;" onclick="toggleEdits();">Edit Labels Content</button>
        <button id="print" style="font-weight:bold;" onclick="window.print();;">Print/Save PDF</button>
    </div>
    <?php
    echo '<div style="width:816px;margin: 25px auto;">';
    if($GLOBALS['SYMB_UID']){
        $labelArr = $labelManager->getLabelArray($_POST['occid']);
        $labelCnt = 0;
        $rowCnt = 0;
        foreach($labelArr as $occid => $occArr){
            $dupCnt = $_POST['q-'.$occid];
            for($i = 0; $i < $dupCnt; $i++){
                if($labelCnt === 0 || $labelCnt%$columnCount === 0){
                    if($labelCnt > 0) {
                        echo '</div>';
                    }
                    echo '<div style="'.$columnStyle.'margin: 0 25px;">';
                    $rowCnt++;
                }
                echo '<div style="'.$labelStyle.'">';
                if($columnCount === 'packet'){
                    echo '<hr style="border-top: 1px dotted black;margin-top:285px;width:500px;" />';
                    echo '<hr style="border-top: 1px dotted black;margin-top:355px;margin-bottom:10px;width:500px;" />';
                }
                if(isset($formatArr['headerPrefix']) || isset($formatArr['headerMidText']) || isset($formatArr['headerSuffix'])){
                    $headerMidVal = isset($formatArr['headerMidText']) ? (int)$formatArr['headerMidText'] : 0;
                    $headerStr = '';
                    $styleStr = $labelWidth . 'clear:both;';
                    $headerStr .= $formatArr['headerPrefix'] ?? '';
                    if($headerMidVal === 1){
                        $headerStr .= $occArr['country'] ?? '';
                    }
                    if($headerMidVal === 2){
                        $headerStr .= $occArr['stateprovince'] ?? '';
                    }
                    if($headerMidVal === 3){
                        $headerStr .= $occArr['county'] ?? '';
                    }
                    if($headerMidVal === 4){
                        $headerStr .= $occArr['family'] ?? '';
                    }
                    $headerStr .= $formatArr['headerSuffix'] ?? '';
                    $styleStr .= isset($formatArr['headerBold']) ? 'font-weight:bold;' : '';
                    $styleStr .= isset($formatArr['headerItalic']) ? 'font-style:italic;' : '';
                    $styleStr .= isset($formatArr['headerUnderline']) ? 'text-decoration:underline;' : '';
                    $styleStr .= isset($formatArr['headerUppercase']) ? 'text-transform:uppercase;' : '';
                    $styleStr .= ($formatArr['headerTextAlign'] !== 'left') ? 'text-align:' . $formatArr['headerTextAlign'] . ';' : '';
                    $styleStr .= 'font-family:'.(isset($formatArr['headerFont']) ? $cssFontFamilies[$formatArr['headerFont']] : $defaultFont).';';
                    $styleStr .= 'font-size:'.($formatArr['headerFontSize'] ?? $defaultFontSize).';';
                    echo '<div style=\''.$styleStr.'\'>'.$headerStr.'</div>';
                    if(isset($formatArr['headerBottomMargin'])){
                        $styleStr = $labelWidth . 'height:'.$formatArr['headerBottomMargin'].'px;clear:both;';
                        echo '<div style=\''.$styleStr.'\'></div>';
                    }
                }


                /*echo $labelManager->getLabelBlock($formatArr['labelBlocks'],$occArr);
                if($occArr['catalognumber']){
                    if($useBarcode){
                        ?>
                        <div class="cn-barcode">
                            <img src="getBarcode.php?bcheight=40&bctext=<?php echo $occArr['catalognumber']; ?>" />
                        </div>
                        <?php
                    }
                    elseif($showcatalognumbers){
                        ?>
                        <div class="catalog-number">
                            <?php echo $occArr['catalognumber']; ?>
                        </div>
                        <?php
                    }
                }
                if($occArr['othercatalognumbers']){
                    ?>
                    <div class="other-catalog-numbers">
                        <?php echo $occArr['othercatalognumbers']; ?>
                    </div>
                    <?php
                }
                if($lFooter) {
                    echo '<div class="label-footer" ' . (isset($formatArr['labelFooter']['style']) ? 'style="' . $formatArr['labelFooter']['style'] . '"' : '') . '>' . $lFooter . '</div>';
                }*/






                foreach($formatFields as $k => $labelFieldBlock){
                    $blockStyleStr = $labelWidth . 'clear:both;';
                    if(isset($labelFieldBlock['blockDisplayLine'])){
                        $borderTop = '';
                        $borderTop .= isset($labelFieldBlock['blockDisplayLineHeight']) ? ' ' . $labelFieldBlock['blockDisplayLineHeight'] . 'px' : ' 1px';
                        if(isset($labelFieldBlock['blockDisplayLineStyle']) && $labelFieldBlock['blockDisplayLineStyle'] === 'dash'){
                            $borderTop .= ' dashed';
                        }
                        if(isset($labelFieldBlock['blockDisplayLineStyle']) && $labelFieldBlock['blockDisplayLineStyle'] === 'dot'){
                            $borderTop .= ' dotted';
                        }
                        else{
                            $borderTop .= ' solid';
                        }
                        $blockStyleStr = $labelWidth . 'border-top:'.$borderTop.' black;';
                        echo '<hr style="'.$blockStyleStr.'" />';
                    }
                    elseif(isset($labelFieldBlock['fields'])) {
                        $fieldsArr = $labelFieldBlock['fields'];
                        $blockStyleStr .= ($labelFieldBlock['blockTextAlign'] === 'left') ? 'display:flex;justify-content:flex-start;' : '';
                        $blockStyleStr .= ($labelFieldBlock['blockTextAlign'] === 'center') ? 'display:flex;justify-content:center;' : '';
                        $blockStyleStr .= ($labelFieldBlock['blockTextAlign'] === 'right') ? 'display:flex;justify-content:flex-end;' : '';
                        $blockStyleStr .= isset($labelFieldBlock['blockLineHeight']) ? 'line-height:'.$labelFieldBlock['blockLineHeight'].'px;' : '';
                        $blockStyleStr .= isset($labelFieldBlock['blockSpaceBefore']) ? 'margin-left:'.$labelFieldBlock['blockSpaceBefore'].'px;' : '';
                        $blockStyleStr .= isset($labelFieldBlock['blockSpaceAfter']) ? 'margin-right:'.$labelFieldBlock['blockSpaceAfter'].'px;' : '';
                        echo '<div style=\''.$blockStyleStr.'\'>';
                        foreach($fieldsArr as $f => $fArr){
                            $field = $fArr['field'];
                            if(strncmp($field, 'barcode-', 8) === 0){
                                $idArr = explode('-', $field);
                                if($idArr){
                                    $bcField = $idArr[1];
                                    if(isset($occArr[$bcField])){
                                        ob_start();
                                        $bc = $labelManager->getBarcodePng(strtoupper($occArr[$bcField]), ($labelFieldBlock['barcodeHeight'] ?? 40), 'code39');
                                        imagepng($bc);
                                        $rawImageBytes = ob_get_clean();
                                        imagedestroy($bc);
                                        $base64Str = base64_encode( $rawImageBytes );
                                        echo "<img src='data:image/png;base64,".$base64Str."' />";
                                    }
                                }
                            }
                            elseif(strncmp($field, 'qr-', 3) === 0){
                                $qr = $labelManager->getQRCodePng($occid, ($labelFieldBlock['qrcodeSize'] ?? 100));
                                if($qr){
                                    $base64Str = base64_encode($qr);
                                    echo "<img src='data:image/png;base64,".$base64Str."' />";
                                }
                            }
                            else{

                            }
                        }
                        echo '</div>';
                    }
                    else{
                        $blockStyleStr .= 'height:' . isset($labelFieldBlock['blockLineHeight']) ? $labelFieldBlock['blockLineHeight'] : $defaultFontSize . 'px;';
                        echo '<div style=\''.$blockStyleStr.'\'></div>';
                    }
                }
                if(isset($formatArr['footerText'])){
                    if(isset($formatArr['footerTopMargin'])){
                        $styleStr = $labelWidth . 'height:'.$formatArr['footerTopMargin'].'px;clear:both;';
                        echo '<div style=\''.$styleStr.'\'></div>';
                    }
                    $styleStr = $labelWidth . 'clear:both;';
                    $styleStr .= isset($formatArr['footerBold']) ? 'font-weight:bold;' : '';
                    $styleStr .= isset($formatArr['footerItalic']) ? 'font-style:italic;' : '';
                    $styleStr .= isset($formatArr['footerUnderline']) ? 'text-decoration:underline;' : '';
                    $styleStr .= isset($formatArr['footerUppercase']) ? 'text-transform:uppercase;' : '';
                    $styleStr .= ($formatArr['footerTextAlign'] !== 'left') ? 'text-align:' . $formatArr['footerTextAlign'] . ';' : '';
                    $styleStr .= 'font-family:'.(isset($formatArr['footerFont']) ? $cssFontFamilies[$formatArr['footerFont']] : $defaultFont).';';
                    $styleStr .= 'font-size:'.($formatArr['footerFontSize'] ?? $defaultFontSize).';';
                    echo '<div style=\''.$styleStr.'\'>'.$formatArr['footerText'].'</div>';
                }
                echo '</div>';
                $labelCnt++;
            }
        }
        if(!$labelCnt) {
            echo '<div style="font-weight:bold;text-size: 120%">No records were retrieved. Perhaps the quantity values were all set to 0?</div>';
        }
    }
    echo '</div>';
    ?>
    </body>
    <script type="text/javascript">
        let labelPage = document.querySelector('.body');

        function toggleEdits() {
            let isEditable = labelPage.contentEditable === 'true';
            if(isEditable){
                console.log(isEditable);
                labelPage.contentEditable = 'false';
                document.querySelector('#edit').innerText = 'Edit Labels Text';
                labelPage.style.border = 'none';
            }
            else{
                console.log(isEditable);
                labelPage.contentEditable = 'true';
                document.querySelector('#edit').innerText = 'Save';
                labelPage.style.border = '2px solid #03fc88';
            }
        }
    </script>
    </html>
    <?php
}
