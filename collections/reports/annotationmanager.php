<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/OccurrenceLabel.php');
include_once(__DIR__ . '/../../classes/Sanitizer.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .Sanitizer::getCleanedRequestPath(true));
}

$collid = (int)$_REQUEST['collid'];
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$datasetManager = new OccurrenceLabel();
$datasetManager->setCollid($collid);

$isEditor = 0;
$annoArr = array();
if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))){
    $isEditor = 1;
}
elseif(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
    $isEditor = 1;
}
if($isEditor){
    $annoArr = $datasetManager->getAnnoQueue();
}
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Print Annotations Labels</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/all.min.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <script>
        function selectAllAnno(cb){
            let boxesChecked = true;
            if(!cb.checked){
                boxesChecked = false;
            }
            const dbElements = document.getElementsByName("detid[]");
            for(let i = 0; i < dbElements.length; i++){
                const dbElement = dbElements[i];
                dbElement.checked = boxesChecked;
            }
        }

        function validateAnnoSelectForm(){
            const dbElements = document.getElementsByName("detid[]");
            for(let i = 0; i < dbElements.length; i++){
                const dbElement = dbElements[i];
                if(dbElement.checked) {
                    return true;
                }
            }
            alert("Please select at least one occurrence");
            return false;
        }

        function openIndPopup(occid){
            openPopup('../individual/index.php?occid=' + occid);
        }

        function openEditorPopup(occid){
            openPopup('../editor/occurrenceeditor.php?occid=' + occid);
        }

        function openPopup(urlStr){
            let wWidth = 900;
            if(document.getElementById('innertext').offsetWidth){
                wWidth = document.getElementById('innertext').offsetWidth*1.05;
            }
            else if(document.body.offsetWidth){
                wWidth = document.body.offsetWidth*0.9;
            }
            const newWindow = window.open(urlStr, 'popup', 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
            if (newWindow.opener == null) {
                newWindow.opener = self;
            }
            return false;
        }

        function changeAnnoFormExport(action,target){
            document.annoselectform.action = action;
            document.annoselectform.target = target;
        }
    </script>
</head>
<body>
<?php
include(__DIR__ . '/../../header.php');
?>
<div class='navpath'>
    <a href='../../index.php'>Home</a> &gt;&gt;
    <?php
    if(stripos(strtolower($datasetManager->getMetaDataTerm('colltype')), 'observation') !== false){
        echo '<a href="../../profile/viewprofile.php?tabindex=1">Personal Management Menu</a> &gt;&gt; ';
    }
    else{
        echo '<a href="../misc/collprofiles.php?collid='.$collid.'&emode=1">Collection Management Panel</a> &gt;&gt; ';
    }
    ?>
    <b>Print Annotations Labels</b>
</div>
<div id="innertext">
    <?php
    if($isEditor){
        $reportsWritable = false;
        if(is_writable($GLOBALS['SERVER_ROOT'].'/temp/report')) {
            $reportsWritable = true;
        }
        if(!$reportsWritable){
            ?>
            <div style="padding:5px;">
                <span style="color:red;">Please contact the site administrator to make temp/report folder writable in order to export to docx files.</span>
            </div>
            <?php
        }
        echo '<h2>'.$datasetManager->getCollName().'</h2>';
        ?>
        <div id="annotations">
            <div>
                <?php
                if($annoArr){
                    ?>
                    <form name="annoselectform" id="annoselectform" action="defaultannotations.php" method="post" onsubmit="return validateAnnoSelectForm();">
                        <div style="margin-top: 15px; margin-left: 15px;">
                            <input name="" value="" type="checkbox" onclick="selectAllAnno(this);" />
                            Select/Deselect all Occurrences
                        </div>
                        <table class="styledtable" style="font-family:Arial,serif;font-size:12px;">
                            <tr>
                                <th style="width:25px;text-align:center;"></th>
                                <th style="width:25px;text-align:center;">#</th>
                                <th style="width:125px;text-align:center;">Collector</th>
                                <th style="width:300px;text-align:center;">Scientific Name</th>
                                <th style="width:400px;text-align:center;">Determination</th>
                            </tr>
                            <?php
                            $trCnt = 0;
                            foreach($annoArr as $detId => $recArr){
                                $trCnt++;
                                ?>
                                <tr <?php echo (($trCnt%2)?'class="alt"':''); ?>>
                                    <td>
                                        <input type="checkbox" name="detid[]" value="<?php echo $detId; ?>" />
                                    </td>
                                    <td>
                                        <input type="text" name="q-<?php echo $detId; ?>" value="1" style="width:20px;border:inset;" />
                                    </td>
                                    <td>
                                        <a href="#" onclick="openIndPopup(<?php echo $recArr['occid']; ?>); return false;">
                                            <?php echo $recArr['collector']; ?>
                                        </a>
                                        <a href="#" onclick="openEditorPopup(<?php echo $recArr['occid']; ?>); return false;">
                                            <i style="height:20px;width:20px;" class="far fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $recArr['sciname']; ?>
                                    </td>
                                    <td>
                                        <?php echo $recArr['determination']; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <fieldset style="margin-top:15px;">
                            <legend><b>Annotation Printing</b></legend>
                            <div style="float:left;">
                                <div style="margin:4px;">
                                    <b>Header:</b>
                                    <input type="text" name="lheading" value="<?php echo $datasetManager->getAnnoCollName(); ?>" style="width:450px" />
                                </div>
                                <div style="margin:4px;">
                                    <b>Footer:</b>
                                    <input type="text" name="lfooter" value="" style="width:450px" />
                                </div>
                                <div style="margin:4px;">
                                    <input type="checkbox" name="speciesauthors" value="1" onclick="" />
                                    <b>Print species authors for infraspecific taxa</b>
                                </div>
                                <div style="margin:4px;">
                                    <input type="checkbox" name="clearqueue" value="1" onclick="" />
                                    <b>Remove selected annotations from queue</b>
                                </div>
                            </div>
                            <div style="float:right;">
                                <input type="hidden" name="collid" value="<?php echo $collid; ?>" />
                                <input type="submit" name="submitaction" onclick="changeAnnoFormExport('defaultannotations.php','_blank');" value="Print in Browser" />
                                <?php
                                if($reportsWritable){
                                    ?>
                                    <br/><br/>
                                    <input type="submit" name="submitaction" onclick="changeAnnoFormExport('defaultannotationsexport.php','_self');" value="Export to DOCX" />
                                    <?php
                                }
                                ?>
                            </div>
                        </fieldset>
                    </form>
                    <?php
                }
                else{
                    ?>
                    <div style="font-weight:bold;margin:20px;font-size:150%;">
                        There are no annotations queued to be printed.
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
    else{
        ?>
        <div style="font-weight:bold;margin:20px;">
            You do not have permissions to print annotation labels for this collection.
            Please contact the site administrator to obtain the necessary permissions.
        </div>
        <?php
    }
    ?>
</div>
<?php include(__DIR__ . '/../../footer.php');?>
</body>
</html>
