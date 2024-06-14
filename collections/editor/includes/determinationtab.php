<?php
include_once(__DIR__ . '/../../../config/symbbase.php');
include_once(__DIR__ . '/../../../classes/OccurrenceEditorManager.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');

$occId = (int)$_GET['occid'];
$occIndex = (int)$_GET['occindex'];
$identBy = $_GET['identby'];
$dateIdent = $_GET['dateident'];
$sciName = $_GET['sciname'];
$crowdSourceMode = (int)$_GET['csmode'];
$editMode = (int)$_GET['em'];

$annotatorname = $_GET['annotatorname'];
$annotatoremail = $_GET['annotatoremail'];
$catalognumber = $_GET['catalognumber'];
$institutioncode = $_GET['institutioncode'];

$occManager = new OccurrenceEditorDeterminations();

$occManager->setOccId($occId); 
$detArr = $occManager->getDetMap($identBy, $dateIdent, $sciName);

$specImgArr = $occManager->getImageMap();

?>
<div id="determdiv" style="width:795px;">
	<div>
		<fieldset style="margin:15px;padding:15px;">
			<legend><b>Determination History</b></legend>
			<div style="float:right;">
				<a href="#" onclick="toggle('newdetdiv');return false;" title="Add New Determination" ><i style="height:20px;width:20px;color:green;" class="fas fa-plus"></i></a>
			</div>
			<?php 
			if(!$detArr){
				?>
				<div style="font-weight:bold;margin:10px;">
					There are no historic annotations for this occurrence
				</div>
				<?php 
			}
			?>
			<div id="newdetdiv" style="display:<?php echo ($detArr?'none':''); ?>;">
				<form name="detaddform" action="occurrenceeditor.php" method="post" onsubmit="return verifyDetForm(this)">
					<fieldset style="margin:15px;padding:15px;">
						<legend><b>Add a New Determination</b></legend>
						<div style="float:right;margin:-7px -4px 0 0;font-weight:bold;">
							<span id="imgProcOnSpanDet" style="display:block;">
								<?php 
								if($specImgArr){  
									?>
									<a href="#" onclick="toggleImageTdOn();return false;">&gt;&gt;</a>
									<?php 
								}
								?>
							</span>
							<span id="imgProcOffSpanDet" style="display:none;">
								<?php 
								if($specImgArr){  
									?>
									<a href="#" onclick="toggleImageTdOff();return false;">&lt;&lt;</a>
									<?php 
								} 
								?>
							</span>
						</div>
						<?php 
						if($editMode === 3){
							?>
							<div style="color:red;margin:10px;">
								While you are a Taxonomy Editor for this taxon, you have not been given explicit editing rights for this collection. 
								You can submit new determinations, but they will need to be approved by the collection manager 
								before they are applied.
							</div>
							<?php 
						}
						?> 
						<div style='margin:3px;'>
							<b>Identification Qualifier:</b>
							<input type="text" name="identificationqualifier" title="e.g. cf, aff, etc" />
						</div>
						<div style='margin:3px;'>
							<b>Scientific Name:</b> 
							<input type="text" id="dafsciname" name="sciname" style="background-color:lightyellow;width:350px;" onfocus="initDetAutocomplete(this.form)" />
							<input type="hidden" id="daftidtoadd" name="tidtoadd" value="" />
							<input type="hidden" name="family" value="" />
						</div>
						<div style='margin:3px;'>
							<b>Author:</b> 
							<input type="text" name="scientificnameauthorship" style="width:200px;" />
						</div>
						<div style='margin:3px;'>
							<b>Determiner:</b> 
							<input type="text" name="identifiedby" style="background-color:lightyellow;width:200px;" />
						</div>
						<div style='margin:3px;'>
							<b>Date:</b> 
							<input type="text" name="dateidentified" style="background-color:lightyellow;" onchange="detDateChanged(this.form);" />
						</div>
						<div style='margin:3px;'>
							<b>Reference:</b> 
							<input type="text" name="identificationreferences" style="width:350px;" />
						</div>
						<div style='margin:3px;'>
							<b>Notes:</b> 
							<input type="text" name="identificationremarks" style="width:350px;" />
						</div>
						<div style='margin:3px;'>
							<input type="checkbox" name="makecurrent" value="1" /> Make this the current determination
						</div>
						<div style='margin:3px;'>
							<input type="checkbox" name="printqueue" value="1" /> Add to Annotation Queue
						</div>
						<div style='margin:15px;'>
							<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
							<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
							
							<input type="hidden" name="annotatorname" value="<?php echo $annotatorname; ?>" />
							<input type="hidden" name="annotatoremail" value="<?php echo $annotatoremail; ?>" />
							<input type="hidden" name="catalognumber" value="<?php echo $catalognumber; ?>" />
							<input type="hidden" name="institutioncode" value="<?php echo $institutioncode; ?>" />
							<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
							<?php 
							if (isset($_GET['collectioncode'])) {
                                echo '<input type="hidden" name="collectioncode" value="' . $_GET['collectioncode'] . '" />';
                            }
							?>
							
							<div style="float:left;">
								<input type="submit" name="submitaction" value="Submit Determination" />
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<?php
			foreach($detArr as $detId => $detRec){
				$canEdit = 0;
				if($editMode < 3 || !$detRec['appliedstatus']) {
                    $canEdit = 1;
                }
				?>
				<div id="detdiv-<?php echo $detId;?>">
					<div>
						<?php 
						if($detRec['identificationqualifier']) {
                            echo $detRec['identificationqualifier'] . ' ';
                        }
						echo '<b><i>'.$detRec['sciname'].'</i></b> '.$detRec['scientificnameauthorship'];
						if($detRec['iscurrent'] && $detRec['appliedstatus']) {
                            echo '<span style="margin-left:10px;color:red;">CURRENT DETERMINATION</span>';
                        }
						if($canEdit){
							?>
							<a href="#" onclick="toggle('editdetdiv-<?php echo $detId;?>');return false;" title="Edit Determination"><i style="height:15px;width:15px;" class="far fa-edit"></i></a>
							<?php
						}
						if(!$detRec['appliedstatus']){
							?>
							<span style="color:red;margin-left:15px;">
								Applied Status Pending
							</span>
							<?php 
						}
						?>
					</div>
                    <?php
                    if($detRec['verbatimscientificname']){
                        ?>
                        <div style='margin:3px 0 0 15px;'>
                            <b>Verbatim Scientific Name:</b> <?php echo $detRec['verbatimscientificname']; ?>
                        </div>
                        <?php
                    }
                    ?>
					<div style='margin:3px 0 0 15px;'>
						<b>Determiner:</b> <?php echo $detRec['identifiedby']; ?>
						<span style="margin-left:40px;">
							<b>Date:</b> <?php echo $detRec['dateidentified']; ?>
						</span>
					</div>
					<?php 
					if($detRec['identificationreferences']){
						?>
						<div style='margin:3px 0 0 15px;'>
							<b>Reference:</b> <?php echo $detRec['identificationreferences']; ?>
						</div>
						<?php 
					}
					if($detRec['identificationremarks']){
						?>
						<div style='margin:3px 0 0 15px;'>
							<b>Notes:</b> <?php echo $detRec['identificationremarks']; ?>
						</div>
						<?php 
					}
					?>
				</div>
				<?php 
				if($canEdit){ 
					?>
					<div id="editdetdiv-<?php echo $detId;?>" style="display:none;margin:15px 5px;">
						<fieldset>
							<legend><b>Edit Determination</b></legend>
							<form name="deteditform" action="occurrenceeditor.php" method="post" onsubmit="return verifyDetForm(this);">
								<div style='margin:3px;'>
									<b>Identification Qualifier:</b>
									<input type="text" name="identificationqualifier" value="<?php echo $detRec['identificationqualifier']; ?>" title="e.g. cf, aff, etc" />
								</div>
								<div style='margin:3px;'>
									<b>Scientific Name:</b> 
									<input type="text" id="defsciname-<?php echo $detId;?>" name="sciname" value="<?php echo $detRec['sciname']; ?>" style="background-color:lightyellow;width:350px;" onfocus="initDetAutocomplete(this.form)" />
									<input type="hidden" id="deftidtoadd" name="tidtoadd" value="" />
									<input type="hidden" name="family" value="" />
								</div>
								<div style='margin:3px;'>
									<b>Author:</b> 
									<input type="text" name="scientificnameauthorship" value="<?php echo $detRec['scientificnameauthorship']; ?>" style="width:200px;" />
								</div>
								<div style='margin:3px;'>
									<b>Determiner:</b> 
									<input type="text" name="identifiedby" value="<?php echo $detRec['identifiedby']; ?>" style="background-color:lightyellow;width:200px;" />
								</div>
								<div style='margin:3px;'>
									<b>Date:</b> 
									<input type="text" name="dateidentified" value="<?php echo $detRec['dateidentified']; ?>" style="background-color:lightyellow;" />
								</div>
								<div style='margin:3px;'>
									<b>Reference:</b> 
									<input type="text" name="identificationreferences" value="<?php echo $detRec['identificationreferences']; ?>" style="width:350px;" />
								</div>
								<div style='margin:3px;'>
									<b>Notes:</b> 
									<input type="text" name="identificationremarks" value="<?php echo $detRec['identificationremarks']; ?>" style="width:350px;" />
								</div>
								<div style='margin:3px;'>
									<b>Sort Sequence:</b> 
									<input type="text" name="sortsequence" value="<?php echo $detRec['sortsequence']; ?>" style="width:40px;" />
								</div>
								<div style='margin:3px;'>
									<input type="checkbox" name="printqueue" value="1" /> Add to Annotation Queue
								</div>
								<div style='margin:15px;'>
									<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
									<input type="hidden" name="detid" value="<?php echo $detId; ?>" />
									<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
									<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
									<input type="submit" name="submitaction" value="Submit Determination Edits" />
								</div>
							</form>
							<?php 
							if($editMode < 3 && !$detRec['iscurrent']){
								?>
								<div style="padding:15px;background-color:lightgreen;width:280px;margin:15px;">
									<form name="detremapform" action="occurrenceeditor.php" method="post">
										<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
										<input type="hidden" name="detid" value="<?php echo $detId; ?>" />
										<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
										<input type="hidden" name="csmode" value="<?php echo $crowdSourceMode; ?>" />
										<?php 
										if($detRec['appliedstatus']){
											?>
											<input type="submit" name="submitaction" value="Make Determination Current" />
											<?php
										}
										else{
											?>
											<input type="submit" name="submitaction" value="Apply Determination" /><br/>
											<input type="checkbox" name="makecurrent" value="1" <?php echo ($detRec['iscurrent']?'checked':''); ?> /> Make Current
											<?php
										}
										?>
									</form>
								</div>
								<?php 
							}
							?>
							<div style="padding:15px;background-color:lightblue;width:155px;margin:15px;">
								<form name="detdelform" action="occurrenceeditor.php" method="post" onsubmit="return window.confirm('Are you sure you want to delete this occurrence determination?');">
									<input type="hidden" name="occid" value="<?php echo $occId; ?>" />
									<input type="hidden" name="detid" value="<?php echo $detId; ?>" />
									<input type="hidden" name="occindex" value="<?php echo $occIndex; ?>" />
									<input type="hidden" name=" <?php echo $crowdSourceMode; ?>" />
									<input type="submit" name="submitaction" value="Delete Determination" />
								</form>
							</div>
						</fieldset>
					</div>
					<?php 
				}
				?>
				<hr style='margin:10px 0 10px 0;' />
				<?php 
			}
			?>
		</fieldset>
	</div>
</div>
