<?php
include_once(__DIR__ . '/../../../config/symbini.php');
include_once(__DIR__ . '/../../../classes/OccurrenceDuplicate.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

$recordedBy = array_key_exists('recordedby',$_REQUEST)?trim(urldecode($_REQUEST['recordedby'])):'';
$recordNumber = array_key_exists('recordnumber',$_REQUEST)?trim($_REQUEST['recordnumber']):'';
$eventDate = array_key_exists('eventdate',$_REQUEST)?trim($_REQUEST['eventdate']):'';
$catNum = array_key_exists('catnum',$_POST)?trim($_POST['catnum']):'';
$queryOccid = array_key_exists('occid',$_POST)?(int)$_POST['occid']:0;
$currentOccid = array_key_exists('curoccid',$_REQUEST)?(int)$_REQUEST['curoccid']:0;
$dupeOccid = array_key_exists('dupeoccid',$_POST)?(int)$_POST['dupeoccid']:0;
$dupeTitle = array_key_exists('dupetitle',$_POST)?$_POST['dupetitle']:'';
$action = array_key_exists('submitaction',$_REQUEST)?$_REQUEST['submitaction']:'';

$dupeManager = new OccurrenceDuplicate();
$dupArr = $dupeManager->getDupeList($recordedBy, $recordNumber, $eventDate, $catNum, $queryOccid, $currentOccid);

?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Duplicate Linker</title>
	<script>
		<?php 
		if($action === 'Link as Duplicate'){
			$dupeManager->linkDuplicates($currentOccid,$dupeOccid,$dupeTitle);
			echo 'window.opener.document.getElementById("dupeRefreshForm").submit();';
			echo 'self.close();';
		}
		?>
		
		function openIndWindow(occid){
            const url = "../../individual/index.php?occid=" + occid;
            open(url, "indlist", "resizable=1,scrollbars=1,toolbar=1,width=1000,height=800,left=100,top=100");
        }
	</script>
</head>
<body>
	<div id="innertext">
		<fieldset style="padding:15px;">
			<legend><b>Link New Specimen</b></legend>
			<form name="adddupform" method="post" action="dupelist.php">
				<div style="margin:3px;">
					<b>Last Name:</b>
					<input name="recordedby" type="text" value="<?php echo $recordedBy; ?>" />
				</div>
				<div style="margin:3px;">
					<b>Number:</b>
					<input name="recordnumber" type="text" value="<?php echo $recordNumber; ?>" />
				</div>
				<div style="margin:3px;">
					<b>Date:</b>
					<input name="eventdate" type="text" value="<?php echo $eventDate; ?>" />
				</div>
				<div style="margin:3px;">
					<b>Catalog Number:</b>
					<input name="catnum" type="text" value="" />
				</div>
				<div style="margin:3px;">
					<b>occid:</b>
					<input name="occid" type="text" value="" />
 				</div>
				<div style="margin:20px;">
					<input name="curoccid" type="hidden" value="<?php echo $currentOccid; ?>" />
					<input name="" type="submit" value="Search for Duplicates" />
 				</div>
			</form>
		</fieldset>
		<fieldset>
			<legend><b>Possible Duplicates</b></legend>
			<?php 
			if($dupArr){
				foreach($dupArr as $dupOccid => $occArr){
					?>
					<div style="margin:30px 10px">
						<div>
							<?php 
							echo $occArr['collname'];
							?>
						</div>
						<div>
							<?php 
							echo $occArr['recordedby'].' '.$occArr['recordnumber'].' <span style="margin-left:15px">'.$occArr['eventdate'];
							if($occArr['verbatimeventdate']) {
                                echo ' (' . $occArr['verbatimeventdate'] . ')';
                            }
							echo '</span>';
							echo '<span style="margin-left:50px">'.$occArr['catalognumber'].'</span>';
							?>
						</div>
						<div>
							<?php 
							echo trim($occArr['country'].', '.$occArr['stateprovince'].', '.$occArr['county'].', '.$occArr['locality'],' ,');
							?>
						</div>
						<div>
							<a href="#" onclick="openIndWindow(<?php echo $dupOccid; ?>)">More Details</a>
						</div>
						<div style="margin:5px 0 20px 15px;">
							<form action="dupelist.php" method="post">
								<input name="curoccid" type="hidden" value="<?php echo $currentOccid; ?>" />
								<input name="dupeoccid" type="hidden" value="<?php echo $dupOccid; ?>" />
								<input name="dupetitle" type="hidden" value="<?php echo $occArr['recordedby'].' '.$occArr['recordnumber'].' '.$occArr['eventdate']; ?>"  />
								<input name="submitaction" type="submit" value="Link as Duplicate" />
							</form>
						</div>
					</div>
					<?php
				}
			}
			else{
				echo '<div style="margin:20px;font-weight:bold">No occurrences found matching search criteria</div>';
			}
			?>
		</fieldset>
	</div>
</body>
</html>
