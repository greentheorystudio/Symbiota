<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/PermissionsManager.php');
include_once(__DIR__ . '/../models/Permissions.php');
include_once(__DIR__ . '/../models/Users.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$loginAs = array_key_exists('loginas',$_REQUEST)?trim($_REQUEST['loginas']): '';
$searchTerm = array_key_exists('searchterm',$_REQUEST)?trim($_REQUEST['searchterm']): '';
$userId = array_key_exists('userid',$_REQUEST)?(int)$_REQUEST['userid']:0;
$delRole = array_key_exists('delrole',$_REQUEST)?$_REQUEST['delrole']: '';
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']: '';
$listType = array_key_exists('listType',$_REQUEST)?$_REQUEST['listType']:'all';
$tablePk = array_key_exists('tablepk',$_REQUEST)?(int)$_REQUEST['tablepk']:0;

$userManager = new PermissionsManager();
$permissions = new Permissions();
$users = new Users();

$newPw = '';

if($GLOBALS['IS_ADMIN']){
	if($loginAs){
		$users->loginAsUser($loginAs);
		header('Location: ../index.php');
	}
	elseif($delRole){
		$permissions->deletePermission($userId, $delRole, $tablePk);
	}
	elseif(array_key_exists('apsubmit',$_POST)){
		foreach($_POST['p'] as $pname){
			$role = $pname;
			$tablePk = '';
			if(strpos($pname,'-')){
				$tok = explode('-',$pname);
				if($tok){
                    $role = $tok[0];
                    $tablePk = $tok[1];
                }
			}
            $permissions->addPermission($userId, $role, $tablePk);
		}
	}
    if($action){
        if($action === 'validate'){
            $users->validateUser($userId);
        }
        elseif($action === 'pwreset'){
            $newPw = $users->resetPassword($userId,true);
        }
        elseif($action === 'validateallunconfirmed'){
            $users->validateAllUnconfirmedUsers();
            $listType = 'all';
        }
        elseif($action === 'deleteallunconfirmed'){
            $users->deleteAllUnconfirmedUsers();
            $listType = 'all';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
	<title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> User Management</title>
	<link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
	<link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <script src="../js/external/all.min.js" type="text/javascript"></script>
</head>
<body>
    <?php
	include(__DIR__ . '/../header.php');
	?>
	<div id="innertext">
		<div style="float:right;">
			<div style="margin:10px 0 15px 0;">
				<fieldset style="background-color:#FFFFCC;padding:0 10px 10px 10px;">
					<legend style="font-weight:bold;">Search</legend>
					Last Name or Login Name:
					<form name='searchform1' action='usermanagement.php' method='post'>
						<input type='text' name='searchterm' title='Enter Last Name'><br/>
						<input name='submit' type='submit' value='Search'>
					</form>
					Quick Search:
					<div style='margin:2px 0 0 10px;'>
						<div><a href='usermanagement.php?searchterm=A'>A</a>|<a href='usermanagement.php?searchterm=B'>B</a>|<a href='usermanagement.php?searchterm=C'>C</a>|<a href='usermanagement.php?searchterm=D'>D</a>|<a href='usermanagement.php?searchterm=E'>E</a>|<a href='usermanagement.php?searchterm=F'>F</a>|<a href='usermanagement.php?searchterm=G'>G</a>|<a href='usermanagement.php?searchterm=H'>H</a></div>
						<div><a href='usermanagement.php?searchterm=I'>I</a>|<a href='usermanagement.php?searchterm=J'>J</a>|<a href='usermanagement.php?searchterm=K'>K</a>|<a href='usermanagement.php?searchterm=L'>L</a>|<a href='usermanagement.php?searchterm=M'>M</a>|<a href='usermanagement.php?searchterm=N'>N</a>|<a href='usermanagement.php?searchterm=O'>O</a>|<a href='usermanagement.php?searchterm=P'>P</a>|<a href='usermanagement.php?searchterm=Q'>Q</a></div>
						<div><a href='usermanagement.php?searchterm=R'>R</a>|<a href='usermanagement.php?searchterm=S'>S</a>|<a href='usermanagement.php?searchterm=T'>T</a>|<a href='usermanagement.php?searchterm=U'>U</a>|<a href='usermanagement.php?searchterm=V'>V</a>|<a href='usermanagement.php?searchterm=W'>W</a>|<a href='usermanagement.php?searchterm=X'>X</a>|<a href='usermanagement.php?searchterm=Y'>Y</a>|<a href='usermanagement.php?searchterm=Z'>Z</a></div>
					</div>
				</fieldset>
			</div>
		</div>
		<?php 
		if($GLOBALS['IS_ADMIN']){
			if($userId){
				$user = $users->getUserByUid($userId);
				?>
				<h1>
					<?php 
						echo $user['firstname']. ' ' .$user['lastname']. ' (#' .$user['uid']. ') ';
						echo "<a href='viewprofile.php?emode=1&tabindex=2&&userid=".$user['uid']. "'><i style='height:15px;width:15px;' class='far fa-edit'></i></a>";
					?>
				</h1>
				<div style="margin-left:10px;">
					<div style="clear:left;">
						<div style="float:left;font-weight:bold;margin-right:8px;">Title: </div>
						<div style="float:left;"><?php echo $user['title'];?></div>
					</div>
					<div style="clear:left;">
						<div style="float:left;font-weight:bold;margin-right:8px;">Institution: </div>
						<div style="float:left;"><?php echo $user['institution'];?></div>
					</div>
					<div style="clear:left;">
						<div style="float:left;font-weight:bold;margin-right:8px;">City: </div>
						<div style="float:left;"><?php echo $user['city'];?></div>
					</div>
					<div style="clear:left;">
						<div style="float:left;font-weight:bold;margin-right:8px;">State: </div>
						<div style="float:left;"><?php echo $user['state'];?></div>
					</div>
					<div style="clear:left;">
						<div style="float:left;font-weight:bold;margin-right:8px;">Zip: </div>
						<div style="float:left;"><?php echo $user['zip'];?></div>
					</div>
					<div style="clear:left;">
						<div style="float:left;font-weight:bold;margin-right:8px;">Country: </div>
						<div style="float:left;"><?php echo $user['country'];?></div>
					</div>
					<div style="clear:left;">
						<div style="float:left;font-weight:bold;margin-right:8px;">Email: </div>
						<div style="float:left;"><?php echo $user['email'];?></div>
					</div>
					<div style="clear:left;">
						<div style="float:left;font-weight:bold;margin-right:8px;">URL: </div>
						<div style="float:left;">
							<a href='<?php echo $user['url'];?>'>
								<?php echo $user['url'];?>
							</a>
						</div>
					</div>
					<div style="clear:left;margin-bottom:10px;">
						<div style="float:left;font-weight:bold;margin-right:8px;">Login: </div>
						<div style="float:left;"><?php echo ($user['username']?$user['username'].' (last login: '.$user['lastlogindate'].')':'login not registered for this user'); ?></div>
					</div>
                    <?php
                    if((int)$user['validated'] !== 1){
                        ?>
                        <div style="clear:left;margin-top:20px;">
                            <span style="font-weight:bold;margin-right:8px;color:red">UNCONFIRMED USER </span>
                            <span>
                                <a href="usermanagement.php?action=validate&userid=<?php echo $userId; ?>"><input type="button" value="Confirm" /></a>
                            </span>
                        </div>
                        <?php
                    }
                    ?>
                    <div style="clear:left;margin-top:20px;">
                        <div>
                            <span>
                                <a href="usermanagement.php?action=pwreset&userid=<?php echo $userId; ?>"><input type="button" value="Reset Password" /></a>
                            </span>
                        </div>
                        <?php
                        if($newPw){
                            ?>
                            <div style="clear:left;margin-top:10px;color:red;">
                                Notify user that their password has been reset to: <span style="font-weight: bold"><?php echo $newPw; ?></span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
				<?php
				if($user['username']){
					?>
					<div style="clear:both;margin:30px 0 20px 30px;">
						<a href="usermanagement.php?loginas=<?php echo $user['username']; ?>">Login as this user</a>
					</div>
					<?php
				}
				?>
				<fieldset style="clear:both;margin:10px;padding: 15px 15px 15px 25px;">
					<legend><b>Current Permissions</b></legend>
					<?php 
					$userPermissions = $userManager->getUserPermissions($userId);
					if($userPermissions){
						?>
						<div>
							<ul>
							<?php 
							if(array_key_exists('SuperAdmin',$userPermissions)){
								?>
								<li>
									<b><?php 
									echo '<span title="'.$userPermissions['SuperAdmin']['aby'].'">';
									echo str_replace('SuperAdmin','Super Administrator',$userPermissions['SuperAdmin']['role']);
									echo '</span>'; 
									?></b> 
									<a href="usermanagement.php?delrole=SuperAdmin&userid=<?php echo $userId; ?>">
										<i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
									</a>
								</li>
								<?php 
							}
							if(array_key_exists('Taxonomy',$userPermissions)){
								?>
								<li>
									<b><?php 
									echo '<span title="'.$userPermissions['Taxonomy']['aby'].'">';
									echo str_replace('Taxonomy','Taxonomy Editor',$userPermissions['Taxonomy']['role']);
									echo '</span>'; 
									?></b> 
									<a href="usermanagement.php?delrole=Taxonomy&userid=<?php echo $userId; ?>">
                                        <i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
									</a>
								</li>
								<?php 
							}
							if(array_key_exists('TaxonProfile',$userPermissions)){
								?>
								<li>
									<b><?php 
									echo '<span title="'.$userPermissions['TaxonProfile']['aby'].'">';
									echo str_replace('TaxonProfile','Taxon Profile Editor',$userPermissions['TaxonProfile']['role']);
									echo '</span>'; 
									?></b> 
									<a href="usermanagement.php?delrole=TaxonProfile&userid=<?php echo $userId; ?>">
                                        <i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
									</a>
								</li>
								<?php 
							}
							if(array_key_exists('KeyAdmin',$userPermissions)){
								?>
								<li>
									<b><?php 
									echo '<span title="'.$userPermissions['KeyAdmin']['aby'].'">';
									echo str_replace('KeyAdmin','Identification Keys Administrator',$userPermissions['KeyAdmin']['role']);
									echo '</span>'; 
									?></b>
									<a href="usermanagement.php?delrole=KeyAdmin&userid=<?php echo $userId; ?>">
                                        <i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
									</a>
								</li>
								<?php 
							}
							if(array_key_exists('KeyEditor',$userPermissions)){
								?>
								<li>
									<b><?php 
									echo '<span title="'.$userPermissions['KeyEditor']['aby'].'">';
									echo str_replace('KeyEditor','Identification Keys Editor',$userPermissions['KeyEditor']['role']);
									echo '</span>'; 
									?></b>
									<a href="usermanagement.php?delrole=KeyEditor&userid=<?php echo $userId; ?>">
                                        <i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
									</a>
								</li>
								<?php 
							}
                            if(array_key_exists('PublicChecklist',$userPermissions)){
                                ?>
                                <li>
                                    <b><?php
                                        echo '<span title="'.$userPermissions['PublicChecklist']['aby'].'">';
                                        echo str_replace('PublicChecklist','Can Create Public Checklists and Biotic Inventory Projects',$userPermissions['PublicChecklist']['role']);
                                        echo '</span>';
                                        ?></b>
                                    <a href="usermanagement.php?delrole=PublicChecklist&userid=<?php echo $userId; ?>">
                                        <i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
                                    </a>
                                </li>
                                <?php
                            }
							if(array_key_exists('RareSppAdmin',$userPermissions)){
								?>
								<li>
									<b><?php 
									echo '<span title="'.$userPermissions['RareSppAdmin']['aby'].'">';
									echo str_replace('RareSppAdmin','Rare Species List Administrator',$userPermissions['RareSppAdmin']['role']);
									echo '</span>'; 
									?></b>
									<a href="usermanagement.php?delrole=RareSppAdmin&userid=<?php echo $userId; ?>">
                                        <i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
									</a>
								</li>
								<?php 
							}
							if(array_key_exists('RareSppReadAll',$userPermissions)){
								?>
								<li>
									<b><?php 
									echo '<span title="'.$userPermissions['RareSppReadAll']['aby'].'">';
									echo str_replace('RareSppReadAll','View and Map Occurrences of Rare Species from all Collections',$userPermissions['RareSppReadAll']['role']);
									echo '</span>'; 
									?></b>
									<a href="usermanagement.php?delrole=RareSppReadAll&userid=<?php echo $userId; ?>">
                                        <i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
									</a>
								</li>
								<?php 
							}
							if(array_key_exists('CollAdmin',$userPermissions)){
								echo '<li><b>Collection Administrator for following collections</b></li>';
								$collList = $userPermissions['CollAdmin'];
								echo '<ul>';
								foreach($collList as $k => $v){
									$cName = '';
									echo '<li><span title="'.$v['aby'].'"><a href="../collections/misc/collprofiles.php?collid='.$k.'" target="_blank">'.$v['name'].'</a></span>';
									echo "<a href='usermanagement.php?delrole=CollAdmin&tablepk=$k&userid=$userId'>";
									echo '<i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>';
									echo '</a></li>';
								}
								echo '</ul>';
							}
							if(array_key_exists('CollEditor',$userPermissions)){
								echo '<li><b>Collection Editor for following collections</b></li>';
								$collList = $userPermissions['CollEditor'];
								echo '<ul>';
								foreach($collList as $k => $v){
									echo '<li><span title="'.$v['aby'].'"><a href="../collections/misc/collprofiles.php?collid='.$k.'" target="_blank">'.$v['name'].'</a></span>';
									echo "<a href='usermanagement.php?delrole=CollEditor&tablepk=$k&userid=$userId'>";
									echo '<i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>';
									echo '</a></li>';
								}
								echo '</ul>';
							}
							if(array_key_exists('RareSppReader',$userPermissions)){
								?>
								<li>
									<b>View and Map Occurrences of Rare Species from following Collections</b>
									<ul>
									<?php 
									$rsrArr = $userPermissions['RareSppReader'];
									foreach($rsrArr as $collId => $v){
										?>
										<li>
											<?php echo '<span title="'.$v['aby'].'">'.$v['name'].'</span>'; ?>
											<a href="usermanagement.php?delrole=RareSppReader&tablepk=<?php echo $collId?>&userid=<?php echo $userId; ?>">
                                                <i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>
											</a>
										</li>
										<?php 
									}
									?>
									</ul>
								</li>
								<?php 
							}
							if(array_key_exists('ProjAdmin',$userPermissions)){
								?>
								<li>
									<b>Administrator for following inventory projects</b>
									<ul>
										<?php 
										$projList = $userPermissions['ProjAdmin'];
										asort($projList);
										foreach($projList as $k => $v){
											echo '<li><a href="../projects/index.php?pid='.$k.'" target="_blank"><span title="'.$v['aby'].'">'.$v['name'].'</span></a>';
											echo "<a href='usermanagement.php?delrole=ProjAdmin&tablepk=$k&userid=$userId'>";
											echo '<i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>';
											echo '</a></li>';
										}
										?>
									</ul>
								</li>
								<?php 
							}
							if(array_key_exists('ClAdmin',$userPermissions)){
								?>
								<li>
									<b>Administrator for following checklists</b>
									<ul>
										<?php 
										$clList = $userPermissions['ClAdmin'];
										asort($clList);
										foreach($clList as $k => $v){
                                            $name = $v['name'] ?? '&lt;resource deleted&gt;';
                                            echo '<li>';
											echo '<a href="../checklists/checklist.php?cl='.$k.'" target="_blank">';
											echo '<span title="'.$v['aby'].'">'.$name.'</span>';
											echo '</a>';
											echo "<a href='usermanagement.php?delrole=ClAdmin&tablepk=$k&userid=$userId'>";
											echo '<i style="height:15px;width:15px;" title="Delete permission" class="far fa-trash-alt"></i>';
											echo '</a></li>';
										}
										?>
									</ul>
								</li>
								<?php 
							}
							?>
							</ul>
						</div>
						<?php 									
					}
					else{
						echo "<h3 style='margin:20px;'>No permissions have to been assigned to this user</h3>";
					}
					?>
					<form name="addpermissions" action="usermanagement.php" method="post">
						<fieldset style="margin-top:10px;padding:0 10px 10px 10px;">
							<legend style="font-weight:bold;">Assign New Permissions</legend>
							<div style="margin:5px;">
								<div style="float:right;margin:10px">
									<input type="submit" name="apsubmit" value="Add Permission" />
								</div>
								<?php
								if(!array_key_exists('SuperAdmin',$userPermissions)){
									echo '<div><input type="checkbox" name="p[]" value="SuperAdmin" /> Super Administrator</div>';
								}
								if(!array_key_exists('Taxonomy',$userPermissions)){
									echo "<div><input type='checkbox' name='p[]' value='Taxonomy' /> Taxonomy Editor</div>";
								}
								if(!array_key_exists('TaxonProfile',$userPermissions)){
									echo "<div><input type='checkbox' name='p[]' value='TaxonProfile' /> Taxon Profile Editor</div>";
								}
								if(!array_key_exists('KeyAdmin',$userPermissions)){
									echo "<div><input type='checkbox' name='p[]' value='KeyAdmin' /> Identification Key Administrator</div>";
								}
								if(!array_key_exists('KeyEditor',$userPermissions)){
									echo "<div><input type='checkbox' name='p[]' value='KeyEditor' /> Identification Key Editor</div>";
								}
                                if(!array_key_exists('PublicChecklist',$userPermissions)){
                                    echo "<div><input type='checkbox' name='p[]' value='PublicChecklist' /> Can Create Public Checklists and Biotic Inventory Projects</div>";
                                }
								?>
							</div>
							<hr/>
							<h2>Occurrence Management</h2>
							<?php
							$showRareSppOption = true;
 							if(array_key_exists('RareSppAdmin', $userPermissions)) {
								$showRareSppOption = false;
 							}
 							else {
 								$isRareSppDude = false;
								?>
								<div style="margin-left:5px;">
									<input type='checkbox' name='p[]' value='RareSppAdmin' />
									Rare Species Administrator (add/remove species from list)
								</div>
								<?php
 							}
 							if(array_key_exists('RareSppReadAll', $userPermissions)) {
								$showRareSppOption = false;
 							}
 							else {
                                ?>
                                <div style="margin-left:5px;">
                                    <input type='checkbox' name='p[]' value='RareSppReadAll' />
                                    Can read Rare Species data for all collections
                                </div>
                                <?php
 							}
							$collArr = $userManager->getCollectionMetadata(0,'specimens');
							$obserArr = $userManager->getCollectionMetadata(0,'observations');
							if(array_key_exists('CollAdmin',$userPermissions)){
								$collArr = array_diff_key($collArr,$userPermissions['CollAdmin']);
								$obserArr = array_diff_key($obserArr,$userPermissions['CollAdmin']);
							}
							if($collArr){
 								?>
								<div style="float:right;margin:10px;">
									<input type='submit' name='apsubmit' value='Add Permission' />
								</div>
 								<h3>Collections</h3>
 								<table>
 									<tr>
 										<th>Admin</th>
 										<th>Editor</th>
 										<?php
                                        if($showRareSppOption) {
                                            echo '<th>Rare</th>';
                                        }
                                        ?>
 										<th>&nbsp;</th>
 									</tr>
									<?php
									foreach($collArr as $collid => $cArr){
										?>
										<tr>
											<td style="text-align: center;">
												<input type='checkbox' name='p[]' value='CollAdmin-<?php echo $collid;?>' title='Collection Administrator' />
											</td>
											<td style="text-align: center;">
												<input type='checkbox' name='p[]' value='CollEditor-<?php echo $collid;?>' title='Able to add and edit occurrence data' <?php echo ((isset($userPermissions['CollEditor'][$collid]))?'DISABLED':'');?> />
											</td>
											<?php 
											if($showRareSppOption){ 
												?>
												<td style="text-align: center;">
													<input type='checkbox' name='p[]' value='RareSppReader-<?php echo $collid;?>' title='Able to read occurrence details for rare species' <?php echo ((isset($userPermissions['RareSppReader'][$collid]))?'DISABLED':'');?> />
												</td>
												<?php 
											} 
											?>
											<td>
												<?php 
												echo $cArr['collectionname'];
                                                $code = '';
                                                if($cArr['institutioncode']){
                                                    $code .= $cArr['institutioncode'];
                                                }
                                                if($cArr['collectioncode']){
                                                    $code .= ($code?'-':'') . $cArr['institutioncode'];
                                                }
												if($code){
                                                    echo ' ('.$code.')';
                                                }
												?>
											</td>
										</tr>
										<?php 
									}
									?>
								</table>
								<?php 
							}
							if($obserArr){
 								?>
								<div style="float:right;margin:10px;">
									<input type='submit' name='apsubmit' value='Add Permission' />
								</div>
 								<h3>Observation Projects</h3>
 								<table>
 									<tr>
 										<th>Admin</th>
 										<th>Editor</th>
 										<?php
                                        if($showRareSppOption) {
                                            echo '<th>Rare</th>';
                                        }
                                        ?>
 										<th>&nbsp;</th>
 									</tr>
									<?php
									foreach($obserArr as $obsid => $oArr){
										?>
										<tr>
											<td style="text-align: center;">
												<input type='checkbox' name='p[]' value='CollAdmin-<?php echo $obsid;?>' title='Collection Administrator' <?php echo ((isset($userPermissions['CollAdmin'][$obsid]))?'DISABLED':'');?> />
											</td>
											<td style="text-align: center;">
												<input type='checkbox' name='p[]' value='CollEditor-<?php echo $obsid;?>' title='Able to add and edit occurrence data' <?php echo ((isset($userPermissions['CollEditor'][$obsid]))?'DISABLED':'');?> />
											</td>
											<?php 
											if($showRareSppOption){ 
												?>
												<td style="text-align: center;">
													<input type='checkbox' name='p[]' value='RareSppReader-<?php echo $obsid;?>' title='Able to read occurrence details for rare species' <?php echo ((isset($userPermissions['RareSppReader'][$obsid]))?'DISABLED':'');?> />
												</td>
												<?php 
											} 
											?>
											<td>
												<?php 
												echo $oArr['collectionname'];
                                                $code = '';
                                                if($oArr['institutioncode']){
                                                    $code .= $oArr['institutioncode'];
                                                }
                                                if($oArr['collectioncode']){
                                                    $code .= ($code?'-':'') . $oArr['institutioncode'];
                                                }
                                                if($code){
                                                    echo ' ('.$code.')';
                                                }
												?>
											</td>
										</tr>
										<?php 
									}
									?>
								</table>
								<?php 
							}
							$pidArr = array();
							if(array_key_exists('ProjAdmin',$userPermissions)){
								$pidArr = array_keys($userPermissions['ProjAdmin']);
							}
							$projectArr = $userManager->getProjectArr($pidArr);
							if($projectArr){
								?>
								<div><hr/></div>
								<div style="float:right;margin:10px;">
									<input type='submit' name='apsubmit' value='Add Permission' />
								</div>
								<h2>Inventory Project Management</h2>
								<?php 
								foreach($projectArr as $k=>$v){
									?>
									<div style='margin-left:15px;'>
										<?php 
										echo '<input type="checkbox" name="p[]" value="ProjAdmin-'.$k.'" />';
										echo '<a href="../projects/index.php?pid='.$k.'" target="_blank">'.$v.'</a>'; 
										?>
									</div>
									<?php 
								}
							}
							$cidArr = array();
							if(array_key_exists('ClAdmin',$userPermissions)){
								$cidArr = array_keys($userPermissions['ClAdmin']);
							}
							$checklistArr = $userManager->getChecklistArr($cidArr);
							if($checklistArr){
								?>
								<div><hr/></div>
								<div style="float:right;margin:10px;">
									<input type='submit' name='apsubmit' value='Add Permission' />
								</div>
								<h2>Checklist Management</h2>
								<?php 
								foreach($checklistArr as $k=>$v){
									?>
									<div style='margin-left:15px;'>
										<?php 
										echo '<input type="checkbox" name="p[]" value="ClAdmin-'.$k.'" /> ';
										echo '<a href="../checklists/checklist.php?cl='.$k.'" target="_blank">'.$v.'</a>';
										?>
									</div>
									<?php 
								}
							}
							?>
							<input type="hidden" name="userid" value="<?php echo $userId;?>" />
						</fieldset>
					</form>
				</fieldset>
				<?php
			}
			else{
                if($GLOBALS['EMAIL_CONFIGURED']){
                    $users->clearOldUnregisteredUsers();
                }
                $userList = $userManager->getUsers($listType, $searchTerm);
				?>
                <h1>Users</h1>
                <div style="height:30px;display:flex;gap:20px;">
                    <div>
                        <form id="userlistform" action="usermanagement.php" method="post">
                            <select name="listType" onchange="this.form.submit();">
                                <option value="all" <?php echo ($listType === 'all'?'selected':''); ?>>All Users</option>
                                <option value="confirmed" <?php echo ($listType === 'confirmed'?'selected':''); ?>>Confirmed Users</option>
                                <option value="unconfirmed" <?php echo ($listType === 'unconfirmed'?'selected':''); ?>>Unconfirmed Users</option>
                            </select>
                            <input name="searchterm" type="hidden" value="<?php echo $searchTerm;?>" />
                        </form>
                    </div>
                    <?php
                    if($listType === 'unconfirmed'){
                        ?>
                        <div>
                            <a href="usermanagement.php?action=validateallunconfirmed"><input type="button" value="Confirm All" /></a>
                        </div>
                        <div>
                            <a href="usermanagement.php?action=deleteallunconfirmed"><input type="button" value="Delete All" /></a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
				foreach($userList as $id => $name){
					echo "<div><a href='usermanagement.php?userid=".$id."'>".$name. '</a></div>';
				}
			}
		}
		else{
			echo '<h3>You must login and have administrator permissions to manage users</h3>';
		}
		?>
	</div>
	<?php
	include(__DIR__ . '/../footer.php');
    include_once(__DIR__ . '/../config/footer-includes.php');
	?>
</body>
</html>
