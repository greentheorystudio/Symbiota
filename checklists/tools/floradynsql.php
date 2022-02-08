<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../classes/InventoryDynSqlManager.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
header('X-Frame-Options: DENY');

$action = array_key_exists('action',$_REQUEST)?htmlspecialchars($_REQUEST['action']): '';
$clid = array_key_exists('clid',$_REQUEST)?(int)$_REQUEST['clid']:0;
$sqlFrag = array_key_exists('sqlfrag',$_REQUEST)?$_REQUEST['sqlfrag']: '';

$dynSqlManager = new InventoryDynSqlManager($clid);
$isEditable = false;
$statusStr = '';
if($GLOBALS['IS_ADMIN'] || (array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS']) && in_array($clid, $GLOBALS['USER_RIGHTS']['ClAdmin'], true))){
    $isEditable = true;

    if($action === 'Save SQL Fragment'){
        $dynSqlManager->saveSql($sqlFrag);
    }
    elseif($action === 'Test SQL Fragment'){
        if($dynSqlManager->testSql($sqlFrag)){
            $statusStr = 'SQL fragment valid';
        }
        else{
            $statusStr = 'ERROR: SQL fragment failed';
        }
    }
}
?>

<html lang="en">

<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Flora Linkage Builder </title>
	<link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
	<script>
		function updateSql(){
            const country = document.getElementById("countryinput").value;
            const state = document.getElementById("stateinput").value;
            const county = document.getElementById("countyinput").value;
            const locality = document.getElementById("localityinput").value;
            const latNorth = document.getElementById("latnorthinput").value;
            const lngWest = document.getElementById("lngwestinput").value;
            const lngEast = document.getElementById("lngeastinput").value;
            const latSouth = document.getElementById("latsouthinput").value;
            let sqlFragStr = "";
			if(country){
				sqlFragStr = "AND (o.country = \"" + country + "\") ";
			}
			if(state){
				sqlFragStr = sqlFragStr + "AND (o.stateprovince = \"" + state + "\") ";
			}
			if(county){
				sqlFragStr = sqlFragStr + "AND (o.county LIKE \"%" + county + "%\") ";
			}
			if(locality){
				sqlFragStr = sqlFragStr + "AND (o.locality LIKE \"%" + locality + "%\"') ";
			}
			if(latNorth && latSouth){
				sqlFragStr = sqlFragStr + "AND (o.decimallatitude BETWEEN " + latSouth + " AND " + latNorth + ") ";
			}
			if(lngWest && lngEast){
				sqlFragStr = sqlFragStr + "AND (o.decimallongitude BETWEEN " + lngWest + " AND " + lngEast + ") ";
			}
			document.getElementById("sqlfrag").value = sqlFragStr.substring(4);
		}

		function buildSql(){
			updateSql();
			return false;
		}
	</script>
</head>

<body>
<?php
	include(__DIR__ . '/../../header.php');
	?>
	<div id='innertext'>
		<?php
		if($clid  && $isEditable){ ?>
			<h1><?php echo $dynSqlManager->getClName(); ?></h1>
			<?php if($statusStr){ ?>
			<div style="margin:20px;font-weight:bold;color:red;">
				<?php echo $statusStr; ?>
			</div>
			<?php } ?>
		<div>
			
		</div>
			<div style="margin:10px 0 15px 0;">
				This editing module will aid you in building an SQL fragment that will be used to help link vouchers to species names within the checklist. 
				When a dynamic SQL fragment exists, the checklist editors will have access to 
				editing tools that will dynamically query occurrence records matching the criteria within the SQL statement. 
				Editors can then go through the list and select the records that are to serve as occurrence vouchers for that checklist.
				See the Flora Voucher Mapping Tutorial for more details. 
			</div>
			<div style="margin-top:10px;">
				<fieldset>
					<legend><b>Current Dynamic SQL Fragment</b></legend>
					<?php echo $dynSqlManager->getDynamicSql()?: 'SQL not yet set' ?>
				</fieldset>
			</div>
			<form name="sqlbuilder" action="" onsubmit="return buildSql();" style="margin-bottom:15px;">
				<fieldset style="padding:15px;">
					<legend><b>SQL Fragment Builder</b></legend>
					<div>
						Use this form to aid in building the SQL fragment. 
						Clicking the 'Build SQL' button will build the SQL using the terms 
						supplied and place it in the form near the bottom of the page. 
					</div>
					<div>
						<b>Country:</b>
						<input id="countryinput" type="text" name="country" />
					</div>
					<div>
						<b>State:</b>
						<input id="stateinput" type="text" name="state" />
					</div>
					<div>
						<b>County:</b>
						<input id="countyinput" type="text" name="county" />
					</div>
					<div>
						<b>Locality:</b>
						<input id="localityinput" type="text" name="locality" />
					</div>
					<div>
						<b>Latitude/Longitude:</b>
					</div>
					<div style="margin-left:75px;">
						<input id="latnorthinput" type="text" name="latnorth" style="width:70px;" title="Latitude North" />
					</div>
					<div>
						<span style="">
							<input id="lngwestinput" type="text" name="lngwest" style="width:70px;" onchange="" title="Longitude West" />
						</span>
						<span style="margin-left:70px;">
							<input id="lngeastinput" type="text" name="lngeast" style="width:70px;" onchange="" title="Longitude East" />
						</span>
					</div>
					<div style="margin-left:75px;">
						<input id="latsouthinput" type="text" name="latsouth" style="width:70px;" onchange="" title="Latitude South" />
					</div>
					<div>
						<input type="submit" name="buildsql" value="Build SQL" />
					</div>
				</fieldset>
			</form>
			<form name="sqlform" action="floradynsql.php" method="post" style="margin-bottom:15px;">
				<div>
					Once SQL fragment meets your requirements, click the 'Save SQL Fragment' button to transfer to the database. 
					The 'Test SQL Fragment' button will test and verify your SQL syntax. 
					Note that you can fine tune the SQL by hand before saving.
				</div>
				<fieldset>
					<legend><b>New SQL Fragment</b></legend>
					<input type="hidden" name="clid" value="<?php echo $clid; ?>"/>
					<textarea id="sqlfrag" rows="5" cols="70"><?php echo $sqlFrag?:$dynSqlManager->getDynamicSql();?></textarea>
					<input type="submit" name="action" value="Test SQL Fragment" />
					<input type="submit" name="action" value="Save SQL Fragment" />
				</fieldset>
			</form>
		<?php } ?>
	</div>
	<?php
 	include(__DIR__ . '/../../footer.php');
	?>

</body>
</html> 
