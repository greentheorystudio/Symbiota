<?php
include_once(__DIR__ . '/../../config/symbbase.php');
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

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);
$formatArr = ($scope && is_numeric($labelIndex)) ? $labelManager->getLabelFormatByID($scope,$labelIndex) : array();
if($formatArr){
    $defaultFont = isset($formatArr['defaultFont']) ? $cssFontFamilies[$formatArr['defaultFont']] : 'Arial, Helvetica, sans-serif';
    $defaultFontSize = isset($formatArr['defaultFontSize']) ? (int)$formatArr['defaultFontSize'] : 12;
    $formatFields = $formatArr['labelBlocks'];
    $columnCount = $formatArr['pageLayout'];
    if(!in_array($columnCount, array('1', '2', '3', '4'), true)) {
        $columnCount = 2;
    }
    if((int)$columnCount === 1){
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
    $columnStyle = 'display:flex;flex-wrap:nowrap;clear:both;justify-content:space-between;';
    $labelStyle = $labelWidth . 'margin:18px 15px;page-break-inside:avoid;';
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
    </head>
    <body style="background-color:white;">
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
                    echo '<div style="'.$columnStyle.'margin: 0 25px;">';
                }
                echo '<div style="'.$labelStyle.'">';
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
                foreach($formatFields as $k => $labelFieldBlock){
                    $addBlock = false;
                    $fieldsArr = array();
                    if(isset($labelFieldBlock['blockDisplayLine'])){
                        $addBlock = true;
                    }
                    else{
                        $fieldsArr = $labelFieldBlock['fields'];
                        foreach($fieldsArr as $f => $fArr){
                            $field = $fArr['field'];
                            if(strncmp($field, 'barcode-', 8) === 0){
                                $addBlock = true;
                            }
                            elseif(strncmp($field, 'qr-', 3) === 0){
                                $addBlock = true;
                            }
                            elseif(isset($occArr[$field]) && $occArr[$field]){
                                $addBlock = true;
                            }
                        }
                    }
                    if($addBlock){
                        $blockStyleStr = $labelWidth . 'clear:both;';
                        if(isset($labelFieldBlock['blockTopMargin'])){
                            $styleStr = $labelWidth . 'height:'.$labelFieldBlock['blockTopMargin'].'px;clear:both;';
                            echo '<div style=\''.$styleStr.'\'></div>';
                        }
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
                            $lineStyleStr = $labelWidth . 'border-top:'.$borderTop.' black;';
                            echo '<div style=\''.$blockStyleStr.'\'>';
                            echo '<hr style="'.$lineStyleStr.'" />';
                        }
                        else{
                            $blockStyleStr .= 'display:flex;flex-wrap:wrap;';
                            if(isset($labelFieldBlock['blockTextAlign'])){
                                $blockStyleStr .= ($labelFieldBlock['blockTextAlign'] === 'left') ? 'justify-content:flex-start;text-align:left;' : '';
                                $blockStyleStr .= ($labelFieldBlock['blockTextAlign'] === 'center') ? 'justify-content:center;text-align:center;' : '';
                                $blockStyleStr .= ($labelFieldBlock['blockTextAlign'] === 'right') ? 'justify-content:flex-end;text-align:right;' : '';
                            }
                            else{
                                $blockStyleStr .= 'justify-content:flex-start;text-align:left;';
                            }
                            $blockStyleStr .= isset($labelFieldBlock['blockLineHeight']) ? 'line-height:'.$labelFieldBlock['blockLineHeight'].'px;' : '';
                            $blockStyleStr .= isset($labelFieldBlock['blockLeftMargin']) ? 'margin-left:'.$labelFieldBlock['blockLeftMargin'].'px;' : '';
                            $blockStyleStr .= isset($labelFieldBlock['blockRightMargin']) ? 'margin-right:'.$labelFieldBlock['blockRightMargin'].'px;' : '';
                            echo '<div style=\''.$blockStyleStr.'\'>';
                            foreach($fieldsArr as $f => $fArr){
                                $value = '';
                                $field = $fArr['field'];
                                if($field === 'genus' && !isset($occArr[$field])){
                                    $value = $occArr['sciname'];
                                }
                                elseif(isset($occArr[$field])){
                                    $value = $occArr[$field];
                                }
                                if(strncmp($field, 'barcode-', 8) === 0){
                                    $idArr = explode('-', $field);
                                    if($idArr){
                                        $bcField = $idArr[1];
                                        if(isset($occArr[$bcField])){
                                            ob_start();
                                            $bc = $labelManager->getBarcodePng(strtoupper($occArr[$bcField]), ($fArr['barcodeHeight'] ?? 40), 'code39');
                                            imagepng($bc);
                                            $rawImageBytes = ob_get_clean();
                                            imagedestroy($bc);
                                            $base64Str = base64_encode( $rawImageBytes );
                                            echo "<img src='data:image/png;base64,".$base64Str."' />";
                                        }
                                    }
                                }
                                elseif(strncmp($field, 'qr-', 3) === 0){
                                    $qr = $labelManager->getQRCodePng($occid, ($fArr['qrcodeSize'] ?? 100));
                                    if($qr){
                                        $base64Str = base64_encode($qr);
                                        echo "<img src='data:image/png;base64,".$base64Str."' />";
                                    }
                                }
                                elseif($value){
                                    echo '<span>';
                                    if(isset($fArr['fieldPrefix'])){
                                        $prefixStyleStr = isset($fArr['fieldPrefixBold']) ? 'font-weight:bold;' : '';
                                        $prefixStyleStr .= isset($fArr['fieldPrefixItalic']) ? 'font-style:italic;' : '';
                                        $prefixStyleStr .= isset($fArr['fieldPrefixUnderline']) ? 'text-decoration:underline;' : '';
                                        $prefixStyleStr .= isset($fArr['fieldPrefixUppercase']) ? 'text-transform:uppercase;' : '';
                                        $prefixStyleStr .= 'font-family:'.(isset($fArr['fieldPrefixFont']) ? $cssFontFamilies[$fArr['fieldPrefixFont']] : $defaultFont).';';
                                        $prefixStyleStr .= 'font-size:'.($fArr['fieldPrefixFontSize'] ?? $defaultFontSize).';';
                                        echo '<span style=\''.$prefixStyleStr.'\'>'.str_replace(' ', '&nbsp;', $fArr['fieldPrefix']).'</span>';
                                    }
                                    $styleStr = isset($fArr['fieldBold']) ? 'font-weight:bold;' : '';
                                    $styleStr .= isset($fArr['fieldItalic']) ? 'font-style:italic;' : '';
                                    $styleStr .= isset($fArr['fieldUnderline']) ? 'text-decoration:underline;' : '';
                                    $styleStr .= isset($fArr['fieldUppercase']) ? 'text-transform:uppercase;' : '';
                                    $styleStr .= 'font-family:'.(isset($fArr['fieldFont']) ? $cssFontFamilies[$fArr['fieldFont']] : $defaultFont).';';
                                    $styleStr .= 'font-size:'.($fArr['fieldFontSize'] ?? $defaultFontSize).';';
                                    echo '<span style=\''.$styleStr.'\'>'.$value.'</span>';
                                    if(isset($fArr['fieldSuffix'])){
                                        $suffixStyleStr = isset($fArr['fieldSuffixBold']) ? 'font-weight:bold;' : '';
                                        $suffixStyleStr .= isset($fArr['fieldSuffixItalic']) ? 'font-style:italic;' : '';
                                        $suffixStyleStr .= isset($fArr['fieldSuffixUnderline']) ? 'text-decoration:underline;' : '';
                                        $suffixStyleStr .= isset($fArr['fieldSuffixUppercase']) ? 'text-transform:uppercase;' : '';
                                        $suffixStyleStr .= 'font-family:'.(isset($fArr['fieldSuffixFont']) ? $cssFontFamilies[$fArr['fieldSuffixFont']] : $defaultFont).';';
                                        $suffixStyleStr .= 'font-size:'.($fArr['fieldSuffixFontSize'] ?? $defaultFontSize).';';
                                        echo '<span style=\''.$suffixStyleStr.'\'>'.str_replace(' ', '&nbsp;', $fArr['fieldSuffix']).'</span>';
                                    }
                                    echo '</span>';
                                }
                            }
                        }
                        echo '</div>';
                        if(isset($labelFieldBlock['blockBottomMargin'])){
                            $styleStr = $labelWidth . 'height:'.$labelFieldBlock['blockBottomMargin'].'px;clear:both;';
                            echo '<div style=\''.$styleStr.'\'></div>';
                        }
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
                if($labelCnt%$columnCount === 0){
                    echo '</div>';
                    $rowCnt++;
                }
            }
        }
        if($labelCnt%$columnCount !== 0){
            echo '</div>';
            $rowCnt++;
        }
        if(!$labelCnt) {
            echo '<div style="font-weight:bold;text-size: 120%">No records were retrieved. Perhaps the quantity values were all set to 0?</div>';
        }
    }
    echo '</div>';
    ?>
    </body>
    </html>
    <?php
}
