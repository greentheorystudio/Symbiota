<?php 
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/DbConnection.php');
include_once(__DIR__ . '/../../classes/DichoManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

$nodeId = array_key_exists('nodeid',$_REQUEST)?(int)$_REQUEST['nodeid']:0;
$stmtId = array_key_exists('stmtid',$_REQUEST)?(int)$_REQUEST['stmtid']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
$statement = array_key_exists('statement',$_REQUEST)?trim($_REQUEST['statement']): '';
$parentStmtId = array_key_exists('parentstmtid',$_REQUEST)?(int)$_REQUEST['parentstmtid']:0;
$taxon = array_key_exists('taxon',$_REQUEST)?$_REQUEST['taxon']: '';
$tid = array_key_exists('tid',$_REQUEST)?(int)$_REQUEST['tid']: 0;
$notes = array_key_exists('notes',$_REQUEST)?$_REQUEST['notes']: '';

$dichoManager = new DichoManager();

if($action){
	$dataArr = array();
	$dataArr['nodeid'] = $nodeId;
	$dataArr['statement'] = $statement;
	$dataArr['tid'] = $tid;
	$dataArr['notes'] = $notes;
	if($action === 'Add New Child Cuplet'){
		$dataArr['parentstmtid'] = $parentStmtId;
		$dataArr['statement2'] = $_REQUEST['statement2'];
		$dataArr['tid2'] = $_REQUEST['tid2'];
		$dataArr['notes2'] = $_REQUEST['notes2'];
		$nodeId = $dichoManager->addCuplet($dataArr);
	}
	else{
		$dataArr['stmtid'] = $stmtId;
		$dichoManager->editStatement($dataArr);
	}
}

$editable = false;
if($GLOBALS['IS_ADMIN'] || array_key_exists('KeyEditor',$GLOBALS['USER_RIGHTS'])){
 	$editable = true;
}

$MsxmlStr = 'Msxml2.XMLHTTP';
$MicrosoftStr = 'Microsoft.XMLHTTP';
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Dichotomous Key Loader</title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <script src="../../js/all.min.js" type="text/javascript"></script>
	<script>
        let cseXmlHttp;
        let targetStr;

        function toggle(target){
            let obj;
            const divObjs = document.getElementsByTagName("div");
            for (let i = 0; i < divObjs.length; i++) {
                obj = divObjs[i];
                if(obj.getAttribute("class") === target || obj.getAttribute("className") === target){
					if(obj.style.display === "none"){
						obj.style.display="block";
					}
					else {
						obj.style.display="none";
					}
				}
			}

            const spanObjs = document.getElementsByTagName("span");
            for (let i = 0; i < spanObjs.length; i++) {
                obj = spanObjs[i];
                if(obj.getAttribute("class") === target || obj.getAttribute("className") === target){
					if(obj.style.display === "none"){
						obj.style.display="inline";
					}
					else {
						obj.style.display="none";
					}
				}
			}
		}

		function checkScinameExistance(inputObj,tStr){
			targetStr = tStr;
            const sciname = inputObj.value;
            if (sciname.length === 0){
		  		return;
		  	}
			cseXmlHttp=GetXmlHttpObject();
			if (cseXmlHttp==null){
		  		alert ("Your browser does not support AJAX!");
		  		return;
		  	}
            let url = "rpc/gettid.php";
            url=url+"?sciname="+sciname;
			url=url+"&sid="+Math.random();
			cseXmlHttp.onreadystatechange=cseStateChanged;
			cseXmlHttp.open("POST",url,true);
			cseXmlHttp.send(null);
		} 
		
		function cseStateChanged(){
			if (cseXmlHttp.readyState === 4){
                const responseStr = cseXmlHttp.responseText;
                if(responseStr === ""){
					alert("INVALID TAXON: Name does not exist in database.");
				}
				else{
					document.getElementById(targetStr).value = responseStr;
				}
			}
		}

		function GetXmlHttpObject(){
            let xmlHttp;
            try{
				xmlHttp=new XMLHttpRequest();
		  	}
			catch (e){
		  		try{
		    		xmlHttp=new ActiveXObject("<?php echo $MsxmlStr; ?>");
		    	}
		  		catch(e){
		    		xmlHttp=new ActiveXObject("<?php echo $MicrosoftStr; ?>");
		    	}
		  	}
			return xmlHttp;
		}
	</script>
</head>

<body>

	<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div id="innertext">
	<?php if($editable){ ?>
		<div style="float:right;cursor:pointer;" onclick="toggle('editcontrols');" title="Toggle Editing on and off">
            <i style="height:20px;width:20px;" class="far fa-edit"></i>
		</div>
	<?php } ?>
		<h1>Dichotomous Key Loader</h1>
		<ul>
		<?php 
		$rows = array();
		if($nodeId){
			$rows = $dichoManager->echoNodeById($nodeId);
		}
		elseif($stmtId){
			$rows = $dichoManager->echoNodeByStmtId($stmtId);
		}
		foreach($rows as $rowCnt => $row){
			if($rowCnt === 0 && $row['parentstmtid']){
				echo "<a href='dichotomous.php?stmtid=".$row['parentstmtid']."'>";
				echo "<i style='height:15px;width:15px;' class='far fa-arrow-alt-circle-left'></i> Go Back";
				echo '</a>';
			}
			?>
			<li>
				<div style='clear:both;margin-top:20px;'>
					<?php 
						echo $row['nodeid'].str_repeat("'",$rowCnt). '. ' .$row['statement'];
						if($editable){
						?>
							<span class="editcontrols" style="cursor:pointer;display:none;" onclick="toggle('editdiv<?php echo $rowCnt; ?>');" title="Edit Statements">
								<i style="height:15px;width:15px;" class="far fa-edit"></i>
							</span>
							<span class="editcontrols" style="cursor:pointer;display:none;" onclick="toggle('adddiv<?php echo $rowCnt; ?>');" title="Add a New Cuplet">
								<i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
							</span>
						<?php 
						}
						
						if($row['childid']){
							if($row['tid']) {
                                echo ' [' . $row['sciname'] . '] ';
                            }
							echo "<div style='float:right;'>".str_repeat('.',20). 'GOTO ';
							echo "<a href='dichotomous.php?nodeid=".$row['childid']."'>";
							echo $row['childid'];
							echo '</a></div>';
						}
						else if($row['tid']) {
                            echo "<div style='float:right;'>" . str_repeat('.', 20) . 'GOTO ' . $row['sciname'] . '</div>';
                        }
					?>
				</div>
				<?php if($editable){ ?>
				<div class="editcontrols" style="display:none;">
					<div class="editdiv<?php echo $rowCnt; ?>" style="margin:10px;display:none;">
						<form name="editform" action="loader.php" method="get">
							<fieldset style="width:360px;">
								<legend>Statement Editor</legend>
								<div>
									Statement: 
									<input type="text" name="statement" value="<?php echo $row['statement']; ?>" size="43"/>
								</div> 
								<div>
									Taxon: 
									<input type="text" id="taxa<?php echo $rowCnt; ?>" name="taxa" value="<?php echo $row['sciname']; ?>" onchange="checkScinameExistance(this,'tid-<?php echo $rowCnt; ?>');" />
									<input type="hidden" id="tid-<?php echo $rowCnt; ?>" name="tid" value="<?php echo $row['tid']; ?>" />
								</div> 
								<div>
									Notes:
									<input type="text" name="notes" value="<?php echo $row['notes']; ?>" size="43"/>
								</div> 
								<div>
									<input type="hidden" name="nodeid" value="<?php echo $nodeId; ?>" />
									<input type="hidden" name="stmtid" value="<?php echo $row['stmtid']; ?>" />
									<input type="submit" name="action" value="Submit Edits" />
								</div>
							</fieldset>
						</form>
					</div>
					<div class="adddiv<?php echo $rowCnt; ?>" style="margin:10px;display:none;">
						<form name="addform" action="loader.php" method="get">
							<fieldset style="width:360px;">
								<legend>Add New Child Cuplet</legend>
								<div style="font-weight:bold;">
									Statement 1:
								</div>
								<div>
									Statement:
									<input type="text" name="statement" value="" size="43"/>
								</div> 
								<div>
									Taxon:
									<input type="text" id="taxon1-<?php echo $rowCnt; ?>" name="taxon" onchange="checkScinameExistance(this,'tid1-<?php echo $rowCnt; ?>');" />
									<input type="hidden" id="tid1-<?php echo $rowCnt; ?>" name="tid" value="" />
								</div> 
								<div>
									Notes:
									<input type="text" name="notes" value="" size="43" />
								</div> 
								<hr/>
								<div style="font-weight:bold;">
									Statement 2:
								</div>
								<div>
									Statement:
									<input type="text" name="statement2" value="" size="43" />
								</div> 
								<div>
									Taxon:
									<input type="text" id="taxon2-<?php echo $rowCnt; ?>" name="taxon2" value="" onchange="checkScinameExistance(this,'tid2-<?php echo $rowCnt; ?>');"/>
									<input type="hidden" id="tid2-<?php echo $rowCnt; ?>" name="tid2" value="" />
								</div> 
								<div>
									Notes:
									<input type="text" name="notes2" value="" size="43" />
								</div> 
								<div>
									<input type="hidden" name="nodeid" value="<?php echo $nodeId; ?>" />
									<input type="hidden" name="parentstmtid" value="<?php echo $row['stmtid']; ?>" />
									<input type="submit" name="action" value="Add New Child Cuplet" />
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			<?php }?> 
			</li>
		<?php } ?> 
		</ul>
	</div>
	<?php 
		include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
