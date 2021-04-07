<?php
include_once(__DIR__ . '/../../config/symbini.php');
include_once(__DIR__ . '/../../classes/SpecLoans.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
ini_set('max_execution_time', 180);

$collId = $_REQUEST['collid'];
$loanId = array_key_exists('loanid',$_REQUEST)?$_REQUEST['loanid']:0;
$exchangeId = array_key_exists('exchangeid',$_REQUEST)?$_REQUEST['exchangeid']:0;
$loanType = array_key_exists('loantype',$_REQUEST)?$_REQUEST['loantype']:0;
$searchTerm = array_key_exists('searchterm',$_POST)?$_POST['searchterm']:'';
$displayAll = array_key_exists('displayall',$_POST)?$_POST['displayall']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;
$eMode = array_key_exists('emode',$_REQUEST)?$_REQUEST['emode']:0;

$isEditor = 0;
if($GLOBALS['SYMB_UID'] && $collId){
	if($GLOBALS['IS_ADMIN'] || (array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollAdmin'], true))
		|| (array_key_exists('CollEditor',$GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollEditor'], true))){
		$isEditor = 1;
	}
}

$loanManager = new SpecLoans();
if($collId) {
    $loanManager->setCollId($collId);
}

$statusStr = '';
if($isEditor && $formSubmit) {
    if($formSubmit === 'Create Loan Out'){
        $statusStr = $loanManager->createNewLoanOut($_POST);
        $loanId = $loanManager->getLoanId();
        $loanType = 'out';
    }
    elseif($formSubmit === 'Create Loan In'){
        $statusStr = $loanManager->createNewLoanIn($_POST);
        $loanId = $loanManager->getLoanId();
        $loanType = 'in';
    }
    elseif($formSubmit === 'Create Exchange'){
        $statusStr = $loanManager->createNewExchange($_POST);
        $exchangeId = $loanManager->getExchangeId();
        $loanType = 'exchange';
    }
    elseif($formSubmit === 'Save Exchange'){
        $statusStr = $loanManager->editExchange($_POST);
        $loanType = 'exchange';
    }
    elseif($formSubmit === 'Save Outgoing'){
        $statusStr = $loanManager->editLoanOut($_POST);
        $loanType = 'out';
    }
    elseif($formSubmit === 'Delete Loan'){
        $status = $loanManager->deleteLoan($loanId);
        if($status) {
            $loanId = 0;
        }
    }
    elseif($formSubmit === 'Delete Exchange'){
        $status = $loanManager->deleteExchange($exchangeId);
        if($status) {
            $exchangeId = 0;
        }
    }
    elseif($formSubmit === 'Save Incoming'){
        $statusStr = $loanManager->editLoanIn($_POST);
        $loanType = 'in';
    }
    elseif($formSubmit === 'Perform Action'){
        $loanManager->editSpecimen($_REQUEST);
    }
    elseif($formSubmit === 'Add New Determinations'){
        include_once(__DIR__ . '/../../classes/OccurrenceEditorManager.php');
        $occManager = new OccurrenceEditorDeterminations();
        $occidArr = $_REQUEST['occid'];
        foreach($occidArr as $k){
            $occManager->setOccId($k);
            $occManager->addDetermination($_REQUEST,$isEditor);
        }
    }
}

$loanOutList = $loanManager->getLoanOutList($searchTerm,$displayAll);
$loansOnWay = $loanManager->getLoanOnWayList();
$loanInList = $loanManager->getLoanInList($searchTerm,$displayAll);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Loan Management</title>
    <link href="../../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="../../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../../css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <script src="../../js/all.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		let tabIndex = <?php echo $tabIndex; ?>;
	</script>
	<script type="text/javascript" src="../../js/symb/collections.loans.js"></script>
</head>
<body>
	<?php
	include(__DIR__ . '/../../header.php');
    ?>
    <div class='navpath'>
        <a href='../../index.php'>Home</a> &gt;&gt;
        <a href="../misc/collprofiles.php?collid=<?php echo $collId; ?>&emode=1">Collection Management Menu</a> &gt;&gt;
        <a href='index.php?collid=<?php echo $collId; ?>'> <b>Loan Management Main Menu</b></a>
    </div>
	<div id="innertext">
		<?php 
		if($GLOBALS['SYMB_UID'] && $isEditor && $collId){
			if($statusStr){
				?>
				<hr/>
				<div style="margin:15px;color:red;">
					<?php echo $statusStr; ?>
				</div>
				<hr/>
				<?php 
			}
			
			if(!$loanId && !$exchangeId){
				?>
				<div id="tabs" style="margin:0;">
				    <ul>
						<li><a href="#loanoutdiv"><span>Outgoing Loans</span></a></li>
						<li><a href="#loanindiv"><span>Incoming Loans</span></a></li>
						<li><a href="exchange.php?collid=<?php echo $collId; ?>"><span>Gifts/Exchanges</span></a></li>
					</ul>
					<div id="loanoutdiv" style="">
						<div style="float:right;">
							<form name='optionform' action='index.php' method='post'>
								<fieldset>
									<legend><b>Options</b></legend>
									<div>
										<b>Search: </b>
										<input type="text" autocomplete="off" name="searchterm" value="<?php echo $searchTerm;?>" size="20" />
									</div>
									<div>
										<input type="radio" name="displayall" value="0"<?php echo ($displayAll === 0?'checked':'');?> /> Display outstanding loans only
									</div>
									<div>
										<input type="radio" name="displayall" value="1"<?php echo ($displayAll?'checked':'');?> /> Display all loans
									</div>
									<div style="float:right;">
										<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
										<input type="submit" name="formsubmit" value="Refresh List" />
									</div>
								</fieldset>
							</form>	
						</div>
						<?php
						if($loanOutList){
							?>
							<div id="loanoutToggle" style="float:right;margin:10px;">
								<a href="#" onclick="displayNewLoanOut();">
									<i style="height:15px;width:15px;color:green;" title="Create New Loan" class="fas fa-plus"></i>
								</a>
							</div>
							<?php
						}
						?>
						<div id="newloanoutdiv" style="display:<?php echo ($loanOutList?'none':'block'); ?>;">
							<form name="newloanoutform" action="index.php" method="post" onsubmit="return verfifyLoanOutAddForm(this);">
								<fieldset>
									<legend><b>New Outgoing Loan</b></legend>
									<div style="padding-top:4px;float:left;">
										<span>
											Entered By:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="createdbyown" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $GLOBALS['PARAMS_ARR']['un']; ?>" onchange=" " />
										</span>
									</div>
									<div style="padding-top:15px;float:right;">
										<span>
											<b>Loan Identifier: </b><input type="text" autocomplete="off" name="loanidentifierown" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="" />
										</span>
									</div>
									<div style="clear:both;padding-top:6px;float:left;">
										<span>
											Send to Institution:
										</span><br />
										<span>
											<select name="reqinstitution" style="width:400px;">
												<option value="">Select Institution</option>
												<option value="">------------------------------------------</option>
												<?php 
												$instArr = $loanManager->getInstitutionArr();
												foreach($instArr as $k => $v){
													echo '<option value="'.$k.'">'.$v.'</option>';
												}
												?>
											</select>
										</span>
										<span>
											<a href="../admin/institutioneditor.php?emode=1" target="_blank" title="Add a New Institution">
												<i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
											</a>
										</span>
									</div>
									<div style="clear:both;padding-top:8px;float:right;">
										<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
										<button name="formsubmit" type="submit" value="Create Loan Out">Create Loan</button>
									</div>
								</fieldset>
							</form>
						</div>
						<?php
						if(!$loanOutList){
							echo '<script type="text/javascript">displayNewLoanOut();</script>';
						}
						?>
						<div>
							<?php 
							if($loanOutList){
								echo '<h3>Outgoing Loan Records</h3>';
								echo '<ul>';
								foreach($loanOutList as $k => $loanArr){
									echo '<li>';
									echo '<a href="index.php?collid='.$collId.'&loanid='.$k.'&loantype=out">';
									echo $loanArr['loanidentifierown'];
									echo '</a>: '.$loanArr['institutioncode'].' ('.$loanArr['forwhom'].')';
									echo ' - '.($loanArr['dateclosed']?'Closed: '.$loanArr['dateclosed']:'<b>OPEN</b>');
									echo '</li>';
								}
								echo '</ul>';
							}
							else{
								echo '<div style="font-weight:bold;font-size:120%;margin-top:10px;">There are no loans out registered for this collection</div>';
							}
							?>
						</div>
						<div style="clear:both;">&nbsp;</div>
					</div>
					<div id="loanindiv" style="">
						<div style="float:right;">
							<form name='optionform' action='index.php' method='post'>
								<fieldset>
									<legend><b>Options</b></legend>
									<div>
										<b>Search: </b><input type="text" autocomplete="off" name="searchterm" value="<?php echo $searchTerm;?>" size="20" />
									</div>
									<div>
										<input type="radio" name="displayall" value="0"<?php echo ($displayAll === 0?'checked':'');?> /> Display outstanding loans only
									</div>
									<div>
										<input type="radio" name="displayall" value="1"<?php echo ($displayAll?'checked':'');?> /> Display all loans
									</div>
									<div style="float:right;">
										<input type="hidden" name="collid" value="<?php echo $collId; ?>" />
										<input type="submit" name="formsubmit" value="Refresh List" />
									</div>
								</fieldset>
							</form>	
						</div>
						<?php
						if($loansOnWay || $loanInList){
							?>
							<div id="loaninToggle" style="float:right;margin:10px;">
								<a href="#" onclick="displayNewLoanIn();">
									<i style="height:15px;width:15px;color:green;" title="Create New Loan" class="fas fa-plus"></i>
								</a>
							</div>
							<?php
						}
						?>
						<div id="newloanindiv" style="display:<?php echo (($loansOnWay || $loanInList)?'none':'block'); ?>;">
							<form name="newloaninform" action="index.php" method="post" onsubmit="return verifyLoanInAddForm(this);">
								<fieldset>
									<legend><b>New Incoming Loan</b></legend>
									<div style="padding-top:4px;float:left;">
										<span>
											Entered By:
										</span><br />
										<span>
											<input type="text" autocomplete="off" name="createdbyborr" tabindex="96" maxlength="32" style="width:100px;" value="<?php echo $GLOBALS['PARAMS_ARR']['un']; ?>" onchange=" " />
										</span>
									</div>
									<div style="padding-top:15px;float:right;">
										<span>
											<b>Loan Identifier: </b>
											<input type="text" autocomplete="off" id="loanidentifierborr" name="loanidentifierborr" maxlength="255" style="width:120px;border:2px solid black;text-align:center;font-weight:bold;color:black;" value="" onchange="inIdentCheck(<?php echo $collId; ?>);" />
										</span>
									</div>
									<div style="clear:both;padding-top:6px;float:left;">
										<span>
											Sent From:
										</span><br />
										<span>
											<select name="iidowner" style="width:400px;">
												<option value="0">Select Institution</option>
												<option value="0">------------------------------------------</option>
												<?php 
												$instArr = $loanManager->getInstitutionArr();
												foreach($instArr as $k => $v){
													echo '<option value="'.$k.'">'.$v.'</option>';
												}
												?>
											</select>
										</span>
										<span>
											<a href="../admin/institutioneditor.php?emode=1" target="_blank" title="Add a New Institution">
												<i style="height:15px;width:15px;color:green;" class="fas fa-plus"></i>
											</a>
										</span>
									</div>
									<div style="clear:both;padding-top:8px;float:right;">
										<input name="collid" type="hidden" value="<?php echo $collId; ?>" />
										<button name="formsubmit" type="submit" value="Create Loan In">Create Loan</button>
									</div>
								</fieldset>
							</form>
						</div>
						<?php
						if(!$loansOnWay && !$loanInList){
							echo '<script type="text/javascript">displayNewLoanIn();</script>';
						}
						?>
						<div>
							<?php 
							if($loansOnWay){
								echo '<h3>Loans on Their Way</h3>';
								echo '<ul>';
								foreach($loansOnWay as $k => $loanArr){
									echo '<li>';
									echo '<a href="index.php?collid='.$collId.'&loanid='.$k.'&loantype=in">';
									echo $loanArr['loanidentifierown'];
									echo ' from '.$loanArr['collectionname'].'</a>';
									echo '</li>';
								}
								echo '</ul>';
							}
							?>
						</div>
						<div>
							<?php 
							echo '<h3>Incoming Loans</h3>';
							echo '<ul>';
							if($loanInList){
								foreach($loanInList as $k => $loanArr){
									echo '<li>';
									echo '<a href="index.php?collid='.$collId.'&loanid='.$k.'&loantype=in">';
									echo $loanArr['loanidentifierborr'];
									echo '</a>: '.$loanArr['institutioncode'].' ('.$loanArr['forwhom'].')';
									echo ' - '.($loanArr['dateclosed']?'Closed: '.$loanArr['dateclosed']:'<b>OPEN</b>');
									echo '</li>';
								}
							}
							else{
								echo '<li>There are no loans received</li>';
							}
							echo '</ul>';
							?>
						</div>
						<div style="clear:both;">&nbsp;</div>
					</div>
				</div>
				<?php 
			}
			elseif($loanType === 'out'){
				include_once('outgoingdetails.php');
			}
			elseif($loanType === 'in'){
				include_once('incomingdetails.php');
			}
			elseif($loanType === 'exchange'){
				include_once('exchangedetails.php');
			}
			else if(!$GLOBALS['SYMB_UID']){
                echo '<h2>Please <a href="'.$GLOBALS['CLIENT_ROOT'].'/profile/index.php?collid='.$collId.'&refurl='.$GLOBALS['CLIENT_ROOT'].'/collections/loans/index.php?collid='.$collId.'">login</a></h2>';
            }
            elseif(!$collId){
                echo '<h2>Collection not defined</h2>';
            }
            elseif(!$isEditor){
                echo '<h2>You are not authorized to manage loans</h2>';
            }
		}
		else if(!$GLOBALS['SYMB_UID']){
            echo 'Please <a href="../../profile/index.php?refurl=../collections/loans/index.php?collid='.$collId.'">login</a>';
        }
        elseif(!$isEditor){
            echo '<h2>You are not authorized to add occurrence records</h2>';
        }
        else{
            echo '<h2>ERROR: unknown error, please contact system administrator</h2>';
        }
		?>
	</div>
	<?php
	include(__DIR__ . '/../../footer.php');
	?>
</body>
</html>
