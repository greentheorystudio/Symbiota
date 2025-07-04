<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/KeyEditorManager.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Cache-control: private; Content-Type: text/html; charset=UTF-8');

if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$action = array_key_exists('action',$_POST)?$_POST['action']: '';
$langValue = array_key_exists('lang',$_REQUEST)?$_REQUEST['lang']:$GLOBALS['DEFAULT_LANG'];
$charValue = array_key_exists('char',$_REQUEST)?$_REQUEST['char']: '';
$childrenStr = array_key_exists('children',$_REQUEST)?$_REQUEST['children']: '';
$tid = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']:0;

$editorManager = new KeyEditorManager();

if(!$tid && $childrenStr){
	$childrenArr = explode(',',$childrenStr);
	$tid = array_pop($childrenArr);
	$childrenStr = implode(',',$childrenArr);
}
$editorManager->setLanguage($langValue);
$editorManager->setTid($tid);

$isEditor = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyEditor',$GLOBALS['USER_RIGHTS']) || array_key_exists('KeyAdmin',$GLOBALS['USER_RIGHTS'])){
	$isEditor = true;
}

if($isEditor && $action === 'Submit Changes') {
    $addArr = $_POST['add'] ?? null;
    $removeArr = $_POST['remove'] ?? null;
    $editorManager->processTaxa($addArr,$removeArr);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Character Administration</title>
    <meta name="description" content="Identification key character administration for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <script>
        let dataChanged = false;
        window.onbeforeunload = verifyClose;
		
		function verifyClose() { 
			if (dataChanged === true) {
				return "You will lose any unsaved data if you don't first submit your changes!"; 
			} 
		}
		
		function showSearch(){
			document.getElementById("searchDiv").style.display="block";
			document.getElementById("searchDisplay").style.display="none";
		}
	</script>
</head>
<body>
<?php
	include(__DIR__ . '/../../header.php');
?>
<div id="breadcrumbs">
    <a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
    <b>Character Administration</b>
</div>
<div style="margin:15px;">
<?php 
if($isEditor){
	?>
  	<form action="editor.php" method="post" onsubmit="dataChanged=false;">
	<?php 
	if($tid){
 		$sn = $editorManager->getTaxonName();
 		if($editorManager->getRankId() > 140){
	  		$sn = "<i>$sn</i>";
 		}
 		echo "<div style='float:right;'>";
 		if($editorManager->getRankId() > 140){
			echo "<a href='editor.php?tid=".$editorManager->getParentTid(). '&children=' .($childrenStr?$childrenStr.',':'').$tid."'>edit parent</a>&nbsp;&nbsp;";
 		}
		if($childrenStr){
			echo "<br><a href='editor.php?children=".$childrenStr."'>back to child</a>";
		}
		echo '</div>';
 		echo "<h2>$sn</h2>";
		$cList = $editorManager->getCharList();
		$depArr = $editorManager->getCharDepArray();
		$charStatesList = $editorManager->getCharStates();
		if($cList){
			$count = 0;
			$minusGif = "<img src='../../images/minus_sm.png'>";
			$plusGif = "<img src='../../images/plus_sm.png'>";
			foreach($cList as $heading => $charArray){ 
				echo "<div style='font-weight:bold;margin:1em 0em 1em 0em; color:#990000;".($charValue? ' display:none;' : '')."'>";
				echo "<span class='".$heading."' onclick=\"toggle('".$heading."');\" style=\"display:none;\">$minusGif</span>";
				echo "<span class='".$heading."' onclick=\"toggle('".$heading."');\" style=\"display:;\">$plusGif</span>";
				echo " $heading</div>\n";
				echo "<div class='".$heading."' id='".$heading."' style='text-indent:1em;".($charValue? '' : ' display:none;')."'>";
				foreach($charArray as $cidKey => $charNameStr){
					if(!$charValue || $charValue === $cidKey){
						echo "<div id='chardiv".$cidKey."' style='display:".(array_key_exists($cidKey,$depArr)? 'hidden' : 'block').";'>";
						echo "<div style='margin-top:1em;'><span style='font-weight:bold;'>$charNameStr</span>\n";
						if($editorManager->getRankId() > 140){
							$onClickStr = "openPopup('editor.php?tid=".$editorManager->getParentTid(). '&char=' .$cidKey."');";
						    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>";
							echo '<a href="#" onclick="'.$onClickStr.'">parent</a>';
							echo "</span>\n";
						}
						echo "</div>\n";
						echo "<div style='text-indent:2.5em;'>Add&nbsp;&nbsp;Remove</div>\n";
						$cStates = $charStatesList[$cidKey];
						foreach($cStates as $csKey => $csValue){
							$testStr = $cidKey. '_' .$csKey;
							$charPresent = $editorManager->isSelected($testStr);
							$inh = $editorManager->getInheritedStr($testStr);
							$displayStr = ($charPresent?"<span style='font-weight:bold;'>": '').$csValue.$inh.($charPresent? '</span>' : '');
							echo "<div style='text-indent:2em;'><input type='checkbox' name='add[]' ".($charPresent && !$inh?"disabled='true' ": ' ')." value='".$testStr."' onChange='dataChanged=true;'/>";
							echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='remove[]' ".(!$charPresent || $inh?"disabled='true' ": ' ')."value='".$testStr."'  onChange='dataChanged=true;'/>";
							echo "&nbsp;&nbsp;&nbsp;$displayStr</div>\n";
						}
						echo '</div>';
						$count++;
						if($count%3 === 0) {
                            echo "<div style='margin-top:1em;'><input type='submit' name='action' value='Submit Changes'/></div>\n";
                        }
					}
				}
				echo "</div>\n";
			}
			echo "<div style='margin-top:1em;'><input type='submit' name='action' value='Submit Changes'/></div>\n";
			if($charValue){
				echo '<div><br><b>Note:</b> changes made here will not be reflected on child page until page is refreshed.</div>';
				echo "<div><input type='hidden' name='char' value='".$charValue."'/></div>";
			}
			?>
			<div>
				<input type="hidden" name="tid" value="<?php echo $editorManager->getTid(); ?>" />
				<input type="hidden" name="children" value="<?php echo $childrenStr; ?>" />
				<input type="hidden" name="lang" value="<?php echo $langValue; ?>" />
			</div>
			<?php 
		}
  	}
	?>
	</form>
	<?php 
}
else{  
	echo '<h1>You do not have authority to edit character data or there is a problem with the database connection.</h1>';
}
?>
</div>
<?php
include_once(__DIR__ . '/../../config/footer-includes.php');
include(__DIR__ . '/../../footer.php');
?>
</body>
</html>	
