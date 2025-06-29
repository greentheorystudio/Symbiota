<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/KeyCharDeficitManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
 
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
$langValue = array_key_exists('lang',$_REQUEST)?$_REQUEST['lang']: '';
$projValue = array_key_exists('proj',$_REQUEST)?$_REQUEST['proj']: '';
$clValue = array_key_exists('cl',$_REQUEST)?$_REQUEST['cl']: '';
$cfValue = array_key_exists('cf',$_REQUEST)?$_REQUEST['cf']: '';
$cidValue = array_key_exists('cid',$_REQUEST)?$_REQUEST['cid']: '';
  
$cdManager = new KeyCharDeficitManager();

if($langValue) {
    $cdManager->setLanguage($langValue);
}
if($projValue) {
    $cdManager->setProject($projValue);
}
$editable = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyEditor',$GLOBALS['USER_RIGHTS']) || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
	$editable = true;
}

$brownStripStr = 'brown_hor_strip.gif';
$editorStr = 'editor.php?tid=';
$charStr = '';
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Character Deficit Finder</title>
    <meta name="description" content="Identification key character deficit finder for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">
		function openPopup(urlStr,windowName){
            let wWidth = 900;
            try{
				if(document.getElementById('main-container').offsetWidth){
					wWidth = document.getElementById('main-container').offsetWidth*1.05;
				}
				else if(document.body.offsetWidth){
					wWidth = document.body.offsetWidth*0.9;
				}
			}
			catch(e){
			}
            const newWindow = window.open(urlStr, windowName, 'scrollbars=1,toolbar=1,resizable=1,width=' + (wWidth) + ',height=600,left=20,top=20');
            if (newWindow.opener == null) {
                newWindow.opener = self;
            }
		}
	</script>
</head>
<body>
    <?php
    include(__DIR__ . '/../../header.php');
    ?>
    <div id="mainContainer" style="padding: 10px 15px 15px;">
    <?php
    if($editable){
        ?>
        <form action="chardeficit.php" method="get">
            <table style="width:700px;border:0;">
                <tr>
                    <td style="width:200px;vertical-align:top;padding-right:20px;border-right: 2px solid black;">
                        <div style='margin-top:1em;font-weight:bold;'>Checklist:</div>
                        <select name="cl">
                            <?php
                            $selectList = $cdManager->getClQueryList();
                            echo '<option>--Select a Checklist--</option>';
                            foreach($selectList as $key => $value){
                                $selectStr = ($key === $clValue? 'SELECTED' : '');
                                echo "<option value='".$key."' $selectStr>$value</option>";
                            }
                            ?>
                        </select>
                        <br/>
                        <div style='margin-top:1em;font-weight:bold;'>Filter Character List:</div>
                        <select name="cf">
                            <?php
                            $selectList = $cdManager->getTaxaQueryList();
                            echo '<option>--Select a Taxon--</option>';
                            foreach($selectList as $key => $value){
                                $selectStr = ($key === $cfValue? 'SELECTED' : '');
                                echo "<option value='".$key."' $selectStr>$value</option>\n";
                            }
                            ?>
                        </select><br/>
                        <div style='margin-top:1em;'><input type='submit' name='action' id='submit' value='Get Characters' /></div>
                        <hr style="height:2px;"/>
                        <input type='submit' name='action' id='submit' value='Get Species List'/>
                        <div style="margin:10px 0 10px 0;height:250px; width:190px; overflow : auto;border:black solid 1px;">
                            <?php
                            if($cfValue !== '--Select a Taxon--'){
                                if($action === 'Get Characters' || $action === 'Get Species List'){
                                    $cList = $cdManager->getCharList($cfValue, $cidValue);
                                    foreach($cList as $value){
                                        echo $value."\n";
                                    }
                                }
                                else{
                                    echo '<h2>Character List Empty</h2>';
                                }
                            }
                            else{
                                echo '<h2>Select as Taxon</h2>';
                            }
                            ?>
                        </div>
                        <input type='submit' name='action' id='submit' value='Get Species List' />
                    </td>
                    <td style="vertical-align: top;">
                        <?php
                        if($action === 'Get Species List' && $cfValue !== '--Select a Taxon--'){
                            $tList = $cdManager->getTaxaList($cidValue, $cfValue, $clValue);
                            if($tList){
                                echo '<h3>Species Count: ' .$cdManager->getTaxaCount()."</h3>\n";
                                foreach($tList as $f=>$sArr){
                                    echo "<div style='margin-top:1em;'>$f</div>\n";
                                    foreach($sArr as $idValue => $spValue){
                                        $onClickStr = "openPopup('editor.php?tid=".$idValue. '&char=' .$cidValue."','technical');";
                                        echo "<div style=''>&nbsp;&nbsp;<a href='editor.php?tid=".$idValue."&lang=English&lang=English' target='_blank'>$spValue</a> ";
                                        echo '(<a href="#" onclick="'.$onClickStr.'">@</a>)</div>\n';
                                    }
                                }
                            }
                            else{
                                echo '<h2>No taxa were returned.</h2>';
                            }
                        }
                        else{
                            echo '<h2>List Empty.</h2>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </form>
        <?php
    }
    else{
        echo '<h1>You do not have authority to edit character data or there is a problem with the connection.</h1> <h3>You must first login to the system.</h3>';
    }
    ?>
    </div>
    <?php
    include_once(__DIR__ . '/../../config/footer-includes.php');
    include(__DIR__ . '/../../footer.php');
    ?>
</body>
</html>
