<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/OccurrenceSupport.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:'';
$action = array_key_exists('formsubmit',$_REQUEST)?$_REQUEST['formsubmit']:'';

$harvManager = new OccurrenceSupport();

$isEditor = 0;
$collList = array();
if($GLOBALS['IS_ADMIN']){
	$isEditor = 1;
	$collList[] = 'all';
}
else{
	if(array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS'])){
		if(in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
			$isEditor = 1;
		}
		$collList = $GLOBALS['USER_RIGHTS']['CollEditor'];
	}
	if(array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS'])){
		if(in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
			$isEditor = 1;
		}
		$collList = array_merge($collList,$GLOBALS['USER_RIGHTS']['CollAdmin']);
	}
}

if($isEditor && $action === 'Download Records') {
    $harvManager->exportCsvFile();
    exit;
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
	    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> - Occurrence Harvester</title>
		<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="../../css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="../../js/jquery.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
			function loadOccurRecord(fieldObj){
                const occid = fieldObj.value;
                fieldObj.value = "";
				if(!occid) {
				    return false;
				}
				if(document.getElementById("occid-"+occid)) {
				    return false;
				}

                const newAnchor = document.createElement('a');
                newAnchor.setAttribute("id", "a-"+occid);
				newAnchor.setAttribute("href", "#");
				newAnchor.setAttribute("onclick", "openIndPopup("+occid+");return false;");
                const newText = document.createTextNode(occid);
                newAnchor.appendChild(newText);

                const newDiv = document.createElement('div');
                newDiv.setAttribute("id", "occid-"+occid);
				newDiv.appendChild(newAnchor);

                const newInput = document.createElement('input');
                newInput.setAttribute("type", "hidden");
				newInput.setAttribute("name", "occid[]");
				newInput.setAttribute("value", occid);

                const listElem = document.getElementById("occidlist");
                listElem.insertBefore(newDiv,listElem.childNodes[0]);
				listElem.appendChild(newInput);

				document.getElementById("emptylistdiv").style.display = "none";
				
				setOccurData(occid);
				fieldObj.focus();
			}

			function setOccurData(occidInVal){
				$.ajax({
					type: "POST",
					url: "rpc/getoccurrence.php",
					dataType: "json",
					data: { occid: occidInVal }
				}).done(function( data ) {
                    const aElem = document.getElementById("a-" + occidInVal);
                    let newText;
                    if(data !== ""){
						newText = document.createTextNode(" - "+data.recordedby+" #"+data.recordnumber+" ("+data.eventdate+")");
					}
					else{
						newText = document.createTextNode(" - unable to locate occurrence record");
					}
					aElem.appendChild(newText);
				});
			}

			function openIndPopup(occid){
                const urlStr = '../individual/index.php?occid=' + occid;
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
		</script>
	</head>
	<body>
	<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt; 
		<b>Occurrence Harvester</b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<div style="margin:15px">
			Scan or type barcode number into field below and then hit enter or tab to add the specimen to the list. 
			Once list is complete, you can enter your catalog number in the text field and then transfer to your collection 
			or file export to a file that can be imported into your local database. 
		</div>
		<div style="margin:20px 0;">
			<hr/>
		</div>
		<div style="width:450px;float:right;">
			<form name="dlform" method="post" action="occurharvester.php" target="_blank">
				<fieldset>
					<legend><b>Specimen Queue</b></legend>
					<div id="emptylistdiv" style="margin:20px;">
						<b>List Empty: </b>enter barcode in field to left
					</div>
					<div id="occidlist" style="margin:10px;">
					</div>
					<?php 
					if($collid){
						?>
						<div style="margin:30px">
							<input name="formsubmit" type="submit" value="Transfer Records" />
						</div>
						<?php 
					}
					?>
					<div style="margin:30px">
						<input name="formsubmit" type="submit" value="Download Records" />
					</div>
				</fieldset>
			</form>
		</div>
		<div style="">
			<b>Occurrence ID:</b><br/>
			<input type="text" name="occidsubmit" onchange="loadOccurRecord(this)" />
		</div>

	</div>
	<?php
	include(__DIR__ . '/../../footer.php');
	?>
	</body>
</html>
